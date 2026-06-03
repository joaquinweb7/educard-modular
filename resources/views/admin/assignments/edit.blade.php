@extends('layouts.admin')
@section('title', 'Editar Asignación Académica')

@section('content')
<div class="admin-header">
    <div>
        <h2>Editar Asignación</h2>
        <p class="muted">Modificar una combinación de Carrera, Semestre, Gestión, Turno y Grupo.</p>
    </div>
    <a href="{{ route('admin.assignments.index') }}" class="btn secondary">Volver</a>
</div>

<div class="card">
    <form action="{{ route('admin.assignments.update', $assignment) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="field">
                <label>Carrera *</label>
                <select name="career_id" required>
                    <option value="">— Seleccionar —</option>
                    @foreach($careers as $career)
                        <option value="{{ $career->id }}" {{ old('career_id', $assignment->career_id) == $career->id ? 'selected' : '' }}>
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
                        <option value="{{ $semester->id }}" {{ old('semester_id', $assignment->semester_id) == $semester->id ? 'selected' : '' }}>
                            {{ $semester->name }}
                        </option>
                    @endforeach
                </select>
                @error('semester_id')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label>Gestión * <span class="muted small">(Ej: I-2026, II-2026)</span></label>
                <input type="text" name="gestion" value="{{ old('gestion', $assignment->gestion) }}" placeholder="I-2026" required>
                @error('gestion')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label>Turno *</label>
                <select name="turno" required>
                    <option value="">— Seleccionar —</option>
                    <option value="Mañana" {{ old('turno', $assignment->turno) == 'Mañana' ? 'selected' : '' }}>Mañana</option>
                    <option value="Tarde" {{ old('turno', $assignment->turno) == 'Tarde' ? 'selected' : '' }}>Tarde</option>
                    <option value="Noche" {{ old('turno', $assignment->turno) == 'Noche' ? 'selected' : '' }}>Noche</option>
                </select>
                @error('turno')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label>Grupo * <span class="muted small">(Ej: A, B, C)</span></label>
                <input type="text" name="grupo" value="{{ old('grupo', $assignment->grupo) }}" placeholder="A" required>
                @error('grupo')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>

            <div class="field">
                <label>Estado *</label>
                <select name="status" required>
                    <option value="active" {{ old('status', $assignment->status) == 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ old('status', $assignment->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('status')<span class="field-error" style="color:var(--danger);font-size:13px">{{ $message }}</span>@enderror
            </div>
        </div>

        <div style="margin-top:24px; text-align:right;">
            <button type="submit" class="btn primary">Actualizar Asignación</button>
        </div>
    </form>
</div>
@endsection
