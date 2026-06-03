<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Credencial;
use Illuminate\Http\Request;

class VerificarCredencialController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string|max:100'
        ]);

        $foto = null;
        if (!($request->code == null)) {
            $credencial = Credencial::where('codigo_credencial', $request->code)->first();
            
            if ($credencial) {
                if (!empty($credencial->foto)) {
                    $foto = $credencial->foto;
                }
                // Nota: a diferencia de carnets, administrativos por ahora no tienen otra tabla (como students)
                // así que solo obtenemos la foto si existe en la misma tabla credenciales.
            }
        }else{
            $credencial = null;
        }
        

        return view('public.verificar-credencial', compact('credencial', 'foto'));
    }

}
