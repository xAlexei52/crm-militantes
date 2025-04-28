<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Militantes</h1>
        
        <a href="<?= APP_URL ?>/register" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-sky-800 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
            <i class="fas fa-plus mr-2"></i> Nuevo Militante
        </a>
    </div>
    
    <!-- Filtros de búsqueda mejorados -->
<div class="bg-white p-4 rounded-lg shadow-md mb-6">
    <form action="<?= APP_URL ?>/admin/militantes" method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre o Apellido</label>
                <input 
                    type="text" 
                    id="nombre" 
                    name="nombre" 
                    value="<?= isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '' ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    placeholder="Buscar por nombre..."
                >
            </div>
            
            <div>
                <label for="clave_elector" class="block text-sm font-medium text-gray-700 mb-1">Clave de Elector</label>
                <input 
                    type="text" 
                    id="clave_elector" 
                    name="clave_elector"
                    value="<?= isset($_GET['clave_elector']) ? htmlspecialchars($_GET['clave_elector']) : '' ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    placeholder="ABCD123456HDFGHI12"
                >
            </div>
            
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select 
                    id="estado" 
                    name="estado" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                >
                    <option value="">Todos los estados</option>
                    <?php
                    // Obtener la lista de estados desde la base de datos
                    try {
                        $conn = getDBConnection();
                        $query = "SELECT DISTINCT estado FROM militantes WHERE estado != '' ORDER BY estado";
                        $estadosResult = $conn->query($query);
                        
                        if ($estadosResult && $estadosResult->num_rows > 0) {
                            while ($row = $estadosResult->fetch_assoc()) {
                                $selected = isset($_GET['estado']) && $_GET['estado'] == $row['estado'] ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($row['estado']) . "\" $selected>" . htmlspecialchars($row['estado']) . "</option>";
                            }
                        }
                    } catch (Exception $e) {
                        // Si hay error, mostrar mensaje en consola y continuar
                        error_log("Error al cargar estados: " . $e->getMessage());
                    }
                    ?>
                </select>
            </div>
            
            <div>
                <label for="municipio" class="block text-sm font-medium text-gray-700 mb-1">Municipio</label>
                <input 
                    type="text" 
                    id="municipio" 
                    name="municipio"
                    value="<?= isset($_GET['municipio']) ? htmlspecialchars($_GET['municipio']) : '' ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    placeholder="Buscar por municipio..."
                >
            </div>
        </div>
        
        <div class="flex justify-end">
            <a href="<?= APP_URL ?>/admin/militantes" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 mr-3">
                <i class="fas fa-eraser mr-2"></i> Limpiar Filtros
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-sky-800 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                <i class="fas fa-search mr-2"></i> Buscar
            </button>
        </div>
    </form>
