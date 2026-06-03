@extends('layouts.admin')

@section('heading', 'Fuentes Tipográficas')

@section('content')
<div class="cards" style="grid-template-columns: 1fr; margin-bottom: 24px;">
    <div class="panel">
        <h2 class="panel-title">Instalar nueva fuente</h2>
        <form action="{{ route('admin.fonts.store') }}" method="POST" enctype="multipart/form-data" class="form-grid" style="align-items: flex-end;">
            @csrf
            <div class="field">
                <label>Nombre de la fuente (Ej: Roboto, Montserrat)</label>
                <input type="text" name="name" required placeholder="Nombre para identificarla en el diseñador">
            </div>
            <div class="field">
                <label>Archivo (.ttf, .woff)</label>
                <input type="file" name="file" required accept=".ttf,.woff,.woff2">
            </div>
            <div class="field">
                <button type="submit" class="btn primary">Instalar Fuente</button>
            </div>
        </form>
    </div>
</div>

<div class="panel">
    <h2 class="panel-title">Fuentes Instaladas</h2>
    
    @if($fonts->isEmpty())
        <div class="empty-state">
            <div class="icon">Aa</div>
            <p>No hay fuentes personalizadas instaladas.<br>Usa el formulario superior para añadir una.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Archivo</th>
                        <th>Fecha de instalación</th>
                        <th style="width:100px;text-align:center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fonts as $font)
                        <tr>
                            <td style="font-weight: 600;">{{ $font->name }}</td>
                            <td><code style="background:var(--surface-2);padding:2px 6px;border-radius:4px">{{ $font->file_path }}</code></td>
                            <td>{{ $font->created_at->format('d/m/Y H:i') }}</td>
                            <td style="text-align:center">
                                <form action="{{ route('admin.fonts.destroy', $font) }}" method="POST" onsubmit="return confirm('¿Eliminar esta fuente? Los diseños que la usen podrían verse afectados.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn sm danger" title="Eliminar">🗑</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
