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
                // Crear directorio para logs si no existe
                $logDir = __DIR__ . '/../logs/';
                if (!file_exists($logDir)) {
                    mkdir($logDir, 0755, true);
                }
                
                // Registrar la subida en el log
                error_log(date('Y-m-d H:i:s') . " - INE subida exitosamente: {$filename}\n", 3, $logDir . 'uploads.log');
                
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
        
        // Procesar imagen del INE si existe
        $imagen_ine = '';
        
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
        } else if (isset($_POST['imagen_ine_path']) && !empty($_POST['imagen_ine_path'])) {
            // Si se preseleccionó la imagen en el paso de OCR
            $imagen_ine = $_POST['imagen_ine_path'];
        }
        
        // Recuperar ID de usuario si está logueado
        $registrado_por = null;
        if (isLoggedIn() && isset($_SESSION['user_id'])) {
            $registrado_por = $_SESSION['user_id'];
        }
        
        // Preparar fecha para la base de datos
        $fechaNacimiento = $_POST['fecha_nacimiento'];
        // Asegurarse de que la fecha esté en formato YYYY-MM-DD para MySQL
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $fechaNacimiento, $matches)) {
            $fechaNacimiento = $matches[3] . '-' . str_pad($matches[2], 2, '0', STR_PAD_LEFT) . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        }
        
        // Crear militante en la base de datos
        $militanteData = [
            // Información personal
            'nombre' => $_POST['nombre'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'] ?? '',
            'fecha_nacimiento' => $fechaNacimiento,
            'genero' => $_POST['genero'],
            'edad' => $edad,
            'lugar_nacimiento' => $_POST['lugar_nacimiento'] ?? '',
            
            // Identificación
            'clave_elector' => $_POST['clave_elector'],
            'curp' => $_POST['curp'] ?? '',
            'folio_nacional' => $_POST['folio_nacional'] ?? '',
            'fecha_inscripcion_padron' => !empty($_POST['fecha_inscripcion_padron']) ? $_POST['fecha_inscripcion_padron'] : null,
            'domicilio' => $_POST['domicilio'] ?? '',
            'calle' => $_POST['calle'] ?? '',
            'codigo_postal' => $_POST['codigo_postal'] ?? '',
            'numero_exterior' => $_POST['numero_exterior'] ?? '',
            'numero_interior' => $_POST['numero_interior'] ?? '',
            'colonia' => $_POST['colonia'] ?? '',
            'estado' => $_POST['estado'],
            'municipio' => $_POST['municipio'],
            'seccion' => $_POST['seccion'] ?? '',
            
            // Contacto
            'telefono' => $_POST['telefono'],
            'email' => $_POST['email'] ?? '',
            'lugar_nacimiento' => $_POST['lugar_nacimiento'] ?? '',
            'imagen_ine' => $imagen_ine,
            'registrado_por' => $registrado_por
        ];
        
        $id = $this->militanteModel->create($militanteData);
        
        if ($id) {
            // Limpiar datos del INE de la sesión
            if (isset($_SESSION['datos_ine'])) {
                unset($_SESSION['datos_ine']);
            }
            
            setFlashMessage('success', '¡Registro exitoso! Militante registrado correctamente.');
            
            // Redireccionar según el rol del usuario
            if (isAdmin()) {
                redirect('admin/militantes');
            } else {
                redirect('home');
            }
        } else {
            setFlashMessage('error', 'Error al registrar. Por favor intente nuevamente.');
            redirect('register');
        }
    }
    
    // Procesa la imagen del INE para extraer datos (versión mejorada)
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
        
        // Validar tipo de archivo
        $fileType = mime_content_type($_FILES['ine_image']['tmp_name']);
        if (!in_array($fileType, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])) {
            http_response_code(400);
            echo json_encode(['error' => 'El formato de archivo no es válido. Use JPG, PNG o GIF.']);
            exit;
        }
        
        // Validar tamaño (límite: 10MB)
        if ($_FILES['ine_image']['size'] > 10 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['error' => 'El archivo es demasiado grande. Máximo 10MB.']);
            exit;
        }
        
        try {
            // Guardar primero la imagen en el servidor (para procesamiento y registros)
            $uploadDir = UPLOAD_DIR . 'ine/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generar nombre único para la imagen
            $filename = uniqid('ine_') . '_' . basename($_FILES['ine_image']['name']);
            $uploadFile = $uploadDir . $filename;
            
            // Mover archivo subido a la carpeta de uploads
            if (!move_uploaded_file($_FILES['ine_image']['tmp_name'], $uploadFile)) {
                throw new Exception("No se pudo guardar la imagen para procesamiento");
            }
            
            // Crear directorios de logs si no existen
            $logDir = __DIR__ . '/../logs/';
            if (!file_exists($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            // Registrar inicio del proceso OCR
            error_log(date('Y-m-d H:i:s') . " - Iniciando OCR para: {$filename}\n", 3, $logDir . 'ocr.log');
            
            // Procesar la imagen con OCR mejorado
            $datos = $this->ocrProcessor->processINEImage($uploadFile);
            
            // Log de datos extraídos (para análisis y mejora del algoritmo)
            error_log(date('Y-m-d H:i:s') . " - Datos extraídos de {$filename}: " . json_encode($datos, JSON_UNESCAPED_UNICODE) . "\n", 3, $logDir . 'ocr.log');
            
            // Procesar fecha de nacimiento para formato consistente
            if (!empty($datos['fecha_nacimiento'])) {
                // Si la fecha está en formato DD/MM/AAAA, convertirla a YYYY-MM-DD
                if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $datos['fecha_nacimiento'], $matches)) {
                    $dia = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                    $mes = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                    $anio = $matches[3];
                    $datos['fecha_nacimiento'] = $anio . '-' . $mes . '-' . $dia;
                }
            }
            
            // Guardar datos en sesión para pre-llenar el formulario
            $_SESSION['datos_ine'] = $datos;
            
            // Devolver datos extraídos y la ruta de la imagen
            echo json_encode([
                'success' => true,
                'datos' => $datos,
                'image_path' => 'ine/' . $filename
            ]);
        } catch (Exception $e) {
            // Registrar error en log
            error_log(date('Y-m-d H:i:s') . " - Error OCR: " . $e->getMessage() . "\n", 3, $logDir . 'error.log');
            
            http_response_code(500);
            echo json_encode(['error' => 'Error al procesar la imagen: ' . $e->getMessage()]);
        }
        
        exit;
    }
}
?>