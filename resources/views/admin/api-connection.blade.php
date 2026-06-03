@extends('layouts.admin')
@section('title', 'Conexión API REST')
@section('heading', 'Conexión con Sistema de Trámites')

@section('content')
<div style="max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <p style="color: #6b7280; margin-bottom: 20px;">
        Configura aquí las credenciales para conectarte con la API del sistema principal de inscripciones (Modular Trámites).
        Esto permitirá buscar y validar automáticamente los datos de un estudiante utilizando su Carnet de Identidad.
    </p>

    <form action="{{ route('admin.api-connection.store') }}" method="POST">
        @csrf
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">URL Base de la API</label>
            <input type="url" name="tramites_api_url" value="{{ old('tramites_api_url', $apiUrl) }}" placeholder="Ej. http://127.0.0.1:8000/api/v1" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px;">
            <small style="color: #9ca3af; display: block; margin-top: 5px;">Asegúrate de incluir <code>/api/v1</code> al final de la URL del sistema de trámites.</small>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px;">API Key (Clave Secreta)</label>
            <input type="text" name="tramites_api_key" value="{{ old('tramites_api_key', $apiKey) }}" placeholder="Clave secreta generada en Trámites" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; font-family: monospace;">
            <small style="color: #9ca3af; display: block; margin-top: 5px;">Copia y pega la clave exactamente como aparece en el panel del sistema de Trámites.</small>
        </div>

        <div style="display: flex; gap: 15px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
            <button type="submit" name="action" value="save" class="btn btn-primary" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Guardar Configuración
            </button>
            <button type="submit" name="action" value="test" class="btn btn-secondary" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Guardar y Probar Conexión
            </button>
        </div>
    </form>
</div>
@endsection
