@extends('layouts.admin')
@section('heading', 'Configuración SMTP de Certificados')
@section('content')

<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Servidor de Correo</h3>
    </div>
    <div class="panel-body mt-4">
        <form action="{{ route('admin.certificados.smtp.update') }}" method="POST">
            @csrf
            <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="field">
                    <label for="host">Host <span style="color:var(--danger)">*</span></label>
                    <input type="text" class="input" id="host" name="host" value="{{ old('host', $smtp->host ?? '') }}" required>
                </div>
                <div class="field">
                    <label for="port">Puerto <span style="color:var(--danger)">*</span></label>
                    <input type="text" class="input" id="port" name="port" value="{{ old('port', $smtp->port ?? '') }}" required>
                </div>
                <div class="field">
                    <label for="username">Nombre de Usuario <span style="color:var(--danger)">*</span></label>
                    <input type="text" class="input" id="username" name="username" value="{{ old('username', $smtp->username ?? '') }}" required>
                </div>
                <div class="field">
                    <label for="password">Contraseña <span style="color:var(--danger)">*</span></label>
                    <input type="password" class="input" id="password" name="password" value="{{ old('password', $smtp->password ?? '') }}" required>
                </div>
                <div class="field">
                    <label for="encryption">Encriptación</label>
                    <input type="text" class="input" id="encryption" name="encryption" value="{{ old('encryption', $smtp->encryption ?? '') }}" placeholder="tls, ssl...">
                </div>
                <div class="field">
                    <label for="from_address">Dirección de Origen <span style="color:var(--danger)">*</span></label>
                    <input type="email" class="input" id="from_address" name="from_address" value="{{ old('from_address', $smtp->from_address ?? '') }}" required>
                </div>
                <div class="field">
                    <label for="from_name">Nombre de Origen <span style="color:var(--danger)">*</span></label>
                    <input type="text" class="input" id="from_name" name="from_name" value="{{ old('from_name', $smtp->from_name ?? '') }}" required>
                </div>
            </div>

            <div class="mt-4" style="text-align: right;">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i> Guardar Configuración SMTP
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
