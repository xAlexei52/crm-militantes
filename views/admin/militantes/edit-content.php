
<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Editar Militante</h1>
        
        <a href="<?= APP_URL ?>/admin/militantes" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Listado
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-800">Información del Militante</h2>
        </div>
        
        <div class="p-6">
            <form id="edit-militante-form" method="POST" action="<?= APP_URL ?>/admin/militantes/save">
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="id" value="<?= $militante['id'] ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Información personal -->
                    <div class="col-span-3">
                        <h3 class="text-lg font-medium text-gray-700 mb-3 pb-2 border-b border-gray-200">Información Personal</h3>
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
                            value="<?= htmlspecialchars($militante['nombre']) ?>"
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
                            value="<?= htmlspecialchars($militante['apellido_paterno']) ?>"
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
                            value="<?= htmlspecialchars($militante['apellido_materno']) ?>"
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
                            value="<?= date('Y-m-d', strtotime($militante['fecha_nacimiento'])) ?>"
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
                            value="<?= htmlspecialchars($militante['lugar_nacimiento'] ?? '') ?>"
                            placeholder="Ej. Guadalajara, Jalisco"
                        >
                    </div>
                    
                    <!-- Edad (solo lectura) -->
                    <div>
                        <label for="edad" class="block text-sm font-medium text-gray-700 mb-1">Edad</label>
                        <input 
                            type="text" 
                            id="edad" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500" 
                            value="<?= htmlspecialchars($militante['edad'] ?? 'Calculada automáticamente') ?>"
                            readonly
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
                            <option value="M" <?= $militante['genero'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= $militante['genero'] == 'F' ? 'selected' : '' ?>>Femenino</option>
                            <option value="O" <?= $militante['genero'] == 'O' ? 'selected' : '' ?>>Otro</option>
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
                            value="<?= htmlspecialchars($militante['clave_elector']) ?>"
                        >
                    </div>
                    
                    <!-- CURP -->
                    <div>
                        <label for="curp" class="block text-sm font-medium text-gray-700 mb-1">CURP</label>
                        <input 
                            type="text" 
                            id="curp" 
                            name="curp" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            value="<?= htmlspecialchars($militante['curp'] ?? '') ?>"
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
                            value="<?= htmlspecialchars($militante['folio_nacional'] ?? '') ?>"
                        >
                    </div>
                    
                    <!-- Fecha de Inscripción al Padrón -->
                    <div>
                        <label for="fecha_inscripcion_padron" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inscripción al Padrón</label>
                        <input 
                            type="date" 
                            id="fecha_inscripcion_padron" 
                            name="fecha_inscripcion_padron" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            value="<?= isset($militante['fecha_inscripcion_padron']) ? date('Y-m-d', strtotime($militante['fecha_inscripcion_padron'])) : '' ?>"
                        >
                    </div>
                    
                    <!-- Información de domicilio -->
                    <div class="col-span-3">
                        <h3 class="text-lg font-medium text-gray-700 mb-3 pb-2 border-b border-gray-200 mt-6">Domicilio</h3>
                    </div>
                    
                    <!-- Domicilio (completo para compatibilidad) -->
                    <div class="col-span-3">
                        <label for="domicilio" class="block text-sm font-medium text-gray-700 mb-1">Domicilio Completo</label>
                        <input 
                            type="text" 
                            id="domicilio" 
                            name="domicilio" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            value="<?= htmlspecialchars($militante['domicilio'] ?? '') ?>"
                        >
                    </div>
                    
                    <!-- Calle -->
                    <div>
                        <label for="calle" class="block text-sm font-medium text-gray-700 mb-1">Calle</label>
                        <input 
                            type="text" 
                            id="calle" 
                            name="calle" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            value="<?= htmlspecialchars($militante['calle'] ?? '') ?>"
                        >
                    </div>
                    
                    <!-- Número Exterior -->
                    <div>
                        <label for="numero_exterior" class="block text-sm font-medium text-gray-700 mb-1">Número Exterior</label>
                        <input 
                            type="text" 
                            id="numero_exterior" 
                            name="numero_exterior" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            value="<?= htmlspecialchars($militante['numero_exterior'] ?? '') ?>"
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
                            value="<?= htmlspecialchars($militante['numero_interior'] ?? '') ?>"
                        >
                    </div>
                    
                    <!-- Colonia -->
                    <div>
                        <label for="colonia" class="block text-sm font-medium text-gray-700 mb-1">Colonia</label>
                        <input 
                            type="text" 
                            id="colonia" 
                            name="colonia" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            value="<?= htmlspecialchars($militante['colonia'] ?? '') ?>"
                        >
                    </div>
                    
                    <!-- Código Postal -->
                    <div>
                        <label for="codigo_postal" class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                        <input 
                            type="text" 
                            id="codigo_postal" 
                            name="codigo_postal" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            value="<?= htmlspecialchars($militante['codigo_postal'] ?? '') ?>"
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
                            <option value="Aguascalientes" <?= $militante['estado'] == 'Aguascalientes' ? 'selected' : '' ?>>Aguascalientes</option>
                            <option value="Baja California" <?= $militante['estado'] == 'Baja California' ? 'selected' : '' ?>>Baja California</option>
                            <option value="Baja California Sur" <?= $militante['estado'] == 'Baja California Sur' ? 'selected' : '' ?>>Baja California Sur</option>
                            <option value="Campeche" <?= $militante['estado'] == 'Campeche' ? 'selected' : '' ?>>Campeche</option>
                            <option value="Chiapas" <?= $militante['estado'] == 'Chiapas' ? 'selected' : '' ?>>Chiapas</option>
                            <option value="Chihuahua" <?= $militante['estado'] == 'Chihuahua' ? 'selected' : '' ?>>Chihuahua</option>
                            <option value="Ciudad de México" <?= $militante['estado'] == 'Ciudad de México' ? 'selected' : '' ?>>Ciudad de México</option>
                            <option value="Coahuila" <?= $militante['estado'] == 'Coahuila' ? 'selected' : '' ?>>Coahuila</option>
                            <option value="Colima" <?= $militante['estado'] == 'Colima' ? 'selected' : '' ?>>Colima</option>
                            <option value="Durango" <?= $militante['estado'] == 'Durango' ? 'selected' : '' ?>>Durango</option>
                            <option value="Estado de México" <?= $militante['estado'] == 'Estado de México' ? 'selected' : '' ?>>Estado de México</option>
                            <option value="Guanajuato" <?= $militante['estado'] == 'Guanajuato' ? 'selected' : '' ?>>Guanajuato</option>
                            <option value="Guerrero" <?= $militante['estado'] == 'Guerrero' ? 'selected' : '' ?>>Guerrero</option>
                            <option value="Hidalgo" <?= $militante['estado'] == 'Hidalgo' ? 'selected' : '' ?>>Hidalgo</option>
                            <option value="Jalisco" <?= $militante['estado'] == 'Jalisco' ? 'selected' : '' ?>>Jalisco</option>
                            <option value="Michoacán" <?= $militante['estado'] == 'Michoacán' ? 'selected' : '' ?>>Michoacán</option>
                            <option value="Morelos" <?= $militante['estado'] == 'Morelos' ? 'selected' : '' ?>>Morelos</option>
                            <option value="Nayarit" <?= $militante['estado'] == 'Nayarit' ? 'selected' : '' ?>>Nayarit</option>
                            <option value="Nuevo León" <?= $militante['estado'] == 'Nuevo León' ? 'selected' : '' ?>>Nuevo León</option>
                            <option value="Oaxaca" <?= $militante['estado'] == 'Oaxaca' ? 'selected' : '' ?>>Oaxaca</option>
                            <option value="Puebla" <?= $militante['estado'] == 'Puebla' ? 'selected' : '' ?>>Puebla</option>
                            <option value="Querétaro" <?= $militante['estado'] == 'Querétaro' ? 'selected' : '' ?>>Querétaro</option>
                            <option value="Quintana Roo" <?= $militante['estado'] == 'Quintana Roo' ? 'selected' : '' ?>>Quintana Roo</option>
                            <option value="San Luis Potosí" <?= $militante['estado'] == 'San Luis Potosí' ? 'selected' : '' ?>>San Luis Potosí</option>
                            <option value="Sinaloa" <?= $militante['estado'] == 'Sinaloa' ? 'selected' : '' ?>>Sinaloa</option>
                            <option value="Sonora" <?= $militante['estado'] == 'Sonora' ? 'selected' : '' ?>>Sonora</option>
                            <option value="Tabasco" <?= $militante['estado'] == 'Tabasco' ? 'selected' : '' ?>>Tabasco</option>
                            <option value="Tamaulipas" <?= $militante['estado'] == 'Tamaulipas' ? 'selected' : '' ?>>Tamaulipas</option>
                            <option value="Tlaxcala" <?= $militante['estado'] == 'Tlaxcala' ? 'selected' : '' ?>>Tlaxcala</option>
                            <option value="Veracruz" <?= $militante['estado'] == 'Veracruz' ? 'selected' : '' ?>>Veracruz</option>
                            <option value="Yucatán" <?= $militante['estado'] == 'Yucatán' ? 'selected' : '' ?>>Yucatán</option>
                            <option value="Zacatecas" <?= $militante['estado'] == 'Zacatecas' ? 'selected' : '' ?>>Zacatecas</option>
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
                            value="<?= htmlspecialchars($militante['municipio']) ?>"
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
                            value="<?= htmlspecialchars($militante['seccion'] ?? '') ?>"
                        >
                    </div>
                    
                    <!-- Información de contacto -->
                    <div class="col-span-3">
                        <h3 class="text-lg font-medium text-gray-700 mb-3 pb-2 border-b border-gray-200 mt-6">Información de Contacto</h3>
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
                            value="<?= htmlspecialchars($militante['telefono']) ?>"
                        >
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            value="<?= htmlspecialchars($militante['email'] ?? '') ?>"
                        >
                    </div>
                    
                    <!-- Información socioeconómica -->
                    <div class="col-span-3">
                        <h3 class="text-lg font-medium text-gray-700 mb-3 pb-2 border-b border-gray-200 mt-6">Información Socioeconómica</h3>
                    </div>
                    
                    <!-- Salario Mensual -->
                    <div>
                        <label for="salario_mensual" class="block text-sm font-medium text-gray-700 mb-1">Salario Mensual</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">$</span>
                            </div>
                            <input 
                                type="number" 
                                id="salario_mensual" 
                                name="salario_mensual" 
                                class="pl-7 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                min="0"
                                step="0.01"
                                value="<?= htmlspecialchars($militante['salario_mensual'] ?? '') ?>"
                            >
                        </div>
                    </div>
                    
                    <!-- Medio de Transporte -->
                    <div>
                        <label for="medio_transporte" class="block text-sm font-medium text-gray-700 mb-1">Género *</label>
                        <select 
                            id="medio_transporte" 
                            name="medio_transporte" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                        >
                            <option value="" disabled selected>Selecciona un medio de transporte</option>
                            <option value="auto_propio">Auto propio</option>
                            <option value="transporte_publico">Transporte público</option>
                            <option value="bicicleta">Bicicleta</option>
                            <option value="moto">Motocicleta</option>
                            <option value="a_pie">A pie</option>
                            <option value="uber_didi">Uber / Didi / Taxi</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <!-- nivel de estudios -->
                    <div>
                        <label for="nivel_estudios" class="block text-sm font-medium text-gray-700 mb-1">Nivel de estudios *</label>
                        <select 
                            id="nivel_estudios" 
                            name="nivel_estudios" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="" disabled selected>Selecciona tu nivel de estudios</option>
                            <option value="sin_estudios">Sin estudios</option>
                            <option value="primaria_incompleta">Primaria incompleta</option>
                            <option value="primaria_completa">Primaria completa</option>
                            <option value="secundaria_incompleta">Secundaria incompleta</option>
                            <option value="secundaria_completa">Secundaria completa</option>
                            <option value="preparatoria_incompleta">Preparatoria / Bachillerato incompleto</option>
                            <option value="preparatoria_completa">Preparatoria / Bachillerato completo</option>
                            <option value="tecnico">Técnico o carrera técnica</option>
                            <option value="universidad_incompleta">Universidad incompleta</option>
                            <option value="universidad_completa">Universidad completa</option>
                            <option value="posgrado">Posgrado (Maestría, Doctorado, etc.)</option>
                        </select>
                    </div>
                    
                    <!-- Información del registro -->
                    <div class="col-span-3">
                        <h3 class="text-lg font-medium text-gray-700 mb-3 pb-2 border-b border-gray-200 mt-6">Información del Registro</h3>
                    </div>
                    
                    <!-- Fecha de Registro (solo lectura) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                        <input 
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500" 
                            value="<?= date('d/m/Y H:i', strtotime($militante['created_at'])) ?>"
                            readonly
                        >
                    </div>
                    
                    <!-- Fecha de Última Actualización (solo lectura) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Última Actualización</label>
                        <input 
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500" 
                            value="<?= isset($militante['updated_at']) ? date('d/m/Y H:i', strtotime($militante['updated_at'])) : 'Sin actualizaciones' ?>"
                            readonly
                        >
                    </div>
                    
                    <!-- Registrado por (solo lectura) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Registrado por</label>
                        <input 
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500" 
                            value="<?php
                                if (isset($militante['registrado_por'])) {
                                    $conn = getDBConnection();
                                    $registradorId = (int)$militante['registrado_por'];
                                    $query = "SELECT nombre FROM usuarios WHERE id = $registradorId";
                                    $result = $conn->query($query);
                                    if ($result && $result->num_rows > 0) {
                                        echo htmlspecialchars($result->fetch_assoc()['nombre']);
                                    } else {
                                        echo 'Usuario desconocido';
                                    }
                                } else {
                                    echo 'No registrado';
                                }
                            ?>"
                            readonly
                        >
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="col-span-3 mt-6 flex justify-end">
                        <a href="<?= APP_URL ?>/admin/militantes" class="mr-4 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Cancelar
                        </a>
                        <button 
                            type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

