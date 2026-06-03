@extends('layouts.public')
@section('content')
<div class="public-wrap">
    <div class="public-card">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('storage/images/logo.png') }}" alt="Logo Institucional" style="max-height: 85px; width: auto; max-width: 100%; border-radius: 6px;">
        </div>
        <h2>Corregir Solicitud de Carnet</h2>

        {{-- Encabezado de contexto --}}
        <div style="
            background:linear-gradient(135deg,rgba(245,158,11,.1),rgba(239,68,68,.06));
            border:1px solid rgba(245,158,11,.3);
            border-radius:var(--radius-sm);
            padding:14px 16px;
            margin-bottom:20px;
            font-size:13.5px;
        ">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
                <div>
                    <div class="muted small" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Número de trámite</div>
                    <div class="procedure-code" style="font-size:14px;padding:5px 12px">{{ $studentRequest->procedure_number }}</div>
                </div>
                <span class="badge {{ $studentRequest->status }}">
                    {{ ['pending'=>'Pendiente','resubmitted'=>'RE-ENVIADO','rejected'=>'Rechazada','observed'=>'Observada'][$studentRequest->status] ?? $studentRequest->status }}
                </span>
            </div>
            @if($studentRequest->observation)
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(245,158,11,.2)">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--warning);margin-bottom:4px">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> Observación del administrador
                    </div>
                    <div style="color:var(--text);font-size:13.5px">{{ $studentRequest->observation }}</div>
                </div>
            @endif
        </div>

        <p class="subtitle" style="margin-bottom:20px">
            Modifica los campos que necesitas corregir. El número de trámite
            <strong>{{ $studentRequest->procedure_number }}</strong> se mantendrá.
        </p>

        {{-- Errores del servidor --}}
        @if($errors->any())
            <div class="alert error" style="margin-bottom:18px">
                <div style="font-weight:700;margin-bottom:6px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Corrige los siguientes errores:</div>
                <ul style="margin:0;padding-left:18px;line-height:1.8">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="edit-form" method="POST"
              action="{{ route('public.student-request.resubmit', $studentRequest->procedure_number) }}"
              enctype="multipart/form-data" novalidate>
            @csrf

            <div class="form-grid" style="margin-bottom:16px">
                {{-- Nombres --}}
                <div class="field">
                    <label for="edit-names">Nombres *</label>
                    <input id="edit-names" type="text" name="names"
                           value="{{ old('names', $studentRequest->names) }}"
                           placeholder="Ej: María Elena"
                           class="{{ $errors->has('names') ? 'input-error' : '' }}"
                           required>
                    @error('names')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                {{-- Apellidos --}}
                <div class="field">
                    <label for="edit-lastnames">Apellidos *</label>
                    <input id="edit-lastnames" type="text" name="lastnames"
                           value="{{ old('lastnames', $studentRequest->lastnames) }}"
                           placeholder="Ej: González Pérez"
                           class="{{ $errors->has('lastnames') ? 'input-error' : '' }}"
                           required>
                    @error('lastnames')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                {{-- CI --}}
                <div class="field">
                    <label for="edit-ci">Cédula de identidad *</label>
                    <input id="edit-ci" type="text" name="ci_number"
                           value="{{ old('ci_number', $studentRequest->ci_number) }}"
                           placeholder="Ej: V-12345678"
                           class="{{ $errors->has('ci_number') ? 'input-error' : '' }}"
                           required>
                    @error('ci_number')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                {{-- Carrera --}}
                <div class="field">
                    <label for="edit-career">Carrera *</label>
                    <select id="edit-career" name="career_id"
                            class="dependent-career {{ $errors->has('career_id') ? 'input-error' : '' }}"
                            required>
                        <option value="">— Seleccionar —</option>
                        @foreach($careers as $career)
                            <option value="{{ $career->id }}"
                                {{ old('career_id', $studentRequest->career_id) == $career->id ? 'selected' : '' }}>
                                {{ $career->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('career_id')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                {{-- Semestre --}}
                <div class="field">
                    <label for="edit-semester">Semestre *</label>
                    <select id="edit-semester" name="semester_id"
                            class="dependent-semester {{ $errors->has('semester_id') ? 'input-error' : '' }}"
                            data-old="{{ old('semester_id', $studentRequest->semester_id) }}"
                            required>
                        <option value="">— Seleccionar —</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}"
                                {{ old('semester_id', $studentRequest->semester_id) == $sem->id ? 'selected' : '' }}>
                                {{ $sem->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('semester_id')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                {{-- Gestión --}}
                <div class="field">
                    <label for="gestion">Gestión *</label>
                    <select id="gestion" name="gestion" 
                            class="dependent-gestion {{ $errors->has('gestion') ? 'input-error' : '' }}"
                            data-old="{{ old('gestion', $studentRequest->gestion) }}"
                            required>
                        <option value="">— Seleccionar —</option>
                    </select>
                    @error('gestion')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                {{-- Turno --}}
                <div class="field">
                    <label for="edit-turno">Turno *</label>
                    <select id="edit-turno" name="turno"
                            class="dependent-turno {{ $errors->has('turno') ? 'input-error' : '' }}"
                            data-old="{{ old('turno', $studentRequest->turno) }}"
                            required>
                        <option value="">— Seleccionar —</option>
                    </select>
                    @error('turno')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                {{-- Grupo --}}
                <div class="field">
                    <label for="edit-grupo">Grupo *</label>
                    <select id="edit-grupo" name="grupo"
                            class="dependent-grupo {{ $errors->has('grupo') ? 'input-error' : '' }}"
                            data-old="{{ old('grupo', $studentRequest->grupo) }}"
                            required>
                        <option value="">— Seleccionar —</option>
                    </select>
                    @error('grupo')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                {{-- Foto --}}
                <div class="field" style="grid-column:1/-1">
                    <label>Fotografía 4×4
                        <span class="muted small" style="font-weight:400;margin-left:6px">
                            Opcional si ya tienes una · JPG/PNG · máx. 2 MB · mín. 500×500 px
                        </span>
                    </label>

                    {{-- Error JS --}}
                    <div id="photo-error" class="alert error" style="display:none;margin-bottom:10px;font-size:13px"></div>
                    @error('photo')<div class="alert error" style="margin-bottom:10px;font-size:13px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> {{ $message }}</div>@enderror

                    {{-- Foto actual --}}
                    @if($studentRequest->photo_path)
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;
                                    background:var(--surface-2);border-radius:var(--radius-sm);padding:10px 14px">
                            <img src="{{ asset('storage/'.$studentRequest->photo_path) }}"
                                 style="width:50px;height:62px;object-fit:cover;border-radius:6px">
                            <div>
                                <div style="font-size:12px;font-weight:600;color:var(--text)">Foto actual registrada</div>
                                <div class="muted small">Si no seleccionas una nueva foto, se mantendrá esta.</div>
                            </div>
                        </div>
                    @endif

                    {{-- Zona de upload --}}
                    <div id="photo-wrap" style="
                        display:flex;align-items:flex-start;gap:16px;
                        padding:16px;background:var(--surface-2);
                        border:2px dashed var(--border);border-radius:var(--radius-sm);
                        transition:border-color .2s,border-style .2s">

                        <div style="flex-shrink:0">
                            <div id="photo-placeholder" style="
                                width:90px;height:112px;background:var(--surface-3);
                                border-radius:8px;display:flex;flex-direction:column;
                                align-items:center;justify-content:center;
                                color:var(--text-dim);font-size:11px;text-align:center;gap:6px">
                                <span style="font-size:28px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg></span>Nueva foto
                            </div>
                            <img id="photo-thumb" src="" alt="Preview nueva foto" style="
                                width:90px;height:112px;object-fit:cover;border-radius:8px;
                                display:none;box-shadow:0 4px 12px rgba(0,0,0,.3)">
                        </div>

                        <div style="flex:1;min-width:0">
                            <div id="photo-filename" style="font-size:13px;color:var(--text-muted);margin-bottom:8px;word-break:break-all">
                                Ningún archivo seleccionado
                            </div>
                            <ul style="font-size:12px;color:var(--text-dim);padding-left:16px;margin:0 0 12px;line-height:1.7">
                                <li>Foto tipo carné <strong>4×4</strong> (formato cuadrado)</li>
                                <li>Fondo <strong>rojo</strong> liso</li>
                                <li>De frente, rostro visible, sin lentes ni gorra</li>
                                <li>Formato <strong>JPG o PNG</strong> únicamente</li>
                                <li>Peso máximo: <strong>2 MB</strong></li>
                                <li>Resolución mínima: <strong>500 × 500 px</strong></li>
                            </ul>
                            <label for="edit-photo" style="
                                display:inline-flex;align-items:center;gap:6px;
                                background:var(--surface-3);border:1px solid var(--border);
                                border-radius:var(--radius-sm);padding:8px 14px;
                                font-size:13px;font-weight:600;cursor:pointer;
                                color:var(--text);transition:border-color .2s">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg> Seleccionar nueva foto
                            </label>
                            <input id="edit-photo" type="file" name="photo"
                                   accept="image/jpeg,image/jpg,image/png"
                                   style="display:none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions">
                <button id="btn-resubmit" type="submit" class="btn primary"
                        style="padding:13px 24px;font-size:15px;flex:1">
                    ↩ Reenviar solicitud corregida
                </button>
                <a href="{{ route('public.student-request.show', $studentRequest->procedure_number) }}"
                   class="btn secondary" style="padding:13px 20px">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/photo-validator.js') }}"></script>
<script src="{{ asset('js/academic-dropdowns.js') }}"></script>
<script>
    initPhotoValidator(
        'edit-photo',
        'photo-thumb',
        'photo-placeholder',
        'photo-filename',
        'photo-error',
        'photo-wrap',
        'edit-form'
    );
</script>
@endsection
