@extends('layouts.admin')
@section('heading', 'Estructura Académica')
@section('content')

<div class="panel">
    <h2 class="panel-title">Módulos disponibles</h2>
    <p class="muted">Selecciona qué elemento de la estructura académica deseas gestionar.</p>
    
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-top:20px">
        
        <a href="{{ route('admin.plugins.academicstructure.gestions.index') }}" style="display:block;background:var(--surface-2);padding:24px;border-radius:12px;text-decoration:none;border:1px solid var(--border);transition:all .2s" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
            <h3 style="margin:0 0 10px;color:var(--text);font-size:18px">Gestiones</h3>
            <p style="margin:0;color:var(--text-muted);font-size:14px;line-height:1.5">Gestiona los periodos o gestiones académicas (ej. 2026, 1/2026).</p>
        </a>

        <a href="{{ route('admin.plugins.academicstructure.semesters.index') }}" style="display:block;background:var(--surface-2);padding:24px;border-radius:12px;text-decoration:none;border:1px solid var(--border);transition:all .2s" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
            <h3 style="margin:0 0 10px;color:var(--text);font-size:18px">Semestres</h3>
            <p style="margin:0;color:var(--text-muted);font-size:14px;line-height:1.5">Administra los semestres u niveles de estudio.</p>
        </a>

        <a href="{{ route('admin.plugins.academicstructure.groups.index') }}" style="display:block;background:var(--surface-2);padding:24px;border-radius:12px;text-decoration:none;border:1px solid var(--border);transition:all .2s" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
            <h3 style="margin:0 0 10px;color:var(--text);font-size:18px">Grupos</h3>
            <p style="margin:0;color:var(--text-muted);font-size:14px;line-height:1.5">Configura los grupos disponibles para las clases (ej. A, B, C).</p>
        </a>

    </div>
</div>

@endsection
