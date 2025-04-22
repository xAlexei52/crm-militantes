<?php
class Militante {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    // Obtener todos los militantes con paginación y filtros
    public function getAll($page = 1, $limit = 10, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        $whereClause = "WHERE 1=1";
        
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
            $whereClause .= " AND municipio = '$municipio'";
        }
        
        // Consulta para obtener militantes con paginación
        $query = "SELECT * FROM militantes $whereClause ORDER BY created_at DESC LIMIT $offset, $limit";
        $result = $this->conn->query($query);
        
        $militantes = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $militantes[] = $row;
            }
        }
        
        // Consulta para contar el total de militantes (para paginación)
        $countQuery = "SELECT COUNT(*) as total FROM militantes $whereClause";
        $countResult = $this->conn->query($countQuery);
        $totalCount = 0;
        
        if ($countResult && $countResult->num_rows > 0) {
            $totalCount = $countResult->fetch_assoc()['total'];
        }
        
        return [
            'militantes' => $militantes,
            'total' => $totalCount,
            'pages' => ceil($totalCount / $limit),
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
    
    // Crear nuevo militante
    public function create($data) {
        // Calcular edad basado en fecha de nacimiento
        $edad = null;
        if (!empty($data['fecha_nacimiento'])) {
            $fechaNac = new DateTime($data['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNac)->y;
        }
        
        // Campos obligatorios
        $nombre = sanitizeInput($data['nombre']);
        $apellido_paterno = sanitizeInput($data['apellido_paterno']);
        $apellido_materno = isset($data['apellido_materno']) ? sanitizeInput($data['apellido_materno']) : '';
        $fecha_nacimiento = sanitizeInput($data['fecha_nacimiento']);
        $genero = sanitizeInput($data['genero']);
        $clave_elector = sanitizeInput($data['clave_elector']);
        $estado = sanitizeInput($data['estado']);
        $municipio = sanitizeInput($data['municipio']);
        
        // Campos adicionales (pueden ser nulos)
        $edad = isset($edad) ? $edad : 'NULL';
        $lugar_nacimiento = isset($data['lugar_nacimiento']) ? "'" . sanitizeInput($data['lugar_nacimiento']) . "'" : 'NULL';
        $curp = isset($data['curp']) ? "'" . sanitizeInput($data['curp']) . "'" : 'NULL';
        $folio_nacional = isset($data['folio_nacional']) ? "'" . sanitizeInput($data['folio_nacional']) . "'" : 'NULL';
        $fecha_inscripcion_padron = isset($data['fecha_inscripcion_padron']) ? "'" . sanitizeInput($data['fecha_inscripcion_padron']) . "'" : 'NULL';
        $domicilio = isset($data['domicilio']) ? "'" . sanitizeInput($data['domicilio']) . "'" : 'NULL';
        $calle = isset($data['calle']) ? "'" . sanitizeInput($data['calle']) . "'" : 'NULL';
        $codigo_postal = isset($data['codigo_postal']) ? "'" . sanitizeInput($data['codigo_postal']) . "'" : 'NULL';
        $numero_exterior = isset($data['numero_exterior']) ? "'" . sanitizeInput($data['numero_exterior']) . "'" : 'NULL';
        $numero_interior = isset($data['numero_interior']) ? "'" . sanitizeInput($data['numero_interior']) . "'" : 'NULL';
        $colonia = isset($data['colonia']) ? "'" . sanitizeInput($data['colonia']) . "'" : 'NULL';
        $seccion = isset($data['seccion']) ? "'" . sanitizeInput($data['seccion']) . "'" : 'NULL';
        $telefono = isset($data['telefono']) ? "'" . sanitizeInput($data['telefono']) . "'" : 'NULL';
        $email = isset($data['email']) ? "'" . sanitizeInput($data['email']) . "'" : 'NULL';
        $imagen_ine = isset($data['imagen_ine']) ? "'" . sanitizeInput($data['imagen_ine']) . "'" : 'NULL';
        $salario_mensual = isset($data['salario_mensual']) ? (float)$data['salario_mensual'] : 'NULL';
        $medio_transporte = isset($data['medio_transporte']) ? "'" . sanitizeInput($data['medio_transporte']) . "'" : 'NULL';
        $nivel_estudios = isset($data['nivel_estudios']) ? "'" . sanitizeInput($data['nivel_estudios']) . "'" : 'NULL';
        $registrado_por = isset($data['registrado_por']) ? (int)$data['registrado_por'] : 'NULL';
        
        // Construir la consulta (utilizando los campos que no son NULL)
        $query = "INSERT INTO militantes (
                    nombre, apellido_paterno, apellido_materno, fecha_nacimiento, 
                    edad, lugar_nacimiento, genero, clave_elector, curp, folio_nacional,
                    fecha_inscripcion_padron, domicilio, calle, codigo_postal, 
                    numero_exterior, numero_interior, colonia, estado, municipio, 
                    seccion, telefono, email, imagen_ine, salario_mensual,
                    medio_transporte, nivel_estudios, registrado_por, created_at
                  ) VALUES (
                    '$nombre', '$apellido_paterno', '$apellido_materno', '$fecha_nacimiento', 
                    $edad, $lugar_nacimiento, '$genero', '$clave_elector', $curp, $folio_nacional,
                    $fecha_inscripcion_padron, $domicilio, $calle, $codigo_postal, 
                    $numero_exterior, $numero_interior, $colonia, '$estado', '$municipio', 
                    $seccion, $telefono, $email, $imagen_ine, $salario_mensual,
                    $medio_transporte, $nivel_estudios, $registrado_por, NOW()
                  )";
        
        if ($this->conn->query($query)) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    // Actualizar militante
    public function update($id, $data) {
        $id = (int)$id;
        
        // Calcular edad basado en fecha de nacimiento si está proporcionada
        $edadStr = '';
        if (!empty($data['fecha_nacimiento'])) {
            $fechaNac = new DateTime($data['fecha_nacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNac)->y;
            $edadStr = "edad = $edad,";
        }
        
        // Construir la parte SET de la consulta con los campos proporcionados
        $setParts = [];
        
        // Campos básicos
        if (isset($data['nombre'])) $setParts[] = "nombre = '" . sanitizeInput($data['nombre']) . "'";
        if (isset($data['apellido_paterno'])) $setParts[] = "apellido_paterno = '" . sanitizeInput($data['apellido_paterno']) . "'";
        if (isset($data['apellido_materno'])) $setParts[] = "apellido_materno = '" . sanitizeInput($data['apellido_materno']) . "'";
        if (isset($data['fecha_nacimiento'])) $setParts[] = "fecha_nacimiento = '" . sanitizeInput($data['fecha_nacimiento']) . "'";
        if (isset($data['genero'])) $setParts[] = "genero = '" . sanitizeInput($data['genero']) . "'";
        if (isset($data['clave_elector'])) $setParts[] = "clave_elector = '" . sanitizeInput($data['clave_elector']) . "'";
        
        // Campos adicionales
        if (isset($data['lugar_nacimiento'])) $setParts[] = "lugar_nacimiento = '" . sanitizeInput($data['lugar_nacimiento']) . "'";
        if (isset($data['curp'])) $setParts[] = "curp = '" . sanitizeInput($data['curp']) . "'";
        if (isset($data['folio_nacional'])) $setParts[] = "folio_nacional = '" . sanitizeInput($data['folio_nacional']) . "'";
        if (isset($data['fecha_inscripcion_padron'])) $setParts[] = "fecha_inscripcion_padron = '" . sanitizeInput($data['fecha_inscripcion_padron']) . "'";
        if (isset($data['domicilio'])) $setParts[] = "domicilio = '" . sanitizeInput($data['domicilio']) . "'";
        if (isset($data['calle'])) $setParts[] = "calle = '" . sanitizeInput($data['calle']) . "'";
        if (isset($data['codigo_postal'])) $setParts[] = "codigo_postal = '" . sanitizeInput($data['codigo_postal']) . "'";
        if (isset($data['numero_exterior'])) $setParts[] = "numero_exterior = '" . sanitizeInput($data['numero_exterior']) . "'";
        if (isset($data['numero_interior'])) $setParts[] = "numero_interior = '" . sanitizeInput($data['numero_interior']) . "'";
        if (isset($data['colonia'])) $setParts[] = "colonia = '" . sanitizeInput($data['colonia']) . "'";
        if (isset($data['estado'])) $setParts[] = "estado = '" . sanitizeInput($data['estado']) . "'";
        if (isset($data['municipio'])) $setParts[] = "municipio = '" . sanitizeInput($data['municipio']) . "'";
        if (isset($data['seccion'])) $setParts[] = "seccion = '" . sanitizeInput($data['seccion']) . "'";
        if (isset($data['telefono'])) $setParts[] = "telefono = '" . sanitizeInput($data['telefono']) . "'";
        if (isset($data['email'])) $setParts[] = "email = '" . sanitizeInput($data['email']) . "'";
        if (isset($data['salario_mensual'])) $setParts[] = "salario_mensual = " . (float)$data['salario_mensual'];
        if (isset($data['medio_transporte'])) $setParts[] = "medio_transporte = '" . sanitizeInput($data['medio_transporte']) . "'";
        if (isset($data['nivel_estudios'])) $setParts[] = "nivel_estudios = '" . sanitizeInput($data['nivel_estudios']) . "'";
        
        // Incluir la edad si la calculamos
        if (!empty($edadStr)) {
            $setParts[] = trim($edadStr, ',');
        }
        
        // Añadir timestamp de actualización
        $setParts[] = "updated_at = NOW()";
        
        // Construir la consulta final
        $setClause = implode(', ', $setParts);
        $query = "UPDATE militantes SET $setClause WHERE id = $id";
        
        return $this->conn->query($query);
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
        
        if ($excludeId) {
            $excludeId = (int)$excludeId;
            $query .= " AND id != $excludeId";
        }
        
        $result = $this->conn->query($query);
        return ($result && $result->num_rows > 0);
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