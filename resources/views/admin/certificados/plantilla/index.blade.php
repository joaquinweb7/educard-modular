@extends('layouts.admin')
@section('heading', 'Plantillas de Certificados')
@section('content')

<div class="panel">
    <form method="GET" class="form-grid" style="grid-template-columns:1fr auto">
        <div class="field">
            <label>Buscar plantillas</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre de la plantilla..." class="input">
        </div>
        <div class="field" style="align-self:end">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="search"></i> Buscar
            </button>
        </div>
    </form>
</div>

<div class="panel" style="margin-top: 18px;">
    <div style="display:flex; justify-content:space-between; margin-bottom:1rem; align-items:center;">
        <h2 style="margin:0">Lista de Plantillas</h2>
        <a href="{{ route('admin.certificados.plantilla.create') }}" class="btn btn-primary">
            <i data-lucide="plus"></i> Nueva Plantilla
        </a>
    </div>
    
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:1rem; margin-top:1.5rem;">
        @forelse($plantillas as $p)
        <div class="panel" style="padding:1rem; display:flex; flex-direction:column; gap:1rem;">
            <div style="width:100%; height:150px; background:#f1f5f9; border-radius:4px; overflow:hidden;">
                <img src="{{ Storage::url($p->imagen) }}" alt="plantilla" style="width:100%; height:100%; object-fit:cover;">
            </div>
            <div>
                <h3 style="margin:0; font-size:1.1rem;">{{ $p->nombre }}</h3>
            </div>
            <div style="display:flex; gap:0.5rem; justify-content:space-between;">
                <a href="{{ route('admin.certificados.plantilla.designer', $p) }}" class="btn btn-primary" style="flex:1; text-align:center;">
                    <i data-lucide="pen-tool"></i> Diseñar
                </a>
                <a href="{{ route('admin.certificados.plantilla.edit', $p) }}" class="btn btn-secondary" title="Editar info">
                    <i data-lucide="edit"></i>
                </a>
                <form action="{{ route('admin.certificados.plantilla.destroy', $p) }}" method="POST" onsubmit="return confirm('¿Eliminar plantilla?')" style="margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" title="Eliminar">
                        <i data-lucide="trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align:center; padding:3rem;">
            No se encontraron plantillas.
        </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $plantillas->links('vendor.pagination.tailwind') }}
    </div>
</div>

@endsection