</div>
    
    <!-- Resultados -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if (!empty($result['militantes'])): ?>
            <!-- Tabla de resultados -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clave de Elector</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Municipio</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($result['militantes'] as $militante): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($militante['nombre']) ?> <?= htmlspecialchars($militante['apellido_paterno']) ?> <?= htmlspecialchars($militante['apellido_materno']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($militante['clave_elector']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($militante['estado']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($militante['municipio']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($militante['telefono'])): ?>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($militante['telefono']) ?></div>
                                    <?php else: ?>
                                        <div class="text-sm text-gray-400">No disponible</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?= date('d/m/Y', strtotime($militante['created_at'])) ?></div>
                                </td>
                                <!-- Modificar la columna de acciones en list-content.php -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= APP_URL ?>/admin/militantes/view?id=<?= $militante['id'] ?>" class="text-sky-600 hover:text-sky-900 mr-3">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="<?= APP_URL ?>/admin/militantes/edit?id=<?= $militante['id'] ?>" class="text-sky-600 hover:text-sky-900 mr-3">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="<?= APP_URL ?>/admin/militantes/delete?id=<?= $militante['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Está seguro de eliminar este registro?');">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación corregida -->
            <?php if (isset($result['pages']) && $result['pages'] > 1): ?>
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando <span class="font-medium"><?= (($result['current_page'] - 1) * 10) + 1 ?></span> a 
                                <span class="font-medium"><?= min($result['current_page'] * 10, $result['total']) ?></span> de 
                                <span class="font-medium"><?= $result['total'] ?></span> resultados
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php
                                // Función para generar URL con parámetros de filtro actuales
                                function getPaginationUrl($page) {
                                    $params = ['page' => $page];
                                    
                                    // Mantener los filtros actuales en la URL
                                    $filterParams = ['nombre', 'clave_elector', 'estado', 'municipio'];
                                    foreach ($filterParams as $param) {
                                        if (isset($_GET[$param]) && $_GET[$param] !== '') {
                                            $params[$param] = $_GET[$param];
                                        }
                                    }
                                    
                                    // Construir la URL base
                                    $url = APP_URL . '/admin/militantes';
                                    
                                    // Añadir parámetros a la URL
                                    if (!empty($params)) {
                                        $url .= '?' . http_build_query($params);
                                    }
                                    
                                    return $url;
                                }
                                ?>
                                
                                <!-- Botón anterior -->
                                <?php if ($result['current_page'] > 1): ?>
                                    <a href="<?= getPaginationUrl($result['current_page'] - 1) ?>" 
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Anterior</span>
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                        <span class="sr-only">Anterior</span>
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                <?php endif; ?>
                                
                                <!-- Números de página -->
                                <?php 
                                // Determinar el rango de páginas a mostrar
                                $start = max(1, $result['current_page'] - 2);
                                $end = min($result['pages'], $result['current_page'] + 2);
                                
                                // Si estamos cerca del inicio o final, ajustar el rango
                                if ($start < 3) {
                                    $end = min($result['pages'], 5);
                                }
                                if ($end > $result['pages'] - 2) {
                                    $start = max(1, $result['pages'] - 4);
                                }
                                
                                // Mostrar enlace a la primera página si no estamos en las primeras páginas
                                if ($start > 1) {
                                    echo '<a href="' . getPaginationUrl(1) . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>';
                                    
                                    // Mostrar elipsis si hay salto
                                    if ($start > 2) {
                                        echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                                    }
                                }
                                
                                // Mostrar las páginas en el rango calculado
                                for ($i = $start; $i <= $end; $i++): 
                                ?>
                                    <?php if ($i == $result['current_page']): ?>
                                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-sky-50 text-sm font-medium text-sky-600">
                                            <?= $i ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="<?= getPaginationUrl($i) ?>" 
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            <?= $i ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                
                                <?php 
                                // Mostrar elipsis y última página si no estamos en las últimas páginas
                                if ($end < $result['pages']) {
                                    // Mostrar elipsis si hay salto
                                    if ($end < $result['pages'] - 1) {
                                        echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                                    }
                                    
                                    // Enlace a la última página
                                    echo '<a href="' . getPaginationUrl($result['pages']) . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $result['pages'] . '</a>';
                                }
                                ?>
                                
                                <!-- Botón siguiente -->
                                <?php if ($result['current_page'] < $result['pages']): ?>
                                    <a href="<?= getPaginationUrl($result['current_page'] + 1) ?>" 
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Siguiente</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                                        <span class="sr-only">Siguiente</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                <?php endif; ?>
                            </nav>
                        </div>
                    </div>
                    
                    <!-- Versión móvil simplificada -->
                    <div class="flex flex-col sm:hidden">
                        <div class="flex justify-between mb-3">
                            <p class="text-sm text-gray-700">
                                Página <span class="font-medium"><?= $result['current_page'] ?></span> de 
                                <span class="font-medium"><?= $result['pages'] ?></span>
                            </p>
                            <p class="text-sm text-gray-700">
                                Total: <span class="font-medium"><?= $result['total'] ?></span>
                            </p>
                        </div>
                        <div class="flex justify-between">
                            <?php if ($result['current_page'] > 1): ?>
                                <a href="<?= getPaginationUrl($result['current_page'] - 1) ?>" 
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-chevron-left mr-2"></i> Anterior
                                </a>
                            <?php else: ?>
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    <i class="fas fa-chevron-left mr-2"></i> Anterior
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($result['current_page'] < $result['pages']): ?>
                                <a href="<?= getPaginationUrl($result['current_page'] + 1) ?>" 
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Siguiente <i class="fas fa-chevron-right ml-2"></i>
                                </a>
                            <?php else: ?>
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    Siguiente <i class="fas fa-chevron-right ml-2"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Mensaje de no resultados -->
            <div class="p-6 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-sky-100 text-sky-500 mb-4">
                    <i class="fas fa-search text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron resultados</h3>
                <p class="text-gray-500">Intenta con otros términos de búsqueda o revisa los filtros aplicados.</p>
            </div>
        <?php endif; ?>
    </div>
</div>