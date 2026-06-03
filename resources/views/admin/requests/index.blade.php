@extends('layouts.admin')
@section('heading', 'Solicitudes de Carnet')
@section('content')

<div class="panel">
    <div class="section-header">
        <h2 class="panel-title mb-0">Solicitudes</h2>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.requests.index') }}" style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Buscar nombre, CI, trámite..."
               style="flex:1;min-width:200px;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:9px 14px;color:var(--text);font-size:13.5px">
        <select name="status" style="background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:9px 14px;color:var(--text);font-size:13.5px;cursor:pointer">
            <option value="">Todos los estados</option>
            <option value="pending"  {{ request('status')=='pending'?'selected':'' }}>Pendiente</option>
            <option value="resubmitted" {{ request('status')=='resubmitted'?'selected':'' }}>Reenviada</option>
            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Aprobada</option>
            <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rechazada</option>
            <option value="observed" {{ request('status')=='observed'?'selected':'' }}>Observada</option>
        </select>
        <button type="submit" class="btn primary">Filtrar</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.requests.index') }}" class="btn secondary">Limpiar</a>
        @endif
    </form>

    <div id="results-container">
        @if($requests->isEmpty())
            <div class="empty-state">
                <div class="icon" style="color: var(--text-muted);">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <p>No se encontraron solicitudes.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>N° Trámite</th>
                            <th>Estudiante</th>
                            <th>C.I.</th>
                            <th>Carrera</th>
                            <th>Semestre</th>
                            <th>Foto IA</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $req)
                            <tr>
                                <td>
                                    @if($req->photo_path)
                                        <img src="{{ asset('storage/'.$req->photo_path) }}" style="width:40px;height:50px;object-fit:cover;border-radius:4px">
                                    @endif
                                </td>
                                <td><span class="procedure-code" style="font-size:12px;padding:4px 10px">{{ $req->procedure_number }}</span></td>
                                <td><strong>{{ $req->names }} {{ $req->lastnames }}</strong></td>
                                <td class="muted">{{ $req->ci_number }}</td>
                                <td>{{ $req->career->name ?? '—' }}</td>
                                <td>{{ $req->semester->name ?? '—' }}</td>
                                <td style="text-align:center;">
                                    @if($req->photo_validation_status === 'passed')
                                        <span title="Cumple 100%" style="color:var(--success);display:inline-flex;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                        </span>
                                    @elseif($req->photo_validation_status === 'failed')
                                        <span title="Rechazada por Algoritmo" style="color:var(--danger);display:inline-flex;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                        </span>
                                    @elseif($req->photo_validation_status === 'manual_review')
                                        <span title="Revisión Manual Requerida" style="color:var(--warning);display:inline-flex;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                        </span>
                                    @else
                                        <span title="Pendiente" style="color:var(--text-muted);display:inline-flex;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $req->status }}">
                                        {{ ['pending'=>'Pendiente','resubmitted'=>'Reenviada','approved'=>'Aprobada','rejected'=>'Rechazada','observed'=>'Observada'][$req->status] ?? $req->status }}
                                    </span>
                                </td>
                                <td class="muted small">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div style="display:flex;gap:6px;align-items:center;justify-content:flex-end">
                                        <form method="POST" action="{{ route('admin.requests.approve', $req) }}" style="margin:0" onsubmit="return confirm('¿Aprobar esta solicitud?')">
                                            @csrf
                                            <button type="submit" class="btn primary sm" style="background:var(--success);color:#fff;border:none;padding:6px" title="Aprobar">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.requests.reject', $req) }}" style="margin:0" onsubmit="return confirm('¿Rechazar esta solicitud?')">
                                            @csrf
                                            <button type="submit" class="btn danger sm" style="background:var(--danger);color:#fff;border:none;padding:6px" title="Rechazar">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.requests.show', $req) }}" class="btn secondary sm" style="padding:6px" title="Ver detalle completo">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.requests.destroy', $req) }}" style="margin:0" onsubmit="event.preventDefault(); window.confirmAction('¿Estás seguro de eliminar permanentemente esta solicitud y sus archivos?', this);">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn danger sm" style="background:#4b5563;color:#fff;border:none;padding:6px" title="Eliminar solicitud">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:16px">{{ $requests->links() }}</div>
        @endif
    </div>
</div>

<script>
function updateTable(url = null) {
    const filterForm = document.querySelector('form[action="{{ route('admin.requests.index') }}"]');
    
    if (!url) {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        url = new URL(filterForm.action);
        url.search = params.toString();
    }

    const container = document.getElementById('results-container');
    container.style.opacity = '0.5';
    container.style.pointerEvents = 'none';

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            container.innerHTML = doc.getElementById('results-container').innerHTML;
            container.style.opacity = '1';
            container.style.pointerEvents = 'auto';
            window.history.pushState({}, '', url);
            attachPaginationListeners();
        })
        .catch(err => {
            console.error('Error fetching data:', err);
            container.style.opacity = '1';
            container.style.pointerEvents = 'auto';
        });
}

function attachPaginationListeners() {
    document.querySelectorAll('#results-container .pagination a, #results-container nav a').forEach(a => {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            updateTable(this.href);
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('form[action="{{ route('admin.requests.index') }}"]');
    if (filterForm) {
        const filterBtn = filterForm.querySelector('button[type="submit"]');
        if (filterBtn) filterBtn.style.display = 'none';

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateTable();
        });

        filterForm.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', function() {
                updateTable();
            });
        });

        let timeout = null;
        const searchInput = filterForm.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    updateTable();
                }, 500);
            });
        }
    }

    attachPaginationListeners();
});
</script>

@endsection
