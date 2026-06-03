@extends('layouts.admin')
@section('heading', 'Configuraciones del Sistema')

@section('content')
<div class="panel" style="max-width: 800px;">
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <div style="margin-bottom: 24px;">
            <h3 style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--primary);">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                    <line x1="9" y1="21" x2="9" y2="9"></line>
                </svg>
                Formulario Público de Solicitudes
            </h3>
            
            <div style="background: var(--surface-2); border: 1px solid var(--border); border-radius: 12px; padding: 20px;">
                <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer;">
                    <input type="checkbox" name="public_form_enabled" value="1" 
                           {{ \App\Models\Setting::get('public_form_enabled', '1') == '1' ? 'checked' : '' }}
                           style="width: 20px; height: 20px; accent-color: var(--primary); margin-top: 2px;">
                    <div>
                        <div style="font-weight: 600; font-size: 15px; color: var(--text);">Habilitar recepción de solicitudes de carnet</div>
                        <div style="font-size: 13px; color: var(--text-dim); margin-top: 4px;">
                            Si se desmarca, los estudiantes verán un mensaje indicando que las solicitudes están cerradas y no podrán enviar nuevos formularios.
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <div>
            <button type="submit" class="btn primary" style="padding: 10px 24px; font-weight: 600;">
                Guardar Configuraciones
            </button>
        </div>
    </form>
</div>
@endsection
