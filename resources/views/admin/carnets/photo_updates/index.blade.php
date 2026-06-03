@extends('layouts.admin')
@section('heading', 'Actualización de Fotos')
@section('content')

<div class="tabs" style="margin-bottom: 20px; border-bottom: 1px solid var(--border);">
    <a href="{{ route('admin.photo-updates.index', ['status' => 'pending']) }}" class="btn {{ $status == 'pending' ? 'primary' : 'light' }}">Pendientes</a>
    <a href="{{ route('admin.photo-updates.index', ['status' => 'approved']) }}" class="btn {{ $status == 'approved' ? 'primary' : 'light' }}">Aprobadas</a>
    <a href="{{ route('admin.photo-updates.index', ['status' => 'rejected']) }}" class="btn {{ $status == 'rejected' ? 'primary' : 'light' }}">Rechazadas</a>
</div>

<div class="card">
    <div style="overflow-x: auto;">
        <table class="table w-100">
            <thead>
            <tr>
                <th>ID</th>
                <th>Código / CI</th>
                <th>Estudiante</th>
                <th>Foto Nueva</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @forelse($updates as $update)
                <tr>
                    <td>#{{ $update->id }}</td>
                    <td>
                        <strong>{{ $update->codigo_estudiante }}</strong><br>
                        <small class="muted">{{ $update->carnet->cedula_identidad }}</small>
                    </td>
                    <td>
                        {{ $update->carnet->nombres }} {{ $update->carnet->apellidos }}<br>
                        <small class="muted">{{ $update->carnet->carrera }}</small>
                    </td>
                    <td>
                        <a href="{{ Storage::url($update->photo_path) }}" target="_blank">
                            <img src="{{ Storage::url($update->photo_path) }}" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border);">
                        </a>
                    </td>
                    <td>{{ $update->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge {{ $update->status == 'pending' ? 'warning' : ($update->status == 'approved' ? 'success' : 'danger') }}">
                            {{ $update->statusLabel() }}
                        </span>
                        @if($update->status == 'rejected' && $update->observation)
                            <div style="font-size: 11px; margin-top: 4px; color: var(--danger);">
                                Obs: {{ $update->observation }}
                            </div>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($update->status == 'pending')
                            <form method="POST" action="{{ route('admin.photo-updates.approve', $update) }}" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn success small" title="Aprobar" onclick="return confirm('¿Aprobar esta foto y actualizar el carnet?')">
                                    <i data-lucide="check-circle"></i>
                                </button>
                            </form>

                            <button type="button" class="btn danger small" title="Rechazar" onclick="openRejectModal({{ $update->id }})">
                                <i data-lucide="x-circle"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center muted" style="padding: 30px;">
                        No hay solicitudes {{ $status == 'pending' ? 'pendientes' : ($status == 'approved' ? 'aprobadas' : 'rechazadas') }}.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 15px;">
        {{ $updates->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>

{{-- Modal de Rechazo --}}
<div id="reject-modal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: var(--surface-1); padding: 24px; border-radius: 8px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3 style="margin-top: 0; margin-bottom: 16px;">Rechazar Foto</h3>
        <form id="reject-form" method="POST" action="">
            @csrf
            <div class="field">
                <label>Observación (Motivo del rechazo)</label>
                <textarea name="observation" class="input w-100" rows="3" required placeholder="Ej. Foto borrosa, no tiene fondo rojo, etc."></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;">
                <button type="button" class="btn light" onclick="document.getElementById('reject-modal').style.display='none'">Cancelar</button>
                <button type="submit" class="btn danger">Confirmar Rechazo</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(id) {
        const modal = document.getElementById('reject-modal');
        const form = document.getElementById('reject-form');
        form.action = `/admin/carnets/photo-updates/${id}/reject`;
        modal.style.display = 'flex';
    }
</script>

@endsection
