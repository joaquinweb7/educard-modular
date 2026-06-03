<?php

namespace App\Http\Controllers\Admin\Certificados;

use Illuminate\Http\Request;
use App\Models\Certificados\PlantillaCertificado;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PlantillaCertificadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $query = PlantillaCertificado::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%");
        }
        $plantillas = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();

        return view('admin.certificados.plantilla.index', compact('plantillas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.certificados.plantilla.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'imagen' => 'required|mimes:png,jpg,jpeg|max:5048',
            'nombre_estudiante_x' => 'nullable|integer|min:0',
            'nombre_estudiante_y' => 'nullable|integer|min:0',
            'nombre_curso_x' => 'nullable|integer|min:0',
            'nombre_curso_y' => 'nullable|integer|min:0',
            'qr_x' => 'nullable|integer|min:0',
            'qr_y' => 'nullable|integer|min:0',
            'codigo_x' => 'nullable|integer|min:0',
            'codigo_y' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            // Guardar el archivo en el directorio 'bg_certificados' y obtener su ruta
            $path = $request->file('imagen')->store('certificados/background_images', 'public');
            $bg = new PlantillaCertificado();
            $bg->nombre = $request->nombre;
            $bg->imagen = $path;
            $bg->nombre_estudiante_x = $request->nombre_estudiante_x;
            $bg->nombre_estudiante_y = $request->nombre_estudiante_y;
            $bg->nombre_curso_x = $request->nombre_curso_x;
            $bg->nombre_curso_y = $request->nombre_curso_y;
            $bg->qr_x = $request->qr_x;
            $bg->qr_y = $request->qr_y;
            $bg->codigo_x = $request->codigo_x;
            $bg->codigo_y = $request->codigo_y;
            $bg->save();

            return redirect()->route('admin.certificados.plantilla.index')->with('success', 'Bg creado correctamente');
        }
        return back()->withErrors([
            'error' => 'Ha ocurrido un error'
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(PlantillaCertificado $plantilla)
    {
    }

    /**
     * Show the drag and drop designer.
     */
    public function designer(PlantillaCertificado $plantilla)
    {
        return view('admin.certificados.plantilla.designer', compact('plantilla'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlantillaCertificado $plantilla)
    {
            // Obtener información del archivo de imagen
        $imageInfo = null;
        if ($plantilla->imagen && Storage::disk('public')->exists($plantilla->imagen)) {
            $imageInfo = [
                'fileSize' => Storage::disk('public')->size($plantilla->imagen),
                'lastModified' => Storage::disk('public')->lastModified($plantilla->imagen)
            ];
        }
        
        return view('admin.certificados.plantilla.edit', compact('plantilla', 'imageInfo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlantillaCertificado $plantilla)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'imagen' => 'nullable|mimes:png,jpg,jpeg|max:5048',
            'nombre_estudiante_x' => 'nullable|integer|min:0',
            'nombre_estudiante_y' => 'nullable|integer|min:0',
            'nombre_curso_x' => 'nullable|integer|min:0',
            'nombre_curso_y' => 'nullable|integer|min:0',
            'qr_x' => 'nullable|integer|min:0',
            'qr_y' => 'nullable|integer|min:0',
            'codigo_x' => 'nullable|integer|min:0',
            'codigo_y' => 'nullable|integer|min:0',
            'design_json' => 'nullable|string',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('imagen')) {
            if ($plantilla->imagen) {
                Storage::disk('public')->delete($plantilla->imagen);
            }
            $plantilla->imagen = $request->imagen->store('certificados/background_images', 'public');
        }

        $plantilla->nombre = $request->nombre;
        $plantilla->nombre_estudiante_x = $request->nombre_estudiante_x ?? $plantilla->nombre_estudiante_x;
        $plantilla->nombre_estudiante_y = $request->nombre_estudiante_y ?? $plantilla->nombre_estudiante_y;
        $plantilla->nombre_curso_x = $request->nombre_curso_x ?? $plantilla->nombre_curso_x;
        $plantilla->nombre_curso_y = $request->nombre_curso_y ?? $plantilla->nombre_curso_y;
        $plantilla->qr_x = $request->qr_x ?? $plantilla->qr_x;
        $plantilla->qr_y = $request->qr_y ?? $plantilla->qr_y;
        $plantilla->codigo_x = $request->codigo_x ?? $plantilla->codigo_x;
        $plantilla->codigo_y = $request->codigo_y ?? $plantilla->codigo_y;
        
        if ($request->has('design_json')) {
            $plantilla->design_json = $request->design_json;
        }
        if ($request->has('width')) {
            // The frontend sends width in cm, we store pixels or what was calculated.
            // Wait, designer.blade.php sends width straight from the hidden input. 
            // I already set the hidden input to just `width`. No, wait! 
            // If the frontend form sends `width` in cm or px? 
            // Let's just store the exact width and height the frontend sent. The designer converts cm to px and sets the dim-badge, but the hidden inputs `f-width` and `f-height` store the CM values or pixels?
            // Wait, in my `designer.blade.php` it stores CM? Let's check: `<input type="hidden" name="width" id="f-width" value="{{ $plantilla->width ?? 1056 }}">`
            // Yes, so it uses the raw px value (1056). 
            $plantilla->width = $request->width;
        }
        if ($request->has('height')) {
            $plantilla->height = $request->height;
        }
        
        $plantilla->save();

        return redirect()->route('admin.certificados.plantilla.edit', $plantilla->id)->with('success', 'Plantilla Actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlantillaCertificado $plantilla)
    {
        Storage::disk('public')->delete($plantilla->imagen);
        $plantilla->delete();


        // Verificar si el curso tiene certificados asociados
        if ($plantilla->certificados()->count() > 0) {
            return redirect()->route('admin.certificados.plantilla.index')->with('error', 'No se puede eliminar la plantilla porque tiene certificados asociados');
        }

        if ($plantilla->delete()) {
            return redirect()->route('admin.certificados.plantilla.index')->with('success','Plantilla eliminado correctamente');
        }
        return back()->with('error', 'Error al eliminar el curso');
    }
}
