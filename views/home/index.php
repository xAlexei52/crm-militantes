<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - <?= APP_NAME ?></title>
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
                        <span class="font-bold text-xl text-black"><?= APP_NAME ?></span>
                    </div>
                </div>
                <div class="flex items-center">
                    <?php if ($loggedIn): ?>
                        <span class="text-gray-600 mr-4">Bienvenido, <?= htmlspecialchars($username) ?></span>
                        <a href="<?= APP_URL ?>/admin/dashboard" class="text-gray-600 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-tachometer-alt mr-1"></i> Panel
                        </a>
                        <a href="<?= APP_URL ?>/logout" class="text-gray-600 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-sign-out-alt mr-1"></i> Salir
                        </a>
                        <?php else: ?>
                        <a href="<?= APP_URL ?>/login" class="text-gray-600 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-sign-in-alt mr-1"></i> Iniciar Sesión
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mensaje flash -->
    <?php if ($flashMessage = getFlashMessage()): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-md p-4 <?= $flashMessage['type'] === 'error' ? 'bg-sky-200 text-red-700' : 'bg-green-100 text-green-700' ?>">
                <?= $flashMessage['message'] ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Contenido principal -->
    <main class="flex-grow">
        <!-- Hero Section -->
        <div class="bg-sky-800 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold tracking-tight">Sistema de Afiliación</h1>
                    <p class="mt-3 max-w-md mx-auto text-lg md:text-xl md:max-w-3xl">
                        Únete a nuestro movimiento y forma parte del cambio que México necesita.
                    </p>
                    <div class="mt-8 flex justify-center">
                        <a href="<?= APP_URL ?>/register" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-black bg-white hover:bg-gray-100 transform transition hover:scale-105">
                            <i class="fas fa-user-plus mr-2"></i> Regístrate Ahora
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Características -->
        <div class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900">¿Por qué afiliarte?</h2>
                    <p class="mt-4 text-lg text-gray-600">
                        Ser parte de nuestro movimiento te brinda múltiples beneficios
                    </p>
                </div>

                <div class="mt-10">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                        <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-sky-200 text-black p-3 rounded-full">
                                        <i class="fas fa-users text-xl"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Comunidad</h3>
                                    <p class="mt-2 text-gray-600">
                                        Forma parte de una comunidad comprometida con el cambio.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-sky-200 text-black p-3 rounded-full">
                                        <i class="fas fa-bullhorn text-xl"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Participación</h3>
                                    <p class="mt-2 text-gray-600">
                                        Participa en la toma de decisiones y actividades.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 bg-gray-50 rounded-lg shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-sky-200 text-black p-3 rounded-full">
                                        <i class="fas fa-hand-holding-heart text-xl"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Beneficios</h3>
                                    <p class="mt-2 text-gray-600">
                                        Accede a eventos exclusivos y oportunidades.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Llamado a la acción -->
        <div class="bg-gray-50 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-sky-800 rounded-lg shadow-xl overflow-hidden">
                    <div class="px-6 py-12 md:py-16 md:px-12 lg:px-16 lg:py-16 xl:flex xl:items-center">
                        <div class="xl:w-0 xl:flex-1">
                            <h2 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                                ¿Listo para unirte?
                            </h2>
                            <p class="mt-3 max-w-3xl text-lg leading-6 text-white">
                                El proceso de afiliación es rápido y sencillo. Solo necesitas tu credencial de elector.
                            </p>
                        </div>
                        <div class="mt-8 sm:w-full sm:max-w-md xl:mt-0 xl:ml-8">
                            <div class="sm:flex">
                                <div class="sm:flex-1">
                                    <a href="<?= APP_URL ?>/register" class="w-full flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-black bg-white hover:bg-gray-100 transform transition hover:scale-105">
                                        Comenzar Registro
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Pie de página -->
    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex justify-center md:justify-start">
                    <span class="text-gray-600">&copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos los derechos reservados.</span>
                </div>
                <div class="flex justify-center md:justify-end mt-4 md:mt-0">
                    <a href="<?= APP_URL ?>/about" class="text-gray-500 hover:text-gray-600 mx-2">
                        Acerca de
                    </a>
                    <a href="<?= APP_URL ?>/contact" class="text-gray-500 hover:text-gray-600 mx-2">
                        Contacto
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>