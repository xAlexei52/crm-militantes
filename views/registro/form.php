<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Afiliación - <?= APP_NAME ?></title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <!-- Definir la variable global de APP_URL para JavaScript -->
    <script>
        // Variable global con la URL base del sistema
        window.appUrl = "<?= APP_URL ?>";
    </script>
    
    <style>
        /* Estilos personalizados */
        .drop-zone {
            border: 2px dashed #cbd5e1;
            transition: all 0.3s ease;
        }
        .drop-zone:hover, .drop-zone.active {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
        
        /* Estilos responsivos adicionales */
        @media (max-width: 640px) {
            .drop-zone {
                padding: 1rem !important;
            }
            
            .grid-cols-1 {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Barra de navegación responsiva -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="<?= APP_URL ?>/" class="font-bold text-xl text-black"><?= APP_NAME ?></a>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <?php if (isLoggedIn()): ?>
                        <span class="hidden md:inline text-gray-600 mr-2">Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <a href="<?= APP_URL ?>/admin/dashboard" class="text-gray-600 hover:text-red-600 px-2 py-1 md:px-3 md:py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-tachometer-alt md:mr-1"></i> <span class="hidden md:inline">Panel</span>
                        </a>
                        <a href="<?= APP_URL ?>/logout" class="text-gray-600 hover:text-red-600 px-2 py-1 md:px-3 md:py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-sign-out-alt md:mr-1"></i> <span class="hidden md:inline">Salir</span>
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
            <div class="rounded-md p-4 <?= $flashMessage['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                <?= $flashMessage['message'] ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Contenido principal -->
    <main class="flex-grow py-4 md:py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-4 md:px-6 py-3 md:py-4 bg-sky-800 text-white">
                    <h1 class="text-xl md:text-2xl font-bold">Registro de Afiliación</h1>
                    <p class="mt-1 text-sm md:text-base">Completa el formulario para registrar nuevo militante</p>
                </div>
                
                <div class="px-4 md:px-6 py-4 md:py-6">
                    <!-- Opción para cargar INE -->
                    <div class="mb-6 border border-gray-200 rounded-lg p-3 md:p-4 bg-gray-50">
                        <h2 class="text-base md:text-lg font-semibold text-gray-700 mb-2 md:mb-4">Acelera el registro con INE</h2>
                        <p class="text-sm text-gray-600 mb-3 md:mb-4">Carga la credencial de elector para llenar automáticamente algunos campos del formulario.</p>
                        
                        <div class="drop-zone p-4 md:p-6 rounded-lg flex flex-col items-center justify-center cursor-pointer" id="dropzone">
                            <i class="fas fa-id-card text-2xl md:text-4xl text-gray-400 mb-2 md:mb-3"></i>
                            <p class="text-sm md:text-base text-gray-600 mb-1 text-center">Arrastra tu imagen aquí o haz clic para seleccionar</p>
                            <p class="text-xs text-gray-500 text-center">Formatos aceptados: JPG, PNG (Máx. 5MB)</p>
                            <input type="file" id="ine-upload" class="hidden" accept="image/*">
                        </div>
                        
                        <div id="loading-indicator" class="mt-4 text-center hidden">
                            <div class="inline-block animate-spin rounded-full h-6 w-6 md:h-8 md:w-8 border-b-2 border-red-600"></div>
                            <p class="mt-2 text-sm text-gray-600">Procesando tu credencial...</p>
                        </div>
                        
                        <div id="ocr-result" class="mt-4 p-3 bg-green-100 text-green-700 rounded-lg hidden">
                            <i class="fas fa-check-circle mr-2"></i> 
                            <span class="text-sm">Información extraída correctamente. El formulario ha sido prellenado.</span>
                        </div>
                        
                        <div id="ocr-error" class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg hidden">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span class="text-sm">No se pudo extraer la información. Por favor, llena el formulario manualmente.</span>
                        </div>
                    </div>
                    
                    <!-- Formulario de registro responsivo -->
                    <form id="registro-form" method="POST" action="<?= APP_URL ?>/register/process" enctype="multipart/form-data" class="w-full">
                        <!-- Token CSRF -->
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <!-- Información personal -->
                            <div class="col-span-1 md:col-span-2">
                                <h3 class="text-base md:text-lg font-medium text-gray-700 mb-2 md:mb-3 pb-2 border-b border-gray-200">Información Personal</h3>
                            </div>
                            
                            <!-- Nombre -->
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre(s) *</label>
                                <input 
                                    type="text" 
                                    id="nombre" 
                                    name="nombre" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                                    required
                                    value="<?= $datosINE['nombre'] ?? '' ?>"
                                >
                            </div>
                            
                            <!-- Apellido Paterno -->
                            <div>
                                <label for="apellido_paterno" class="block text-sm font-medium text-gray-700 mb-1">Apellido Paterno *</label>
                                <input 
                                    type="text" 
                                    id="apellido_paterno" 
                                    name="apellido_paterno" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                                    required
                                    value="<?= $datosINE['apellido_paterno'] ?? '' ?>"
                                >
                            </div>
                            
                            <!-- Apellido Materno -->
                            <div>
                                <label for="apellido_materno" class="block text-sm font-medium text-gray-700 mb-1">Apellido Materno</label>
                                <input 
                                    type="text" 
                                    id="apellido_materno" 
                                    name="apellido_materno" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    value="<?= $datosINE['apellido_materno'] ?? '' ?>"
                                >
                            </div>
                            
                            <!-- Fecha de Nacimiento -->
                            <div>
                                <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento *</label>
                                <input 
                                    type="date" 
                                    id="fecha_nacimiento" 
                                    name="fecha_nacimiento" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                                    required
                                    value="<?= $datosINE['fecha_nacimiento'] ? date('Y-m-d', strtotime($datosINE['fecha_nacimiento'])) : '' ?>"
                                >
                            </div>
                            
                            <!-- Género -->
                            <div>
                                <label for="genero" class="block text-sm font-medium text-gray-700 mb-1">Género *</label>
                                <select 
                                    id="genero" 
                                    name="genero" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                                    required
                                >
                                    <option value="" disabled <?= !isset($datosINE['genero']) ? 'selected' : '' ?>>Selecciona una opción</option>
                                    <option value="M" <?= isset($datosINE['genero']) && $datosINE['genero'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="F" <?= isset($datosINE['genero']) && $datosINE['genero'] == 'F' ? 'selected' : '' ?>>Femenino</option>
                                    <option value="O">Otro</option>
                                </select>
                            </div>
                            
                            <!-- Clave de Elector -->
                            <div>
                                <label for="clave_elector" class="block text-sm font-medium text-gray-700 mb-1">Clave de Elector *</label>
                                <input 
                                    type="text" 
                                    id="clave_elector" 
                                    name="clave_elector" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    required
                                    pattern="[A-Z0-9]+"
                                    value="<?= $datosINE['clave_elector'] ?? '' ?>"
                                >
                                <p class="mt-1 text-xs text-gray-500">18 caracteres alfanuméricos</p>
                            </div>
                            
                            <!-- CURP -->
                            <div>
                                <label for="curp" class="block text-sm font-medium text-gray-700 mb-1">CURP</label>
                                <input 
                                    type="text" 
                                    id="curp" 
                                    name="curp" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    pattern="[A-Z0-9]+"
                                    value="<?= $datosINE['curp'] ?? '' ?>"
                                >
                            </div>

                            <!-- Lugar de Nacimiento -->
                            <div>
                                <label for="lugar_nacimiento" class="block text-sm font-medium text-gray-700 mb-1">Lugar de Nacimiento</label>
                                <input 
                                    type="text" 
                                    id="lugar_nacimiento" 
                                    name="lugar_nacimiento" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    placeholder="Ej. Guadalajara, Jalisco"
                                >
                            </div>

                            <!-- Folio Nacional -->
                            <div>
                                <label for="folio_nacional" class="block text-sm font-medium text-gray-700 mb-1">Folio Nacional</label>
                                <input 
                                    type="text" 
                                    id="folio_nacional" 
                                    name="folio_nacional" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    placeholder="Número de folio nacional"
                                    value="<?= $datosINE['folio_nacional'] ?? '' ?>"
                                >
                            </div>

                            <!-- Información de contacto -->
                            <div class="col-span-1 md:col-span-2">
                                <h3 class="text-base md:text-lg font-medium text-gray-700 mb-2 md:mb-3 pb-2 border-b border-gray-200 mt-4 md:mt-6">Información de Contacto y Domicilio</h3>
                            </div>
                            
                            <!-- Domicilio (solo lectura, se llenará automáticamente) -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="domicilio" class="block text-sm font-medium text-gray-700 mb-1">Domicilio Completo (Generado Automáticamente)</label>
                                <input 
                                    type="text" 
                                    id="domicilio" 
                                    name="domicilio" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    readonly
                                    value="<?= $datosINE['domicilio'] ?? '' ?>"
                                >
                                <p class="text-xs text-gray-500 mt-1">Este campo se actualiza automáticamente según los campos individuales abajo.</p>
                            </div>

                            <!-- Componentes individuales del domicilio -->
                            <div class="col-span-1 md:col-span-2">
                                <h4 class="text-md font-medium text-gray-600 mb-2">Detalles de Domicilio</h4>
                            </div>

                            <!-- Calle -->
                            <div>
                                <label for="calle" class="block text-sm font-medium text-gray-700 mb-1">Calle *</label>
                                <input 
                                    type="text" 
                                    id="calle" 
                                    name="calle" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    required
                                    value="<?= $datosINE['calle'] ?? '' ?>"
                                >
                            </div>

                            <!-- Número Exterior -->
                            <div>
                                <label for="numero_exterior" class="block text-sm font-medium text-gray-700 mb-1">Número Exterior *</label>
                                <input 
                                    type="text" 
                                    id="numero_exterior" 
                                    name="numero_exterior" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    required
                                    value="<?= $datosINE['numero_exterior'] ?? '' ?>"
                                >
                            </div>

                            <!-- Número Interior -->
                            <div>
                                <label for="numero_interior" class="block text-sm font-medium text-gray-700 mb-1">Número Interior</label>
                                <input 
                                    type="text" 
                                    id="numero_interior" 
                                    name="numero_interior" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    placeholder="Opcional"
                                    value="<?= $datosINE['numero_interior'] ?? '' ?>"
                                >
                            </div>

                            <!-- Colonia -->
                            <div>
                                <label for="colonia" class="block text-sm font-medium text-gray-700 mb-1">Colonia *</label>
                                <input 
                                    type="text" 
                                    id="colonia" 
                                    name="colonia" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    required
                                    value="<?= $datosINE['colonia'] ?? '' ?>"
                                >
                            </div>

                            <!-- Código Postal -->
                            <div>
                                <label for="codigo_postal" class="block text-sm font-medium text-gray-700 mb-1">Código Postal *</label>
                                <input 
                                    type="text" 
                                    id="codigo_postal" 
                                    name="codigo_postal" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    pattern="[0-9]{5}"
                                    placeholder="5 dígitos"
                                    required
                                    value="<?= $datosINE['codigo_postal'] ?? '' ?>"
                                >
                            </div>

                            
                          <!-- Estado -->
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                                <select 
                                    id="estado" 
                                    name="estado" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    required
                                >
                                    <option value="" disabled <?= !isset($datosINE['estado']) ? 'selected' : '' ?>>Selecciona un estado</option>
                                    <option value="Aguascalientes" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Aguascalientes' ? 'selected' : '' ?>>Aguascalientes</option>
                                    <option value="Baja California" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Baja California' ? 'selected' : '' ?>>Baja California</option>
                                    <option value="Baja California Sur" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Baja California Sur' ? 'selected' : '' ?>>Baja California Sur</option>
                                    <option value="Campeche" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Campeche' ? 'selected' : '' ?>>Campeche</option>
                                    <option value="Chiapas" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Chiapas' ? 'selected' : '' ?>>Chiapas</option>
                                    <option value="Chihuahua" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Chihuahua' ? 'selected' : '' ?>>Chihuahua</option>
                                    <option value="Ciudad de México" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Ciudad de México' ? 'selected' : '' ?>>Ciudad de México</option>
                                    <option value="Coahuila" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Coahuila' ? 'selected' : '' ?>>Coahuila</option>
                                    <option value="Colima" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Colima' ? 'selected' : '' ?>>Colima</option>
                                    <option value="Durango" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Durango' ? 'selected' : '' ?>>Durango</option>
                                    <option value="Estado de México" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Estado de México' ? 'selected' : '' ?>>Estado de México</option>
                                    <option value="Guanajuato" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Guanajuato' ? 'selected' : '' ?>>Guanajuato</option>
                                    <option value="Guerrero" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Guerrero' ? 'selected' : '' ?>>Guerrero</option>
                                    <option value="Hidalgo" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Hidalgo' ? 'selected' : '' ?>>Hidalgo</option>
                                    <option value="Jalisco" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Jalisco' ? 'selected' : '' ?>>Jalisco</option>
                                    <option value="Michoacán" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Michoacán' ? 'selected' : '' ?>>Michoacán</option>
                                    <option value="Morelos" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Morelos' ? 'selected' : '' ?>>Morelos</option>
                                    <option value="Nayarit" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Nayarit' ? 'selected' : '' ?>>Nayarit</option>
                                    <option value="Nuevo León" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Nuevo León' ? 'selected' : '' ?>>Nuevo León</option>
                                    <option value="Oaxaca" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Oaxaca' ? 'selected' : '' ?>>Oaxaca</option>
                                    <option value="Puebla" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Puebla' ? 'selected' : '' ?>>Puebla</option>
                                    <option value="Querétaro" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Querétaro' ? 'selected' : '' ?>>Querétaro</option>
                                    <option value="Quintana Roo" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Quintana Roo' ? 'selected' : '' ?>>Quintana Roo</option>
                                    <option value="San Luis Potosí" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'San Luis Potosí' ? 'selected' : '' ?>>San Luis Potosí</option>
                                    <option value="Sinaloa" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Sinaloa' ? 'selected' : '' ?>>Sinaloa</option>
                                    <option value="Sonora" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Sonora' ? 'selected' : '' ?>>Sonora</option>
                                    <option value="Tabasco" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Tabasco' ? 'selected' : '' ?>>Tabasco</option>
                                    <option value="Tamaulipas" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Tamaulipas' ? 'selected' : '' ?>>Tamaulipas</option>
                                    <option value="Tlaxcala" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Tlaxcala' ? 'selected' : '' ?>>Tlaxcala</option>
                                    <option value="Veracruz" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Veracruz' ? 'selected' : '' ?>>Veracruz</option>
                                    <option value="Yucatán" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Yucatán' ? 'selected' : '' ?>>Yucatán</option>
                                    <option value="Zacatecas" <?= isset($datosINE['estado']) && $datosINE['estado'] == 'Zacatecas' ? 'selected' : '' ?>>Zacatecas</option>
                                </select>
                            </div>
                            
                            <!-- Municipio -->
                            <div>
                                <label for="municipio" class="block text-sm font-medium text-gray-700 mb-1">Municipio *</label>
                                <input 
                                    type="text" 
                                    id="municipio" 
                                    name="municipio" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    required
                                    value="<?= $datosINE['municipio'] ?? '' ?>"
                                >
                            </div>
                            
                            <!-- Sección Electoral -->
                            <div>
                                <label for="seccion" class="block text-sm font-medium text-gray-700 mb-1">Sección Electoral</label>
                                <input 
                                    type="text" 
                                    id="seccion" 
                                    name="seccion" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    pattern="[0-9]+"
                                    value="<?= $datosINE['seccion'] ?? '' ?>"
                                >
                            </div>
                            
                            <!-- Teléfono -->
                            <div>
                                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono Celular *</label>
                                <input 
                                    type="tel" 
                                    id="telefono" 
                                    name="telefono" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    required
                                    pattern="[0-9]{10}"
                                    placeholder="10 dígitos"
                                >
                                <p class="mt-1 text-xs text-gray-500">Formato: 10 dígitos sin espacios ni guiones</p>
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    placeholder="ejemplo@correo.com"
                                >
                            </div>
                            
                            <!-- Cargar imagen del INE -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="imagen_ine" class="block text-sm font-medium text-gray-700 mb-1">Subir imagen de credencial de elector (opcional)</label>
                                <input 
                                    type="file" 
                                    id="imagen_ine" 
                                    name="imagen_ine" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    accept="image/*"
                                >
                                <p class="mt-1 text-xs text-gray-500">Formatos aceptados: JPG, PNG (Max. 5MB)</p>
                            </div>
                            
                            <!-- Términos y condiciones -->
                            <div class="col-span-1 md:col-span-2 mt-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input 
                                            id="terms" 
                                            name="terms" 
                                            type="checkbox" 
                                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                            required
                                        >
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="terms" class="font-medium text-gray-700">Acepto los términos y condiciones</label>
                                        <p class="text-gray-500">Al registrarme, acepto ser contactado por medios electrónicos y que mis datos sean utilizados conforme al aviso de privacidad.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="col-span-1 md:col-span-2 mt-6 flex flex-col sm:flex-row justify-end">
                                <a href="<?= APP_URL ?>/" class="mb-3 sm:mb-0 sm:mr-4 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Cancelar
                                </a>
                                <button 
                                    type="submit" 
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-sky-800 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                >
                                    Registrar Militante
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Pie de página responsivo -->
    <footer class="bg-white border-t border-gray-200 mt-8 md:mt-12">
        <div class="max-w-7xl mx-auto py-4 md:py-6 px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex justify-center md:justify-start">
                    <span class="text-sm text-gray-600">&copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos los derechos reservados.</span>
                </div>
                <div class="flex justify-center md:justify-end mt-3 md:mt-0">
                    <a href="<?= APP_URL ?>/about" class="text-sm text-gray-500 hover:text-gray-600 mx-2">
                        Acerca de
                    </a>
                    <a href="<?= APP_URL ?>/contact" class="text-sm text-gray-500 hover:text-gray-600 mx-2">
                        Contacto
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Script mejorado para procesamiento de INE -->
    <script src="<?= APP_URL ?>/js/ine_processor.js"></script>
    <script src="<?= APP_URL ?>/js/form_handler.js"></script>
</body>
</html>