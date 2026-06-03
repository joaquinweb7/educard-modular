@extends('layouts.admin')
@section('heading', 'Editar Carnet')
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
    input[type="text"], input[type="date"], select, textarea {
        width: 100%;
        padding: 12px 16px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text);
        font-size: 15px;
        transition: all 0.2s ease;
    }
    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .input-error { border-color: #ef4444 !important; background: rgba(239, 68, 68, 0.05) !important; }
</style>

<div class="panel">
    <div class="public-card">
        <h2 style="margin-bottom: 20px; font-size: 20px;">Editar Carnet: {{ $carnet->nombres }} {{ $carnet->apellidos }}</h2>

        @if($errors->any() || session('error'))
            <div class="alert error" style="margin-bottom:18px; padding: 12px; background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444;">
                <ul style="margin:0;padding-left:18px;line-height:1.8; color: #ef4444;">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.carnets.update', $carnet) }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            <div class="form-grid">
                {{-- Nombres --}}
                <div class="field">
                    <label>Nombres *</label>
                    <input type="text" name="nombres" value="{{ old('nombres', $carnet->nombres) }}" class="{{ $errors->has('nombres') ? 'input-error' : '' }}" required>
                </div>

                {{-- Apellidos --}}
                <div class="field">
                    <label>Apellidos *</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos', $carnet->apellidos) }}" class="{{ $errors->has('apellidos') ? 'input-error' : '' }}" required>
                </div>

                {{-- Cédula Identidad --}}
                <div class="field">
                    <label>Cédula de Identidad *</label>
                    <input type="text" name="cedula_identidad" value="{{ old('cedula_identidad', $carnet->cedula_identidad) }}" class="{{ $errors->has('cedula_identidad') ? 'input-error' : '' }}" required>
                </div>

                {{-- Código Estudiante --}}
                <div class="field">
                    <label>Código Estudiante *</label>
                    <input type="text" name="codigo_estudiante" value="{{ old('codigo_estudiante', $carnet->codigo_estudiante) }}" class="{{ $errors->has('codigo_estudiante') ? 'input-error' : '' }}" required>
                </div>

                {{-- Carrera --}}
                <div class="field">
                    <label>Carrera *</label>
                    <input type="text" name="carrera" value="{{ old('carrera', $carnet->carrera) }}" class="{{ $errors->has('carrera') ? 'input-error' : '' }}" required>
                </div>

                {{-- Semestre --}}
                <div class="field">
                    <label>Semestre *</label>
                    <input type="text" name="semestre" value="{{ old('semestre', $carnet->semestre) }}" class="{{ $errors->has('semestre') ? 'input-error' : '' }}" required>
                </div>

                {{-- Fechas --}}
                <div class="field">
                    <label>Fecha Emisión *</label>
                    <input type="date" name="fecha_emision" value="{{ old('fecha_emision', $carnet->fecha_emision) }}" class="{{ $errors->has('fecha_emision') ? 'input-error' : '' }}" required>
                </div>
                <div class="field">
                    <label>Fecha Caducidad *</label>
                    <input type="date" name="fecha_caducidad" value="{{ old('fecha_caducidad', $carnet->fecha_caducidad) }}" class="{{ $errors->has('fecha_caducidad') ? 'input-error' : '' }}" required>
                </div>

                {{-- Estado --}}
                <div class="field">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="vigente" {{ old('estado', $carnet->estado) == 'vigente' ? 'selected' : '' }}>Vigente</option>
                        <option value="caducado" {{ old('estado', $carnet->estado) == 'caducado' ? 'selected' : '' }}>Caducado</option>
                        <option value="suspendido" {{ old('estado', $carnet->estado) == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                    </select>
                </div>
            </div>

            <div class="actions" style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
                <a href="{{ route('admin.carnets.index') }}" class="btn secondary">Cancelar</a>
                <button type="submit" class="btn primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
