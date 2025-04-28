<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?= APP_NAME ?></title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 min-h-screen flex">
    <!-- Columna Izquierda - Imagen y descripción -->
    <div class="hidden md:flex md:w-1/2 bg-sky-800 text-white p-10 flex-col justify-between">
        <div>
            <a href="<?= APP_URL ?>/" class="inline-flex items-center text-white mb-10 hover:text-gray-200 transition">
                <i class="fas fa-arrow-left mr-2"></i> Volver al inicio
            </a>
            <h1 class="text-3xl font-bold mb-6"><?= APP_NAME ?></h1>
            <p class="text-xl mb-4">Sistema de Afiliación para Militantes</p>
            <p class="text-gray-200">Ingresa a tu cuenta para gestionar el registro de militantes, consultar estadísticas y enviar mensajes a los afiliados.</p>
        </div>
        
        <div class="mt-auto">
            <div class="border-t border-sky-700 pt-4">
                <p class="text-sm text-gray-200">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Columna Derecha - Formulario de login -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Botón volver para móviles -->
            <div class="md:hidden mb-6">
                <a href="<?= APP_URL ?>/" class="inline-flex items-center text-sky-800 hover:text-sky-700">
                    <i class="fas fa-arrow-left mr-2"></i> Volver al inicio
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-sky-800 py-4 px-6">
                    <h1 class="text-white text-2xl font-bold text-center"><?= APP_NAME ?></h1>
                </div>
                
                <div class="py-8 px-6">
                    <h2 class="text-center text-xl text-gray-700 font-semibold mb-6">Iniciar Sesión</h2>
                    
                    <?php if ($flashMessage = getFlashMessage()): ?>
                        <div class="mb-4 p-3 rounded <?= $flashMessage['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                            <?= $flashMessage['message'] ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= APP_URL ?>/login/process" class="space-y-6">
                        <!-- Token CSRF -->
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm text-gray-700 font-medium mb-2">Correo Electrónico</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" 
                                    placeholder="ejemplo@correo.com" 
                                    required
                                >
                            </div>
                        </div>
                        
                        <!-- Contraseña -->
                        <div>
                            <label for="password" class="block text-sm text-gray-700 font-medium mb-2">Contraseña</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" 
                                    placeholder="Contraseña" 
                                    required
                                >
                            </div>
                        </div>
                        
                        <!-- Recordarme -->
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-300 rounded"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Recordar mi sesión
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-sm">
                                <a href="<?= APP_URL ?>/password/reset" class="font-medium text-sky-600 hover:text-sky-500">
                                    ¿Olvidó su contraseña?
                                </a>
                            </div>
                        </div>
                                                
                        <!-- Botón de Envío -->
                        <div>
                            <button 
                                type="submit" 
                                class="w-full py-2 px-4 bg-sky-800 hover:bg-sky-700 text-white font-bold rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-opacity-50"
                            >
                                Ingresar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="py-4 px-6 bg-gray-50 border-t border-gray-100 text-center text-sm text-gray-600">
                    Sistema de Afiliación &copy; <?= date('Y') ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>