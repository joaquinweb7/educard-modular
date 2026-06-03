@extends('layouts.admin')
@section('heading', 'Gestión de Plugins')
@section('content')

<div class="panel" style="margin-bottom:16px">
    <div class="panel-title">Instalar nuevo plugin</div>
    @if(config('app.allow_plugin_upload', false))
        <form method="POST" action="{{ route('admin.plugins.store') }}" enctype="multipart/form-data"
              style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
            @csrf
            <div class="field" style="flex:1;min-width:220px">
                <label for="plugin-zip">Archivo ZIP del plugin</label>
                <input id="plugin-zip" type="file" name="plugin_zip" accept=".zip">
            </div>
            <button id="btn-install-plugin" type="submit" class="btn primary">⬆ Instalar plugin</button>
        </form>
    @else
        <div class="alert warning" style="margin: 0;">
            <p style="margin: 0;"><strong>Subida deshabilitada:</strong> Por razones de seguridad en el entorno de producción, la subida de plugins mediante interfaz web está deshabilitada.</p>
            <p style="margin: 5px 0 0; font-size: 12px;">Para subir un plugin, debes activar <code>ALLOW_PLUGIN_UPLOAD=true</code> en el archivo <code>.env</code>.</p>
        </div>
    @endif
</div>

<div class="panel">
    <div class="section-header">
        <h2 class="panel-title mb-0">Plugins instalados</h2>
        <span class="muted small">{{ $plugins->count() }} plugin(s)</span>
    </div>

    @if($plugins->isEmpty())
        <div class="empty-state">
            <div class="icon">🔌</div>
            <p>No hay plugins instalados. Sube un archivo ZIP para instalar uno.</p>
        </div>
    @else
        <div class="plugin-grid">
            @foreach($plugins as $plugin)
                <div class="plugin-card">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
                        <div class="plugin-name">{{ $plugin->display_name }}</div>
                        <span class="badge {{ $plugin->status }}">
                            {{ ['active'=>'Activo','inactive'=>'Inactivo','installed'=>'Instalado'][$plugin->status] ?? $plugin->status }}
                        </span>
                    </div>
                    <div class="plugin-desc">{{ $plugin->description ?? 'Sin descripción.' }}</div>
                    <div class="plugin-meta">
                        v{{ $plugin->version }} · {{ $plugin->author ?? 'Desconocido' }}
                    </div>
                    <div class="actions">
                        @if($plugin->isActive())
                            <form method="POST" action="{{ route('admin.plugins.deactivate', $plugin) }}">
                                @csrf
                                <button id="btn-deactivate-{{ $plugin->id }}" type="submit" class="btn warning sm">⏸ Desactivar</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.plugins.activate', $plugin) }}">
                                @csrf
                                <button id="btn-activate-{{ $plugin->id }}" type="submit" class="btn success sm">▶ Activar</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.plugins.destroy', $plugin) }}"
                              onsubmit="return confirm('¿Eliminar plugin {{ $plugin->display_name }} del registro?')">
                            @csrf @method('DELETE')
                            <button id="btn-delete-plugin-{{ $plugin->id }}" type="submit" class="btn danger sm"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Eliminar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
