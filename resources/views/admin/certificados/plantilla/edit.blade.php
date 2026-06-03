@extends('layouts.admin')
@section('heading', 'Editar Plantilla')
@section('content')

<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Actualizar Plantilla</h3>
    </div>
    <div class="panel-body mt-4">
        <form action="{{ route('admin.certificados.plantilla.update', $plantilla->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-grid" style="grid-template-columns: 1fr; gap: 1rem;">
                <div class="field">
                    <label for="nombre">Nombre de la Plantilla</label>
                    <input type="text" class="input" id="nombre" name="nombre" placeholder="Nombre de la plantilla" value="{{ old('nombre', $plantilla->nombre) }}" required>
                    @error('nombre') <span style="color:var(--danger)">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="imagen">Nueva Imagen Base (Opcional)</label>
                    <input type="file" class="input" id="imagen" name="imagen" accept="image/*">
                    <small style="color:var(--text-muted)">Deja este campo vacío si no deseas cambiar la imagen actual.</small>
                    @error('imagen') <span style="color:var(--danger)">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4" style="text-align: right;">
                <a href="{{ route('admin.certificados.plantilla.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i> Actualizar Plantilla
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
