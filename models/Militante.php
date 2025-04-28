<?php
class Militante {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }

    // Agregar estos métodos al modelo Militante.php

/**
 * Obtiene las actividades recientes (últimos militantes registrados)
 * @param int $limit Número de registros a obtener
 * @return array Lista de actividades recientes
 */
public function getRecentActivity($limit = 5) {
    $limit = (int)$limit;
    $query = "SELECT m.id, m.nombre, m.apellido_paterno, m.apellido_materno, 
                     m.estado, m.municipio, m.created_at, u.nombre as registrado_por
              FROM militantes m
              LEFT JOIN usuarios u ON m.registrado_por = u.id
              ORDER BY m.created_at DESC
              LIMIT $limit";
    
    $result = $this->conn->query($query);
    
    $actividades = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $actividades[] = $row;
        }
    }
    
    return $actividades;
}

    /**
     * Obtiene estadísticas de crecimiento mensual
     * @param int $months Número de meses a comparar
     * @return array Estadísticas de crecimiento
     */
    public function getMonthlyGrowth($months = 2) {
        $months = (int)$months;
        
        // Obtener el mes actual y el mes anterior
        $currentMonth = date('Y-m');
        $previousMonth = date('Y-m', strtotime('-1 month'));
        
        // Contar militantes del mes actual
        $queryCurrentMonth = "SELECT COUNT(*) as total FROM militantes 
                            WHERE DATE_FORMAT(created_at, '%Y-%m') = '$currentMonth'";
        $resultCurrentMonth = $this->conn->query($queryCurrentMonth);
        $currentTotal = 0;
        
        if ($resultCurrentMonth && $resultCurrentMonth->num_rows > 0) {
            $currentTotal = $resultCurrentMonth->fetch_assoc()['total'];
        }
        
        // Contar militantes del mes anterior
        $queryPreviousMonth = "SELECT COUNT(*) as total FROM militantes 
                            WHERE DATE_FORMAT(created_at, '%Y-%m') = '$previousMonth'";
        $resultPreviousMonth = $this->conn->query($queryPreviousMonth);
        $previousTotal = 0;
        
        if ($resultPreviousMonth && $resultPreviousMonth->num_rows > 0) {
            $previousTotal = $resultPreviousMonth->fetch_assoc()['total'];
        }
        
        // Calcular porcentaje de crecimiento
        $crecimiento = 0;
        if ($previousTotal > 0) {
            $crecimiento = (($currentTotal - $previousTotal) / $previousTotal) * 100;
        } elseif ($currentTotal > 0) {
            $crecimiento = 100; // Si el mes anterior era 0, el crecimiento es 100%
        }
        
        return [
            'mes_actual' => [
                'periodo' => date('F Y'), // Nombre del mes y año
                'total' => $currentTotal
            ],
            'mes_anterior' => [
                'periodo' => date('F Y', strtotime('-1 month')),
                'total' => $previousTotal
            ],
            'crecimiento_porcentaje' => round($crecimiento, 1),
            'tendencia' => $crecimiento >= 0 ? 'positiva' : 'negativa'
        ];
    }

    /**
     * Obtiene estadísticas de género
     * @return array Conteo por género
     */
    public function getGenderStats() {
        $query = "SELECT genero, COUNT(*) as total FROM militantes GROUP BY genero";
        $result = $this->conn->query($query);
        
        $stats = [
            'M' => 0,
            'F' => 0,
            'O' => 0
        ];
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $stats[$row['genero']] = (int)$row['total'];
            }
        }
        
        return $stats;
    }
    
    // Obtener todos los militantes con paginación y filtros
    public function getAll($page = 1, $limit = 10, $filters = []) {
        // Validar parámetros
        $page = max(1, intval($page));
        $limit = max(1, intval($limit));
        $offset = ($page - 1) * $limit;
        
        // Construir la cláusula WHERE base
        $whereClause = "WHERE 1=1";
        $params = [];
        
        // Aplicar filtros si existen
        if (!empty($filters['nombre'])) {
            $nombre = sanitizeInput($filters['nombre']);
            $whereClause .= " AND (nombre LIKE '%$nombre%' OR apellido_paterno LIKE '%$nombre%' OR apellido_materno LIKE '%$nombre%')";
        }
        
        if (!empty($filters['clave_elector'])) {
            $claveElector = sanitizeInput($filters['clave_elector']);
            $whereClause .= " AND clave_elector LIKE '%$claveElector%'";
        }
        
        if (!empty($filters['estado'])) {
            $estado = sanitizeInput($filters['estado']);
            $whereClause .= " AND estado = '$estado'";
        }
        
        if (!empty($filters['municipio'])) {
            $municipio = sanitizeInput($filters['municipio']);
            $whereClause .= " AND municipio LIKE '%$municipio%'";
        }
        
        // Consulta para contar el total de militantes (para paginación)
        $countQuery = "SELECT COUNT(*) as total FROM militantes $whereClause";
        $countResult = $this->conn->query($countQuery);
        $totalCount = 0;
        
        if ($countResult && $countResult->num_rows > 0) {
            $totalCount = $countResult->fetch_assoc()['total'];
        }
        
        // Calcular total de páginas
        $totalPages = ceil($totalCount / $limit);
        
        // Si la página solicitada es mayor que el total de páginas, ir a la última página
        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }
        
        // Consulta para obtener militantes con paginación
        $query = "SELECT * FROM militantes $whereClause ORDER BY created_at DESC LIMIT $offset, $limit";
        
        // Depuración
        error_log("SQL Query: $query", 3, __DIR__ . '/../logs/sql.log');
        
        $result = $this->conn->query($query);
        
        $militantes = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $militantes[] = $row;
            }
        }
        
        return [
            'militantes' => $militantes,
            'total' => $totalCount,
            'pages' => $totalPages,
            'current_page' => $page
        ];
    }
    
    // Obtener un militante por ID
    public function getById($id) {
        $id = (int)$id;
        $query = "SELECT * FROM militantes WHERE id = $id LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    public function create($data) {
        // Calcular edad basada en fecha de nacimiento
        $edad = null;
        if (!empty($data['fecha_nacimiento'])) {
            $fechaNac = new DateTime($data['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNac)->y;
        }
        
        // Preparar campos obligatorios
        $nombre = sanitizeInput($data['nombre']);
        $apellido_paterno = sanitizeInput($data['apellido_paterno']);
        $apellido_materno = isset($data['apellido_materno']) ? sanitizeInput($data['apellido_materno']) : '';
        $fecha_nacimiento = sanitizeInput($data['fecha_nacimiento']);
        $genero = sanitizeInput($data['genero']);
        $clave_elector = sanitizeInput($data['clave_elector']);
        $estado = sanitizeInput($data['estado']);
        $municipio = sanitizeInput($data['municipio']);
        
        // Preparar valores para campos opcionales evitando 'NULL' como string
        $edad_val = isset($edad) ? $edad : 'NULL';
        $lugar_nacimiento_val = !empty($data['lugar_nacimiento']) ? "'" . sanitizeInput($data['lugar_nacimiento']) . "'" : 'NULL';
        $curp_val = !empty($data['curp']) ? "'" . sanitizeInput($data['curp']) . "'" : 'NULL';
        $folio_nacional_val = !empty($data['folio_nacional']) ? "'" . sanitizeInput($data['folio_nacional']) . "'" : 'NULL';
        $fecha_inscripcion_padron_val = !empty($data['fecha_inscripcion_padron']) ? "'" . sanitizeInput($data['fecha_inscripcion_padron']) . "'" : 'NULL';
        $domicilio_val = !empty($data['domicilio']) ? "'" . sanitizeInput($data['domicilio']) . "'" : 'NULL';
        $calle_val = !empty($data['calle']) ? "'" . sanitizeInput($data['calle']) . "'" : 'NULL';
        $codigo_postal_val = !empty($data['codigo_postal']) ? "'" . sanitizeInput($data['codigo_postal']) . "'" : 'NULL';
        $numero_exterior_val = !empty($data['numero_exterior']) ? "'" . sanitizeInput($data['numero_exterior']) . "'" : 'NULL';
        $numero_interior_val = !empty($data['numero_interior']) ? "'" . sanitizeInput($data['numero_interior']) . "'" : 'NULL';
        $colonia_val = !empty($data['colonia']) ? "'" . sanitizeInput($data['colonia']) . "'" : 'NULL';
        $seccion_val = !empty($data['seccion']) ? "'" . sanitizeInput($data['seccion']) . "'" : 'NULL';
        $telefono_val = !empty($data['telefono']) ? "'" . sanitizeInput($data['telefono']) . "'" : 'NULL';
        $email_val = !empty($data['email']) ? "'" . sanitizeInput($data['email']) . "'" : 'NULL';
        $imagen_ine_val = !empty($data['imagen_ine']) ? "'" . sanitizeInput($data['imagen_ine']) . "'" : 'NULL';
        
        // Campos numéricos o decimales
        $salario_mensual_val = isset($data['salario_mensual']) && $data['salario_mensual'] !== '' ? (float)$data['salario_mensual'] : 'NULL';
        $registrado_por_val = isset($data['registrado_por']) && $data['registrado_por'] !== '' ? (int)$data['registrado_por'] : 'NULL';
        
        // Otros campos de texto
        $medio_transporte_val = !empty($data['medio_transporte']) ? "'" . sanitizeInput($data['medio_transporte']) . "'" : 'NULL';
        $nivel_estudios_val = !empty($data['nivel_estudios']) ? "'" . sanitizeInput($data['nivel_estudios']) . "'" : 'NULL';
        
        // Construir la consulta (NO incluimos el campo ID para que MySQL use AUTO_INCREMENT)
        $query = "INSERT INTO militantes (
                    nombre, apellido_paterno, apellido_materno, fecha_nacimiento, 
                    edad, lugar_nacimiento, genero, clave_elector, curp, folio_nacional,
                    fecha_inscripcion_padron, domicilio, calle, codigo_postal, 
                    numero_exterior, numero_interior, colonia, estado, municipio, 
                    seccion, telefono, email, imagen_ine, salario_mensual,
                    medio_transporte, nivel_estudios, registrado_por, created_at
                  ) VALUES (
                    '$nombre', '$apellido_paterno', '$apellido_materno', '$fecha_nacimiento', 
                    $edad_val, $lugar_nacimiento_val, '$genero', '$clave_elector', $curp_val, $folio_nacional_val,
                    $fecha_inscripcion_padron_val, $domicilio_val, $calle_val, $codigo_postal_val, 
                    $numero_exterior_val, $numero_interior_val, $colonia_val, '$estado', '$municipio', 
                    $seccion_val, $telefono_val, $email_val, $imagen_ine_val, $salario_mensual_val,
                    $medio_transporte_val, $nivel_estudios_val, $registrado_por_val, NOW()
                  )";
        
        // Depuración (guarda la consulta en un archivo de log)
        error_log('SQL Query: ' . $query, 3, __DIR__ . '/../logs/sql.log');
        
        if ($this->conn->query($query)) {
            return $this->conn->insert_id;
        }
        
        // Si hay error, registrarlo
        error_log('SQL Error: ' . $this->conn->error, 3, __DIR__ . '/../logs/sql_error.log');
        return false;
    }
    
    public function update($id, $data) {
        $id = (int)$id;
        
        // Calcular edad basado en fecha de nacimiento si está proporcionada
        if (!empty($data['fecha_nacimiento'])) {
            $fechaNac = new DateTime($data['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNac)->y;
            $data['edad'] = $edad; // Añadir la edad calculada a los datos
        }
        
        // Construir la parte SET de la consulta con los campos proporcionados
        $setParts = [];
        
        // Campos de texto básicos
        $textFields = [
            'nombre', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento',
            'genero', 'clave_elector', 'lugar_nacimiento', 'curp', 'folio_nacional',
            'fecha_inscripcion_padron', 'domicilio', 'calle', 'codigo_postal',
            'numero_exterior', 'numero_interior', 'colonia', 'estado', 'municipio',
            'seccion', 'telefono', 'email', 'imagen_ine', 'medio_transporte', 'nivel_estudios'
        ];
        
        foreach ($textFields as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $setParts[] = "$field = '" . sanitizeInput($data[$field]) . "'";
            } else if (isset($data[$field]) && $data[$field] === '') {
                $setParts[] = "$field = NULL";
            }
        }
        
        // Campos numéricos
        if (isset($data['edad'])) {
            $setParts[] = "edad = " . (int)$data['edad'];
        }
        
        if (isset($data['salario_mensual'])) {
            if ($data['salario_mensual'] === '' || $data['salario_mensual'] === null) {
                $setParts[] = "salario_mensual = NULL";
            } else {
                $setParts[] = "salario_mensual = " . (float)$data['salario_mensual'];
            }
        }
        
        // Añadir timestamp de actualización
        $setParts[] = "updated_at = NOW()";
        
        // Verificar que haya cambios para actualizar
        if (empty($setParts)) {
            return true; // No hay cambios que hacer, consideramos exitoso
        }
        
        // Construir la consulta final
        $setClause = implode(', ', $setParts);
        $query = "UPDATE militantes SET $setClause WHERE id = $id";
        
        // Depuración (guarda la consulta en un archivo de log)
        error_log('SQL Update Query: ' . $query, 3, __DIR__ . '/../logs/sql.log');
        
        $result = $this->conn->query($query);
        
        if (!$result) {
            error_log('SQL Update Error: ' . $this->conn->error, 3, __DIR__ . '/../logs/sql_error.log');
        }
        
        return $result;
    }
    
    // Eliminar militante
    public function delete($id) {
        $id = (int)$id;
        $query = "DELETE FROM militantes WHERE id = $id";
        return $this->conn->query($query);
    }
    
    // Verificar si ya existe clave electoral
    public function existsClaveElector($claveElector, $excludeId = null) {
        $claveElector = sanitizeInput($claveElector);
        $query = "SELECT id FROM militantes WHERE clave_elector = '$claveElector'";
        
        if ($excludeId !== null) {
            $excludeId = (int)$excludeId;
            $query .= " AND id != $excludeId";
        }
        
        // Depuración
        error_log('existsClaveElector Query: ' . $query, 3, __DIR__ . '/../logs/sql.log');
        
        $result = $this->conn->query($query);
        $exists = ($result && $result->num_rows > 0);
        
        // Depuración
        error_log('existsClaveElector Result: ' . ($exists ? 'true' : 'false'), 3, __DIR__ . '/../logs/sql.log');
        
        return $exists;
    }
    
    // Obtener conteo de militantes por estado
    public function getCountByState() {
        $query = "SELECT estado, COUNT(*) as total FROM militantes GROUP BY estado ORDER BY total DESC";
        $result = $this->conn->query($query);
        
        $stats = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $stats[] = $row;
            }
        }
        
        return $stats;
    }
    
    // Obtener militantes para envío de mensajes (con filtros)
    public function getForMessages($filters = []) {
        $whereClause = "WHERE telefono IS NOT NULL AND telefono != ''";
        
        // Aplicar filtros si existen
        if (!empty($filters['estado'])) {
            $estado = sanitizeInput($filters['estado']);
            $whereClause .= " AND estado = '$estado'";
        }
        
        if (!empty($filters['municipio'])) {
            $municipio = sanitizeInput($filters['municipio']);
            $whereClause .= " AND municipio = '$municipio'";
        }
        
        if (!empty($filters['genero'])) {
            $genero = sanitizeInput($filters['genero']);
            $whereClause .= " AND genero = '$genero'";
        }
        
        $query = "SELECT id, nombre, apellido_paterno, apellido_materno, telefono FROM militantes $whereClause";
        $result = $this->conn->query($query);
        
        $militantes = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $militantes[] = $row;
            }
        }
        
        return $militantes;
    }
}
?>