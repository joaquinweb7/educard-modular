@extends('layouts.admin')
@section('title', 'Nuevo Semestre')

@section('content')
<div class="admin-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
    <div>
        <h2>Nuevo Semestre</h2>
        <p class="muted">Registrar un nuevo semestre en el catálogo.</p>
    </div>
    <a href="{{ route('admin.semesters.index') }}" class="btn secondary">Volver</a>
</div>

<div class="card">
    <form action="{{ route('admin.semesters.store') }}" method="POST">
        @csrf

        <div class="form-grid" style="grid-template-columns: 1fr; gap: 20px; max-width: 500px;">
            <div class="field">
                <label>Nombre del Semestre *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej: Primer Semestre" required>
                @error('name')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label>Número Ordinal *</label>
                <input type="number" name="number" value="{{ old('number') }}" placeholder="Ej: 1" min="1" max="20" required>
                @error('number')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
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
            <button type="submit" class="btn primary">Guardar Semestre</button>
        </div>
    </form>
</div>
@endsection
