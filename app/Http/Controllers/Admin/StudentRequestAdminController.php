<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRequest;
use App\Services\StudentCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentRequest::with(['career', 'semester'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('names', 'like', '%'.$request->search.'%')
                    ->orWhere('lastnames', 'like', '%'.$request->search.'%')
                    ->orWhere('ci_number', 'like', '%'.$request->search.'%')
                    ->orWhere('procedure_number', 'like', '%'.$request->search.'%');
            });
        }

        return view('admin.requests.index', ['requests' => $query->paginate(15)->withQueryString()]);
    }

    public function show(StudentRequest $request)
    {
        $request->load(['career', 'semester', 'reviewer']);
        return view('admin.requests.show', ['studentRequest' => $request]);
    }

    public function approve(StudentRequest $request)
    {
        DB::transaction(function () use ($request) {
            $student = Student::firstOrNew(['ci_number' => $request->ci_number]);
            if (! $student->exists || ! $student->student_code) {
                // Usamos el student_code que heredó desde Trámites al hacer la solicitud
                $student->student_code = $request->student_code;
            }
            $student->fill([
                'names' => $request->names,
                'lastnames' => $request->lastnames,
                'career_id' => $request->career_id,
                'semester_id' => $request->semester_id,
                'gestion' => $request->gestion,
                'turno' => $request->turno,
                'grupo' => $request->grupo,
                'photo_path' => $request->photo_path,
                'status' => 'active',
                'approved_request_id' => $request->id,
            ]);
            $student->save();

            $request->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'observation' => null,
            ]);
        });

        return redirect()->route('admin.requests.show', $request)->with('success', 'Solicitud aprobada correctamente.');
    }

    public function reject(StudentRequest $request, Request $httpRequest)
    {
        $data = $httpRequest->validate(['observation' => ['nullable', 'string', 'max:1000']]);
        $request->update([
            'status' => 'rejected',
            'observation' => $data['observation'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        return redirect()->route('admin.requests.show', $request)->with('success', 'Solicitud rechazada.');
    }

    public function observe(StudentRequest $request, Request $httpRequest)
    {
        $data = $httpRequest->validate(['observation' => ['required', 'string', 'max:1000']]);
        $request->update([
            'status' => 'observed',
            'observation' => $data['observation'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        return redirect()->route('admin.requests.show', $request)->with('success', 'Solicitud observada.');
    }
    public function destroy(StudentRequest $request)
    {
        // Si hay una foto asociada, eliminarla del storage
        if ($request->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($request->photo_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($request->photo_path);
        }

        $request->delete();

        return redirect()->route('admin.requests.index')->with('success', 'Solicitud y datos asociados eliminados correctamente.');
    }
}
