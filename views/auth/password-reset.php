<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - <?= APP_NAME ?></title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Barra de navegación -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="<?= APP_URL ?>/" class="font-bold text-xl text-black"><?= APP_NAME ?></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mensaje flash -->
    <?php if ($flashMessage = getFlashMessage()): ?>
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-md p-4 <?= $flashMessage['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                <?= $flashMessage['message'] ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Contenido principal -->
    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h1 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Establecer Nueva Contraseña
                </h1>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Ingrese su nueva contraseña para la cuenta asociada con <?= htmlspecialchars($_GET['email']) ?>.
                </p>
            </div>
            
            <div class="mt-8 bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" action="<?= APP_URL ?>/password/reset/<?= $token ?>/process" method="POST" id="reset-form">
                    <!-- Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email']) ?>">
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Nueva Contraseña
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                minlength="8"
                                class="pl-10 focus:ring-sky-500 focus:border-sky-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border"
                                placeholder="Mínimo 8 caracteres"
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Su contraseña debe tener al menos 8 caracteres.</p>
                    </div>
                    
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700">
                            Confirmar Contraseña
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input
                                id="password_confirm"
                                name="password_confirm"
                                type="password"
                                required
                                minlength="8"
                                class="pl-10 focus:ring-sky-500 focus:border-sky-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border"
                                placeholder="Confirme su contraseña"
                            />
                        </div>
                    </div>
                    
                    <div>
                        <button
                            type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-sky-800 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500"
                        >
                            Guardar Nueva Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <!-- Pie de página -->
    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex justify-center md:justify-start">
                    <span class="text-gray-600">&copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos los derechos reservados.</span>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reset-form');
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
</body>
</html>