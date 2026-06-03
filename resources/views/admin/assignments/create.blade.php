@extends('layouts.admin')
@section('title', 'Nueva Asignación Académica')

@section('content')
<div class="admin-header">
    <div>
        <h2>Nueva Asignación</h2>
        <p class="muted">Habilitar una nueva combinación de Gestión, Turno y Grupo para una Carrera.</p>
    </div>
    <a href="{{ route('admin.assignments.index') }}" class="btn secondary">Volver</a>
</div>

<div class="card">
    <form action="{{ route('admin.assignments.store') }}" method="POST">
        @csrf

        <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="field">
                <label>Carrera *</label>
                <select name="career_id" required>
                    <option value="">— Seleccionar —</option>
                    @foreach($careers as $career)
                        <option value="{{ $career->id }}" {{ old('career_id') == $career->id ? 'selected' : '' }}>
                            {{ $career->name }}
                        </option>
                    @endforeach
                </select>
                @error('career_id')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label>Semestre *</label>
                <select name="semester_id" required>
                    <option value="">— Seleccionar —</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                            {{ $semester->name }}
                        </option>
                    @endforeach
                </select>
                @error('semester_id')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label>Gestión * <span class="muted small">(Ej: I-2026, II-2026)</span></label>
                <input type="text" name="gestion" value="{{ old('gestion') }}" placeholder="I-2026" required>
                @error('gestion')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label>Turno *</label>
                <select name="turno" required>
                    <option value="">— Seleccionar —</option>
                    <option value="Mañana" {{ old('turno') == 'Mañana' ? 'selected' : '' }}>Mañana</option>
                    <option value="Tarde" {{ old('turno') == 'Tarde' ? 'selected' : '' }}>Tarde</option>
                    <option value="Noche" {{ old('turno') == 'Noche' ? 'selected' : '' }}>Noche</option>
                </select>
                @error('turno')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label>Grupo * <span class="muted small">(Ej: A, B, C)</span></label>
                <input type="text" name="grupo" value="{{ old('grupo') }}" placeholder="A" required>
                @error('grupo')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="margin-top:24px; text-align:right;">
            <button type="submit" class="btn primary">Guardar Asignación</button>
        </div>
    </form>
</div>
@endsection
