@extends('layouts.admin')
@section('heading', 'Carnets Derivados para Impresión')
@section('content')

<div class="panel" style="margin-bottom:18px">
    <div class="panel-title">Filtros y Plantilla</div>
    
    <div class="form-grid" style="margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:16px;">
        <div class="field" style="grid-column:1/-1">
            <label for="global-template-id">1. Selecciona la Plantilla a utilizar *</label>
            <select id="global-template-id" required>
                <option value="">— Seleccionar plantilla —</option>
                @foreach($templates as $tpl)
                    <option value="{{ $tpl->id }}">{{ $tpl->name }}</option>
                @endforeach
            </select>
            @if($templates->isEmpty())
                <span class="muted small"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> No hay plantillas activas. <a href="{{ route('admin.card-templates.create') }}" style="color:var(--primary)">Crea una</a>.</span>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('admin.cards.derived.index') }}">
        <div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr))">
            <div class="field">
                <label>Buscar</label>
                <input name="search" value="{{ request('search') }}" placeholder="Nombre, código...">
            </div>
            <div class="field">
                <label>Carrera</label>
                <select name="career_id" class="dependent-career">
                    <option value="">Todas</option>
                    @foreach($careers as $c)
                        <option value="{{ $c->id }}" @selected(request('career_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Semestre</label>
                <select name="semester_id" class="dependent-semester" data-old="{{ request('semester_id') }}">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="field">
                <label>Gestión</label>
                <select name="gestion" class="dependent-gestion" data-old="{{ request('gestion') }}">
                    <option value="">Todas</option>
                </select>
            </div>
            <div class="field">
                <label>Turno</label>
                <select name="turno" class="dependent-turno" data-old="{{ request('turno') }}">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="field">
                <label>Grupo</label>
                <select name="grupo" class="dependent-grupo" data-old="{{ request('grupo') }}">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="field">
                <label>Impresión</label>
                <select name="print_status">
                    <option value="">Todos</option>
                    <option value="printed" @selected(request('print_status')=='printed')>Impresos</option>
                    <option value="not_printed" @selected(request('print_status')=='not_printed')>No Impresos</option>
                </select>
            </div>
            <div class="field" style="display:flex;align-items:flex-end">
                <button type="submit" class="btn secondary" style="width:100%">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px; vertical-align:-3px"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Filtrar
                </button>
            </div>
        </div>
    </form>
</div>

<div class="panel" id="results-container">
    <div class="section-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h2 class="panel-title mb-0">Carnets Derivados ({{ $students->total() }})</h2>
        <div style="display:flex; gap:10px;">
            <form method="POST" action="{{ route('admin.cards.derived.pdf') }}" id="form-generate-all" onsubmit="return validateTemplate('form-generate-all')">
                @csrf
                <!-- Pasamos los filtros actuales como hidden inputs -->
                <input type="hidden" name="card_template_id" class="hidden-template-id">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="career_id" value="{{ request('career_id') }}">
                <input type="hidden" name="semester_id" value="{{ request('semester_id') }}">
                <input type="hidden" name="gestion" value="{{ request('gestion') }}">
                <input type="hidden" name="turno" value="{{ request('turno') }}">
                <input type="hidden" name="grupo" value="{{ request('grupo') }}">
                
                <button type="submit" class="btn primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px; vertical-align:-3px"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    Imprimir TODOS los derivados en PDF
                </button>
            </form>
        </div>
    </div>

    @if($students->isEmpty())
        <div class="empty-state">
            <div class="icon" style="color:var(--text-muted)">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <p>No hay estudiantes derivados para impresión con estos filtros.</p>
        </div>
    @else
        <div class="table-wrap" style="margin-top:16px">
            <table class="table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Código</th>
                        <th>Estudiante</th>
                        <th>Carrera</th>
                        <th>Semestre</th>
                        <th>Gestión / Grupo</th>
                        <th>Derivado el</th>
                        <th>Impreso</th>
                        <th style="text-align:right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>
                            @if($student->photo_path)
                                <img src="{{ asset('storage/'.$student->photo_path) }}" style="width:40px;height:50px;object-fit:cover;border-radius:4px">
                            @endif
                        </td>
                        <td><strong>{{ $student->student_code }}</strong></td>
                        <td>{{ $student->fullName() }}<br><small class="muted">{{ $student->ci_number }}</small></td>
                        <td>{{ $student->career?->name ?? '—' }}</td>
                        <td>{{ $student->semester?->name ?? '—' }}</td>
                        <td>{{ $student->gestion }}<br><small class="muted">{{ $student->grupo }} ({{ $student->turno }})</small></td>
                        <td>{{ $student->derived_at ? $student->derived_at->format('d/m/Y H:i') : '—' }}</td>
                        <td>
                            @if($student->is_printed)
                                <span class="badge success">Sí</span>
                            @else
                                <span class="badge danger">No</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <!-- Toggle Manual (Toggle Print) -->
                                <form method="POST" action="{{ route('admin.students.toggle-print', $student) }}" style="margin:0">
                                    @csrf
                                    <button type="submit" class="btn {{ $student->is_printed ? 'warning' : 'success' }} sm" style="padding:4px 8px" title="{{ $student->is_printed ? 'Desmarcar impreso' : 'Marcar impreso (Manual)' }}">
                                        {{ $student->is_printed ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>' : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg>' }}
                                    </button>
                                </form>

                                <!-- Generar PDF individual (Automático) -->
                                <form method="POST" action="{{ route('admin.cards.derived.pdf') }}" class="form-generate-single" onsubmit="return validateTemplate(this)">
                                    @csrf
                                    <input type="hidden" name="card_template_id" class="hidden-template-id">
                                    <input type="hidden" name="student_ids" value="{{ $student->id }}">
                                    <button type="submit" class="btn secondary sm" style="padding:6px; color:var(--text)" title="Imprimir solo a este estudiante">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:14px">{{ $students->links() }}</div>
    @endif
</div>

<script src="{{ asset('js/academic-dropdowns.js') }}"></script>
<script>
function validateTemplate(formOrId) {
    var templateId = document.getElementById('global-template-id').value;
    if (!templateId) {
        showToast('Por favor, selecciona primero una Plantilla de carnet en la parte superior.', 'error');
        document.getElementById('global-template-id').focus();
        return false;
    }
    
    var form = typeof formOrId === 'string' ? document.getElementById(formOrId) : formOrId;
    var hiddenInput = form.querySelector('.hidden-template-id');
    if (hiddenInput) {
        hiddenInput.value = templateId;
    }
    return true;
}

function updateTable(url = null) {
    const filterForm = document.querySelector('form[action="{{ route('admin.cards.derived.index') }}"]');
    
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
    const filterForm = document.querySelector('form[action="{{ route('admin.cards.derived.index') }}"]');
    if (filterForm) {
        // Ocultar botón de filtrar
        const filterBtn = filterForm.querySelector('button[type="submit"]');
        if (filterBtn) filterBtn.parentElement.style.display = 'none';

        // Evitar submit normal del formulario
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateTable();
        });

        // Auto-submit en selects via AJAX
        filterForm.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', function() {
                // Si cambia la carrera, limpiar los dependientes
                if (this.name === 'career_id') {
                    filterForm.querySelector('select[name="semester_id"]').value = '';
                    filterForm.querySelector('select[name="gestion"]').value = '';
                    filterForm.querySelector('select[name="turno"]').value = '';
                    filterForm.querySelector('select[name="grupo"]').value = '';
                } else if (this.name === 'semester_id') {
                    filterForm.querySelector('select[name="gestion"]').value = '';
                    filterForm.querySelector('select[name="turno"]').value = '';
                    filterForm.querySelector('select[name="grupo"]').value = '';
                } else if (this.name === 'gestion') {
                    filterForm.querySelector('select[name="turno"]').value = '';
                    filterForm.querySelector('select[name="grupo"]').value = '';
                } else if (this.name === 'turno') {
                    filterForm.querySelector('select[name="grupo"]').value = '';
                }
                updateTable();
            });
        });

        // Debounce en búsqueda via AJAX
        let timeout = null;
        filterForm.querySelector('input[name="search"]').addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                updateTable();
            }, 500);
        });
    }

    attachPaginationListeners();
});
</script>

@endsection
