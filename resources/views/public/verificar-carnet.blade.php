@extends('layouts.public')
@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

body {
    font-family: 'Inter', system-ui, sans-serif;
    background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
    min-height: 100vh;
    margin: 0;
    color: #334e68;
}

.public-wrap {
    display: flex;
    justify-content: center;
    padding: 40px 20px;
    min-height: 100vh;
}

.public-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0,0,0,0.05);
    padding: 40px;
    max-width: 650px;
    width: 100%;
}

h2 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 8px;
    color: #102a43;
    text-align: center;
}

.subtitle {
    color: #627d98;
    text-align: center;
    margin-bottom: 32px;
    font-size: 15px;
}

.field {
    margin-bottom: 24px;
}

.field label {
    display: block;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
    color: #627d98;
    margin-bottom: 8px;
    text-align: left;
}

input[type="text"] {
    width: 100%;
    padding: 12px 16px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    color: #334e68;
    font-size: 15px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

input[type="text"]:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn.primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
}

.btn.primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
}

.result-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 24px;
    border: 1px solid #e2e8f0;
    text-align: left;
    animation: slideUp 0.4s ease forwards;
    margin-top: 16px;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 13px;
}

.status-valid {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.status-invalid {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.detail-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 0;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
}

.detail-table tr {
    border-bottom: 1px solid #e2e8f0;
    display: flex;
}

.detail-table tr:last-child {
    border-bottom: none;
}

.detail-table th {
    background-color: #f8fafc;
    color: #64748b;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 14px 16px;
    width: 35%;
    text-align: left;
    border-right: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
}

.detail-table td {
    padding: 14px 16px;
    color: #0f172a;
    font-weight: 500;
    font-size: 15px;
    width: 65%;
    display: flex;
    align-items: center;
}

.empty-state {
    padding: 40px 0;
    color: #94a3b8;
    text-align: center;
}

.empty-state svg {
    width: 48px;
    height: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.brand-footer {
    margin-top: 40px;
    text-align: center;
}

.brand-footer img {
    max-height: 40px;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.brand-footer img:hover {
    opacity: 1;
}

@media (max-width: 640px) {
    .field-flex {
        flex-direction: column;
    }
    .btn {
        width: 100%;
    }
    .public-card {
        padding: 24px 20px;
    }
    .detail-table tr {
        flex-direction: column;
    }
    .detail-table th, .detail-table td {
        width: 100%;
        border-right: none;
    }
    .detail-table th {
        padding-bottom: 4px;
        border-bottom: none;
    }
    .detail-table td {
        padding-top: 4px;
        padding-bottom: 16px;
    }
}
</style>

<div class="public-wrap">
    <div class="public-card">
        
        <div style="text-align: center; margin-bottom: 24px;">
            <i data-lucide="shield-check" style="width: 48px; height: 48px; color: #3b82f6; margin-bottom: 8px;"></i>
            <h2>Verificación de Carnet</h2>
            <p class="subtitle">Ingresa el código único del estudiante para validar su identidad</p>
        </div>

        <form method="GET">
            <div class="field">
                <label>Código de Estudiante</label>
                <div class="field-flex" style="display: flex; gap: 12px; align-items: stretch;">
                    <div style="flex: 1;">
                        <input type="text" name="code" placeholder="Ej. EST-12345" value="{{ request('code') }}" required autocomplete="off">
                    </div>
                    <button type="submit" class="btn primary">
                        <i data-lucide="search" style="width:18px;height:18px;"></i> Verificar
                    </button>
                </div>
            </div>
        </form>

        @if (request()->has('code'))
            @if ($carnet)
                <div class="result-card">
                    <div style="display: flex; gap: 24px; flex-wrap: wrap-reverse; align-items: center;">
                        <div style="flex: 1; min-width: 250px;">
                            <table class="detail-table">
                                <tbody>
                            <tr>
                                <th>Código Estudiante</th>
                                <td><span style="background:#dcfce7; color:#166534; font-weight:600; font-family:monospace; padding:4px 10px; border-radius:6px;">{{ $carnet->codigo_estudiante }}</span></td>
                            </tr>
                            <tr>
                                <th>Nombres</th>
                                <td>{{ $carnet->nombres }}</td>
                            </tr>
                            <tr>
                                <th>Apellidos</th>
                                <td>{{ $carnet->apellidos }}</td>
                            </tr>
                            <tr>
                                <th>Cédula de Identidad</th>
                                <td>{{ $carnet->cedula_identidad }}</td>
                            </tr>
                            <tr>
                                <th>Carrera</th>
                                <td>{{ $carnet->carrera }}</td>
                            </tr>
                            <tr>
                                <th>Semestre</th>
                                <td>{{ $carnet->semestre }}</td>
                            </tr>
                            <tr>
                                <th>Validez</th>
                                <td>Del {{ \Carbon\Carbon::parse($carnet->fecha_emision)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($carnet->fecha_caducidad)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Estado</th>
                                <td>
                                    <span class="status-badge {{ strtolower($carnet->estado) == 'vigente' ? 'status-valid' : 'status-invalid' }}">
                                        <i data-lucide="{{ strtolower($carnet->estado) == 'vigente' ? 'check-circle' : 'alert-circle' }}" style="width:16px;height:16px;margin-right:6px;"></i>
                                        {{ strtoupper($carnet->estado) }}
                                    </span>
                                </td>
                            </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="photo-container" style="flex: 0 0 150px; width: 150px; margin: 0 auto;">
                            @if($foto)
                                <img src="{{ Storage::url($foto) }}" alt="Foto Estudiante" style="width: 100%; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); aspect-ratio: 3/4; object-fit: cover;">
                            @else
                                <div style="width: 100%; aspect-ratio: 3/4; background: #e2e8f0; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8;">
                                    <i data-lucide="user" style="width:48px;height:48px;margin-bottom:8px;"></i>
                                    <span style="font-size: 12px; font-weight: 600;">Sin Foto</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="result-card" style="text-align: center; border-color: #fecaca; background: #fff5f5;">
                    <div class="empty-state" style="color:#991b1b; padding: 24px 0;">
                        <i data-lucide="user-x" style="width:48px;height:48px;opacity:0.8;margin-bottom:16px;color:#ef4444"></i>
                        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color:#7f1d1d">Carnet no encontrado</h3>
                        <p style="font-size: 14px; margin:0">El código ingresado no coincide con ningún estudiante registrado en nuestro sistema.</p>
                    </div>
                </div>
            @endif
        @else
            <div class="empty-state">
                <i data-lucide="fingerprint" style="width:48px;height:48px;margin-bottom:16px;"></i>
                <p style="font-size: 14px; margin:0">El resultado de la verificación aparecerá aquí</p>
            </div>
        @endif

        <div class="brand-footer">
            <img src="{{ asset('logo_itecnoba.png') }}" alt="ITECNOBA Logo">
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();
</script>
@endsection