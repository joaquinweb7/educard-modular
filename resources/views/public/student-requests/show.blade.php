@extends('layouts.public')
@section('content')
<div class="public-wrap">
    <div class="public-card" style="max-width: 700px">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('storage/images/logo.png') }}" alt="Logo Institucional" style="max-height: 85px; width: auto; max-width: 100%; border-radius: 6px;">
        </div>
        <h2>Estado de tu Solicitud</h2>
        <p class="subtitle" style="margin-bottom:24px">
            Tu solicitud fue registrada exitosamente.
        </p>

        {{-- Éxito de reenvío --}}
        @if(session('success'))
            <div class="alert success" style="margin-bottom:18px"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg> {{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="alert info" style="margin-bottom:18px">ℹ {{ session('info') }}</div>
        @endif

        {{-- Número de trámite destacado --}}
        <div style="
            text-align:center;
            background: linear-gradient(135deg, rgba(99,102,241,.12), rgba(6,182,212,.08));
            border: 1px solid rgba(99,102,241,.25);
            border-radius: var(--radius);
            padding: 24px 20px;
            margin-bottom: 22px;
        ">
            <div class="muted small" style="margin-bottom:8px;text-transform:uppercase;letter-spacing:.8px;font-size:11px">
                Número de trámite
            </div>
            <div class="procedure-code" style="font-size:22px;padding:12px 24px;display:inline-flex;letter-spacing:2px">
                {{ $studentRequest->procedure_number }}
            </div>
            <div class="muted small" style="margin-top:10px;font-size:11.5px">
                Guarda este número para consultar el estado de tu solicitud
            </div>
        </div>

        {{-- Estado actual --}}
        <div style="text-align:center;margin-bottom:20px">
            <span class="badge {{ $studentRequest->status }}" style="font-size:13.5px;padding:9px 18px">
                {{ ['pending'=>'⏳ Pendiente de revisión','resubmitted'=>'RE-ENVIADO','approved'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg> Aprobada','rejected'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Rechazada','observed'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> Observada'][$studentRequest->status] ?? $studentRequest->status }}
            </span>
        </div>

        {{-- Datos de la solicitud --}}
        <div style="background:var(--surface-2);border-radius:var(--radius-sm);padding:16px;margin-bottom:20px">
            <div style="font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:var(--text-dim);margin-bottom:12px">
                Datos registrados
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13.5px">
                <div>
                    <div class="muted small">Nombres</div>
                    <div style="font-weight:600">{{ $studentRequest->names }}</div>
                </div>
                <div>
                    <div class="muted small">Apellidos</div>
                    <div style="font-weight:600">{{ $studentRequest->lastnames }}</div>
                </div>
                <div>
                    <div class="muted small">C.I.</div>
                    <div>{{ $studentRequest->ci_number }}</div>
                </div>
                <div>
                    <div class="muted small">Carrera</div>
                    <div>{{ $studentRequest->career->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="muted small">Semestre</div>
                    <div>{{ $studentRequest->semester->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="muted small">Gestión</div>
                    <div>{{ $studentRequest->gestion }}</div>
                </div>
                <div>
                    <div class="muted small">Turno</div>
                    <div>{{ $studentRequest->turno }}</div>
                </div>
                <div>
                    <div class="muted small">Grupo</div>
                    <div>{{ $studentRequest->grupo }}</div>
                </div>
                <div>
                    <div class="muted small">Fecha de envío</div>
                    <div>{{ $studentRequest->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- Observación si hay --}}
        @if($studentRequest->observation)
            <div class="alert warning" style="margin-bottom:16px">
                <strong>Observación:</strong> {{ $studentRequest->observation }}
            </div>
        @endif

        {{-- Mensaje si aprobada --}}
        @if($studentRequest->status === 'approved')
            <div class="alert success" style="margin-bottom:16px">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg> Tu solicitud fue aprobada. Puedes descargar tu constancia a continuación.
            </div>
        @endif

        {{-- Botón PDF — SIEMPRE visible --}}
        <a id="btn-download-constancy"
           href="{{ route('public.student-request.constancy', $studentRequest->procedure_number) }}"
           class="btn primary w-100"
           style="padding:14px;font-size:15px;margin-bottom:10px;display:flex;align-items:center;justify-content:center;gap:8px">
            <span>⬇</span> Descargar constancia en PDF
        </a>

        {{-- Botón de corrección (solo si no está aprobada) --}}
        @if($studentRequest->status !== 'approved')
            <a id="btn-edit-request"
               href="{{ route('public.student-request.edit', $studentRequest->procedure_number) }}"
               class="btn warning w-100"
               style="padding:12px;margin-bottom:10px;display:flex;align-items:center;justify-content:center;gap:6px">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Corregir / reenviar solicitud
            </a>
        @endif

        {{-- Consultar estado --}}
        <a href="{{ route('public.student-request.track') }}"
           id="btn-go-track"
           class="btn secondary w-100"
           style="padding:12px;margin-bottom:10px">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg> Consultar estado de mi trámite
        </a>

        <a href="{{ route('public.student-request.create') }}" class="btn light w-100" style="padding:11px;font-size:13px">
            Nueva solicitud
        </a>
    </div>
</div>
@endsection
