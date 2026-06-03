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
    .field {
        margin-bottom: 20px;
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
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
        margin-bottom: 18px;
    }
    .alert.error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #fca5a5;
    }
    .alert.success {
        background: var(--success-bg);
        border: 1px solid var(--success-border);
        color: var(--success-text);
    }
    .readonly-field {
        background: rgba(255, 255, 255, 0.02) !important;
        cursor: not-allowed;
        color: #94a3b8 !important;
    }
</style>

<div class="public-wrap">
    <div class="public-card">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('storage/images/logo.png') }}" alt="Logo Institucional" style="max-height: 85px; width: auto; max-width: 100%; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        </div>

        <h2>Actualización de Foto</h2>
        <p class="subtitle">Ingresa tu código de estudiante para buscar tu carnet y subir tu foto.</p>

        @if(session('success'))
            <div class="alert success">
                <div style="font-weight:700;margin-bottom:6px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg> ¡Éxito!</div>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert error">
                <div style="font-weight:700;margin-bottom:6px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Error</div>
                <ul style="margin:0;padding-left:18px;line-height:1.8">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(request()->filled('code') && !$carnet)
            <div class="alert error">
                No se encontró ningún carnet registrado con el código: {{ request('code') }}
            </div>
        @endif

        {{-- Formulario de Búsqueda --}}
        <form method="GET" action="{{ route('public.actualizar-foto.create') }}" style="margin-bottom: 24px;">
            <div class="field">
                <label>Código de Estudiante</label>
                <div style="display: flex; gap: 12px;">
                    <input type="text" name="code" value="{{ request('code') }}" placeholder="Ej. EST-12345" required>
                    <button type="submit" class="btn primary">Buscar</button>
                </div>
            </div>
        </form>

        {{-- Si el carnet existe, mostrar formulario de foto --}}
        @if($carnet)
            <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 32px 0;">
            
            <form id="request-form" method="POST" action="{{ route('public.actualizar-foto.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="codigo_estudiante" value="{{ $carnet->codigo_estudiante }}">

                <div class="form-grid" style="margin-bottom:16px">
                    <div class="field" style="grid-column: 1 / -1;">
                        <label>Estudiante Encontrado</label>
                        <input type="text" value="{{ $carnet->nombres }} {{ $carnet->apellidos }}" class="readonly-field" readonly>
                    </div>
                    <div class="field">
                        <label>Carrera</label>
                        <input type="text" value="{{ $carnet->carrera }}" class="readonly-field" readonly>
                    </div>
                    <div class="field">
                        <label>Cédula de Identidad</label>
                        <input type="text" value="{{ $carnet->cedula_identidad }}" class="readonly-field" readonly>
                    </div>

                    {{-- Foto --}}
                    <div class="field" style="grid-column:1/-1">
                        <label>SUBIR NUEVA FOTO 4×4 *
                            <span style="font-weight:400;margin-left:6px; color: #94a3b8; font-size: 11px;">JPG/PNG · máx. 2 MB</span>
                        </label>

                        <div id="photo-error" class="alert error" style="display:none;margin-bottom:10px;font-size:13px"></div>

                        <div id="photo-wrap" style="
                            display:flex;align-items:flex-start;gap:20px;
                            padding:20px;background: var(--input-bg);
                            border:2px dashed var(--input-border);border-radius: 12px;
                            transition:all .2s;
                        ">
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

                            <div style="flex:1;min-width:0">
                                <div id="photo-filename" style="font-size:14px;color:#cbd5e1;margin-bottom:12px;word-break:break-all; font-weight: 500;">
                                    Ningún archivo seleccionado
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

                <button type="submit" class="btn primary w-100"
                        style="width: 100%; padding: 16px; font-size: 16px; margin-top: 10px; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);">
                    Enviar Foto para Revisión
                </button>
            </form>
        @endif
    </div>
</div>

<script src="{{ asset('js/photo-validator.js') }}"></script>
<script>
    if (document.getElementById('req-photo')) {
        initPhotoValidator(
            'req-photo',        // input file
            'photo-thumb',      // img preview
            'photo-placeholder',// placeholder div
            'photo-filename',   // nombre archivo
            'photo-error',      // caja de error JS
            'photo-wrap',       // zona de borde
            'request-form'      // formulario a bloquear
        );
    }
</script>
@endsection
