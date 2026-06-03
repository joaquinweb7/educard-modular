@extends('layouts.public')
@section('content')
<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --success-bg: rgba(16, 185, 129, 0.1);
        --success-border: rgba(16, 185, 129, 0.4);
        --success-text: #34d399;
        --warning-bg: rgba(245, 158, 11, 0.1);
        --warning-border: rgba(245, 158, 11, 0.4);
        --warning-text: #fbbf24;
        --input-bg: rgba(255, 255, 255, 0.05);
        --input-border: rgba(255, 255, 255, 0.1);
    }
    body {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        font-family: 'Inter', system-ui, sans-serif;
        color: #f8fafc;
    }
    .public-wrap {
        display: flex;
        justify-content: center;
        padding: 40px 20px;
        min-height: 100vh;
    }
    .public-card {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        padding: 40px;
        max-width: 600px;
        width: 100%;
    }
    h2 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #ffffff;
        text-align: center;
    }
    .subtitle {
        color: #94a3b8;
        text-align: center;
        margin-bottom: 32px;
        font-size: 15px;
    }
    .field label {
        display: block;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        color: #cbd5e1;
        margin-bottom: 8px;
    }
    input[type="text"], select {
        width: 100%;
        padding: 12px 16px;
        background: var(--input-bg);
        border: 1px solid var(--input-border);
        border-radius: 8px;
        color: #ffffff;
        font-size: 15px;
        transition: all 0.2s ease;
    }
    input[type="text"]:focus, select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }
    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }
    .btn.primary {
        background: var(--primary);
        color: white;
    }
    .btn.primary:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }
    .btn.secondary {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        text-decoration: none;
        display: inline-block;
    }
    .btn.secondary:hover {
        background: rgba(255, 255, 255, 0.15);
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .alert {
        padding: 16px;
        border-radius: 8px;
        font-size: 14px;
    }
    .alert.error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #fca5a5;
    }
    .input-error { border-color: #ef4444 !important; background: rgba(239, 68, 68, 0.05) !important; }
    .success-border { border-color: #10b981 !important; }
    @keyframes spin { 100% { transform: rotate(360deg); } }
</style>

<div class="public-wrap">
    <div class="public-card">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('storage/images/logo.png') }}" alt="Logo Institucional" style="max-height: 85px; width: auto; max-width: 100%; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        </div>
        @if(!$enabled)
            <div style="text-align: center; padding: 40px 20px;">
                <div style="width: 64px; height: 64px; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <h2 style="margin-bottom: 16px; color: #f8fafc;">Solicitudes Cerradas</h2>
                <p style="color: #94a3b8; font-size: 15px; line-height: 1.6; max-width: 400px; margin: 0 auto 30px;">
                    Se ha cerrado la recepción de solicitudes o no se encuentra habilitado para esta gestión.
                </p>
                <a href="{{ route('public.student-request.track') }}" class="btn primary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Seguimiento de trámite existente
                </a>
            </div>
        @else
        <h2>Solicitud de Carnet Estudiantil</h2>
        <p class="subtitle">Completa el formulario para iniciar tu trámite. Recibirás un número de seguimiento.</p>

        {{-- Errores del servidor (validación backend) --}}
        @if($errors->any() || session('error'))
            <div class="alert error" style="margin-bottom:18px">
                @if(session('error'))
                    <div style="font-weight:700;margin-bottom:6px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> {{ session('error') }}</div>
                    <div style="margin-top:8px">
                        @if(session('duplicate_request'))
                            <a href="{{ route('public.student-request.track') }}" style="color:white; text-decoration:underline; font-weight:bold;">👉 Realizar seguimiento de mi trámite aquí</a>
                        @else
                            <a href="http://127.0.0.1:8000/" target="_blank" style="color:white; text-decoration:underline; font-weight:bold;">👉 Ir al formulario de inscripción</a>
                        @endif
                    </div>
                @else
                    <div style="font-weight:700;margin-bottom:6px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Corrige los siguientes errores:</div>
                    <ul style="margin:0;padding-left:18px;line-height:1.8">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div id="ci-validation-error" class="alert error" style="display:none; margin-bottom:18px">
            <div style="font-weight:700;margin-bottom:6px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Estudiante no encontrado</div>
            <p style="margin:0;line-height:1.8">Tu carnet no está registrado en el sistema de Trámites. Debes inscribirte primero para poder solicitar el carnet estudiantil.</p>
            <div style="margin-top:8px">
                <a href="http://127.0.0.1:8000/" target="_blank" style="color:white; text-decoration:underline; font-weight:bold;">👉 Realizar mi inscripción aquí</a>
            </div>
        </div>

        <div id="ci-duplicate-error" class="alert warning" style="display:none; margin-bottom:18px">
            <div style="font-weight:700;margin-bottom:6px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> Solicitud en curso</div>
            <p style="margin:0;line-height:1.8">Ya tienes una solicitud de carnet registrada anteriormente.</p>
            <div style="margin-top:8px">
                <a href="{{ route('public.student-request.track') }}" style="color:var(--warning-text); text-decoration:underline; font-weight:bold;">👉 Realizar seguimiento de mi trámite aquí</a>
            </div>
        </div>

        <form id="request-form" method="POST"
              action="{{ route('public.student-request.store') }}"
              enctype="multipart/form-data" novalidate>
            @csrf

            <div class="form-grid" style="margin-bottom:16px">
                {{-- CI --}}
                <div class="field" style="grid-column: 1 / -1; position: relative;">
                    <label for="req-ci">Cédula de identidad *</label>
                    <div style="display: flex; gap: 12px; align-items: stretch;">
                        <div style="position: relative; flex: 1;">
                            <input id="req-ci" type="text" name="ci_number"
                                   value="{{ old('ci_number') }}"
                                   placeholder="Ej: V-12345678"
                                   class="{{ $errors->has('ci_number') ? 'input-error' : '' }}"
                                   required style="padding-right: 170px;">
                            <div id="ci-success-msg" style="display:none; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: var(--success-bg); color: var(--success-text); padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid var(--success-border); pointer-events: none;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg> Estudiante encontrado
                            </div>
                        </div>
                        <button type="button" id="btn-validate-ci" class="btn primary" style="white-space: nowrap;">
                            Validar CI
                        </button>
                    </div>
                    @error('ci_number')<span class="field-error" style="color: #fca5a5; font-size: 12px; margin-top: 6px; display: block;">{{ $message }}</span>@enderror
                </div>

                {{-- Código de Estudiante --}}
                <div class="field" style="grid-column: 1 / -1;">
                    <label for="req-student-code">Código de Estudiante</label>
                    <input id="req-student-code" type="text" name="student_code"
                           value="{{ old('student_code') }}"
                           placeholder="Autocompletado al validar"
                           readonly style="background: var(--input-bg); cursor: not-allowed; font-weight: bold; font-family: monospace; font-size: 16px; letter-spacing: 1px;">
                </div>

                {{-- Nombres --}}
                <div class="field">
                    <label for="req-names">Nombres *</label>
                    <input id="req-names" type="text" name="names"
                           value="{{ old('names') }}"
                           placeholder="Autocompletado al validar"
                           class="{{ $errors->has('names') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('names')<span class="field-error" style="color: #fca5a5; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Apellidos --}}
                <div class="field">
                    <label for="req-lastnames">Apellidos *</label>
                    <input id="req-lastnames" type="text" name="lastnames"
                           value="{{ old('lastnames') }}"
                           placeholder="Autocompletado al validar"
                           class="{{ $errors->has('lastnames') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('lastnames')<span class="field-error" style="color: #fca5a5; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Carrera --}}
                <div class="field">
                    <label for="req-career">Carrera *</label>
                    <input id="req-career" type="text" name="career_name"
                           value="{{ old('career_name') }}"
                           placeholder="Autocompletado"
                           class="{{ $errors->has('career_name') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('career_name')<span class="field-error" style="color: #fca5a5; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Semestre --}}
                <div class="field">
                    <label for="req-semester">Semestre *</label>
                    <input id="req-semester" type="text" name="semester_name"
                           value="{{ old('semester_name') }}"
                           placeholder="Autocompletado"
                           class="{{ $errors->has('semester_name') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('semester_name')<span class="field-error" style="color: #fca5a5; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Gestión --}}
                <div class="field">
                    <label for="req-gestion">Gestión *</label>
                    <input id="req-gestion" type="text" name="gestion" 
                           value="{{ old('gestion') }}"
                           placeholder="Autocompletado"
                           class="{{ $errors->has('gestion') ? 'input-error' : '' }}" 
                           required readonly style="cursor: not-allowed;">
                    @error('gestion')<span class="field-error" style="color: #fca5a5; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Turno --}}
                <div class="field">
                    <label for="req-turno">Turno *</label>
                    <input id="req-turno" type="text" name="turno"
                           value="{{ old('turno') }}"
                           placeholder="Autocompletado"
                           class="{{ $errors->has('turno') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('turno')<span class="field-error" style="color: #fca5a5; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Grupo --}}
                <div class="field">
                    <label for="req-grupo">Grupo *</label>
                    <input id="req-grupo" type="text" name="grupo"
                           value="{{ old('grupo') }}"
                           placeholder="Autocompletado"
                           class="{{ $errors->has('grupo') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('grupo')<span class="field-error" style="color: #fca5a5; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Foto con validación JS + preview --}}
                <div class="field" style="grid-column:1/-1">
                    <label>FOTO CARNET 4×4 *
                        <span style="font-weight:400;margin-left:6px; color: #94a3b8; font-size: 11px;">JPG/PNG · máx. 2 MB · mín. 500×500 px</span>
                    </label>

                    {{-- Error JS inline --}}
                    <div id="photo-error" class="alert error" style="display:none;margin-bottom:10px;font-size:13px"></div>

                    {{-- Error servidor --}}
                    @error('photo')<div class="alert error" style="margin-bottom:10px;font-size:13px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> {{ $message }}</div>@enderror

                    {{-- Zona de upload --}}
                    <div id="photo-wrap" style="
                        display:flex;align-items:flex-start;gap:20px;
                        padding:20px;background: var(--input-bg);
                        border:2px dashed var(--input-border);border-radius: 12px;
                        transition:all .2s;
                    ">
                        {{-- Miniatura --}}
                        <div style="flex-shrink:0">
                            <div id="photo-placeholder" style="
                                width:100px;height:100px;background:rgba(255,255,255,0.05);
                                border-radius:12px;display:flex;flex-direction:column;
                                align-items:center;justify-content:center;
                                color:#94a3b8;font-size:11px;text-align:center;gap:8px">
                                <span style="font-size:32px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg></span>
                            </div>
                            <img id="photo-thumb" src="" alt="Preview" style="
                                width:100px;height:100px;object-fit:cover;border-radius:12px;
                                display:none;box-shadow:0 10px 15px -3px rgba(0,0,0,0.3)">
                        </div>

                        {{-- Info --}}
                        <div style="flex:1;min-width:0">
                            <div id="photo-filename" style="font-size:14px;color:#cbd5e1;margin-bottom:12px;word-break:break-all; font-weight: 500;">
                                Ningún archivo seleccionado
                            </div>
                            <ul style="font-size:12px;color:#94a3b8;padding-left:16px;margin:0 0 16px;line-height:1.7; display:none;" id="photo-checklist-old">
                                <li>Fondo <strong>rojo</strong> liso, rostro visible de frente</li>
                                <li>Formato <strong>JPG o PNG</strong>, máx 2 MB</li>
                            </ul>
                            
                            <div id="photo-checklist" style="display:none; margin-bottom: 16px; background: rgba(0,0,0,0.1); border-radius: 8px; padding: 12px; border: 1px solid var(--border);">
                                <div style="font-weight: 600; font-size: 13px; margin-bottom: 8px; color: var(--text);">Lista de Verificación Fotográfica</div>
                                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 13px;">
                                    <div id="check-ratio" style="display:flex; align-items:center; gap:8px;">
                                        <span class="icon" style="display:flex; align-items:center; justify-content:center; width:20px; height:20px;"></span> <span>Relación 1:1, Mínimo 500x500px <span style="font-size:11px; color:var(--text-dim);">(Obligatorio)</span></span>
                                    </div>
                                    <div id="check-size" style="display:flex; align-items:center; gap:8px;">
                                        <span class="icon" style="display:flex; align-items:center; justify-content:center; width:20px; height:20px;"></span> <span>Peso menor a 2MB <span style="font-size:11px; color:var(--text-dim);">(Obligatorio)</span></span>
                                    </div>
                                    <div id="check-bg" style="display:flex; align-items:center; gap:8px;">
                                        <span class="icon" style="display:flex; align-items:center; justify-content:center; width:20px; height:20px;"></span> <span style="color:var(--text-muted);">Fondo Rojo Uniforme <span style="font-size:11px; color:var(--warning);">(Revisión Avanzada)</span></span>
                                    </div>
                                    <div id="check-suit" style="display:flex; align-items:center; gap:8px;">
                                        <span class="icon" style="display:flex; align-items:center; justify-content:center; width:20px; height:20px;"></span> <span style="color:var(--text-muted);">Traje / Camisa Formal <span style="font-size:11px; color:var(--warning);">(Revisión Avanzada)</span></span>
                                    </div>
                                    <div id="check-hair" style="display:flex; align-items:center; gap:8px;">
                                        <span class="icon" style="display:flex; align-items:center; justify-content:center; width:20px; height:20px;"></span> <span style="color:var(--text-muted);">Peinado Presentable <span style="font-size:11px; color:var(--warning);">(Revisión Avanzada)</span></span>
                                    </div>
                                </div>
                            </div>
                            <label for="req-photo" style="
                                display:inline-flex;align-items:center;gap:8px;
                                background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);
                                border-radius:8px;padding:10px 16px;
                                font-size:14px;font-weight:600;cursor:pointer;
                                color:#ffffff;transition:all .2s">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg> Seleccionar foto
                            </label>
                            <input id="req-photo" type="file" name="photo"
                                   accept="image/jpeg,image/jpg,image/png"
                                   required style="display:none">
                        </div>
                    </div>
                </div>
            </div>

            <button id="btn-submit-request" type="submit" class="btn primary w-100"
                    style="width: 100%; padding: 16px; font-size: 16px; margin-top: 10px; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);">
                Enviar Solicitud
            </button>
        </form>

        @endif
        <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 32px 0;">
        <div style="text-align:center">
            <div style="color: #94a3b8; font-size: 13px; margin-bottom: 12px;">¿Ya tienes un trámite en curso?</div>
            <a href="{{ route('public.student-request.track') }}" id="btn-go-track"
               class="btn secondary" style="width: 100%; text-align: center;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg> Consultar estado de mi trámite
            </a>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast-notification" style="
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translate(-50%, 100px);
    width: 90%;
    max-width: 800px;
    background: #333;
    color: white;
    padding: 16px 24px;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-weight: 600;
    font-size: 15px;
