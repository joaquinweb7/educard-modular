@extends('layouts.admin')
@section('heading', 'Plantillas de Carnet')
@section('content')

<div class="panel">
    <div class="section-header">
        <h2 class="panel-title mb-0">Plantillas</h2>
        <a href="{{ route('admin.card-templates.create') }}" id="btn-new-template" class="btn primary">+ Nueva plantilla</a>
    </div>

    @if($templates->isEmpty())
        <div class="empty-state">
            <div class="icon">🪪</div>
            <p>No hay plantillas de carnet. Crea una para comenzar a diseñar carnets.</p>
            <div style="margin-top:12px">
                <a href="{{ route('admin.card-templates.create') }}" class="btn primary">Crear primera plantilla</a>
            </div>
        </div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Dimensiones</th>
                        <th>Estado</th>
                        <th>Creada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                        <tr>
                            <td><strong>{{ $template->name }}</strong></td>
                            <td class="muted">{{ round($template->width / 37.795276, 2) }} × {{ round($template->height / 37.795276, 2) }} cm</td>
                            <td>
                                <span class="badge {{ $template->status === 'active' ? 'active' : 'inactive' }}">
                                    {{ $template->status === 'active' ? 'Activa' : 'Inactiva' }}
                                </span>
                                @if($template->is_default)
                                    <span class="badge approved" style="margin-left:4px">Por defecto</span>
                                @endif
                            </td>
                            <td class="muted small">{{ $template->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.card-templates.edit', $template) }}" class="btn secondary sm"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Diseñar</a>
                                    <form method="POST" action="{{ route('admin.card-templates.destroy', $template) }}"
                                          onsubmit="return confirm('¿Eliminar plantilla {{ $template->name }}?')">
                                        @csrf @method('DELETE')
                                        <button id="btn-del-template-{{ $template->id }}" type="submit" class="btn danger sm"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px">{{ $templates->links() }}</div>
    @endif
</div>

@endsection
