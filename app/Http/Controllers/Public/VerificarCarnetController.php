<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Carnet;
use Illuminate\Http\Request;

class VerificarCarnetController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string|max:100'
        ]);

        $foto = null;
        if (!($request->code == null)) {
            $carnet = Carnet::where('codigo_estudiante', $request->code)->first();
            
            if ($carnet) {
                // Buscar la foto: primero en la tabla carnets, si no, en la tabla students (desde trámites)
                if (!empty($carnet->foto)) {
                    $foto = $carnet->foto;
                } else {
                    $student = \App\Models\Student::where('student_code', $carnet->codigo_estudiante)->first();
                    if ($student && !empty($student->photo_path)) {
                        $foto = $student->photo_path;
                    }
                }
            }
        }else{
            $carnet = null;
        }
        

        return view('public.verificar-carnet', compact('carnet', 'foto'));
    }

}
