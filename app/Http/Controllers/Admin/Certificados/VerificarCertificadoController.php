<?php

namespace App\Http\Controllers\Admin\Certificados;

use App\Http\Controllers\Controller;
use App\Models\Certificados\Certificado;
use Illuminate\Http\Request;

class VerificarCertificadoController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string|max:100'
        ]);

        $certificado = Certificado::where('codigo', $request->code)->first();

        return view('admin.certificados.verificacion.verificar', compact('certificado'));
    }


    public function descargarView($codigo)
    {
        $certificado = Certificado::where('codigo', $codigo)->first();
        return view('admin.certificados.verificacion.descargar', compact('certificado'));
    }

    public function descargarCertificado(Request $request, $codigo)
    {
        $certificado = Certificado::where('codigo', $codigo)->where('carnet', $request->input('carnet'))->first();

        if (!$certificado) {
            return redirect()->back()->with('error', 'No se encontró el certificado o no coincide el carnet.');
        }

        $pdf = new GeneradorCertificadoController();
        $pdf = $pdf->genCert($certificado->nombre_estudiante, $certificado->curso->nombre, $certificado->codigo, $certificado->plantilla);
        $pdf->Output($codigo . '.pdf', 'D');

        return back()->with('success', 'Certificado descargado con éxito.');
    }

    
}
