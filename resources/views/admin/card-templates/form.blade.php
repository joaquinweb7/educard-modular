@extends('layouts.admin')
@section('heading', 'Nueva Plantilla de Carnet')
@section('content')

<div class="panel" style="max-width:600px">
    <div class="section-header">
        <h2 class="panel-title mb-0">Crear plantilla</h2>
        <a href="{{ route('admin.card-templates.index') }}" class="btn secondary sm">← Volver</a>
    </div>

    <form method="POST" action="{{ route('admin.card-templates.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid" style="margin-bottom:16px">
            <div class="field" style="grid-column:1/-1">
                <label for="template-name">Nombre de la plantilla</label>
                <input id="template-name" type="text" name="name" value="{{ old('name') }}"
                       placeholder="Ej: Carnet 2026 — Estándar" required>
            </div>
            <div class="field">
                <label for="template-width">Ancho (cm)</label>
                <input id="template-width" type="number" step="0.01" name="width" value="{{ old('width', 8.5) }}" min="3" max="100" required>
            </div>
            <div class="field">
                <label for="template-height">Alto (cm)</label>
                <input id="template-height" type="number" step="0.01" name="height" value="{{ old('height', 5.4) }}" min="3" max="100" required>
            </div>
            <div class="field" style="grid-column:1/-1">
                <label for="template-bg">Imagen de fondo (JPG/PNG, máx 4 MB)</label>
                <input id="template-bg" type="file" name="background" accept="image/jpeg,image/png">
            </div>
        </div>

        <div class="actions">
            <button id="btn-create-template" type="submit" class="btn primary">Crear y diseñar</button>
            <a href="{{ route('admin.card-templates.index') }}" class="btn secondary">Cancelar</a>
        </div>
    </form>
</div>

@endsection
