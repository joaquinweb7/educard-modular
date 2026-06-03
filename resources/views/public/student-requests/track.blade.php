@extends('layouts.public')
@section('content')
<div class="public-wrap">
    <div class="public-card" style="max-width: 600px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('storage/images/logo.png') }}" alt="Logo Institucional" style="max-height: 85px; width: auto; max-width: 100%; border-radius: 6px;">
        </div>
        <h2>Consultar Trámite</h2>
        <p class="subtitle">Ingresa tu número de trámite para verificar el estado de tu solicitud de carnet.</p>

        @if(session('error'))
            <div class="alert error" style="margin-bottom:18px">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> {{ session('error') }}
            </div>
        @endif

        {{-- Formulario de búsqueda --}}
        <form id="track-form" method="GET" action="{{ route('public.student-request.track') }}">
            <div class="field" style="margin-bottom:16px">
                <label for="procedure-number-input">Número de trámite</label>
                <input id="procedure-number-input"
                       type="text"
                       name="tramite"
                       value="{{ old('tramite', request('tramite')) }}"
                       placeholder="Ej: TRM-2026-000001"
                       required
                       autocomplete="off"
                       style="font-family:'Courier New',monospace;font-size:16px;letter-spacing:1px;text-transform:uppercase"
                       oninput="this.value=this.value.toUpperCase()">
                <span class="muted small">El número de trámite fue entregado al momento de enviar tu solicitud.</span>
            </div>
            <button id="btn-search-tramite" type="submit" class="btn primary w-100"
                    style="padding:14px;font-size:15px">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg> Buscar trámite
            </button>
        </form>

        @if(isset($studentRequest))
            {{-- Resultado de la búsqueda --}}
            <hr class="divider">

            <div style="
                background: linear-gradient(135deg, rgba(99,102,241,.1), rgba(6,182,212,.06));
                border: 1px solid rgba(99,102,241,.2);
                border-radius: var(--radius);
                padding: 20px;
                margin-bottom: 18px;
            ">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;flex-wrap:wrap;gap:8px">
                    <div>
                        <div class="muted small" style="text-transform:uppercase;letter-spacing:.6px;font-size:10.5px;margin-bottom:4px">Número de trámite</div>
                        <div class="procedure-code" style="font-size:16px;padding:7px 14px">
                            {{ $studentRequest->procedure_number }}
                        </div>
                    </div>
                    <span class="badge {{ $studentRequest->status }}" style="font-size:13px;padding:8px 14px;align-self:center">
                        {!! ['pending'=>'⏳ Pendiente','approved'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg> Aprobada','rejected'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Rechazada','observed'=>'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> Observada'][$studentRequest->status] ?? $studentRequest->status !!}
                    </span>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13.5px;margin-bottom:14px">
                    <div>
                        <div class="muted small">Estudiante</div>
                        <div style="font-weight:600">{{ $studentRequest->names }} {{ $studentRequest->lastnames }}</div>
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
                        <div class="muted small">Enviada</div>
                        <div>{{ $studentRequest->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($studentRequest->reviewed_at)
                        <div>
                            <div class="muted small">Revisada</div>
                            <div>{{ $studentRequest->reviewed_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif
                </div>

                @if($studentRequest->observation)
                    <div class="alert warning" style="margin-bottom:0">
                        <div><strong>Observación de la administración:</strong></div>
                        <div>{{ $studentRequest->observation }}</div>
                    </div>
                @endif
            </div>

            {{-- Mensaje informativo según estado --}}
            @if($studentRequest->status === 'pending')
                <div class="alert info" style="margin-bottom:14px">
                    ℹ Tu solicitud está siendo revisada. Puedes corregir tus datos si lo necesitas.
                </div>
            @elseif($studentRequest->status === 'observed')
                <div class="alert warning" style="margin-bottom:14px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> Tu solicitud tiene observaciones del administrador. Corrígela y reenvíala.
                </div>
            @elseif($studentRequest->status === 'rejected')
                <div class="alert error" style="margin-bottom:14px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Tu solicitud fue rechazada. Puedes corregir los datos y reenviarla.
                </div>
            @elseif($studentRequest->status === 'approved')
                <div class="alert success" style="margin-bottom:14px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><polyline points="20 6 9 17 4 12"></polyline></svg> Tu solicitud fue aprobada. Ya puedes descargar tu constancia.
                </div>
            @endif

            {{-- Descarga PDF — siempre disponible --}}
            <a id="btn-download-pdf-track"
               href="{{ route('public.student-request.constancy', $studentRequest->procedure_number) }}"
               class="btn primary w-100"
               style="padding:13px;font-size:14.5px;margin-bottom:10px;display:flex;align-items:center;justify-content:center;gap:8px">
                <span>⬇</span> Descargar constancia PDF
            </a>

            {{-- Botón corregir (visible si no está aprobada) --}}
            @if($studentRequest->status !== 'approved')
                <a id="btn-edit-from-track"
                   href="{{ route('public.student-request.edit', $studentRequest->procedure_number) }}"
                   class="btn warning w-100"
                   style="padding:12px;margin-bottom:10px;display:flex;align-items:center;justify-content:center;gap:6px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Corregir / reenviar solicitud
                </a>
            @endif
        @endif

        <hr class="divider">
        <a href="{{ route('public.student-request.create') }}" class="btn secondary w-100">
            ← Nueva solicitud de carnet
        </a>
    </div>
</div>
@endsection
