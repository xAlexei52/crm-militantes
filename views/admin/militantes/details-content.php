

<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detalles del Militante</h1>
        
        <div class="flex space-x-3">
            <a href="<?= APP_URL ?>/admin/militantes/edit?id=<?= $militante['id'] ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
            <a href="<?= APP_URL ?>/admin/militantes" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Listado
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Tarjeta de perfil -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center space-x-4">
            <div class="h-14 w-14 rounded-full bg-sky-100 flex items-center justify-center text-sky-800">
                <i class="fas fa-user text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    <?= htmlspecialchars($militante['nombre']) ?> <?= htmlspecialchars($militante['apellido_paterno']) ?> <?= htmlspecialchars($militante['apellido_materno']) ?>
                </h2>
                <p class="text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                        <i class="fas fa-check-circle mr-1"></i> Activo
                    </span>
                    <span class="text-sm">Desde: <?= date('d/m/Y', strtotime($militante['created_at'])) ?></span>
                </p>
            </div>
        </div>
        
        <!-- Contenido principal -->
        <div class="p-6">
            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button id="tab-personal" class="border-sky-800 text-sky-800 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm active-tab" onclick="showTab('personal')">
                        Información Personal
                    </button>
                    <button id="tab-contacto" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" onclick="showTab('contacto')">
                        Contacto y Domicilio
                    </button>
                    <button id="tab-adicional" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" onclick="showTab('adicional')">
                        Información Adicional
                    </button>
                    <!-- <button id="tab-documento" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" onclick="showTab('documento')">
                        Documento INE
                    </button> -->
                </nav>
            </div>
            
            <!-- Tab: Información Personal -->
            <div id="content-personal" class="tab-content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Nombre Completo</h3>
                        <p class="text-base text-gray-900"><?= htmlspecialchars($militante['nombre']) ?> <?= htmlspecialchars($militante['apellido_paterno']) ?> <?= htmlspecialchars($militante['apellido_materno']) ?></p>
                    </div>
                    
                    <!-- Fecha de Nacimiento -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Fecha de Nacimiento</h3>
                        <p class="text-base text-gray-900">
                            <?= date('d/m/Y', strtotime($militante['fecha_nacimiento'])) ?>
                            <?php if (!empty($militante['edad'])): ?>
                                <span class="text-sm text-gray-500 ml-2">(<?= $militante['edad'] ?> años)</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <!-- Género -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Género</h3>
                        <p class="text-base text-gray-900">
                            <?php
                            $genero = '';
                            switch($militante['genero']) {
                                case 'M': $genero = 'Masculino'; break;
                                case 'F': $genero = 'Femenino'; break;
                                case 'O': $genero = 'Otro'; break;
                                default: $genero = 'No especificado';
                            }
                            echo htmlspecialchars($genero);
                            ?>
                        </p>
                    </div>
                    
                    <!-- Lugar de Nacimiento -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Lugar de Nacimiento</h3>
                        <p class="text-base text-gray-900"><?= !empty($militante['lugar_nacimiento']) ? htmlspecialchars($militante['lugar_nacimiento']) : 'No especificado' ?></p>
                    </div>
                    
                    <!-- Clave de Elector -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Clave de Elector</h3>
                        <p class="text-base text-gray-900 font-mono"><?= htmlspecialchars($militante['clave_elector']) ?></p>
                    </div>
                    
                    <!-- CURP -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">CURP</h3>
                        <p class="text-base text-gray-900 font-mono"><?= !empty($militante['curp']) ? htmlspecialchars($militante['curp']) : 'No especificado' ?></p>
                    </div>
                    
                    <!-- Folio Nacional -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Folio Nacional</h3>
                        <p class="text-base text-gray-900"><?= !empty($militante['folio_nacional']) ? htmlspecialchars($militante['folio_nacional']) : 'No especificado' ?></p>
                    </div>
                    
                    <!-- Fecha de Inscripción al Padrón -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Fecha de Inscripción al Padrón</h3>
                        <p class="text-base text-gray-900"><?= !empty($militante['fecha_inscripcion_padron']) ? date('d/m/Y', strtotime($militante['fecha_inscripcion_padron'])) : 'No especificado' ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Tab: Contacto y Domicilio -->
            <div id="content-contacto" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Teléfono -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Teléfono</h3>
                        <p class="text-base text-gray-900"><?= !empty($militante['telefono']) ? htmlspecialchars($militante['telefono']) : 'No especificado' ?></p>
                    </div>
                    
                    <!-- Email -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Correo Electrónico</h3>
                        <p class="text-base text-gray-900"><?= !empty($militante['email']) ? htmlspecialchars($militante['email']) : 'No especificado' ?></p>
                    </div>
                    
                    <!-- Domicilio Completo -->
                    <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Domicilio Completo</h3>
                        <p class="text-base text-gray-900"><?= !empty($militante['domicilio']) ? htmlspecialchars($militante['domicilio']) : 'No especificado' ?></p>
                    </div>
                    
                    <!-- Calle y número -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Calle y Número</h3>
                        <p class="text-base text-gray-900">
                            <?php 
                            $direccion = '';
                            if (!empty($militante['calle'])) {
                                $direccion .= $militante['calle'];
                                
                                if (!empty($militante['numero_exterior'])) {
                                    $direccion .= ' #' . $militante['numero_exterior'];
                                }
                                
                                if (!empty($militante['numero_interior'])) {
                                    $direccion .= ', Int. ' . $militante['numero_interior'];
                                }
                                
                                echo htmlspecialchars($direccion);
                            } else {
                                echo 'No especificado';
                            }
                            ?>
                        </p>
                    </div>
                    
                    <!-- Colonia y Código Postal -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Colonia y Código Postal</h3>
                        <p class="text-base text-gray-900">
                            <?php 
                            $coloniaCP = '';
                            if (!empty($militante['colonia'])) {
                                $coloniaCP .= 'Col. ' . $militante['colonia'];
                                
                                if (!empty($militante['codigo_postal'])) {
                                    $coloniaCP .= ', C.P. ' . $militante['codigo_postal'];
                                }
                                
                                echo htmlspecialchars($coloniaCP);
                            } else if (!empty($militante['codigo_postal'])) {
                                echo 'C.P. ' . htmlspecialchars($militante['codigo_postal']);
                            } else {
                                echo 'No especificado';
                            }
                            ?>
                        </p>
                    </div>
                    
                    <!-- Municipio y Estado -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Municipio</h3>
                        <p class="text-base text-gray-900"><?= htmlspecialchars($militante['municipio']) ?></p>
                    </div>
                    
                    <!-- Estado -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Estado</h3>
                        <p class="text-base text-gray-900"><?= htmlspecialchars($militante['estado']) ?></p>
                    </div>
                    
                    <!-- Sección Electoral -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Sección Electoral</h3>
                        <p class="text-base text-gray-900"><?= !empty($militante['seccion']) ? htmlspecialchars($militante['seccion']) : 'No especificado' ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Tab: Información Adicional -->
            <div id="content-adicional" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Salario Mensual -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Salario Mensual</h3>
                        <p class="text-base text-gray-900">
                            <?= !empty($militante['salario_mensual']) ? '$' . number_format($militante['salario_mensual'], 2) : 'No especificado' ?>
                        </p>
                    </div>
                    
                    <!-- Medio de Transporte -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Medio de Transporte</h3>
                        <p class="text-base text-gray-900">
                            <?php 
                            if (!empty($militante['medio_transporte'])) {
                                // Convertir el valor de la base de datos a texto más amigable
                                $transportes = [
                                    'auto_propio' => 'Auto propio',
                                    'transporte_publico' => 'Transporte público',
                                    'bicicleta' => 'Bicicleta',
                                    'moto' => 'Motocicleta',
                                    'a_pie' => 'A pie',
                                    'uber_didi' => 'Uber / Didi / Taxi',
                                    'otro' => 'Otro'
                                ];
                                echo isset($transportes[$militante['medio_transporte']]) ? 
                                    htmlspecialchars($transportes[$militante['medio_transporte']]) : 
                                    htmlspecialchars($militante['medio_transporte']);
                            } else {
                                echo 'No especificado';
                            }
                            ?>
                        </p>
                    </div>
                    
                    <!-- Nivel de Estudios -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Nivel de Estudios</h3>
                        <p class="text-base text-gray-900">
                            <?php 
                            if (!empty($militante['nivel_estudios'])) {
                                // Convertir el valor de la base de datos a texto más amigable
                                $estudios = [
                                    'sin_estudios' => 'Sin estudios',
                                    'primaria_incompleta' => 'Primaria incompleta',
                                    'primaria_completa' => 'Primaria completa',
                                    'secundaria_incompleta' => 'Secundaria incompleta',
                                    'secundaria_completa' => 'Secundaria completa',
                                    'preparatoria_incompleta' => 'Preparatoria/Bachillerato incompleto',
                                    'preparatoria_completa' => 'Preparatoria/Bachillerato completo',
                                    'tecnico' => 'Técnico o carrera técnica',
                                    'universidad_incompleta' => 'Universidad incompleta',
                                    'universidad_completa' => 'Universidad completa',
                                    'posgrado' => 'Posgrado (Maestría, Doctorado, etc.)'
                                ];
                                echo isset($estudios[$militante['nivel_estudios']]) ? 
                                    htmlspecialchars($estudios[$militante['nivel_estudios']]) : 
                                    htmlspecialchars($militante['nivel_estudios']);
                            } else {
                                echo 'No especificado';
                            }
                            ?>
                        </p>
                    </div>
                    
                    <!-- Sección Electoral -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Sección Electoral</h3>
                        <p class="text-base text-gray-900">
                            <?= !empty($militante['seccion']) ? htmlspecialchars($militante['seccion']) : 'No especificado' ?>
                        </p>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <h3 class="text-lg font-medium text-gray-700 mb-3 pb-2 border-b border-gray-200 mt-6">Información del Registro</h3>
                    </div>

                    <!-- Fecha de Registro -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Fecha de Registro</h3>
                        <p class="text-base text-gray-900"><?= date('d/m/Y H:i', strtotime($militante['created_at'])) ?></p>
                    </div>
                    
                    <!-- Fecha de Última Actualización -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Última Actualización</h3>
                        <p class="text-base text-gray-900">
                            <?= isset($militante['updated_at']) && $militante['updated_at'] != $militante['created_at'] ? 
                                date('d/m/Y H:i', strtotime($militante['updated_at'])) : 
                                'Sin actualizaciones desde el registro' ?>
                        </p>
                    </div>
                    
                    <!-- Registrado por (versión mejorada) -->
                    <div class="bg-gray-50 p-4 rounded-lg col-span-1 md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Registrado por</h3>
                        <?php if (!empty($militante['registrado_por'])): ?>
                            <?php
                                // Consultar la tabla de usuarios para obtener el nombre y email
                                $conn = getDBConnection();
                                $registradorId = (int)$militante['registrado_por'];
                                $query = "SELECT nombre, email, role FROM usuarios WHERE id = $registradorId LIMIT 1";
                                $result = $conn->query($query);
                                
                                if ($result && $result->num_rows > 0):
                                    $registrador = $result->fetch_assoc();
                            ?>
                                <div class="flex items-center mt-1">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-base font-medium text-gray-900"><?= htmlspecialchars($registrador['nombre']) ?></p>
                                        <div class="flex items-center">
                                            <p class="text-sm text-gray-500 mr-2"><?= htmlspecialchars($registrador['email']) ?></p>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= $registrador['role'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' ?>">
                                                <?= $registrador['role'] === 'admin' ? 'Administrador' : 'Operador' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-base text-gray-900">
                                    Usuario desconocido (ID: <?= $registradorId ?>)
                                </p>
                                <p class="text-xs text-gray-500">
                                    El usuario registrador ya no existe en el sistema
                                </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-base text-gray-900">
                                No registrado
                            </p>
                            <p class="text-xs text-gray-500">
                                Registro añadido sin usuario asociado
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
                        
            <!-- Tab: Documento INE -->
            <!-- <div id="content-documento" class="tab-content hidden">
                <?php if (!empty($militante['imagen_ine'])): ?>
                <div class="flex justify-center">
                    <div class="max-w-lg">
                        <div class="bg-gray-50 p-4 rounded-lg text-center mb-4">
                            <h3 class="text-base font-medium text-gray-700 mb-3">Imagen de Credencial para Votar</h3>
                            <div class="border border-gray-300 rounded-lg p-2">
                                <img 
                                    src="<?= APP_URL ?>/uploads/<?= htmlspecialchars($militante['imagen_ine']) ?>" 
                                    alt="Credencial de Elector" 
                                    class="mx-auto max-h-96 object-contain">
                            </div>
                            <a href="<?= APP_URL ?>/uploads/<?= htmlspecialchars($militante['imagen_ine']) ?>" 
                                target="_blank" 
                                class="inline-flex items-center px-4 py-2 mt-4 border border-transparent text-sm font-medium rounded-md text-white bg-sky-800 hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-search-plus mr-2"></i> Ver en tamaño completo
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="bg-gray-50 p-6 rounded-lg text-center">
                    <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-full bg-gray-200 text-gray-400 mb-4">
                        <i class="fas fa-id-card text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No hay imagen disponible</h3>
                    <p class="text-gray-500 mb-4">No se ha subido una imagen de la credencial para votar.</p>
                    <a href="<?= APP_URL ?>/admin/militantes/edit?id=<?= $militante['id'] ?>" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-sky-800 hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-edit mr-2"></i> Editar militante para subir imagen
                    </a>
                </div>
                <?php endif; ?>
            </div> -->
        </div>
    </div>
</div>

<script>
    function showTab(tabName) {
        // Ocultar todos los contenidos
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Mostrar el contenido seleccionado
        document.getElementById('content-' + tabName).classList.remove('hidden');
        
        // Actualizar las clases de los tabs
        document.querySelectorAll('button[id^="tab-"]').forEach(tab => {
            tab.classList.remove('border-sky-800', 'text-sky-800');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Marcar el tab activo
        document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
        document.getElementById('tab-' + tabName).classList.add('border-sky-800', 'text-sky-800');
    }
</script>