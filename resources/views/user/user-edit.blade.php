{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold">Editar Usuario</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
{{-- Nota: Asegúrate de que $user->id se pase correctamente --}}
<form method="POST" action="{{ route('users.update', $user->id) }}">
    @csrf 
    @method('PUT')
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        {{-- Mostrar Errores de Validación --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            
            {{-- Nombre --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
            </div>

            {{-- Teléfono --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Teléfono</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
            </div>

            {{-- Ubicación --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Ubicación</label>
                <input type="text" name="location" value="{{ old('location', $user->location) }}" class="form-control">
            </div>

            {{-- Rol --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Rol <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select" required>
                    @foreach ($roles as $roleId => $roleName)
                        <option value="{{ $roleId }}" 
                            @selected($user->roles->contains($roleId) || $user->getRoleNames()->first() == $roleName)>
                            {{ ucfirst($roleName) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Estado --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="1" @selected(old('status', $user->status) == 1)>Activo</option>
                    <option value="0" @selected(old('status', $user->status) == 0)>Inactivo</option>
                </select>
            </div>

        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        {{-- Usé btn-primary (Azul) para guardar, si prefieres el amarillo pon btn-warning --}}
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </div>
</form>