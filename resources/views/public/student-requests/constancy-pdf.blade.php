<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Constancia de Solicitud — {{ $studentRequest->procedure_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1f2937; background: #fff; padding: 36px 40px; font-size: 13px; }

        /* Encabezado */
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 16px; border-bottom: 3px solid #6366f1; margin-bottom: 24px; }
        .header-left .logo { font-size: 22px; font-weight: 900; color: #6366f1; margin-bottom: 3px; }
        .header-left .tagline { font-size: 11px; color: #6b7280; }
        .header-right { text-align: right; }
        .header-right .doc-title { font-size: 14px; font-weight: 700; color: #374151; }
        .header-right .doc-date { font-size: 11px; color: #6b7280; margin-top: 3px; }

        /* Número de trámite */
        .procedure-box {
            text-align: center;
            background: #f5f3ff;
            border: 2px solid #8b5cf6;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 22px;
        }
        .procedure-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #7c3aed; font-weight: 700; margin-bottom: 8px; }
        .procedure-num { font-size: 26px; font-weight: 900; color: #6366f1; font-family: Courier New, monospace; letter-spacing: 3px; }
        .procedure-hint { font-size: 10.5px; color: #6b7280; margin-top: 8px; }

        /* Estado */
        .status-row { display: flex; justify-content: center; margin-bottom: 20px; }
        .status-badge { display: inline-block; padding: 7px 18px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .status-pending  { background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }
        .status-approved { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .status-rejected { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
        .status-observed { background: #ede9fe; color: #5b21b6; border: 1px solid #8b5cf6; }

        /* Sección de datos */
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .7px; color: #6b7280; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb; }
        .data-grid { display: table; width: 100%; margin-bottom: 20px; border-collapse: collapse; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .data-row { display: table-row; }
        .data-row:nth-child(even) .data-cell { background: #f9fafb; }
        .data-label { display: table-cell; padding: 9px 14px; font-weight: 600; font-size: 12px; color: #374151; width: 38%; border-bottom: 1px solid #f3f4f6; background: #f9fafb; }
        .data-value { display: table-cell; padding: 9px 14px; font-size: 13px; color: #1f2937; border-bottom: 1px solid #f3f4f6; }

        /* Observación */
        .obs-box { background: #fffbeb; border: 1px solid #f59e0b; border-radius: 7px; padding: 12px 14px; margin-bottom: 20px; }
        .obs-title { font-size: 11px; font-weight: 700; color: #92400e; text-transform: uppercase; margin-bottom: 5px; }
        .obs-text { font-size: 12.5px; color: #78350f; }

        /* Nota legal */
        .note-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 7px; padding: 12px 14px; margin-bottom: 20px; }
        .note-text { font-size: 11.5px; color: #0c4a6e; line-height: 1.6; }

        /* Footer */
        .footer { border-top: 1px solid #e5e7eb; padding-top: 14px; text-align: center; color: #9ca3af; font-size: 10px; line-height: 1.6; }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <div class="header">
        <div class="header-left">
            <div class="logo">⬡ EduCard Modular</div>
            <div class="tagline">Sistema de carnets estudiantiles</div>
        </div>
        <div class="header-right">
            <div class="doc-title">Constancia de Solicitud</div>
            <div class="doc-date">Generado el {{ now()->format('d/m/Y \a \l\a\s H:i') }}</div>
        </div>
    </div>

    {{-- Número de trámite --}}
    <div class="procedure-box">
        <div class="procedure-label">Número de Trámite</div>
        <div class="procedure-num">{{ $studentRequest->procedure_number }}</div>
        <div class="procedure-hint">Conserva este número para consultar el estado de tu solicitud</div>
    </div>

    {{-- Estado --}}
    <div class="status-row">
        <span class="status-badge status-{{ $studentRequest->status }}">
            Estado: {{ ['pending'=>'Pendiente de revisión','approved'=>'Aprobada','rejected'=>'Rechazada','observed'=>'Observada — requiere corrección'][$studentRequest->status] ?? ucfirst($studentRequest->status) }}
        </span>
    </div>

    {{-- Datos personales --}}
    <div class="section-title">Datos del solicitante</div>
    <div class="data-grid">
        <div class="data-row">
            <div class="data-label">Nombres</div>
            <div class="data-value">{{ $studentRequest->names }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Apellidos</div>
            <div class="data-value">{{ $studentRequest->lastnames }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Cédula de Identidad</div>
            <div class="data-value">{{ $studentRequest->ci_number }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Carrera</div>
            <div class="data-value">{{ $studentRequest->career->name ?? '—' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Semestre</div>
            <div class="data-value">{{ $studentRequest->semester->name ?? '—' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Fecha de solicitud</div>
            <div class="data-value">{{ $studentRequest->created_at->format('d/m/Y H:i') }}</div>
        </div>
        @if($studentRequest->reviewed_at)
        <div class="data-row">
            <div class="data-label">Fecha de revisión</div>
            <div class="data-value">{{ $studentRequest->reviewed_at->format('d/m/Y H:i') }}</div>
        </div>
        @endif
    </div>

    {{-- Observación --}}
    @if($studentRequest->observation)
        <div class="obs-box">
            <div class="obs-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> Observación de la administración</div>
            <div class="obs-text">{{ $studentRequest->observation }}</div>
        </div>
    @endif

    {{-- Nota informativa --}}
    <div class="note-box">
        <div class="note-text">
            <strong>Nota:</strong> Este documento certifica que se recibió la solicitud de carnet estudiantil con los datos indicados.
            La aprobación o rechazo de la misma depende del proceso de verificación de la administración.
            Puedes consultar el estado de tu trámite en cualquier momento en:
            <strong>{{ config('app.url') }}/consultar</strong>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        EduCard Modular — Sistema de carnets estudiantiles con arquitectura de plugins<br>
        Documento generado automáticamente el {{ now()->format('d \d\e F \d\e Y') }} — No requiere firma ni sello para su validez digital
    </div>

</body>
</html>
