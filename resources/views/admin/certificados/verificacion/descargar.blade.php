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
    margin-bottom: 24px;
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
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
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

.btn.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

.btn.success:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(16, 185, 129, 0.3);
}

.alert-message {
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    font-weight: 500;
    font-size: 0.95rem;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
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

.student-code {
    display: inline-block;
    background: #f1f5f9;
    padding: 0.3rem 0.8rem;
    border-radius: 6px;
    font-family: monospace;
    color: #475569;
    font-weight: 600;
    margin-top: 0.5rem;
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
}
</style>

<div class="public-wrap">
    <div class="public-card">
        
        <div style="text-align: center; margin-bottom: 24px;">
            <i data-lucide="file-badge" style="width: 48px; height: 48px; color: #10b981; margin-bottom: 8px;"></i>
            <h2>Descarga de Certificado</h2>
            <p class="subtitle">Para verificar tu identidad y proceder con la descarga, por favor ingresa tu número de carnet o cédula de identidad.</p>
            <div class="student-code">CÓDIGO: {{ request()->route('codigo') ?? $certificado->codigo ?? '' }}</div>
        </div>

        @if (session('success'))
        <div class="alert-message alert-success">
            <i data-lucide="check-circle" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:4px;"></i>
            {{ session('success') }}
        </div>
        @elseif(session('error'))
        <div class="alert-message alert-error">
            <i data-lucide="x-circle" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:4px;"></i>
            {{ session('error') }}
        </div>
        @endif

        <form method="POST">
            @csrf
            <div class="field">
                <label>Número de Carnet</label>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <input type="text" name="carnet" placeholder="Ej. 1234567" required autocomplete="off">
                    <button type="submit" class="btn success" style="width: 100%; padding: 14px;">
                        <i data-lucide="download-cloud" style="width:18px;height:18px;"></i> Validar y Descargar
                    </button>
                </div>
            </div>
        </form>

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
