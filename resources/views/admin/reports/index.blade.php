@extends('layouts.admin')
@section('heading', 'Reportes')
@section('content')

<div class="cards" style="margin-bottom:20px">
    <div class="stat">
        <div class="number">{{ $requests }}</div>
        <div class="label">Total solicitudes</div>
    </div>
    <div class="stat">
        <div class="number">{{ $students }}</div>
        <div class="label">Estudiantes activos</div>
    </div>
    <div class="stat">
        <div class="number">{{ $generatedCards }}</div>
        <div class="label">Carnets generados</div>
    </div>
    <div class="stat" style="display:flex;align-items:center;justify-content:center">
        <a href="{{ route('admin.reports.pdf', request()->all()) }}" class="btn primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px; vertical-align:-3px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Descargar Reporte (PDF)
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <div class="panel">
        <h2 class="panel-title">Estudiantes por carrera</h2>
        @if($studentsByCareer->isEmpty())
            <div class="empty-state">
                <div class="icon" style="color:var(--text-muted)">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                </div>
                <p>Sin datos disponibles.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Carrera</th><th style="text-align:right">Estudiantes</th></tr></thead>
                    <tbody>
                        @foreach($studentsByCareer as $career)
                            <tr>
                                <td>{{ $career->name }}</td>
                                <td style="text-align:right;font-weight:700">{{ $career->students_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="panel">
        <h2 class="panel-title">Estudiantes por semestre</h2>
        @if($studentsBySemester->isEmpty())
            <div class="empty-state">
                <div class="icon" style="color:var(--text-muted)">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                </div>
                <p>Sin datos disponibles.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Semestre</th><th style="text-align:right">Estudiantes</th></tr></thead>
                    <tbody>
                        @foreach($studentsBySemester as $sem)
                            <tr>
                                <td>{{ $sem->name }}</td>
                                <td style="text-align:right;font-weight:700">{{ $sem->students_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="panel" style="margin-top: 20px;">
    <h2 class="panel-title">Reporte Detallado de Impresiones</h2>
    
    <form method="GET" action="{{ route('admin.reports.index') }}" class="form-grid" style="grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr 1fr auto; margin-bottom: 20px;">
        <div class="field">
            <label>Buscar</label>
            <input type="text" name="search" placeholder="CI, Nombre..." value="{{ request('search') }}">
        </div>
        
        <div class="field">
            <label>Carrera</label>
            <select name="career_id">
                <option value="">Todas</option>
                @foreach($careers as $c)
                    <option value="{{ $c->id }}" {{ request('career_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="field">
            <label>Semestre</label>
            <select name="semester_id">
                <option value="">Todos</option>
                @foreach($semesters as $s)
                    <option value="{{ $s->id }}" {{ request('semester_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="field">
            <label>Gestión</label>
            <select name="gestion">
                <option value="">Todas</option>
                @foreach($gestiones as $g)
                    <option value="{{ $g }}" {{ request('gestion') == $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Turno</label>
            <select name="turno">
                <option value="">Todos</option>
                @foreach($turnos as $t)
                    <option value="{{ $t }}" {{ request('turno') == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Grupo</label>
            <select name="grupo">
                <option value="">Todos</option>
                @foreach($grupos as $gr)
                    <option value="{{ $gr }}" {{ request('grupo') == $gr ? 'selected' : '' }}>{{ $gr }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Impresión</label>
            <select name="print_status">
                <option value="printed" {{ request('print_status', 'printed') == 'printed' ? 'selected' : '' }}>Impresos</option>
                <option value="pending" {{ request('print_status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                <option value="all" {{ request('print_status') == 'all' ? 'selected' : '' }}>Todos</option>
            </select>
        </div>
        
        <div class="field">
            <label>&nbsp;</label>
            <button type="submit" name="export" value="excel" class="btn success" title="Exportar reporte a Excel (CSV)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px; vertical-align:-3px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Excel
            </button>
        </div>
    </form>

    <div id="results-container">
        @if($studentsList->isEmpty())
            <div class="empty-state">No se encontraron registros.</div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>C.I. / Código</th>
                        <th>Carrera</th>
                        <th>Semestre / Gestión</th>
                        <th>Estado de Impresión</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentsList as $student)
                        <tr>
                            <td>
                                <div style="font-weight:600">{{ $student->names }} {{ $student->lastnames }}</div>
                            </td>
                            <td>
                                <div>{{ $student->ci_number }}</div>
                                <div style="font-size:12px;color:var(--text-muted)">{{ $student->student_code ?? 'S/C' }}</div>
                            </td>
                            <td>{{ $student->career->name ?? '-' }}</td>
                            <td>
                                <div>{{ $student->semester->name ?? '-' }}</div>
                                <div style="font-size:12px;color:var(--text-muted)">{{ $student->gestion }}</div>
                            </td>
                            <td>
                                @if($student->is_printed)
                                    <span class="badge" style="background:var(--success);color:#fff">Impreso {{ $student->printed_at ? $student->printed_at->format('d/m/Y') : '' }}</span>
                                @else
                                    <span class="badge" style="background:var(--warning);color:#fff">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
            <div style="margin-top:16px">
                {{ $studentsList->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function updateTable(url = null) {
    const filterForm = document.querySelector('form.form-grid');
    
    if (!url) {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        // Ensure export is empty for ajax filtering
        params.delete('export');
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
    const filterForm = document.querySelector('form.form-grid');
    if (filterForm) {
        // Ocultar solo el botón Filtrar
        const filterBtn = filterForm.querySelector('button[value=""]');
        if (filterBtn) filterBtn.parentElement.style.display = 'none';

        filterForm.addEventListener('submit', function(e) {
            if (e.submitter && e.submitter.value && e.submitter.value !== '') {
                return; // Let normal submit happen for export buttons
            }
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
