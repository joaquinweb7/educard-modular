@extends('layouts.admin')

@section('content')
<div class="col-xl-12 col-lg-12 col-sm-12 layout-top-spacing layout-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Detalles del Curso</h4>
                    <div>
                        <a href="{{ route('admin.certificados.curso.edit', $curso) }}" class="btn btn-primary me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                            </svg>
                            Editar
                        </a>
                        <a href="{{ route('admin.certificados.curso.index') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>ID:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $curso->id }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Nombre:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $curso->nombre }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Duración:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $curso->duracion ?: 'No especificada' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Estado:</strong>
                            </div>
                            <div class="col-md-8">
                                @if($curso->estado == 'activo')
                                    <span class="badge badge-light-success">{{ $curso->estado }}</span>
                                @else
                                    <span class="badge badge-light-danger">{{ $curso->estado }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Descripción:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $curso->descripcion ?: 'Sin descripción' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Creado el:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $curso->created_at->format('d/m/Y H:i:s') }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Actualizado el:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $curso->updated_at->format('d/m/Y H:i:s') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Estadísticas</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span>Certificados asociados:</span>
                                    <span class="badge badge-light-primary">{{ $curso->certificados->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($curso->certificados->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Certificados Asociados</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($curso->certificados->take(5) as $certificado)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $certificado->nombre_estudiante }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $certificado->codigo }}</small>
                                </div>
                                <span class="badge badge-light-info">{{ $certificado->created_at->format('d/m/Y') }}</span>
                            </div>
                            @endforeach
                            @if($curso->certificados->count() > 5)
                            <div class="list-group-item text-center">
                                <small class="text-muted">Y {{ $curso->certificados->count() - 5 }} más...</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
