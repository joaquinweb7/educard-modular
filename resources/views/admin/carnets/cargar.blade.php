@extends('layouts.admin')
@section('heading', 'Carga Masiva de Carnets')
@section('content')

<style>
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
    input[type="file"] {
        width: 100%;
        padding: 12px 16px;
        background: var(--surface-2);
        border: 1px dashed var(--border);
        border-radius: 8px;
        color: var(--text);
        font-size: 15px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    input[type="file"]:hover {
        border-color: var(--primary);
        background: rgba(79, 70, 229, 0.05);
    }
    .alert.info {
        background: rgba(59, 130, 246, 0.1);
        border-left: 4px solid #3b82f6;
        padding: 15px;
        margin-bottom: 20px;
        color: #3b82f6;
    }
</style>

<div class="panel">
    <div class="public-card">
        <h2 style="margin-bottom: 20px; font-size: 20px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: text-bottom; margin-right: 8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            Importar Estudiantes (Excel)
        </h2>

        <div class="alert info">
            <p style="margin:0; font-weight: 500;">Instrucciones:</p>
            <ol style="margin-top: 8px; margin-bottom: 0; padding-left: 20px;">
                <li>Descargue la plantilla de ejemplo para ver el formato requerido.</li>
                <li>Asegúrese de no modificar las cabeceras del archivo.</li>
                <li>Guarde el archivo y súbalo aquí.</li>
            </ol>
            <div style="margin-top: 15px;">
                <a href="{{ route('admin.carnets.descargar.plantilla') }}" class="btn secondary sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: text-bottom; margin-right: 4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Descargar Plantilla Excel
                </a>
            </div>
        </div>

        @if($errors->any() || session('error'))
            <div class="alert error" style="margin-bottom:18px; padding: 12px; background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444;">
                <ul style="margin:0;padding-left:18px;line-height:1.8; color: #ef4444;">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert success" style="margin-bottom:18px; padding: 12px; background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; color: #10b981;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.carnets.subir') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="field">
                <label>Seleccionar archivo Excel (.xlsx)</label>
                <input type="file" name="excel_file" accept=".xlsx,.xls" required>
            </div>

            <div class="actions" style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
                <a href="{{ route('admin.carnets.index') }}" class="btn secondary">Volver</a>
                <button type="submit" class="btn primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: text-bottom; margin-right: 6px;"><polyline points="16 16 12 12 8 16"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path><polyline points="16 16 12 12 8 16"></polyline></svg>
                    Subir Archivo e Importar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
