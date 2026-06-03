@extends('main-1.layouts.app')

@push('styles')
@Vite('resources/src/assets/css/dark/elements/alert.css')
@Vite('resources/src/assets/css/light/elements/alert.css')
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-id-card me-2"></i>
                            Detalles del Carnet
                        </h3>
                        <div>
                            <a href="{{ route('carnets.edit', $carnet->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Editar
                            </a>
                            <a href="{{ route('carnets.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Información Personal -->
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user me-2"></i>
                                        Información Personal
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Nombre:</strong></div>
                                        <div class="col-sm-8">{{ $carnet->nombre ?? 'N/A' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Email:</strong></div>
                                        <div class="col-sm-8">
                                            @if($carnet->email)
                                                <a href="mailto:{{ $carnet->email }}">{{ $carnet->email }}</a>
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Teléfono:</strong></div>
                                        <div class="col-sm-8">
                                            @if($carnet->telefono)
                                                <a href="tel:{{ $carnet->telefono }}">{{ $carnet->telefono }}</a>
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Carnet -->
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-id-badge me-2"></i>
                                        Información del Carnet
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Código:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-info fs-6">{{ $carnet->codigo }}</span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Tipo:</strong></div>
                                        <div class="col-sm-8">
                                            @if($carnet->tipo)
                                                <span class="badge bg-primary">{{ ucfirst($carnet->tipo) }}</span>
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Estado:</strong></div>
                                        <div class="col-sm-8">
                                            @if($carnet->estado == 'vigente')
                                                <span class="badge bg-success">Vigente</span>
                                            @elseif($carnet->estado == 'caducado')
                                                <span class="badge bg-secondary">Caducado</span>
                                            @elseif($carnet->estado == 'suspendido')
                                                <span class="badge bg-warning">Suspendido</span>
                                            @else
                                                <span class="badge bg-info">N/A</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4"><strong>Vencimiento:</strong></div>
                                        <div class="col-sm-8">
                                            @if($carnet->fecha_vencimiento)
                                                {{ \Carbon\Carbon::parse($carnet->fecha_vencimiento)->format('d/m/Y') }}
                                                @if(\Carbon\Carbon::parse($carnet->fecha_vencimiento)->isPast())
                                                    <span class="badge bg-danger ms-2">Vencido</span>
                                                @elseif(\Carbon\Carbon::parse($carnet->fecha_vencimiento)->diffInDays(now()) <= 30)
                                                    <span class="badge bg-warning ms-2">Por vencer</span>
                                                @else
                                                    <span class="badge bg-success ms-2">Vigente</span>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    @if($carnet->observaciones)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Observaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $carnet->observaciones }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Información del Sistema -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-cog me-2"></i>
                                        Información del Sistema
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <div class="col-sm-4"><strong>ID:</strong></div>
                                                <div class="col-sm-8">{{ $carnet->id }}</div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4"><strong>Creado:</strong></div>
                                                <div class="col-sm-8">
                                                    {{ $carnet->created_at ? $carnet->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <div class="col-sm-4"><strong>Actualizado:</strong></div>
                                                <div class="col-sm-8">
                                                    {{ $carnet->updated_at ? $carnet->updated_at->format('d/m/Y H:i:s') : 'N/A' }}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4"><strong>QR Code:</strong></div>
                                                <div class="col-sm-8">
                                                    <a href="{{ route('verificar.carnet.index', ['code' => $carnet->codigo]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-qrcode me-1"></i>
                                                        Ver QR
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-tools me-2"></i>
                                        Acciones Rápidas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('carnets.edit', $carnet->id) }}" class="btn btn-warning">
                                            <i class="fas fa-edit me-2"></i>
                                            Editar Carnet
                                        </a>
                                        <a href="{{ route('verificar.carnet.index', ['code' => $carnet->codigo]) }}" target="_blank" class="btn btn-info">
                                            <i class="fas fa-search me-2"></i>
                                            Verificar Carnet
                                        </a>
                                        <button class="btn btn-success" onclick="window.print()">
                                            <i class="fas fa-print me-2"></i>
                                            Imprimir
                                        </button>
                                        <form action="{{ route('carnets.destroy', $carnet->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este carnet?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash me-2"></i>
                                                Eliminar
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

