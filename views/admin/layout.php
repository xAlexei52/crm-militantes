<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Panel de Administración' ?> - <?= APP_NAME ?></title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Estilos adicionales -->
    <style>
        .sidebar-active {
            background-color: rgba(7, 89, 133, 0.1);
            color: #0369a1;
            border-left: 3px solid #0369a1;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Barra superior -->
    <nav class="bg-sky-800 shadow-md z-10 text-white">
        <div class="max-w-full px-4 py-2">
            <div class="flex items-center justify-between h-14">
                <div class="flex items-center">
                    <!-- Botón de menú móvil -->
                    <button id="menu-toggle" class="text-white focus:outline-none lg:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <a href="<?= APP_URL ?>/admin/dashboard" class="ml-4 lg:ml-0 font-bold text-xl text-white"><?= APP_NAME ?></a>
                </div>
                
                <div class="flex items-center">
                    <div class="relative">
                        <button id="user-menu-button" class="flex items-center px-3 py-2 text-sm text-white focus:outline-none">
                            <span class="mr-2"><?= $_SESSION['user_name'] ?></span>
                            <i class="fas fa-user-circle text-xl"></i>
                        </button>
                        <!-- Menú desplegable -->
                        <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="<?= APP_URL ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" target="_blank">
                                <i class="fas fa-external-link-alt mr-2"></i> Ver sitio
                            </a>
                            <a href="<?= APP_URL ?>/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="flex flex-1 overflow-hidden">
        <!-- Barra lateral modificada para mostrar elementos según el rol -->
        <div id="sidebar" class="lg:block hidden bg-white w-64 flex-shrink-0 border-r border-gray-200">
            <div class="h-full py-4 flex flex-col justify-between">
                <div>
                    <div class="px-4 mb-6">
                        <div class="flex flex-col items-center">
                            <div class="h-12 w-12 rounded-full bg-sky-100 flex items-center justify-center text-sky-600 mb-2">
                                <i class="fas <?= isAdmin() ? 'fa-user-shield' : 'fa-user' ?> text-xl"></i>
                            </div>
                            <div class="text-center">
                                <p class="font-medium"><?= $_SESSION['user_name'] ?></p>
                                <p class="text-xs text-gray-500"><?= isAdmin() ? 'Administrador' : 'Operador' ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-4 space-y-1">
                        <!-- Dashboard accesible para todos los usuarios logueados -->
                        <a href="<?= APP_URL ?>/admin/dashboard" class="block px-4 py-2 rounded-md text-sm font-medium transition <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'sidebar-active' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                        </a>
                        
                        <!-- Militantes accesible para todos los usuarios logueados -->
                        <a href="<?= APP_URL ?>/admin/militantes" class="block px-4 py-2 rounded-md text-sm font-medium transition <?= strpos($_SERVER['REQUEST_URI'], '/admin/militantes') !== false ? 'sidebar-active' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i class="fas fa-users mr-3"></i> Militantes
                        </a>
                        
                        <!-- Mensajes accesible para todos los usuarios logueados -->
                        <a href="<?= APP_URL ?>/admin/mensajes" class="block px-4 py-2 rounded-md text-sm font-medium transition <?= strpos($_SERVER['REQUEST_URI'], '/admin/mensajes') !== false ? 'sidebar-active' : 'text-gray-600 hover:bg-gray-50' ?>">
                            <i class="fas fa-sms mr-3"></i> Envío de Mensajes
                        </a>
                        
                        <!-- Gestión de usuarios, solo visible para administradores -->
                        <?php if (isAdmin()): ?>
                            <a href="<?= APP_URL ?>/admin/usuarios" class="block px-4 py-2 rounded-md text-sm font-medium transition <?= strpos($_SERVER['REQUEST_URI'], '/admin/usuarios') !== false ? 'sidebar-active' : 'text-gray-600 hover:bg-gray-50' ?>">
                                <i class="fas fa-users-cog mr-3"></i> Gestión de Usuarios
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Botón de cerrar sesión en el sidebar (igual para todos) -->
                <div class="px-4 mt-auto border-t border-gray-200 pt-4">
                    <a href="<?= APP_URL ?>/" class="block px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        <i class="fas fa-home mr-3"></i> Ir al sitio
                    </a>
                    <a href="<?= APP_URL ?>/logout" class="block px-4 py-2 rounded-md text-sm font-medium text-red-600 hover:bg-red-50 transition mt-2">
                        <i class="fas fa-sign-out-alt mr-3"></i> Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Contenido principal -->
        <div class="flex-1 overflow-auto">
            <!-- Mensaje flash -->
            <?php if ($flashMessage = getFlashMessage()): ?>
                <div class="p-4">
                    <div class="rounded-md p-4 <?= $flashMessage['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                        <?= $flashMessage['message'] ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Contenido específico de cada página -->
            <div class="p-4">
                <?php 
                // Corrección para incluir el archivo de contenido con la ruta correcta
                require_once dirname(__FILE__) . '/' . $content; 
                ?>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script>
        // Toggle del menú de usuario
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        
        userMenuButton.addEventListener('click', function() {
            userMenu.classList.toggle('hidden');
        });
        
        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function(event) {
            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
        
        // Toggle de la barra lateral en móvil
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('hidden');
        });
        
        // Si la pantalla es pequeña, cerrar sidebar al hacer clic en un enlace
        if (window.innerWidth < 1024) {
            const sidebarLinks = sidebar.querySelectorAll('a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    sidebar.classList.add('hidden');
                });
            });
        }
    </script>
</body>
</html>