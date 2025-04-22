<?php
require_once 'models/Militante.php';
require_once 'utils/OCRProcessor.php';

class RegistroController {
    private $militanteModel;
    private $ocrProcessor;
    
    public function __construct() {
        $this->militanteModel = new Militante();
        $this->ocrProcessor = new OCRProcessor();
    }
    
    // Muestra el formulario de registro
    public function showForm() {
        // Obtener datos del INE de la sesión si existen
        $datosINE = isset($_SESSION['datos_ine']) ? $_SESSION['datos_ine'] : null;
        
        require 'views/registro/form.php';
    }

    // Procesa la imagen del INE (solo guarda la imagen, no extrae datos)
    public function uploadINE() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }
        
        // Verificar si se envió un archivo
        if (!isset($_FILES['ine_image']) || $_FILES['ine_image']['error'] != 0) {
            http_response_code(400);
            echo json_encode(['error' => 'No se recibió imagen o hubo un error al subirla']);
            exit;
        }
        
        try {
            // Crear directorio si no existe
            $uploadDir = UPLOAD_DIR . 'ine/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generar nombre único para la imagen
            $filename = uniqid() . '_' . basename($_FILES['ine_image']['name']);
            $uploadFile = $uploadDir . $filename;
            
            // Mover archivo subido a la carpeta de uploads
            if (move_uploaded_file($_FILES['ine_image']['tmp_name'], $uploadFile)) {
                echo json_encode([
                    'success' => true,
                    'image_path' => 'ine/' . $filename
                ]);
            } else {
                throw new Exception("No se pudo guardar la imagen");
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al guardar la imagen: ' . $e->getMessage()]);
        }
        
        exit;
    }
    
    // Procesa el formulario de registro
    public function processForm() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('register');
        }
        
        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Error de seguridad. Intente nuevamente.');
            redirect('register');
        }
        
        // Validar campos requeridos
        $requiredFields = ['nombre', 'apellido_paterno', 'fecha_nacimiento', 'genero', 'clave_elector', 'estado', 'municipio', 'telefono'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                setFlashMessage('error', 'Por favor complete todos los campos requeridos.');
                redirect('register');
            }
        }
        
        // Verificar si la clave de elector ya existe
        if ($this->militanteModel->existsClaveElector($_POST['clave_elector'])) {
            setFlashMessage('error', 'Esta clave de elector ya está registrada.');
            redirect('register');
        }
        
        // Preparar datos del formulario
        $imagen_ine = '';
        
        // Procesar imagen del INE si existe
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
            }
        }
        
        // Crear militante en la base de datos
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
            'telefono' => $_POST['telefono'],
            'email' => $_POST['email'] ?? '',
            'imagen_ine' => $imagen_ine
        ];
        
        $id = $this->militanteModel->create($militanteData);
        
        if ($id) {
            // Limpiar datos del INE de la sesión
            if (isset($_SESSION['datos_ine'])) {
                unset($_SESSION['datos_ine']);
            }
            
            setFlashMessage('success', '¡Registro exitoso! Gracias por afiliarte.');
            redirect('home');
        } else {
            setFlashMessage('error', 'Error al registrar. Por favor intente nuevamente.');
            redirect('register');
        }
    }
    
    // Procesa la imagen del INE para extraer datos
    public function processINE() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }
        
        // Verificar si se envió un archivo
        if (!isset($_FILES['ine_image']) || $_FILES['ine_image']['error'] != 0) {
            http_response_code(400);
            echo json_encode(['error' => 'No se recibió imagen o hubo un error al subirla']);
            exit;
        }
        
        // Procesar la imagen con OCR
        try {
            $tempFile = $_FILES['ine_image']['tmp_name'];
            $datos = $this->ocrProcessor->processINEImage($tempFile);
            
            // Guardar datos en sesión para pre-llenar el formulario
            $_SESSION['datos_ine'] = $datos;
            
            // Devolver datos extraídos
            echo json_encode([
                'success' => true,
                'datos' => $datos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al procesar la imagen: ' . $e->getMessage()]);
        }
        
        exit;
    }
}
?>