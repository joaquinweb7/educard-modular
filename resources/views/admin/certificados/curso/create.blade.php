@extends('layouts.admin')
@section('heading', 'Crear Nuevo Curso')
@section('content')

<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Formulario de Curso</h3>
    </div>
    <div class="panel-body mt-4">
        <form action="{{ route('admin.certificados.curso.store') }}" method="POST">
            @csrf
            <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="field">
                    <label for="nombre">Nombre del Curso</label>
                    <input type="text" class="input" id="nombre" name="nombre" placeholder="Nombre del curso" value="{{ old('nombre') }}" required>
                    @error('nombre') <span style="color:var(--danger)">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4" style="text-align: right;">
                <a href="{{ route('admin.certificados.curso.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i> Guardar Curso
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
