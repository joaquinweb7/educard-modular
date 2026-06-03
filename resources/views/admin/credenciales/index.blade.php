@extends('layouts.admin')
@section('heading', 'Lista de Credenciales Administrativas')
@section('content')

<div class="panel">
    <form method="GET" class="form-grid" style="grid-template-columns:1fr auto">
        <div class="field">
            <label>Buscar</label>
            <input name="search" value="{{ request('search') }}" placeholder="Nombre, apellidos, C.I. o código">
        </div>
        <div class="field" style="align-self: end;">
            <button class="btn primary">Buscar</button>
        </div>
    </form>
</div>

<div class="panel" id="results-container" style="margin-top:18px">
    <div class="actions" style="margin-bottom:12px">
        <a class="btn primary" href="{{ route('admin.credenciales.create') }}">Crear nuevo</a>
        <a class="btn" href="{{ route('admin.credenciales.subir') }}">Importar Excel</a>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>C.I.</th>
                <th>Código Cred.</th>
                <th>Cargo Principal</th>
                <th>Cargo Sec. / Área</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($credenciales as $credencial)
        <tr>
            <td>{{ $credencial->nombres }}</td>
            <td>{{ $credencial->apellidos }}</td>
            <td>{{ $credencial->cedula_identidad }}</td>
            <td><strong>{{ $credencial->codigo_credencial }}</strong></td>
            <td>{{ $credencial->cargo_principal }}</td>
            <td>{{ $credencial->cargo_secundario }} <br> {{ $credencial->departamento }}</td>
            <td>
                @if(strtolower($credencial->estado) == 'vigente')
                    <span class="badge success">VIGENTE</span>
                @elseif(strtolower($credencial->estado) == 'suspendido')
                    <span class="badge warning">SUSPENDIDO</span>
                @else
                    <span class="badge danger">CADUCADO</span>
                @endif
            </td>
            <td>
                <div style="display:flex;gap:6px">
                    <a href="{{ route('admin.credenciales.edit', $credencial) }}" class="btn secondary sm" style="padding:6px; color:var(--primary); border: 1px solid rgba(79,70,229,0.2)" title="Editar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </a>
                    <form method="POST" action="{{ route('admin.credenciales.destroy', $credencial) }}" onsubmit="return confirm('¿Eliminar esta credencial?')" style="margin:0">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn danger sm" style="padding:6px;background:var(--danger);color:#fff;border:none" title="Eliminar">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align: center; padding: 20px; color: var(--text-muted);">
                No se encontraron credenciales.
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:14px">
        {{ $credenciales->links() }}
    </div>
</div>

<script>
function updateTable(url = null) {
    const filterForm = document.querySelector('form.form-grid');
    
    if (!url) {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        url = new URL(window.location.href.split('?')[0]);
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
    const filterForm = document.querySelector('form.form-grid');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateTable();
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