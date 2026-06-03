@extends('layouts.admin')
@section('heading', 'Crear Nueva Plantilla')
@section('content')

<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Formulario de Plantilla</h3>
    </div>
    <div class="panel-body mt-4">
        <form action="{{ route('admin.certificados.plantilla.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-grid" style="grid-template-columns: 1fr; gap: 1rem;">
                <div class="field">
                    <label for="nombre">Nombre de la Plantilla</label>
                    <input type="text" class="input" id="nombre" name="nombre" placeholder="Nombre de la plantilla" value="{{ old('nombre') }}" required>
                    @error('nombre') <span style="color:var(--danger)">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="imagen">Imagen Base (Fondo del certificado)</label>
                    <input type="file" class="input" id="imagen" name="imagen" accept="image/*" required>
                    @error('imagen') <span style="color:var(--danger)">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4" style="text-align: right;">
                <a href="{{ route('admin.certificados.plantilla.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i> Guardar Plantilla
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
