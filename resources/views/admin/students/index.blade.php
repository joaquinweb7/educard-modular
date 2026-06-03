@extends('layouts.admin')
@section('heading','Estudiantes')
@section('content')
<div class="panel">
    <form method="GET" class="form-grid" style="grid-template-columns:2fr 1fr 1fr 1fr auto">
        <div class="field"><label>Buscar</label><input name="search" value="{{ request('search') }}" placeholder="Nombre, código o C.I."></div>
        <div class="field"><label>Carrera</label><select name="career_id"><option value="">Todas</option>@foreach($careers as $career)<option value="{{ $career->id }}" @selected(request('career_id')==$career->id)>{{ $career->name }}</option>@endforeach</select></div>
        <div class="field"><label>Semestre</label><select name="semester_id"><option value="">Todos</option>@foreach($semesters as $semester)<option value="{{ $semester->id }}" @selected(request('semester_id')==$semester->id)>{{ $semester->name }}</option>@endforeach</select></div>
        <div class="field"><label>Impresión</label><select name="print_status"><option value="">Todos</option><option value="printed" @selected(request('print_status')=='printed')>Impresos</option><option value="not_printed" @selected(request('print_status')=='not_printed')>No Impresos</option></select></div>
        <div class="field"><label>&nbsp;</label><button class="btn primary">Filtrar</button></div>
    </form>
</div>
<div class="panel" id="results-container" style="margin-top:18px">
    <div class="actions" style="margin-bottom:12px"><a class="btn primary" href="{{ route('admin.students.create') }}">Registrar manualmente</a><a class="btn" href="{{ route('admin.students.import.create') }}">Registro masivo</a></div>
    <table class="table"><thead><tr><th>Foto</th><th>Código</th><th>Estudiante</th><th>C.I.</th><th>Carrera</th><th>Semestre</th><th>Impreso</th><th>Acciones</th></tr></thead><tbody>
    @foreach($students as $student)
    <tr>
        <td>@if($student->photo_path)<img src="{{ asset('storage/'.$student->photo_path) }}" style="width:45px;height:55px;object-fit:cover;border-radius:8px">@endif</td>
        <td><strong>{{ $student->student_code }}</strong></td>
        <td>{{ $student->fullName() }}</td>
        <td>{{ $student->ci_number }}</td>
        <td>{{ $student->career?->name }}</td>
        <td>{{ $student->semester?->name }}</td>
        <td>
            @if($student->is_printed)
                <span class="badge success" title="Impreso el {{ $student->printed_at?->format('d/m/Y H:i') }}">Sí</span>
            @else
                <span class="badge danger">No</span>
            @endif
        </td>
        <td>
            <div style="display:flex;gap:6px">
                <form method="POST" action="{{ route('admin.students.toggle-print', $student) }}" style="margin:0">
                    @csrf
                    <button type="submit" class="btn {{ $student->is_printed ? 'warning' : 'success' }} sm" style="padding:6px" title="{{ $student->is_printed ? 'Desmarcar impreso' : 'Marcar impreso' }}">
                        @if($student->is_printed)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                        @else
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        @endif
                    </button>
                </form>
                <a href="{{ route('admin.students.edit', $student) }}" class="btn secondary sm" style="padding:6px; color:var(--primary); border: 1px solid rgba(79,70,229,0.2)" title="Editar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('¿Eliminar estudiante?')" style="margin:0">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn danger sm" style="padding:6px;background:var(--danger);color:#fff;border:none" title="Eliminar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @endforeach
    </tbody></table>
    <div style="margin-top:14px">{{ $students->links() }}</div>
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
        const filterBtn = filterForm.querySelector('button.btn.primary');
        if (filterBtn) filterBtn.parentElement.style.display = 'none';

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
