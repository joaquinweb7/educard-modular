@extends('layouts.admin')
@section('heading', 'Detalle de Solicitud')
@section('content')

<div style="margin-bottom: 16px;">
    <a href="{{ route('admin.requests.index') }}" class="btn secondary sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;vertical-align:-2px"><path d="M19 12H5"></path><polyline points="12 19 5 12 12 5"></polyline></svg>
        Volver a Solicitudes
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:16px">
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- Info principal --}}
        <div class="panel" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: none; border-radius: 12px; overflow: hidden; padding: 0;">
            <div style="background: var(--surface-2); padding: 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="panel-title mb-0" style="font-size: 20px; color: var(--text); margin-bottom:4px;">Análisis de Fotografía</h2>
                    <div class="muted small">Trámite: <span class="procedure-code" style="font-size: 13px; padding: 4px 12px; background: rgba(79, 70, 229, 0.1); color: var(--primary); font-weight: 600; border-radius: 20px;">{{ $studentRequest->procedure_number }}</span></div>
                </div>
                <span class="badge {{ $studentRequest->status }}" style="font-size: 14px; padding: 6px 16px; border-radius: 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                    {{ ['pending'=>'Pendiente','resubmitted'=>'RE-ENVIADO','approved'=>'Aprobada','rejected'=>'Rechazada','observed'=>'Observada'][$studentRequest->status] ?? $studentRequest->status }}
                </span>
            </div>

            <div style="padding: 24px;">
                @php
                    $photoPath = $studentRequest->photo_path ? storage_path('app/public/' . $studentRequest->photo_path) : null;
                    $photoSize = 0;
                    $photoWidth = 0;
                    $photoHeight = 0;
                    if($photoPath && file_exists($photoPath)) {
                        $photoSize = filesize($photoPath);
                        $dimensions = @getimagesize($photoPath);
                        if($dimensions) {
                            $photoWidth = $dimensions[0];
                            $photoHeight = $dimensions[1];
                        }
                    }
                    $sizeFormatted = $photoSize > 1048576 ? round($photoSize / 1048576, 2) . ' MB' : round($photoSize / 1024, 2) . ' KB';
                    $validSize = $photoSize > 0 && $photoSize <= 2097152; // Max 2MB
                    $validDimensions = $photoWidth >= 300 && $photoHeight >= 300;
                    $details = json_decode($studentRequest->photo_validation_details ?? '{}', true);
                @endphp

                <div style="display:flex;gap:32px;">
                    <div style="flex: 0 0 160px;">
                        @if($studentRequest->photo_path)
                            <img src="{{ asset('storage/'.$studentRequest->photo_path) }}"
                                 class="photo-preview" alt="Foto del estudiante" style="width: 160px; height: 160px; object-fit: cover; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 2px solid var(--border);">
                        @else
                            <div class="photo-preview" style="width: 160px; height: 160px; border-radius: 12px; background: var(--surface-3); display: flex; align-items: center; justify-content: center; color: var(--text-dim); border: 2px dashed var(--border);">Sin foto</div>
                        @endif
                    </div>
                    
                    <div style="flex:1;">
                        <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary)"><rect x="3" y="11" width="18" height="10" rx="2"></rect><circle cx="12" cy="5" r="2"></circle><path d="M12 7v4"></path><line x1="8" y1="16" x2="8" y2="16"></line><line x1="16" y1="16" x2="16" y2="16"></line></svg> 
                            Validaciones Técnicas y de IA
                            @if($studentRequest->photo_validation_status === 'passed')
                                <span class="badge success">Aprobada</span>
                            @elseif($studentRequest->photo_validation_status === 'manual_review')
                                <span class="badge warning">Revisión Manual</span>
                            @elseif($studentRequest->photo_validation_status === 'failed')
                                <span class="badge danger">Falló</span>
                            @endif
                        </h3>

                        @if($studentRequest->photo_validation_status === 'pending')
                            <div style="background: var(--surface-2); padding: 24px; border-radius: 8px; border: 1px dashed var(--border); text-align: center; color: var(--text-muted);">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom: 8px; color: var(--warning);"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <div>El análisis de la fotografía por Inteligencia Artificial aún está pendiente o no se completó.<br><small>Es posible que deba ejecutar el servicio de validación de imágenes.</small></div>
                            </div>
                        @else
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: var(--surface-2); padding: 16px; border-radius: 8px; border: 1px solid var(--border);">
                                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid var(--border-light); padding-bottom: 8px;">
                                    <span style="font-size:13.5px; color:var(--text-muted)">Peso del archivo:</span>
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <span style="font-weight:600;">{{ $sizeFormatted }}</span>
                                        {!! $validSize ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--success)"><polyline points="20 6 9 17 4 12"></polyline></svg>' : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--warning)"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>' !!}
                                    </div>
                                </div>
                                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid var(--border-light); padding-bottom: 8px;">
                                    <span style="font-size:13.5px; color:var(--text-muted)">Dimensiones (px):</span>
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <span style="font-weight:600;">{{ $photoWidth }} x {{ $photoHeight }}</span>
                                        {!! $validDimensions ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--success)"><polyline points="20 6 9 17 4 12"></polyline></svg>' : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--warning)"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>' !!}
                                    </div>
                                </div>

                                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid var(--border-light); padding-bottom: 8px;">
                                    <span style="font-size:13.5px; color:var(--text-muted)">Relación 1:1:</span>
                                    <span>{!! isset($details['aspect_ratio_1_1']) && $details['aspect_ratio_1_1'] ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--success)"><polyline points="20 6 9 17 4 12"></polyline></svg>' : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--danger)"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' !!}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid var(--border-light); padding-bottom: 8px;">
                                    <span style="font-size:13.5px; color:var(--text-muted)">Fondo rojo:</span>
                                    <span>{!! isset($details['red_background']) && $details['red_background'] ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--success)"><polyline points="20 6 9 17 4 12"></polyline></svg>' : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--danger)"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' !!}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid var(--border-light); padding-bottom: 8px;">
                                    <span style="font-size:13.5px; color:var(--text-muted)">Rostro detectado:</span>
                                    <span>{!! isset($details['face_detected']) && $details['face_detected'] ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--success)"><polyline points="20 6 9 17 4 12"></polyline></svg>' : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--danger)"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' !!}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid var(--border-light); padding-bottom: 8px;">
                                    <span style="font-size:13.5px; color:var(--text-muted)">Vestimenta formal:</span>
                                    <span>{!! isset($details['formal_attire_heuristic']) && $details['formal_attire_heuristic'] ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--success)"><polyline points="20 6 9 17 4 12"></polyline></svg>' : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--danger)"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' !!}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; align-items:center; grid-column: span 2;">
                                    <span style="font-size:13.5px; color:var(--text-muted)">Cabello recogido / orejas descubiertas:</span>
                                    <span>{!! isset($details['hair_heuristic']) && $details['hair_heuristic'] ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--success)"><polyline points="20 6 9 17 4 12"></polyline></svg>' : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--danger)"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' !!}</span>
                                </div>
                            </div>
                            
                            @if(isset($details['error']))
                                <div class="alert danger" style="margin-top: 12px; font-family: monospace; font-size: 12px;">
                                    Error del Script: {{ $details['error'] }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="panel" style="margin-top: 24px;">
            <h2 class="panel-title mb-0" style="margin-bottom: 20px;">Datos Personales y Académicos</h2>
            <div class="form-grid" style="gap: 20px 16px;">
                <div style="background: var(--surface-2); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border);">
                    <div class="muted small" style="margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px; font-weight: 600;">Nombres</div>
                    <div style="font-weight: 600; font-size: 15px; color: var(--text);">{{ $studentRequest->names }}</div>
                </div>
                <div style="background: var(--surface-2); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border);">
                    <div class="muted small" style="margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px; font-weight: 600;">Apellidos</div>
                    <div style="font-weight: 600; font-size: 15px; color: var(--text);">{{ $studentRequest->lastnames }}</div>
                </div>
                <div style="background: var(--surface-2); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border);">
                    <div class="muted small" style="margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px; font-weight: 600;">C.I.</div>
                    <div style="font-size: 15px; color: var(--text);">{{ $studentRequest->ci_number }}</div>
                </div>
                <div style="background: rgba(245, 158, 11, 0.05); padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(245, 158, 11, 0.2);">
                    <div style="margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px; font-weight: 600; color: #d97706;">Código Estudiante</div>
                    <div style="font-weight: 700; font-size: 15px; color: #b45309; font-family: monospace;">{{ $studentRequest->student_code ?? 'N/A' }}</div>
                </div>
                <div style="background: var(--surface-2); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border);">
                    <div class="muted small" style="margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px; font-weight: 600;">Carrera</div>
                    <div style="font-size: 15px; color: var(--text);">{{ $studentRequest->career->name ?? '—' }}</div>
                </div>
                <div style="background: var(--surface-2); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border);">
                    <div class="muted small" style="margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px; font-weight: 600;">Semestre</div>
                    <div style="font-size: 15px; color: var(--text);">{{ $studentRequest->semester->name ?? '—' }}</div>
                </div>
                <div style="background: var(--surface-2); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border);">
                    <div class="muted small" style="margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px; font-weight: 600;">Gestión</div>
                    <div style="font-size: 15px; color: var(--text);">{{ $studentRequest->gestion }}</div>
                </div>
                <div style="background: var(--surface-2); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border);">
                    <div class="muted small" style="margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px; font-weight: 600;">Turno</div>
                    <div style="font-size: 15px; color: var(--text);">{{ $studentRequest->turno }}</div>
                </div>
                <div style="background: var(--surface-2); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border);">
                    <div class="muted small" style="margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px; font-weight: 600;">Grupo</div>
                    <div style="font-size: 15px; color: var(--text);">{{ $studentRequest->grupo }}</div>
                </div>
                <div style="background: var(--surface-2); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border);">
                    <div class="muted small" style="margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11px; font-weight: 600;">Enviada</div>
                    <div style="font-size: 15px; color: var(--text);">{{ $studentRequest->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            @if($studentRequest->observation)
                <div class="alert warning" style="margin-top:20px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <strong>Observación:</strong> {{ $studentRequest->observation }}
                </div>
            @endif

            @if($studentRequest->reviewer)
                <div class="muted small" style="margin-top:16px">
                    Revisado por: {{ $studentRequest->reviewer->name }} — {{ $studentRequest->reviewed_at?->format('d/m/Y H:i') }}
                </div>
            @endif
        </div>

    </div>

    {{-- Acciones --}}
    @if(in_array($studentRequest->status, ['pending', 'observed', 'resubmitted']))
        <div style="display:flex;flex-direction:column;gap:12px">
            {{-- Aprobar --}}
            <div class="panel" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--success);"></div>
                <div class="panel-title" style="margin-top: 8px; color: var(--success); display:flex; align-items:center; gap:6px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Aprobar Solicitud
                </div>
                <p class="muted small" style="margin-bottom: 20px;">Se aprobará la solicitud para la generación del carnet estudiantil.</p>
                <form method="POST" action="{{ route('admin.requests.approve', $studentRequest) }}">
                    @csrf
                    <button id="btn-approve" type="submit" class="btn success w-100" style="padding: 12px; font-weight: 600;">Aprobar Solicitud</button>
                </form>
            </div>

            {{-- Observar --}}
            <div class="panel" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 12px; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--warning);"></div>
                <div class="panel-title" style="margin-top: 8px; color: var(--warning); display:flex; align-items:center; gap:6px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    Observar solicitud
                </div>
                <form method="POST" action="{{ route('admin.requests.observe', $studentRequest) }}">
                    @csrf
                    <div class="field" style="margin-bottom:16px">
                        <label for="observation-text" style="font-size: 12px;">Observación (requerida)</label>
                        <textarea id="observation-text" name="observation" rows="3" placeholder="Indica qué debe corregir el estudiante..." style="border-radius: 8px;"></textarea>
                    </div>
                    <button id="btn-observe" type="submit" class="btn warning w-100" style="padding: 12px; font-weight: 600;">Enviar observación</button>
                </form>
            </div>

            {{-- Rechazar --}}
            <div class="panel" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--danger);"></div>
                <div class="panel-title" style="margin-top: 8px; color: var(--danger); display:flex; align-items:center; gap:6px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    Rechazar Solicitud
                </div>
                <form method="POST" action="{{ route('admin.requests.reject', $studentRequest) }}">
                    @csrf
                    <div class="field" style="margin-bottom:16px">
                        <label for="reject-reason" style="font-size: 12px;">Motivo (opcional)</label>
                        <textarea id="reject-reason" name="observation" rows="2" placeholder="Motivo del rechazo..." style="border-radius: 8px;"></textarea>
                    </div>
                    <button id="btn-reject" type="submit" class="btn danger w-100" style="padding: 12px; font-weight: 600;">Rechazar</button>
                </form>
            </div>
        </div>
    @else
        <div class="panel" style="border-radius: 12px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px;">
            <div class="panel-title" style="margin-bottom: 16px; color: var(--text-muted);">Estado final de la solicitud</div>
            <span class="badge {{ $studentRequest->status }}" style="font-size:16px;padding:10px 24px; border-radius: 30px;">
                {!! ['approved'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg> Aprobada','rejected'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Rechazada','observed'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> Observada'][$studentRequest->status] ?? $studentRequest->status !!}
            </span>
        </div>
    @endif
</div>

@endsection
