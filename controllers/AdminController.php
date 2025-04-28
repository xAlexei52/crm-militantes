<?php
require_once 'models/Militante.php';

class AdminController {
    private $militanteModel;
    
    public function __construct() {
        $this->militanteModel = new Militante();
    }
    
    // Reemplazar la función dashboard en el AdminController con esta versión actualizada

// Muestra el dashboard del administrador
    public function dashboard() {
        try {
            // Obtener conteo de militantes por estado
            $estadoPorEstado = $this->militanteModel->getCountByState();
            
            // Obtener total de militantes
            $totalMilitantes = array_reduce($estadoPorEstado, function($carry, $item) {
                return $carry + $item['total'];
            }, 0);
            
            // Obtener actividades recientes (últimos 5 militantes registrados)
            $actividadesRecientes = $this->militanteModel->getRecentActivity(5);
            
            // Obtener estadísticas de crecimiento
            $crecimientoMensual = $this->militanteModel->getMonthlyGrowth();
            
            // Cargar vista
            require 'views/admin/dashboard.php';
        } catch (Exception $e) {
            // Log del error
            error_log("Error en dashboard: " . $e->getMessage());
            
            // Mostrar error genérico al usuario
            setFlashMessage('error', 'Ocurrió un error al cargar el dashboard');
            redirect('admin/dashboard');
        }
    }
    // Lista todos los militantes con filtros y paginación
    public function listMilitantes() {
        try {
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
            
            $filters = [];
            $validFilters = ['nombre', 'clave_elector', 'estado', 'municipio'];
            
            foreach ($validFilters as $filter) {
                if (isset($_GET[$filter]) && !empty($_GET[$filter])) {
                    $filters[$filter] = $_GET[$filter];
                }
            }
            
            $result = $this->militanteModel->getAll($page, $limit, $filters);
            
            require 'views/admin/militantes/list.php';
        } catch (Exception $e) {
            // Log del error
            error_log("Error en listMilitantes: " . $e->getMessage());
            
            // Mostrar error genérico al usuario
            $result = [
                'militantes' => [],
                'total' => 0,
                'pages' => 1,
                'current_page' => 1,
                'error' => 'Ocurrió un error al obtener los militantes'
            ];
            
            require 'views/admin/militantes/list.php';
        }
    }
    
    // Muestra el formulario de edición de militante
    public function editMilitante() {
        // Verificar si se proporcionó un ID
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de militante no proporcionado');
            redirect('admin/militantes');
        }
        
        $id = (int)$_GET['id'];
        $militante = $this->militanteModel->getById($id);
        
        if (!$militante) {
            setFlashMessage('error', 'Militante no encontrado');
            redirect('admin/militantes');
        }
        