">
    <span id="toast-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
    <span id="toast-msg">Mensaje</span>
</div>

<script src="{{ asset('js/photo-validator.js') }}"></script>
<script>
    initPhotoValidator(
        'req-photo',        // input file
        'photo-thumb',      // img preview
        'photo-placeholder',// placeholder div
        'photo-filename',   // nombre archivo
        'photo-error',      // caja de error JS
        'photo-wrap',       // zona de borde
        'request-form'      // formulario a bloquear
    );

    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toast-notification');
        const icon = document.getElementById('toast-icon');
        const msgEl = document.getElementById('toast-msg');

        toast.style.background = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#3b82f6');
        icon.innerHTML = type === 'success' ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg>' : (type === 'error' ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' : 'ℹ');
        msgEl.textContent = msg;

        toast.style.transform = 'translate(-50%, 0)';
        toast.style.opacity = '1';

        setTimeout(() => {
            toast.style.transform = 'translate(-50%, 100px)';
            toast.style.opacity = '0';
        }, 4000);
    }

    document.getElementById('btn-validate-ci').addEventListener('click', async function() {
        const ciInput = document.getElementById('req-ci');
        const ci = ciInput.value.trim();
        const btn = this;
        const errorDiv = document.getElementById('ci-validation-error');
        const duplicateErrorDiv = document.getElementById('ci-duplicate-error');
        const successMsg = document.getElementById('ci-success-msg');
        
        const namesInput = document.getElementById('req-names');
        const lastnamesInput = document.getElementById('req-lastnames');
        const studentCodeInput = document.getElementById('req-student-code');
        const careerInput = document.getElementById('req-career');
        const semesterInput = document.getElementById('req-semester');
        const gestionInput = document.getElementById('req-gestion');
        const turnoInput = document.getElementById('req-turno');
        const grupoInput = document.getElementById('req-grupo');

        const autocompletedInputs = [namesInput, lastnamesInput, careerInput, semesterInput, gestionInput, turnoInput, grupoInput];
        
        if(!ci) {
            showToast('Por favor, ingresa una cédula de identidad primero.', 'warning');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Validando...';
        errorDiv.style.display = 'none';
        duplicateErrorDiv.style.display = 'none';
        successMsg.style.display = 'none';
        
        // Restaurar estilos de CI
        ciInput.style.background = 'var(--input-bg)';
        ciInput.style.borderColor = 'var(--input-border)';
        
        // Restaurar estilos
        autocompletedInputs.forEach(input => {
            input.style.background = 'var(--input-bg)';
            input.style.borderColor = 'var(--input-border)';
            input.style.color = '#ffffff';
        });
        
        studentCodeInput.style.background = 'var(--input-bg)';
        studentCodeInput.style.borderColor = 'var(--input-border)';
        studentCodeInput.style.color = '#ffffff';

        try {
            const response = await fetch(`/api/tramites/estudiantes/${ci}`);
            const data = await response.json();

            if (response.ok && data.success) {
                // Autocompletar datos
                namesInput.value = data.data.first_name || '';
                lastnamesInput.value = data.data.last_name || '';
                studentCodeInput.value = data.data.student_code || '';
                
                careerInput.value = data.data.career || 'No registrado';
                semesterInput.value = data.data.semester || 'No registrado';
                gestionInput.value = data.data.gestion || 'No registrado';
                turnoInput.value = data.data.turno || 'No registrado';
                grupoInput.value = data.data.grupo || 'No registrado';
                
                // Aplicar estilo verde al CI
                ciInput.style.borderColor = 'var(--success-text)';
                successMsg.style.display = 'block';

                // Aplicar estilo verde a los completados
                autocompletedInputs.forEach(input => {
                    input.style.background = 'var(--success-bg)';
                    input.style.borderColor = 'var(--success-border)';
                    input.style.color = 'var(--success-text)';
                });
                
                // Aplicar estilo naranja al código de estudiante
                studentCodeInput.style.background = 'var(--warning-bg)';
                studentCodeInput.style.borderColor = 'var(--warning-border)';
                studentCodeInput.style.color = 'var(--warning-text)';
                
                showToast('¡Datos validados y completados correctamente!', 'success');
            } else {
                autocompletedInputs.forEach(input => input.value = '');
                studentCodeInput.value = '';
                
                if (data.has_request) {
                    duplicateErrorDiv.style.display = 'block';
                    showToast('Ya tienes una solicitud en curso.', 'warning');
                } else {
                    errorDiv.style.display = 'block';
                    showToast('No se encontraron los datos.', 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Ocurrió un error al intentar conectarse al sistema.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Validar CI';
        }
    });
</script>
@endsection
