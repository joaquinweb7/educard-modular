@extends('layouts.admin')
@section('title', 'Editar Carrera')

@section('content')
<div class="admin-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
    <div>
        <h2>Editar Carrera</h2>
        <p class="muted">Modificar los datos de la carrera: {{ $career->name }}</p>
    </div>
    <a href="{{ route('admin.careers.index') }}" class="btn secondary">Volver</a>
</div>

<div class="card">
    <form action="{{ route('admin.careers.update', $career) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid" style="grid-template-columns: 1fr; gap: 20px; max-width: 500px;">
            <div class="field">
                <label>Nombre de la Carrera *</label>
                <input type="text" name="name" value="{{ old('name', $career->name) }}" required>
                @error('name')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label>Estado *</label>
                <select name="status" required>
                    <option value="active" {{ old('status', $career->status) == 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ old('status', $career->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('status')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="margin-top:24px;">
            <button type="submit" class="btn primary">Actualizar Carrera</button>
        </div>
    </form>
</div>
@endsection
