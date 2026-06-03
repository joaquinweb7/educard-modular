@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')
@section('heading', 'Usuarios')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.users.create') }}" class="btn primary">
        <i class="icon-user-plus mr-2"></i> Nuevo Usuario
    </a>
</div>

<div class="panel">

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge success">Administrador</span>
                            @else
                                <span class="badge">Personalizado</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:6px">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn secondary sm" style="padding:4px 8px" title="Editar"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Eliminar usuario?');" style="margin:0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn danger sm" style="padding:4px 8px; background:var(--danger); color:#fff; border:none" title="Eliminar"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #777;">No hay usuarios registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
</div>
@endsection
