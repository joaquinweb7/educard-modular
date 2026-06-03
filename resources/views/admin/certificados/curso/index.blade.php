@extends('layouts.admin')
@section('heading', 'Cursos de Certificados')
@section('content')

<div class="panel">
    <form method="GET" class="form-grid" style="grid-template-columns:1fr auto">
        <div class="field">
            <label>Buscar cursos</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre del curso..." class="input">
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
        <h2 style="margin:0">Lista de Cursos</h2>
        <a href="{{ route('admin.certificados.curso.create') }}" class="btn btn-primary">
            <i data-lucide="plus"></i> Nuevo Curso
        </a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Curso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cursos as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->nombre }}</td>
                    <td>
                        <div style="display:flex; gap:0.5rem;">
                            <a href="{{ route('admin.certificados.curso.edit', $c) }}" class="btn btn-secondary" style="padding:0.4rem;" title="Editar">
                                <i data-lucide="edit" style="width:16px;height:16px"></i>
                            </a>
                            <form action="{{ route('admin.certificados.curso.destroy', $c) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este curso?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding:0.4rem;" title="Eliminar">
                                    <i data-lucide="trash" style="width:16px;height:16px"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center; padding:2rem;">No se encontraron cursos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $cursos->links('vendor.pagination.tailwind') }}
    </div>
</div>

@endsection
