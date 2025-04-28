<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Usuario</h1>
        
        <a href="<?= APP_URL ?>/admin/usuarios" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Listado
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Información del Usuario</h2>
        </div>
        
        <div class="p-6">
            <form id="create-user-form" method="POST" action="<?= APP_URL ?>/admin/usuarios/store">
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                
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
                        >
                    </div>
                    
                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                            required
                            minlength="8"
                        >
                        <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
                    </div>
                    
                    <!-- Confirmar Contraseña -->
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña *</label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                            required
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
                        >
                            <option value="operador">Operador</option>
                            <option value="admin">Administrador</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Los administradores tienen acceso completo al sistema</p>
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
                        Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('create-user-form');
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirm');
        
        form.addEventListener('submit', function(event) {
            // Verificar que las contraseñas coincidan
            if (password.value !== passwordConfirm.value) {
                event.preventDefault();
                alert('Las contraseñas no coinciden');
                passwordConfirm.focus();
            }
        });
    });
</script>