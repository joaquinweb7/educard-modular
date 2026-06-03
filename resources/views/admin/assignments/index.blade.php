@extends('layouts.admin')
@section('title', 'Asignaciones Académicas')

@section('content')
<div class="admin-header">
    <div>
        <h2>Asignaciones Académicas</h2>
        <p class="muted">Configura las gestiones, turnos y grupos habilitados por carrera.</p>
    </div>
    <a href="{{ route('admin.assignments.create') }}" class="btn primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Nueva Asignación
    </a>
</div>

<div class="card" style="padding:0">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Carrera</th>
                    <th>Semestre</th>
                    <th>Gestión</th>
                    <th>Turno</th>
                    <th>Grupo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                    <tr>
                        <td style="font-weight: 500;">{{ $assignment->career->name }}</td>
                        <td>{{ $assignment->semester?->name ?? '—' }}</td>
                        <td>{{ $assignment->gestion }}</td>
                        <td>{{ $assignment->turno }}</td>
                        <td>
                            <span class="badge" style="background:var(--surface-3);color:var(--text)">
                                {{ $assignment->grupo }}
                            </span>
                        </td>
                        <td>
                            @if($assignment->status === 'active')
                                <span class="badge" style="background:var(--success-dim);color:var(--success)">Activo</span>
                            @else
                                <span class="badge" style="background:var(--danger-dim);color:var(--danger)">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.assignments.edit', $assignment) }}" class="btn secondary small" style="color:var(--primary); border: 1px solid rgba(79,70,229,0.2)" title="Editar">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                                <form action="{{ route('admin.assignments.destroy', $assignment) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Eliminar esta asignación?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn secondary small" style="color:var(--danger); border: 1px solid rgba(239,68,68,0.2)" title="Eliminar">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding:32px 16px;color:var(--text-muted)">
                            No hay asignaciones registradas. Crea una para habilitar las opciones en los formularios.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
