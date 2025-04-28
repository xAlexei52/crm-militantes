<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Editar Usuario</h1>
        
        <a href="<?= APP_URL ?>/admin/usuarios" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Listado
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Información del Usuario</h2>
            <?php if ($usuario['id'] == 1): ?>
                <p class="text-sm text-orange-600 mt-1">
                    <i class="fas fa-info-circle mr-1"></i> Este es el usuario administrador principal. Algunos campos pueden tener restricciones.
                </p>
            <?php endif; ?>
        </div>
        
        <div class="p-6">
            <form id="edit-user-form" method="POST" action="<?= APP_URL ?>/admin/usuarios/update">
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                            required
                            value="<?= htmlspecialchars($usuario['nombre']) ?>"
                        >
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                            required
                            value="<?= htmlspecialchars($usuario['email']) ?>"
                        >
                    </div>
                    
                    <!-- Contraseña (opcional al editar) -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                            minlength="8"
                        >
                        <p class="mt-1 text-xs text-gray-500">Dejar en blanco para mantener la contraseña actual</p>
                    </div>
                    
                    <!-- Confirmar Contraseña -->
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                            minlength="8"
                        >
                    </div>
                    
                    <!-- Rol -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
                        <select 
                            id="role" 
                            name="role" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                            required
                            <?= $usuario['id'] == 1 ? 'disabled' : '' ?>
                        >
                            <option value="operador" <?= $usuario['role'] == 'operador' ? 'selected' : '' ?>>Operador</option>
                            <option value="admin" <?= $usuario['role'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                        <?php if ($usuario['id'] == 1): ?>
                            <input type="hidden" name="role" value="admin">
                            <p class="mt-1 text-xs text-orange-600">El rol del administrador principal no puede ser cambiado</p>
                        <?php else: ?>
                            <p class="mt-1 text-xs text-gray-500">Los administradores tienen acceso completo al sistema</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Información del registro -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fecha de Registro (solo lectura) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                        <input 
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500" 
                            value="<?= date('d/m/Y H:i', strtotime($usuario['created_at'])) ?>"
                            readonly
                        >
                    </div>
                    
                    <!-- Fecha de Último Acceso (solo lectura) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Último Acceso</label>
                        <input 
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500" 
                            value="<?= $usuario['last_login'] ? date('d/m/Y H:i', strtotime($usuario['last_login'])) : 'Nunca' ?>"
                            readonly
                        >
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="mt-6 flex justify-end">
                    <a href="<?= APP_URL ?>/admin/usuarios" class="mr-4 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Cancelar
                    </a>
                    <button 
                        type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('edit-user-form');
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirm');
        
        form.addEventListener('submit', function(event) {
            // Verificar que las contraseñas coincidan solo si se está cambiando la contraseña
            if (password.value || passwordConfirm.value) {
                if (password.value !== passwordConfirm.value) {
                    event.preventDefault();
                    alert('Las contraseñas no coinciden');
                    passwordConfirm.focus();
                }
            }
        });
    });
</script>