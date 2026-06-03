@extends('layouts.admin')
@section('heading', 'Prueba de Email')
@section('content')

<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Enviar Correo de Prueba</h3>
    </div>
    <div class="panel-body mt-4">
        <form action="{{ route('admin.certificados.smtp.email-test') }}" method="POST">
            @csrf
            <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="field">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="input" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                </div>
                <div class="field">
                    <label for="curso">Curso</label>
                    <input type="text" class="input" id="curso" name="curso" value="{{ old('curso') }}" required>
                </div>
                <div class="field">
                    <label for="codigo">Código</label>
                    <input type="text" class="input" id="codigo" name="codigo" value="{{ old('codigo') }}" required>
                </div>
                <div class="field">
                    <label for="email">Email Destino</label>
                    <input type="email" class="input" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="field">
                    <label for="certificado">Plantilla a usar</label>
                    <select class="input" id="certificado" name="certificado" required>
                        <option value="" disabled selected>Seleccione una plantilla</option>
                        @foreach($plantillas as $plantilla)
                            <option value="{{ $plantilla->value }}">{{ $plantilla->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4" style="text-align: right;">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="send"></i> Enviar Prueba
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
