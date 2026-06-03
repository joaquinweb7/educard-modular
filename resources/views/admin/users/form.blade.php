@csrf
<div class="form-grid">
    <div class="field">
        <label for="name">Nombre</label>
        <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required>
        @error('name') <span style="color:red; font-size: 12px;">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="email">Correo Electrónico</label>
        <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required>
        @error('email') <span style="color:red; font-size: 12px;">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="password">Contraseña {{ isset($user) ? '(dejar en blanco para no cambiar)' : '' }}</label>
        <input type="password" name="password" id="password" {{ isset($user) ? '' : 'required' }}>
        @error('password') <span style="color:red; font-size: 12px;">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label>Rol del Usuario</label>
        <select name="role" id="role" onchange="togglePermissions()">
            <option value="custom" {{ old('role', $user->role ?? 'custom') === 'custom' ? 'selected' : '' }}>Personalizado (Elegir permisos)</option>
            <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Administrador (Acceso Total)</option>
        </select>
        @error('role') <span style="color:red; font-size: 12px;">{{ $message }}</span> @enderror
    </div>
</div>

<div id="permissions_section" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
    <label style="display:block; font-weight:bold; margin-bottom: 10px;">Accesos a Secciones (Permisos)</label>
    <div style="display: flex; flex-wrap: wrap; gap: 15px;">
        @foreach($sections as $section)
            @if(isset($section['route']) && $section['route'] !== 'admin.dashboard')
                <label style="display: flex; align-items: center; gap: 5px; cursor: pointer;">
                    <input type="checkbox" name="permissions[]" value="{{ $section['route'] }}"
                        @if(is_array(old('permissions', $user->permissions ?? [])) && in_array($section['route'], old('permissions', $user->permissions ?? []))) checked @endif>
                    <i class="icon-{{ $section['icon'] ?? 'circle' }}" style="color: #666;"></i> {{ $section['title'] }}
                </label>
            @endif
        @endforeach
    </div>
    <p style="font-size: 12px; color: #777; margin-top: 10px;">* El "Inicio" siempre está disponible para todos los usuarios.</p>
</div>

<div style="margin-top: 20px; display: flex; gap: 10px;">
    <button type="submit" class="btn primary">Guardar Usuario</button>
    <a href="{{ route('admin.users.index') }}" class="btn">Cancelar</a>
</div>

<script>
function togglePermissions() {
    const roleSelect = document.getElementById('role');
    const permSection = document.getElementById('permissions_section');
    if (roleSelect.value === 'admin' || roleSelect.value === 'super_admin') {
        permSection.style.display = 'none';
    } else {
        permSection.style.display = 'block';
    }
}
// Run on load
document.addEventListener('DOMContentLoaded', togglePermissions);
</script>
