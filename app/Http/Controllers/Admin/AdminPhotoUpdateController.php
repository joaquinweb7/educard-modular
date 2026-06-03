<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarnetPhotoUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPhotoUpdateController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        
        $updates = CarnetPhotoUpdate::with('carnet')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.carnets.photo_updates.index', compact('updates', 'status'));
    }

    public function approve(CarnetPhotoUpdate $update)
    {
        if ($update->status !== 'pending') {
            return back()->with('error', 'Solo se pueden aprobar solicitudes pendientes.');
        }

        // Actualizar el carnet
        $carnet = $update->carnet;
        
        // Borrar foto anterior del carnet si existe y no es la misma
        if ($carnet->foto && Storage::disk('public')->exists($carnet->foto) && $carnet->foto !== $update->photo_path) {
            Storage::disk('public')->delete($carnet->foto);
        }

        $carnet->foto = $update->photo_path;
        $carnet->save();

        // Marcar como aprobada
        $update->status = 'approved';
        $update->save();

        return back()->with('success', 'Foto aprobada y carnet actualizado exitosamente.');
    }

    public function reject(Request $request, CarnetPhotoUpdate $update)
    {
        $request->validate([
            'observation' => 'required|string|max:255'
        ]);

        if ($update->status !== 'pending') {
            return back()->with('error', 'Solo se pueden rechazar solicitudes pendientes.');
        }

        $update->status = 'rejected';
        $update->observation = $request->observation;
        $update->save();

        return back()->with('success', 'Solicitud rechazada correctamente.');
    }
}
