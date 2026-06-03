@extends('layouts.admin')
@section('heading', 'Semestres Académicos')
@section('content')

<div class="panel" style="margin-bottom:20px">
    <h2 class="panel-title">Nuevo Semestre</h2>
    <form method="POST" action="{{ route('admin.plugins.academicstructure.semesters.store') }}">
        @csrf
        <div style="display:flex;gap:12px;align-items:flex-end">
            <div class="field" style="flex:1;margin:0">
                <label>Nombre del semestre</label>
                <input type="text" name="name" placeholder="Ej: Primer Semestre" required>
            </div>
            <div class="field" style="width:150px;margin:0">
                <label>Número (Orden)</label>
                <input type="number" name="number" placeholder="Ej: 1" required>
            </div>
            <input type="hidden" name="status" value="active">
            <button class="btn primary">Crear Semestre</button>
        </div>
    </form>
</div>

<div class="panel">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h2 class="panel-title mb-0">Semestres Registrados (Núcleo)</h2>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Nº</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th style="width:100px;text-align:right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($semesters as $semester)
                <tr>
                    <td>{{ $semester->number }}</td>
                    <td>{{ $semester->name }}</td>
                    <td><span class="badge {{ $semester->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($semester->status) }}</span></td>
                    <td style="text-align:right">
                        <form method="POST" action="{{ route('admin.plugins.academicstructure.semesters.destroy', $semester->id) }}" onsubmit="return confirm('¿Eliminar semestre? Esto podría afectar a los estudiantes inscritos.')" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn danger sm">✕</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:24px;color:var(--text-dim)">No hay semestres registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top:20px">
        <a href="{{ route('admin.plugins.academicstructure.index') }}" class="btn secondary sm">← Volver al menú principal</a>
    </div>
</div>

@endsection
