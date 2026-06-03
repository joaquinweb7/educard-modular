@extends('layouts.admin')
@section('heading', 'Registro Manual de Estudiante')
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
        --input-bg: var(--surface-2);
        --input-border: var(--border);
    }
    
    .public-card {
        background: var(--surface-1);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 30px;
        max-width: 800px;
        margin: 0 auto;
    }
    .field label {
        display: block;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 8px;
    }
    input[type="text"], select {
        width: 100%;
        padding: 12px 16px;
        background: var(--input-bg);
        border: 1px solid var(--input-border);
        border-radius: 8px;
        color: var(--text);
        font-size: 15px;
        transition: all 0.2s ease;
    }
    input[type="text"]:focus, select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .input-error { border-color: #ef4444 !important; background: rgba(239, 68, 68, 0.05) !important; }
</style>

<div class="panel">
    <div class="public-card">
        <h2 style="margin-bottom: 20px; font-size: 20px;">Registrar Estudiante</h2>
        <p class="subtitle" style="color: var(--text-muted); margin-bottom: 30px;">
            El registro manual valida directamente los datos con el sistema de Trámites.
        </p>

        @if($errors->any() || session('error'))
            <div class="alert error" style="margin-bottom:18px">
                @if(session('error'))
                    <div style="font-weight:700;margin-bottom:6px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> {{ session('error') }}</div>
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
            <p style="margin:0;line-height:1.8">El carnet ingresado no está registrado en el sistema de Trámites.</p>
        </div>

        <div id="ci-duplicate-error" class="alert warning" style="display:none; margin-bottom:18px">
            <div style="font-weight:700;margin-bottom:6px;color:#d97706"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> Trámite en curso</div>
            <p style="margin:0;line-height:1.8;color:#b45309">Ya existe una solicitud de carnet pendiente para este estudiante. Número de trámite: <strong id="duplicate-procedure-number"></strong></p>
        </div>

        <form id="request-form" method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data" novalidate>
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg> Encontrado
                            </div>
                        </div>
                        <button type="button" id="btn-validate-ci" class="btn primary" style="white-space: nowrap;">
                            Validar CI
                        </button>
                    </div>
                    @error('ci_number')<span class="field-error" style="color: #ef4444; font-size: 12px; margin-top: 6px; display: block;">{{ $message }}</span>@enderror
                </div>

                {{-- Código de Estudiante --}}
                <div class="field" style="grid-column: 1 / -1;">
                    <label for="req-student-code">Código de Estudiante</label>
                    <input id="req-student-code" type="text" name="student_code"
                           value="{{ old('student_code') }}"
                           placeholder="Autocompletado al validar"
                           style="background: var(--input-bg); font-weight: bold; font-family: monospace; font-size: 16px; letter-spacing: 1px;">
                    <label style="display:inline-flex; align-items:center; margin-top: 8px; font-size: 13px; color: var(--text-muted); font-weight: 400; text-transform: none; letter-spacing: normal;">
                        <input type="checkbox" name="auto_code" value="1" checked style="margin-right: 6px;">
                        Generar automáticamente si el sistema de Trámites no provee uno
                    </label>
                </div>

                {{-- Nombres --}}
                <div class="field">
                    <label for="req-names">Nombres *</label>
                    <input id="req-names" type="text" name="names"
                           value="{{ old('names') }}"
                           placeholder="Autocompletado al validar"
                           class="{{ $errors->has('names') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('names')<span class="field-error" style="color: #ef4444; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Apellidos --}}
                <div class="field">
                    <label for="req-lastnames">Apellidos *</label>
                    <input id="req-lastnames" type="text" name="lastnames"
                           value="{{ old('lastnames') }}"
                           placeholder="Autocompletado al validar"
                           class="{{ $errors->has('lastnames') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('lastnames')<span class="field-error" style="color: #ef4444; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Carrera --}}
                <div class="field">
                    <label for="req-career">Carrera *</label>
                    <input id="req-career" type="text" name="career_name"
                           value="{{ old('career_name') }}"
                           placeholder="Autocompletado"
                           class="{{ $errors->has('career_name') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('career_name')<span class="field-error" style="color: #ef4444; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Semestre --}}
                <div class="field">
                    <label for="req-semester">Semestre *</label>
                    <input id="req-semester" type="text" name="semester_name"
                           value="{{ old('semester_name') }}"
                           placeholder="Autocompletado"
                           class="{{ $errors->has('semester_name') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('semester_name')<span class="field-error" style="color: #ef4444; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Gestión --}}
                <div class="field">
                    <label for="req-gestion">Gestión *</label>
                    <input id="req-gestion" type="text" name="gestion" 
                           value="{{ old('gestion') }}"
                           placeholder="Autocompletado"
                           class="{{ $errors->has('gestion') ? 'input-error' : '' }}" 
                           required readonly style="cursor: not-allowed;">
                    @error('gestion')<span class="field-error" style="color: #ef4444; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Turno --}}
                <div class="field">
                    <label for="req-turno">Turno *</label>
                    <input id="req-turno" type="text" name="turno"
                           value="{{ old('turno') }}"
                           placeholder="Autocompletado"
                           class="{{ $errors->has('turno') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('turno')<span class="field-error" style="color: #ef4444; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Grupo --}}
                <div class="field">
                    <label for="req-grupo">Grupo *</label>
                    <input id="req-grupo" type="text" name="grupo"
                           value="{{ old('grupo') }}"
                           placeholder="Autocompletado"
                           class="{{ $errors->has('grupo') ? 'input-error' : '' }}"
                           required readonly style="cursor: not-allowed;">
                    @error('grupo')<span class="field-error" style="color: #ef4444; font-size: 12px;">{{ $message }}</span>@enderror
                </div>

                {{-- Foto con validación JS + preview --}}
                <div class="field" style="grid-column:1/-1">
                    <label>FOTO CARNET 4×4 *
                        <span style="font-weight:400;margin-left:6px; color: var(--text-muted); font-size: 11px;">JPG/PNG · máx. 2 MB · mín. 500×500 px</span>
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
                                width:100px;height:100px;background:rgba(0,0,0,0.1);
                                border-radius:12px;display:flex;flex-direction:column;
                                align-items:center;justify-content:center;
                                color:var(--text-dim);font-size:11px;text-align:center;gap:8px">
                                <span style="font-size:32px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg></span>
                            </div>
                            <img id="photo-thumb" src="" alt="Preview" style="
                                width:100px;height:100px;object-fit:cover;border-radius:12px;
                                display:none;box-shadow:0 10px 15px -3px rgba(0,0,0,0.3)">
                        </div>

                        {{-- Info --}}
                        <div style="flex:1;min-width:0">
                            <div id="photo-filename" style="font-size:14px;color:var(--text);margin-bottom:12px;word-break:break-all; font-weight: 500;">
                                Ningún archivo seleccionado
                            </div>
                            
                            <div id="photo-checklist" style="display:none; margin-bottom: 16px; background: rgba(0,0,0,0.1); border-radius: 8px; padding: 12px; border: 1px solid var(--border);">
                                <div style="font-weight: 600; font-size: 13px; margin-bottom: 8px; color: var(--text);">Lista de Verificación Fotográfica</div>
                                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 13px;">
                                    <div id="check-ratio" style="display:flex; align-items:center; gap:8px;">
                                        <span class="icon" style="display:flex; align-items:center; justify-content:center; width:20px; height:20px;"></span> <span>Relación 1:1, Mínimo 500x500px <span style="font-size:11px; color:var(--text-dim);">(Obligatorio)</span></span>
                                    </div>
                                    <div id="check-size" style="display:flex; align-items:center; gap:8px;">
                                        <span class="icon" style="display:flex; align-items:center; justify-content:center; width:20px; height:20px;"></span> <span>Peso menor a 2MB <span style="font-size:11px; color:var(--text-dim);">(Obligatorio)</span></span>
                                    </div>
                                </div>
                            </div>
                            <label for="req-photo" style="
                                display:inline-flex;align-items:center;gap:8px;
                                background:var(--surface-3);border:1px solid var(--border);
                                border-radius:8px;padding:10px 16px;
                                font-size:14px;font-weight:600;cursor:pointer;
                                color:var(--text);transition:all .2s">
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
                    style="width: 100%; padding: 14px; font-size: 15px; margin-top: 10px;">
                Guardar Estudiante
            </button>
        </form>
    </div>
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

    document.getElementById('btn-validate-ci').addEventListener('click', async function() {
        const ciInput = document.getElementById('req-ci');
        const ci = ciInput.value.trim();
        const btn = this;
        const errorDiv = document.getElementById('ci-validation-error');
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
            window.showToast('Por favor, ingresa una cédula de identidad primero.', 'warning');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Validando...';
        errorDiv.style.display = 'none';
        document.getElementById('ci-duplicate-error').style.display = 'none';
        successMsg.style.display = 'none';
        
        // Restaurar estilos de CI
        ciInput.style.background = 'var(--input-bg)';
        ciInput.style.borderColor = 'var(--input-border)';
        
        // Restaurar estilos
        autocompletedInputs.forEach(input => {
            input.style.background = 'var(--input-bg)';
            input.style.borderColor = 'var(--input-border)';
            input.style.color = 'var(--text)';
        });
        
        studentCodeInput.style.background = 'var(--input-bg)';
        studentCodeInput.style.borderColor = 'var(--input-border)';
        studentCodeInput.style.color = 'var(--text)';

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
                
                window.showToast('¡Datos validados y completados correctamente!', 'success');
            } else {
                autocompletedInputs.forEach(input => input.value = '');
                studentCodeInput.value = '';
                
                if (data.has_request) {
                    document.getElementById('ci-duplicate-error').style.display = 'block';
                    if (data.procedure_number) {
                        document.getElementById('duplicate-procedure-number').textContent = data.procedure_number;
                    }
                    window.showToast('Ya existe una solicitud pendiente.', 'warning');
                } else {
                    errorDiv.style.display = 'block';
                    window.showToast('No se encontraron los datos.', 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            window.showToast('Ocurrió un error al intentar conectarse al sistema.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Validar CI';
        }
    });
</script>
@endsection
