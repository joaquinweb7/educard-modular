@extends('layouts.admin')
@section('title', 'Nueva Carrera')

@section('content')
<div class="admin-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
    <div>
        <h2>Nueva Carrera</h2>
        <p class="muted">Registrar una nueva carrera en el catálogo.</p>
    </div>
    <a href="{{ route('admin.careers.index') }}" class="btn secondary">Volver</a>
</div>

<div class="card">
    <form action="{{ route('admin.careers.store') }}" method="POST">
        @csrf

        <div class="form-grid" style="grid-template-columns: 1fr; gap: 20px; max-width: 500px;">
            <div class="field">
                <label>Nombre de la Carrera *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej: Ingeniería de Sistemas" required>
                @error('name')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label>Estado *</label>
                <select name="status" required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('status')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="margin-top:24px;">
            <button type="submit" class="btn primary">Guardar Carrera</button>
        </div>
    </form>
</div>
@endsection
