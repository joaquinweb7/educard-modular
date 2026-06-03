@extends('layouts.admin')
@section('title', 'Catálogo de Carreras')

@section('content')
<div class="admin-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
    <div>
        <h2>Carreras</h2>
        <p class="muted">Gestión del catálogo de carreras para las listas desplegables.</p>
    </div>
    <a href="{{ route('admin.careers.create') }}" class="btn primary">Nueva Carrera</a>
</div>

<div class="card">
    @if($careers->isEmpty())
        <div class="empty-state">
            <p>No hay carreras registradas.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de la Carrera</th>
                        <th>Estado</th>
                        <th style="text-align:right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($careers as $career)
                        <tr>
                            <td>{{ $career->id }}</td>
                            <td><strong>{{ $career->name }}</strong></td>
                            <td>
                                @if($career->status === 'active')
                                    <span class="badge success">Activo</span>
                                @else
                                    <span class="badge danger">Inactivo</span>
                                @endif
                            </td>
                            <td style="text-align:right">
                                <div style="display:flex; gap:6px; justify-content:flex-end;">
                                    <a href="{{ route('admin.careers.edit', $career) }}" class="btn secondary sm" title="Editar"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                                    
                                    @if($career->status === 'active')
                                        <form method="POST" action="{{ route('admin.careers.destroy', $career) }}" onsubmit="return confirm('¿Desactivar esta carrera? No aparecerá en las nuevas solicitudes.')" style="margin:0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn warning sm" title="Desactivar"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px;">
            {{ $careers->links() }}
        </div>
    @endif
</div>
@endsection
