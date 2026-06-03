@extends('layouts.admin')
@section('heading', 'Dashboard')
@section('content')

<div class="cards">
    <div class="stat">
        <div class="number">{{ $totalRequests }}</div>
        <div class="label">Total solicitudes</div>
    </div>
    <div class="stat">
        <div class="number">{{ $pendingRequests }}</div>
        <div class="label">Pendientes</div>
    </div>
    <div class="stat">
        <div class="number">{{ $students }}</div>
        <div class="label">Estudiantes</div>
    </div>
    <div class="stat">
        <div class="number">{{ $generatedCards }}</div>
        <div class="label">Carnets generados</div>
    </div>
</div>

<div class="panel">
    <div class="section-header">
        <h2 class="panel-title mb-0">Últimas solicitudes</h2>
        <a href="{{ route('admin.requests.index') }}" class="btn secondary sm">Ver todas</a>
    </div>
    @if($latestRequests->isEmpty())
        <div class="empty-state">
            <div class="icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></div>
            <p>No hay solicitudes aún. Las solicitudes públicas aparecerán aquí.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Trámite</th>
                        <th>Estudiante</th>
                        <th>Carrera</th>
                        <th>Semestre</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestRequests as $req)
                        <tr>
                            <td><span class="procedure-code" style="font-size:12px;padding:4px 10px">{{ $req->procedure_number }}</span></td>
                            <td><strong>{{ $req->names }} {{ $req->lastnames }}</strong></td>
                            <td>{{ $req->career->name ?? '—' }}</td>
                            <td>{{ $req->semester->name ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $req->status }}">
                                    {{ ['pending'=>'Pendiente','approved'=>'Aprobada','rejected'=>'Rechazada','observed'=>'Observada'][$req->status] ?? $req->status }}
                                </span>
                            </td>
                            <td class="muted small">{{ $req->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.requests.show', $req) }}" class="btn secondary sm">Ver</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
