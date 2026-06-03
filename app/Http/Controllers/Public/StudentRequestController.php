<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Semester;
use App\Models\StudentRequest;
use App\Services\ProcedureNumberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class StudentRequestController extends Controller
{
    // ── Mensajes de validación en español limpio ─────────────────────────────
    private array $messages = [
        'names.required'     => 'El campo Nombres es obligatorio.',
        'names.max'          => 'Los nombres no pueden superar 150 caracteres.',
        'lastnames.required' => 'El campo Apellidos es obligatorio.',
        'lastnames.max'      => 'Los apellidos no pueden superar 150 caracteres.',
        'ci_number.required' => 'La cédula de identidad es obligatoria.',
        'ci_number.max'      => 'La cédula no puede superar 50 caracteres.',
        'career_id.required' => 'Debes seleccionar una carrera.',
        'career_id.exists'   => 'La carrera seleccionada no es válida.',
        'semester_id.required' => 'Debes seleccionar un semestre.',
        'semester_id.exists'   => 'El semestre seleccionado no es válido.',
        'gestion.required'   => 'El campo Gestión es obligatorio.',
        'turno.required'     => 'Debes seleccionar un turno.',
        'grupo.required'     => 'El campo Grupo es obligatorio.',
        'photo.required'     => 'La fotografía es obligatoria.',
        'photo.image'        => 'El archivo debe ser una imagen (JPG o PNG).',
        'photo.mimes'        => 'Solo se aceptan imágenes en formato JPG o PNG.',
        'photo.max'          => 'La imagen no puede pesar más de 2 MB.',
    ];

    // ── Formulario de nueva solicitud ─────────────────────────────────────────
    public function create()
    {
        $enabled = \App\Models\Setting::get('public_form_enabled', '1') == '1';
        
        return view('public.student-requests.create', [
            'careers'   => Career::where('status', 'active')->orderBy('name')->get(),
            'semesters' => Semester::where('status', 'active')->orderBy('number')->get(),
            'enabled'   => $enabled,
        ]);
    }

    public function validateCi($ci)
    {
        // 1. Validar si ya existe una solicitud en Educard
        $existingRequest = \App\Models\StudentRequest::where('ci_number', $ci)->first();
        if ($existingRequest) {
            return response()->json([
                'success' => false, 
                'has_request' => true,
                'procedure_number' => $existingRequest->procedure_number,
                'message' => 'Ya tienes una solicitud realizada.'
            ], 409); // 409 Conflict
        }

        $apiUrl = Setting::get('tramites_api_url');
        $apiKey = Setting::get('tramites_api_key');

        if (!$apiUrl || !$apiKey) {
            return response()->json(['success' => false, 'message' => 'API de Trámites no configurada en Educard.'], 500);
        }

        try {
            $response = Http::withHeaders(['X-API-KEY' => $apiKey, 'Accept' => 'application/json'])
                ->get("{$apiUrl}/estudiantes/ci/{$ci}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['success' => false, 'message' => 'Estudiante no encontrado en el sistema de trámites.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error de conexión con el sistema de trámites.'], 500);
        }
    }

    // ── Guardar nueva solicitud ───────────────────────────────────────────────
    public function store(Request $request, ProcedureNumberService $procedureService)
    {
        if (\App\Models\Setting::get('public_form_enabled', '1') != '1') {
            abort(403, 'La recepción de solicitudes se encuentra cerrada.');
        }

        $data = $request->validate([
            'names'       => ['required', 'string', 'max:150'],
            'lastnames'   => ['required', 'string', 'max:150'],
            'ci_number'   => ['required', 'string', 'max:50'],
            'career_name'   => ['required', 'string', 'max:150'],
            'semester_name' => ['required', 'string', 'max:150'],
            'gestion'       => ['required', 'string', 'max:50'],
            'turno'         => ['required', 'string', 'max:50'],
            'grupo'         => ['required', 'string', 'max:50'],
            'photo'       => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], $this->messages);

        $photoPath = $request->file('photo')->store('student-requests/photos', 'public');

        // Validar si el estudiante ya tiene una solicitud
        $existingRequest = \App\Models\StudentRequest::where('ci_number', $data['ci_number'])->first();
        if ($existingRequest) {
            return back()->withInput()->with([
                'error' => 'Ya existe una solicitud registrada para este C.I. Tu número de trámite es: ' . $existingRequest->procedure_number,
                'duplicate_request' => true
            ]);
        }

        // Validar con la API de Trámites
        $apiUrl = Setting::get('tramites_api_url');
        $apiKey = Setting::get('tramites_api_key');
        
        $response = Http::withHeaders(['X-API-KEY' => $apiKey, 'Accept' => 'application/json'])
            ->get("{$apiUrl}/estudiantes/ci/{$data['ci_number']}");

        if (!$response->successful()) {
            return back()->withInput()->with('error', 'Tu carnet no está registrado en el sistema de Trámites. Debes inscribirte primero.');
        }

        $apiData = $response->json('data');

        $careerName = $apiData['career'] ?? $data['career_name'];
        $career = \App\Models\Career::firstOrCreate(
            ['name' => $careerName],
            ['status' => 'active']
        );

        $semesterName = $apiData['semester'] ?? $data['semester_name'];
        preg_match('/\d+/', $semesterName, $matches);
        $semesterNumber = !empty($matches) ? (int)$matches[0] : null;

        if ($semesterNumber) {
            $semester = \App\Models\Semester::where('number', $semesterNumber)->first();
            if (!$semester) {
                $semester = \App\Models\Semester::create([
                    'name' => $semesterName,
                    'number' => $semesterNumber,
                    'status' => 'active'
                ]);
            }
        } else {
            $semester = \App\Models\Semester::where('name', $semesterName)->first();
            if (!$semester) {
                $maxNumber = \App\Models\Semester::max('number') ?? 0;
                $semester = \App\Models\Semester::create([
                    'name' => $semesterName,
                    'number' => $maxNumber + 1,
                    'status' => 'active'
                ]);
            }
        }

        $studentRequest = StudentRequest::create([
            'procedure_number' => $procedureService->generate(),
            'student_code'     => $apiData['student_code'],
            'names'            => $apiData['first_name'] ?? $data['names'],
            'lastnames'        => $apiData['last_name'] ?? $data['lastnames'],
            'ci_number'        => $data['ci_number'],
            'career_id'        => $career->id,
            'semester_id'      => $semester->id,
            'gestion'          => $apiData['gestion'] ?? $data['gestion'],
            'turno'            => $apiData['turno'] ?? $data['turno'],
            'grupo'            => $apiData['grupo'] ?? $data['grupo'],
            'photo_path'       => $photoPath,
            'status'           => 'pending',
            'submitted_at'     => now(),
        ]);

        // Registrar combinación automáticamente en asignaciones
        \App\Models\AcademicAssignment::firstOrCreate(
            [
                'career_id'   => $career->id,
                'semester_id' => $semester->id,
                'gestion'     => $apiData['gestion'] ?? $data['gestion'],
                'turno'       => $apiData['turno'] ?? $data['turno'],
                'grupo'       => $apiData['grupo'] ?? $data['grupo'],
            ],
            ['status' => 'active']
        );

        // Ejecutar validación de IA
        try {
            $validationService = new \App\Services\PhotoValidationService();
            $validationResult = $validationService->validate($photoPath);
            
            $studentRequest->update([
                'photo_validation_status' => $validationResult['status'] ?? 'manual_review',
                'photo_validation_details' => json_encode($validationResult['details'] ?? [])
            ]);
        } catch (\Exception $e) {
            // Failsafe
            $studentRequest->update([
                'photo_validation_status' => 'manual_review',
                'photo_validation_details' => json_encode(['error' => $e->getMessage()])
            ]);
        }

        return redirect()->route('public.student-request.show', $studentRequest->procedure_number);
    }

    // ── Constancia / estado ───────────────────────────────────────────────────
    public function show(string $procedureNumber)
    {
        $studentRequest = StudentRequest::with(['career', 'semester'])
            ->where('procedure_number', $procedureNumber)
            ->firstOrFail();

        return view('public.student-requests.show', compact('studentRequest'));
    }

    public function edit(string $procedureNumber)
    {
        $studentRequest = StudentRequest::with(['career', 'semester'])
            ->where('procedure_number', $procedureNumber)
            ->firstOrFail();

        // Solo se puede corregir si NO está aprobada
        if ($studentRequest->status === 'approved') {
            return redirect()
                ->route('public.student-request.show', $procedureNumber)
                ->with('info', 'Tu solicitud ya fue aprobada y no puede ser modificada.');
        }

        return view('public.student-requests.edit', [
            'studentRequest' => $studentRequest,
            'careers'        => Career::where('status', 'active')->orderBy('name')->get(),
            'semesters'      => Semester::where('status', 'active')->orderBy('number')->get(),
        ]);
    }

    // ── Guardar corrección (reenvío con mismo número de trámite) ──────────────
    public function resubmit(Request $request, string $procedureNumber)
    {
        $studentRequest = StudentRequest::where('procedure_number', $procedureNumber)->firstOrFail();

        if ($studentRequest->status === 'approved') {
            return redirect()
                ->route('public.student-request.show', $procedureNumber)
                ->with('info', 'Tu solicitud ya fue aprobada y no puede ser modificada.');
        }

        $photoRules = $studentRequest->photo_path
            ? ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
            : ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'];

        $data = $request->validate([
            'names'       => ['required', 'string', 'max:150'],
            'lastnames'   => ['required', 'string', 'max:150'],
            'ci_number'   => ['required', 'string', 'max:50'],
            'career_id'   => ['required', 'exists:careers,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'gestion'     => ['required', 'string', 'max:50'],
            'turno'       => ['required', 'string', 'max:50'],
            'grupo'       => ['required', 'string', 'max:50'],
            'photo'       => $photoRules,
        ], array_merge($this->messages, [
            'photo.required' => 'Debes subir una fotografía actualizada.',
        ]));

        // Si se sube una nueva foto, reemplazar la anterior
        if ($request->hasFile('photo')) {
            if ($studentRequest->photo_path) {
                Storage::disk('public')->delete($studentRequest->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('student-requests/photos', 'public');
        }

        $studentRequest->update([
            'names'        => $data['names'],
            'lastnames'    => $data['lastnames'],
            'ci_number'    => $data['ci_number'],
            'career_id'    => $data['career_id'],
            'semester_id'  => $data['semester_id'],
            'gestion'      => $data['gestion'],
            'turno'        => $data['turno'],
            'grupo'        => $data['grupo'],
            'photo_path'   => $data['photo_path'] ?? $studentRequest->photo_path,
            'status'       => 'resubmitted',      // cambia a reenviado
            'observation'  => null,               // limpia la observación
            'reviewed_by'  => null,
            'reviewed_at'  => null,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('public.student-request.show', $procedureNumber)
            ->with('success', '¡Solicitud reenviada correctamente! El número de trámite se mantiene.');
    }

    // ── Consulta pública por número de trámite ────────────────────────────────
    public function track(Request $request)
    {
        $studentRequest = null;

        if ($request->filled('tramite')) {
            $procedureNumber = strtoupper(trim($request->input('tramite')));
            $studentRequest  = StudentRequest::with(['career', 'semester'])
                ->where('procedure_number', $procedureNumber)
                ->first();

            if (! $studentRequest) {
                return back()
                    ->withInput()
                    ->with('error', 'No se encontró ningún trámite con el número "' . $procedureNumber . '". Verifica e intenta de nuevo.');
            }
        }

        return view('public.student-requests.track', compact('studentRequest'));
    }

    // ── Descargar constancia PDF ──────────────────────────────────────────────
    public function downloadCertificate(string $procedureNumber)
    {
        $studentRequest = StudentRequest::with(['career', 'semester'])
            ->where('procedure_number', $procedureNumber)
            ->firstOrFail();

        $pdf = Pdf::loadView('public.student-requests.constancy-pdf', compact('studentRequest'))
            ->setPaper('letter');

        return $pdf->download('Constancia_Solicitud_' . $studentRequest->procedure_number . '.pdf');
    }

    public function validatePhotoAdvanced(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048', 'dimensions:min_width=500,min_height=500']
        ]);

        $file = $request->file('photo');
        $tempPath = $file->store('temp_photos', 'public');

        try {
            $validationService = new \App\Services\PhotoValidationService();
            $result = $validationService->validate($tempPath);
            
            // Clean up temp file
            \Illuminate\Support\Facades\Storage::disk('public')->delete($tempPath);

            return response()->json([
                'success' => true,
                'status' => $result['status'],
                'details' => $result['details']
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($tempPath);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
