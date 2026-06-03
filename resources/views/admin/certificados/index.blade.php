@extends('layouts.admin')
@section('heading', 'Lista de Certificados')
@section('content')

<div class="panel">
    <form method="GET" class="form-grid" style="grid-template-columns:1fr auto">
        <div class="field">
            <label>Buscar certificados</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, carnet, email o código..." class="input">
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
        <h2 style="margin:0">Certificados Emitidos</h2>
        <div style="display:flex; gap:0.5rem;">
            <a href="{{ route('admin.certificados.create') }}" class="btn btn-primary">
                <i data-lucide="plus"></i> Emitir Certificado
            </a>
            <a href="{{ route('admin.certificados.subir') }}" class="btn btn-secondary">
                <i data-lucide="upload"></i> Carga Masiva
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Curso</th>
                    <th>Carnet</th>
                    <th>Código</th>
                    <th>Plantilla</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificados as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->nombre_estudiante }}<br><small style="color:var(--text-muted)">{{ $c->email }}</small></td>
                    <td>{{ $c->curso->nombre ?? 'N/A' }}</td>
                    <td>{{ $c->carnet }}</td>
                    <td><span class="badge bg-primary" style="font-family:monospace">{{ $c->codigo }}</span></td>
                    <td>
                        @if($c->plantilla)
                        <a href="{{ route('admin.certificados.test', $c->plantilla->id) }}" target="_blank">
                            <img src="{{ Storage::url($c->plantilla->imagen) }}" alt="plantilla" style="height:40px; border-radius:4px; object-fit:cover;">
                        </a>
                        @else
                        N/A
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap:0.5rem;">
                            <form action="{{ route('admin.certificados.smtp.sendEmail', $c) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-secondary" style="padding:0.4rem;" title="Enviar por email" onclick="return confirm('¿Enviar certificado por correo a {{ $c->email }}?')">
                                    <i data-lucide="send" style="width:16px;height:16px"></i>
                                </button>
                            </form>
                            <a href="{{ route('admin.certificados.edit', $c) }}" class="btn btn-secondary" style="padding:0.4rem;" title="Editar">
                                <i data-lucide="edit" style="width:16px;height:16px"></i>
                            </a>
                            <form action="{{ route('admin.certificados.destroy', $c) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este certificado?')">
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
                    <td colspan="7" style="text-align:center; padding:2rem;">No se encontraron certificados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $certificados->links('vendor.pagination.tailwind') }}
    </div>
</div>

@endsection
