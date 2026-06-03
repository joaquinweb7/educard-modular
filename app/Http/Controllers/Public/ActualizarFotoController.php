<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Carnet;
use App\Models\CarnetPhotoUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActualizarFotoController extends Controller
{
    public function create(Request $request)
    {
        $carnet = null;
        if ($request->filled('code')) {
            $carnet = Carnet::where('codigo_estudiante', $request->code)->first();
        }

        return view('public.actualizar-foto', compact('carnet'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_estudiante' => 'required|string',
            'photo' => 'required|image|max:2048'
        ]);

        $carnet = Carnet::where('codigo_estudiante', $request->codigo_estudiante)->firstOrFail();

        // Guardar la foto
        $path = $request->file('photo')->store('photos/updates', 'public');

        // Crear la solicitud
        CarnetPhotoUpdate::create([
            'carnet_id' => $carnet->id,
            'codigo_estudiante' => $carnet->codigo_estudiante,
            'photo_path' => $path,
            'status' => 'pending'
        ]);

        return redirect()->route('public.actualizar-foto.create')->with('success', 'Tu solicitud de actualización de foto ha sido enviada correctamente. Espera a que sea aprobada por administración.');
    }
}
