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
                    <p class="text-2xl font-bold text-gray-700"><?= count($estadoPorEstado ?? []) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Registros recientes -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 uppercase">Tendencia</p>
                    <p class="text-2xl font-bold text-gray-700">↑ 12%</p>
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
                                        <?= round(($estado['total'] / $totalMilitantes) * 100, 1) ?>%
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                            <div class="bg-red-600 h-2 rounded-full" style="width: <?= ($estado['total'] / $totalMilitantes) * 100 ?>%"></div>
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
            
            <ul class="space-y-4">
                <li class="flex items-start">
                    <div class="flex-shrink-0 bg-green-100 rounded-full p-2">
                        <svg class="h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700">Nuevo militante registrado</p>
                        <p class="text-xs text-gray-500">Hace 30 minutos</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <div class="flex-shrink-0 bg-blue-100 rounded-full p-2">
                        <svg class="h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700">Actualización de datos de militante</p>
                        <p class="text-xs text-gray-500">Hace 2 horas</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <div class="flex-shrink-0 bg-purple-100 rounded-full p-2">
                        <svg class="h-4 w-4 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700">Campaña SMS enviada a 250 militantes</p>
                        <p class="text-xs text-gray-500">Ayer a las 15:30</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-full p-2">
                        <svg class="h-4 w-4 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700">Actualización de datos en masa</p>
                        <p class="text-xs text-gray-500">Hace 2 días</p>
                    </div>
                </li>
            </ul>
            
            <a href="#" class="block text-center text-sm font-medium text-red-600 hover:text-red-500 mt-4">
                Ver todas las actividades
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
            
            <a href="<?= APP_URL ?>/admin/mensajes" class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition flex items-center border border-gray-200">
                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-3">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Enviar Mensajes</p>
                    <p class="text-xs text-gray-500">Comunicaciones masivas</p>
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
        </div>
    </div>
</div>