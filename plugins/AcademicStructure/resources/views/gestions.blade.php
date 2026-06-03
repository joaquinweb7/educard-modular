@extends('layouts.admin')
@section('heading', 'Gestiones Académicas')
@section('content')

<div class="panel" style="margin-bottom:20px">
    <h2 class="panel-title">Nueva Gestión</h2>
    <form method="POST" action="{{ route('admin.plugins.academicstructure.gestions.store') }}">
        @csrf
        <div style="display:flex;gap:12px;align-items:flex-end">
            <div class="field" style="flex:1;margin:0">
                <label>Nombre de la gestión</label>
                <input type="text" name="name" placeholder="Ej: 2026" required>
            </div>
            <button class="btn primary">Crear Gestión</button>
        </div>
    </form>
</div>

<div class="panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h2 class="panel-title mb-0">Gestiones Registradas</h2>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th style="width:100px;text-align:right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gestions as $gestion)
                <tr>
                    <td>{{ $gestion->id }}</td>
                    <td>{{ $gestion->name }}</td>
                    <td><span class="badge {{ $gestion->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($gestion->status) }}</span></td>
                    <td style="text-align:right">
                        <form method="POST" action="{{ route('admin.plugins.academicstructure.gestions.destroy', $gestion->id) }}" onsubmit="return confirm('¿Eliminar gestión?')" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn danger sm">✕</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:24px;color:var(--text-dim)">No hay gestiones registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top:20px">
        <a href="{{ route('admin.plugins.academicstructure.index') }}" class="btn secondary sm">← Volver al menú principal</a>
    </div>
</div>

@endsection
