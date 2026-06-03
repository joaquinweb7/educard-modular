@extends('layouts.admin')
@section('heading', 'Editar Certificado')
@section('content')

<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Actualizar Certificado</h3>
    </div>
    <div class="panel-body mt-4">
        <form action="{{ route('admin.certificados.update', $certificado->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="field">
                    <label for="nombre_estudiante">Nombre Estudiante</label>
                    <input type="text" class="input" id="nombre_estudiante" name="nombre_estudiante" placeholder="Nombre completo" value="{{ old('nombre_estudiante', $certificado->nombre_estudiante) }}" required>
                    @error('nombre_estudiante') <span style="color:var(--danger)">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="nombre_curso">Curso</label>
                    <select class="input" id="nombre_curso" name="nombre_curso" required>
                        <option value="" disabled>Seleccione un curso</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}" {{ (old('nombre_curso', $certificado->nombre_curso_id) == $curso->id) ? 'selected' : '' }}>{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                    @error('nombre_curso') <span style="color:var(--danger)">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="carnet">Carnet</label>
                    <input type="text" class="input" id="carnet" name="carnet" placeholder="Número de carnet" value="{{ old('carnet', $certificado->carnet) }}" required>
                    @error('carnet') <span style="color:var(--danger)">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" class="input" id="email" name="email" placeholder="ejemplo@dominio.com" value="{{ old('email', $certificado->email) }}" required>
                    @error('email') <span style="color:var(--danger)">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="codigo">Código Único</label>
                    <input type="text" class="input" id="codigo" name="codigo" placeholder="Ej: CERT-2026-001" value="{{ old('codigo', $certificado->codigo) }}" required>
                    @error('codigo') <span style="color:var(--danger)">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="plantilla_id">Plantilla</label>
                    <select class="input" id="plantilla_id" name="plantilla_id" required>
                        <option value="" disabled>Seleccione una plantilla</option>
                        @foreach($plantillas as $plantilla)
                            <option value="{{ $plantilla->id }}" {{ (old('plantilla_id', $certificado->plantilla_id) == $plantilla->id) ? 'selected' : '' }}>{{ $plantilla->nombre }}</option>
                        @endforeach
                    </select>
                    @error('plantilla_id') <span style="color:var(--danger)">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4" style="text-align: right;">
                <a href="{{ route('admin.certificados.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i> Actualizar Certificado
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
