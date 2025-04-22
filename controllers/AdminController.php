<?php
require_once 'models/Militante.php';

class AdminController {
    private $militanteModel;
    
    public function __construct() {
        $this->militanteModel = new Militante();
    }
    
    // Muestra el dashboard del administrador
    public function dashboard() {
        // Obtener estadísticas
        $estadoPorEstado = $this->militanteModel->getCountByState();
        $totalMilitantes = 0;
        
        // Verificar que estadoPorEstado es un array antes de usarlo
        if (is_array($estadoPorEstado)) {
            $totalMilitantes = array_reduce($estadoPorEstado, function($carry, $item) {
                return $carry + $item['total'];
            }, 0);
        }
        
        require 'views/admin/dashboard.php';
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
        
        // Preparar datos del formulario
        $militanteData = [
            'nombre' => $_POST['nombre'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'] ?? '',
            'fecha_nacimiento' => $_POST['fecha_nacimiento'],
            'genero' => $_POST['genero'],
            'clave_elector' => $_POST['clave_elector'],
            'curp' => $_POST['curp'] ?? '',
            'domicilio' => $_POST['domicilio'] ?? '',
            'estado' => $_POST['estado'],
            'municipio' => $_POST['municipio'],
            'seccion' => $_POST['seccion'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'email' => $_POST['email'] ?? ''
            
        ];
        
        // Actualizar militante en la base de datos
        $success = $this->militanteModel->update($id, $militanteData);
        
        if ($success) {
            setFlashMessage('success', 'Militante actualizado correctamente.');
            redirect('admin/militantes');
        } else {
            setFlashMessage('error', 'Error al actualizar el militante. Por favor intente nuevamente.');
            redirect('admin/militantes/edit?id=' . $id);
        }
    }
    
    // Elimina un militante
    public function deleteMilitante() {
        // Verificar si se proporcionó un ID
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de militante no proporcionado');
            redirect('admin/militantes');
        }
        
        // Verificar token CSRF si se envía por POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
                setFlashMessage('error', 'Error de seguridad. Intente nuevamente.');
                redirect('admin/militantes');
            }
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
        $success = $this->militanteModel->delete($id);
        
        if ($success) {
            setFlashMessage('success', 'Militante eliminado correctamente.');
        } else {
            setFlashMessage('error', 'Error al eliminar el militante. Por favor intente nuevamente.');
        }
        
        redirect('admin/militantes');
    }
}
?>