        require 'views/admin/militantes/edit.php';
    }
    
    // Guarda los cambios en un militante
    public function saveMilitante() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('admin/militantes');
    }
    
    // Verificar CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Error de seguridad. Intente nuevamente.');
        redirect('admin/militantes');
    }
    
    // Verificar si se proporcionó un ID
    if (!isset($_POST['id'])) {
        setFlashMessage('error', 'ID de militante no proporcionado');
        redirect('admin/militantes');
    }
    
    $id = (int)$_POST['id'];
    
    // Validar campos requeridos
    $requiredFields = ['nombre', 'apellido_paterno', 'fecha_nacimiento', 'clave_elector', 'estado', 'municipio'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            setFlashMessage('error', 'Por favor complete todos los campos requeridos.');
            redirect('admin/militantes/edit?id=' . $id);
        }
    }
    
    // Verificar si la clave de elector ya existe en otro registro
    if ($this->militanteModel->existsClaveElector($_POST['clave_elector'], $id)) {
        setFlashMessage('error', 'Esta clave de elector ya está registrada para otro militante.');
        redirect('admin/militantes/edit?id=' . $id);
    }
    
    // Calcular edad basado en fecha de nacimiento
    $edad = null;
    if (!empty($_POST['fecha_nacimiento'])) {
        $fechaNac = new DateTime($_POST['fecha_nacimiento']);
        $hoy = new DateTime();
        $edad = $hoy->diff($fechaNac)->y;
    }
    
    // Procesar imagen del INE si se subió una nueva
    $imagen_ine = '';
    $militante = $this->militanteModel->getById($id);
    
    if (isset($_FILES['imagen_ine']) && $_FILES['imagen_ine']['error'] == 0) {
        $uploadDir = UPLOAD_DIR . 'ine/';
        
        // Crear directorio si no existe
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generar nombre único para la imagen
        $filename = uniqid() . '_' . basename($_FILES['imagen_ine']['name']);
        $uploadFile = $uploadDir . $filename;
        
        // Mover archivo subido a la carpeta de uploads
        if (move_uploaded_file($_FILES['imagen_ine']['tmp_name'], $uploadFile)) {
            $imagen_ine = 'ine/' . $filename;
            
            // Eliminar imagen anterior si existe
            if (!empty($militante['imagen_ine'])) {
                $oldImagePath = UPLOAD_DIR . $militante['imagen_ine'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }
    } else {
        // Mantener la imagen existente
        $imagen_ine = $militante['imagen_ine'] ?? '';
    }
    
    // Preparar datos para actualizar
    $militanteData = [
        // Información personal
        'nombre' => $_POST['nombre'],
        'apellido_paterno' => $_POST['apellido_paterno'],
        'apellido_materno' => $_POST['apellido_materno'] ?? '',
        'fecha_nacimiento' => $_POST['fecha_nacimiento'],
        'genero' => $_POST['genero'],
        'edad' => $edad,
        'lugar_nacimiento' => $_POST['lugar_nacimiento'] ?? '',
        
        // Identificación
        'clave_elector' => $_POST['clave_elector'],
        'curp' => $_POST['curp'] ?? '',
        'folio_nacional' => $_POST['folio_nacional'] ?? '',
        'fecha_inscripcion_padron' => $_POST['fecha_inscripcion_padron'] ?? '',
        
        // Domicilio
        'domicilio' => $_POST['domicilio'] ?? '',
        'calle' => $_POST['calle'] ?? '',
        'numero_exterior' => $_POST['numero_exterior'] ?? '',
        'numero_interior' => $_POST['numero_interior'] ?? '',
        'colonia' => $_POST['colonia'] ?? '',
        'codigo_postal' => $_POST['codigo_postal'] ?? '',
        'estado' => $_POST['estado'],
        'municipio' => $_POST['municipio'],
        'seccion' => $_POST['seccion'] ?? '',
        
        // Contacto
        'telefono' => $_POST['telefono'] ?? '',
        'email' => $_POST['email'] ?? '',
        
        // Información socioeconómica
        'salario_mensual' => isset($_POST['salario_mensual']) && $_POST['salario_mensual'] !== '' ? $_POST['salario_mensual'] : null,
        'medio_transporte' => isset($_POST['medio_transporte']) && $_POST['medio_transporte'] !== '' ? $_POST['medio_transporte'] : null,
        'nivel_estudios' => isset($_POST['nivel_estudios']) && $_POST['nivel_estudios'] !== '' ? $_POST['nivel_estudios'] : null,
        
        // Imagen
        'imagen_ine' => $imagen_ine
    ];
    
    // Actualizar militante en la base de datos
    $success = $this->militanteModel->update($id, $militanteData);
    
    if ($success) {
        // Registrar la actividad en el log si existe la función
        if (function_exists('logActivity')) {
            logActivity('actualización', 'Militante actualizado', 'militante', $id);
        }
        
        setFlashMessage('success', 'Militante actualizado correctamente.');
        redirect('admin/militantes');
    } else {
        setFlashMessage('error', 'Error al actualizar el militante. Por favor intente nuevamente.');
        redirect('admin/militantes/edit?id=' . $id);
    }
}
    
    // Elimina un militante// Elimina un militante
public function deleteMilitante() {
    // Verificar si se proporcionó un ID
    if (!isset($_GET['id'])) {
        setFlashMessage('error', 'ID de militante no proporcionado');
        redirect('admin/militantes');
    }
    
    $id = (int)$_GET['id'];
    
    // Confirmar eliminación si se accede por GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $militante = $this->militanteModel->getById($id);
        
        if (!$militante) {
            setFlashMessage('error', 'Militante no encontrado');
            redirect('admin/militantes');
        }
        
        require 'views/admin/militantes/delete.php';
        return;
    }
    
    // Proceso de eliminación si se accede por POST
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Error de seguridad. Intente nuevamente.');
        redirect('admin/militantes');
    }
    
    // Obtener datos del militante para poder eliminar la imagen después
    $militante = $this->militanteModel->getById($id);
    
    // Eliminar el registro de la base de datos
    $success = $this->militanteModel->delete($id);
    
    if ($success) {
        // Si hay una imagen asociada, eliminarla
        if (!empty($militante['imagen_ine'])) {
            $imagePath = UPLOAD_DIR . $militante['imagen_ine'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        // Registrar la actividad en el log si existe la función
        if (function_exists('logActivity')) {
            logActivity('eliminación', 'Militante eliminado', 'militante', $id);
        }
        
        setFlashMessage('success', 'Militante eliminado correctamente.');
    } else {
        setFlashMessage('error', 'Error al eliminar el militante. Por favor intente nuevamente.');
    }
    
    redirect('admin/militantes');
}



// Muestra los detalles de un militante
public function viewMilitante() {
    // Verificar si se proporcionó un ID
    if (!isset($_GET['id'])) {
        setFlashMessage('error', 'ID de militante no proporcionado');
        redirect('admin/militantes');
    }
    
    $id = (int)$_GET['id'];
    
    // Obtener datos del militante
    $militante = $this->militanteModel->getById($id);
    
    if (!$militante) {
        setFlashMessage('error', 'Militante no encontrado');
        redirect('admin/militantes');
    }
    
    // Cargar la vista de detalles
    require 'views/admin/militantes/details.php';
}


}
?>