<?php

namespace App\Http\Controllers\Admin\Certificados;

use Illuminate\Http\Request;
use App\Models\Certificados\Curso;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Certificados\Certificado;
use App\Models\Certificados\PlantillaCertificado;
use Barryvdh\DomPDF\Facade\Pdf;
use Faker\Factory as Faker;

class GeneradorCertificadoController extends Controller
{
    public function cargar()
    {
        $plantillas = PlantillaCertificado::select('id as value', 'nombre', 'imagen')->get();
        $cursos = Curso::all();
        return view('admin.certificados.main', compact('plantillas', 'cursos'));
    }

    public function changeImage(Request $request){
        $request->validate([
            'image' => 'required|image|mimes:png|max:4048',
        ]);
        Storage::put('template_low.png', $request->file('image'));
        return response()->json([
            'message' => 'Imagen actualizada con éxito',
        ]);
    }

    public function generate(Request $request)
    {
        $batchId = $request->input('batch_id');
        if (!$batchId) {
            abort(400, 'No se proporcionó un ID de lote');
        }

        $certificados = Certificado::with('curso', 'plantilla')->where('batch_id', $batchId)->get();

        if ($certificados->isEmpty()) {
            abort(404, 'No hay estudiantes registrados en este lote');
        }

        $plantilla = $certificados->first()->plantilla;
        $design = $plantilla->design_json ? json_decode($plantilla->design_json, true) : ['objects' => []];
        $elements = $design['objects'] ?? [];
        
        $width = $plantilla->width ?? 1056;
        $height = $plantilla->height ?? 816;

        $html = '<!DOCTYPE html><html><head><style>
            @page { margin: 0; size: ' . round($width * 0.75) . 'px ' . round($height * 0.75) . 'px; }
            body { margin: 0; padding: 0; font-family: sans-serif; }
            .page { position: relative; width: ' . $width . 'px; height: ' . $height . 'px; page-break-after: always; transform-origin: top left; transform: scale(0.75); }
            .bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; }
            .element { position: absolute; line-height: 1.2; }
        </style></head><body>';

        foreach ($certificados as $cert) {
            $html .= '<div class="page">';
            
            if ($plantilla->imagen) {
                $bgPath = storage_path('app/public/' . $plantilla->imagen);
                if (file_exists($bgPath)) {
                    $type = pathinfo($bgPath, PATHINFO_EXTENSION);
                    $data = file_get_contents($bgPath);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    $html .= '<img class="bg" src="' . $base64 . '">';
                }
            }

            $variables = [
                'NOMBRE_ESTUDIANTE' => $cert->nombre_estudiante,
                'CURSO' => $cert->curso->nombre ?? '',
                'CARNET' => $cert->carnet,
                'CODIGO' => $cert->codigo,
                'EMAIL' => $cert->email,
                'FECHA_ACTUAL' => now()->format('d/m/Y')
            ];

            // Si no hay design JSON, hacer un fallback para probar coordenadas hardcodeadas
            if (empty($elements)) {
                $styleNombre = "left: {$plantilla->nombre_estudiante_x}px; top: {$plantilla->nombre_estudiante_y}px; font-size: 30px; font-weight: bold; width: 100%; text-align: center;";
                $html .= '<div class="element" style="' . $styleNombre . '">' . htmlspecialchars($cert->nombre_estudiante) . '</div>';
                
                $styleCurso = "left: {$plantilla->nombre_curso_x}px; top: {$plantilla->nombre_curso_y}px; font-size: 25px; font-weight: bold; width: 100%; text-align: center;";
                $html .= '<div class="element" style="' . $styleCurso . '">' . htmlspecialchars($cert->curso->nombre ?? '') . '</div>';
                
                $styleCodigo = "left: {$plantilla->codigo_x}px; top: {$plantilla->codigo_y}px; font-size: 16px;";
                $html .= '<div class="element" style="' . $styleCodigo . '">' . htmlspecialchars($cert->codigo) . '</div>';
            } else {
                foreach ($elements as $el) {
                    $content = $el['content'] ?? '';
                    preg_match_all('/\[([^\]]+)\]/', $content, $matches);
                    foreach ($matches[1] as $match) {
                        $varName = trim(strtoupper($match));
                        $replacement = $variables[$varName] ?? '';
                        $content = str_replace('[' . $match . ']', $replacement, $content);
                    }

                    $w = (isset($el['width']) && $el['width'] > 0) ? $el['width'] . 'px' : 'auto';
                    $h = (isset($el['height']) && $el['height'] > 0) ? $el['height'] . 'px' : 'auto';
                    $align = $el['textAlign'] ?? 'left';
                    $color = $el['color'] ?? '#000';
                    $size = $el['fontSize'] ?? '16';
                    $weight = $el['fontWeight'] ?? 'normal';
                    
                    $display = (isset($el['width']) && $el['width'] > 0) ? 'display: block; text-align: ' . $align . ';' : '';
                    $style = "left: {$el['x']}px; top: {$el['y']}px; width: {$w}; height: {$h}; color: {$color}; font-size: {$size}px; font-weight: {$weight}; {$display}";
                    
                    $html .= '<div class="element" style="' . $style . '">' . nl2br(htmlspecialchars($content)) . '</div>';
                }
            }
            $html .= '</div>';
        }

        $html .= '</body></html>';

        $pdf = Pdf::loadHTML($html)->setPaper([0, 0, round($width * 0.75), round($height * 0.75)]);
        return $pdf->download('Certificados_' . $batchId . '.pdf');
    }

    public function downloadZip()
    {
        // Obsoleto, la generación ahora es un PDF multipágina unificado
        return back()->with('error', 'El archivo ZIP ya no es necesario, descargue el PDF directamente.');
    }

    public function test(PlantillaCertificado $plantilla)
    {
        // Test provisional con fallback html
        return "Proceso actualizado. Usa el diseñador Drag and Drop o genera desde el sistema principal.";
    }
}
