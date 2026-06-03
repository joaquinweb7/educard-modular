@extends('layouts.admin')
@section('heading', 'Grupos Académicos')
@section('content')

<div class="panel" style="margin-bottom:20px">
    <h2 class="panel-title">Nuevo Grupo</h2>
    <form method="POST" action="{{ route('admin.plugins.academicstructure.groups.store') }}">
        @csrf
        <div style="display:flex;gap:12px;align-items:flex-end">
            <div class="field" style="flex:1;margin:0">
                <label>Nombre del grupo</label>
                <input type="text" name="name" placeholder="Ej: A, B, C..." required>
            </div>
            <button class="btn primary">Crear Grupo</button>
        </div>
    </form>
</div>

<div class="panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h2 class="panel-title mb-0">Grupos Registrados</h2>
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
            @forelse($groups as $group)
                <tr>
                    <td>{{ $group->id }}</td>
                    <td>{{ $group->name }}</td>
                    <td><span class="badge {{ $group->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($group->status) }}</span></td>
                    <td style="text-align:right">
                        <form method="POST" action="{{ route('admin.plugins.academicstructure.groups.destroy', $group->id) }}" onsubmit="return confirm('¿Eliminar grupo?')" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn danger sm">✕</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:24px;color:var(--text-dim)">No hay grupos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top:20px">
        <a href="{{ route('admin.plugins.academicstructure.index') }}" class="btn secondary sm">← Volver al menú principal</a>
    </div>
</div>

@endsection
