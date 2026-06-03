@extends('layouts.admin')

@push('styles')
<style>
/* css content here */
</style>
@endpush

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xxl-9 col-xl-10 col-lg-11">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Detalles del Certificado</h3>
                    <div>
                        <a href="{{ route('admin.certificados.edit', $certificado) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('admin.certificados.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Información del Estudiante -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0 text-primary">
                                        <i class="fas fa-user me-2"></i>Información del Estudiante
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Nombre:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $certificado->nombre_estudiante }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Carnet:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $certificado->carnet }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Email:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <a href="mailto:{{ $certificado->email }}" class="text-primary">
                                                {{ $certificado->email }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Curso -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0 text-success">
                                        <i class="fas fa-graduation-cap me-2"></i>Información del Curso
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Curso:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $certificado->curso->nombre ?? 'No especificado' }}
                                        </div>
                                    </div>
                                    @if($certificado->curso && $certificado->curso->descripcion)
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Descripción:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $certificado->curso->descripcion }}
                                        </div>
                                    </div>
                                    @endif
                                    @if($certificado->curso && $certificado->curso->duracion)
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Duración:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $certificado->curso->duracion }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Información del Certificado -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0 text-info">
                                        <i class="fas fa-certificate me-2"></i>Información del Certificado
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Código:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <code class="bg-light p-2 rounded">{{ $certificado->codigo }}</code>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Plantilla:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $certificado->plantilla->nombre ?? 'No especificada' }}
                                        </div>
                                    </div>
                                    @if($certificado->plantilla && $certificado->plantilla->imagen)
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Vista Previa:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <img src="{{ Storage::url($certificado->plantilla->imagen) }}" 
                                                 alt="Vista previa de la plantilla" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 150px; max-height: 100px;">
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Información del Sistema -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0 text-secondary">
                                        <i class="fas fa-info-circle me-2"></i>Información del Sistema
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>ID:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            #{{ $certificado->id }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Creado:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $certificado->created_at->format('d/m/Y H:i:s') }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Actualizado:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $certificado->updated_at->format('d/m/Y H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0 text-warning">
                                        <i class="fas fa-cogs me-2"></i>Acciones Disponibles
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('admin.certificados.edit', $certificado) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Editar Certificado
                                        </a>
                                        <a href="{{ route('admin.certificados.smtp.sendEmail', $certificado) }}" class="btn btn-info">
                                            <i class="fas fa-envelope"></i> Enviar por Email
                                        </a>
                                        @if($certificado->plantilla)
                                        <a href="{{ route('admin.certificados.test', ['plantilla' => $certificado->plantilla->id]) }}" 
                                           class="btn btn-success" target="_blank">
                                            <i class="fas fa-eye"></i> Ver Plantilla
                                        </a>
                                        @endif
                                        <form action="{{ route('admin.certificados.destroy', $certificado) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Está seguro de que desea eliminar este certificado?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const isDarkMode = JSON.parse(localStorage.getItem('theme')).settings.layout.darkMode
</script>
@endpush
