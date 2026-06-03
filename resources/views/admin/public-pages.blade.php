@extends('layouts.admin')

@section('title', 'Páginas Públicas')
@section('heading', 'Páginas de Acceso Público')

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
    
    <div style="grid-column: 1 / -1; margin-bottom: 10px;">
        <p style="color: #6b7280; font-size: 14px;">
            A continuación encontrarás los enlaces a las páginas públicas del sistema Educard. Puedes compartir estos enlaces con los estudiantes para que puedan iniciar sus solicitudes o hacer seguimiento.
        </p>
    </div>

    @foreach($pages as $page)
    <div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; display: flex; flex-direction: column;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
            <div style="background: #eff6ff; color: #3b82f6; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="{{ $page['icon'] }}" style="width: 20px; height: 20px;"></i>
            </div>
            <h3 style="margin: 0; font-size: 16px; color: #1f2937;">{{ $page['title'] }}</h3>
        </div>
        
        <p style="color: #6b7280; font-size: 14px; margin: 0 0 20px 0; flex-grow: 1;">
            {{ $page['description'] }}
        </p>

        <div style="background: #f3f4f6; padding: 10px; border-radius: 6px; display: flex; align-items: center; gap: 10px;">
            <input type="text" readonly value="{{ $page['url'] }}" style="width: 100%; background: transparent; border: none; font-family: monospace; font-size: 12px; color: #4b5563; outline: none;">
            <a href="{{ $page['url'] }}" target="_blank" title="Abrir en nueva pestaña" style="color: #6b7280; hover: color: #374151;">
                <i data-lucide="external-link" style="width: 16px; height: 16px;"></i>
            </a>
        </div>
    </div>
    @endforeach

</div>
@endsection
