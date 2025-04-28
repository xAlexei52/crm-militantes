<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>
    
    <!-- Tarjetas de resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total de militantes -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase">Total de Militantes</p>
                    <p class="text-2xl font-bold text-gray-700"><?= $totalMilitantes ?? 0 ?></p>
                </div>
            </div>
        </div>
        
        <!-- Estados representados -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                    <i class="fas fa-map-marker-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase">Estados Representados</p>
                    <p class="text-2xl font-bold text-gray-700"><?= is_array($estadoPorEstado) ? count($estadoPorEstado) : 0 ?></p>
                </div>
            </div>
        </div>
        
        <!-- Tendencia de crecimiento -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 <?= $crecimientoMensual['tendencia'] === 'positiva' ? 'border-green-500' : 'border-yellow-500' ?>">
            <div class="flex items-center">
                <div class="p-3 rounded-full <?= $crecimientoMensual['tendencia'] === 'positiva' ? 'bg-green-100 text-green-500' : 'bg-yellow-100 text-yellow-500' ?> mr-4">
                    <i class="fas <?= $crecimientoMensual['tendencia'] === 'positiva' ? 'fa-chart-line' : 'fa-chart-line' ?> text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase">Tendencia</p>
                    <p class="text-2xl font-bold text-gray-700">
                        <?= $crecimientoMensual['tendencia'] === 'positiva' ? '↑' : '↓' ?> 
                        <?= abs($crecimientoMensual['crecimiento_porcentaje']) ?>%
                    </p>
                    <p class="text-xs text-gray-500">vs. mes anterior</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráficos y tablas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Militantes por estado -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Militantes por Estado</h2>
            
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (isset($estadoPorEstado) && is_array($estadoPorEstado) && count($estadoPorEstado) > 0): ?>
                            <?php foreach ($estadoPorEstado as $estado): ?>
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($estado['estado']) ?></td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500"><?= $estado['total'] ?></td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                                        <?= $totalMilitantes > 0 ? round(($estado['total'] / $totalMilitantes) * 100, 1) : 0 ?>%
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                            <div class="bg-red-600 h-2 rounded-full" style="width: <?= $totalMilitantes > 0 ? ($estado['total'] / $totalMilitantes) * 100 : 0 ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No hay datos disponibles</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Actividades recientes -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Actividades Recientes</h2>
            
            <?php if (isset($actividadesRecientes) && is_array($actividadesRecientes) && count($actividadesRecientes) > 0): ?>
                <ul class="space-y-4">
                    <?php foreach ($actividadesRecientes as $actividad): ?>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 bg-green-100 rounded-full p-2">
                                <svg class="h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-700">
                                    Nuevo militante: <?= htmlspecialchars($actividad['nombre'] . ' ' . $actividad['apellido_paterno'] . ' ' . $actividad['apellido_materno']) ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?= timeAgo(strtotime($actividad['created_at'])) ?> 
                                    <?php if (!empty($actividad['registrado_por'])): ?>
                                        por <?= htmlspecialchars($actividad['registrado_por']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-gray-500">No hay actividades recientes</p>
                </div>
            <?php endif; ?>
            
            <a href="<?= APP_URL ?>/admin/militantes" class="block text-center text-sm font-medium text-red-600 hover:text-red-500 mt-4">
                Ver todos los militantes
            </a>
        </div>
    </div>
    
    <!-- Acciones rápidas -->
    <div class="mt-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Acciones Rápidas</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="<?= APP_URL ?>/admin/militantes" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center border border-gray-200">
                <div class="p-3 rounded-full bg-red-100 text-red-500 mr-3">
                    <i class="fas fa-search"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Buscar Militantes</p>
                    <p class="text-xs text-gray-500">Accede al listado completo</p>
                </div>
            </a>
            
            <a href="<?= APP_URL ?>/register" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center border border-gray-200">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-3">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Registrar Militante</p>
                    <p class="text-xs text-gray-500">Nuevo ingreso</p>
                </div>
            </a>
            
            <?php if (isAdmin()): ?>
            <a href="<?= APP_URL ?>/admin/usuarios" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center border border-gray-200">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-3">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Gestión de Usuarios</p>
                    <p class="text-xs text-gray-500">Administra los usuarios del sistema</p>
                </div>
            </a>
            <?php else: ?>
            <a href="<?= APP_URL ?>/admin/militantes" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center border border-gray-200">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-3">
                    <i class="fas fa-file-export"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Exportar Datos</p>
                    <p class="text-xs text-gray-500">Descarga informes</p>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
/**
 * Función para formatear tiempo en formato "hace X tiempo"
 * @param int $timestamp Timestamp a formatear
 * @return string Texto formateado
 */
function timeAgo($timestamp) {
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return 'hace unos segundos';
    } elseif ($difference < 3600) {
        $minutes = round($difference / 60);
        return 'hace ' . $minutes . ' ' . ($minutes == 1 ? 'minuto' : 'minutos');
    } elseif ($difference < 86400) {
        $hours = round($difference / 3600);
        return 'hace ' . $hours . ' ' . ($hours == 1 ? 'hora' : 'horas');
    } elseif ($difference < 604800) {
        $days = round($difference / 86400);
        return 'hace ' . $days . ' ' . ($days == 1 ? 'día' : 'días');
    } elseif ($difference < 2592000) {
        $weeks = round($difference / 604800);
        return 'hace ' . $weeks . ' ' . ($weeks == 1 ? 'semana' : 'semanas');
    } else {
        // Si es más de un mes, mostramos la fecha
        return date('d/m/Y', $timestamp);
    }
}
?>