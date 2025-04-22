<?php
// Incluir archivos de configuración
require_once 'config/database.php';
require_once 'config/app.php';

// Determinar la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];

// Para debuggear las rutas - Descomentar si necesitas ver qué está recibiendo
// echo "REQUEST_URI: " . $request_uri . "<br>";
// echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";

// Extraer la ruta base de la aplicación
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$base_path = rtrim($script_name, '/');

// Remover la ruta base de la URL solicitada
$request_uri = str_replace($base_path, '', $request_uri);

// Para debuggear las rutas - Descomentar si necesitas ver qué está procesando
// echo "BASE_PATH: " . $base_path . "<br>";
// echo "PROCESSED URI: " . $request_uri . "<br>";
// die();

// Separar el path y los parámetros de consulta
$request_path = parse_url($request_uri, PHP_URL_PATH);
$request_path = rtrim($request_path, '/');

// Si no hay path o es solo /, usar home
if (empty($request_path) || $request_path == '/') {
    $request_path = '/home';
}

// Router básico
switch ($request_path) {
    // Rutas públicas
    case '/home':
        require 'controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
        
    case '/login':
        require 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->showLogin();
        break;
        
    case '/login/process':
        require 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->processLogin();
        break;
        
    case '/logout':
        require 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case '/register':
        require 'controllers/RegistroController.php';
        $controller = new RegistroController();
        $controller->showForm();
        break;
        
    case '/register/process':
        require 'controllers/RegistroController.php';
        $controller = new RegistroController();
        $controller->processForm();
        break;
        
    case '/register/process-ine':
        require 'controllers/RegistroController.php';
        $controller = new RegistroController();
        $controller->processINE();
        break;
    
        case '/register/upload-ine':
        require_once __DIR__ . '/controllers/RegistroController.php';
        $controller = new RegistroController();
        $controller->uploadINE();
        break;
        
    // Rutas de administrador
    case '/admin':
    case '/admin/dashboard':
        if (!isAdmin()) {
            redirect('login');
        }
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->dashboard();
        break;
        
    case '/admin/militantes':
        if (!isAdmin()) {
            redirect('login');
        }
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->listMilitantes();
        break;
        
    case '/admin/militantes/edit':
        if (!isAdmin()) {
            redirect('login');
        }
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->editMilitante();
        break;
        
    case '/admin/militantes/save':
        if (!isAdmin()) {
            redirect('login');
        }
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->saveMilitante();
        break;
        
    case '/admin/militantes/delete':
        if (!isAdmin()) {
            redirect('login');
        }
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->deleteMilitante();
        break;
        
    case '/admin/mensajes':
        if (!isAdmin()) {
            redirect('login');
        }
        require 'controllers/MensajesController.php';
        $controller = new MensajesController();
        $controller->index();
        break;
        
    case '/admin/mensajes/send':
        if (!isAdmin()) {
            redirect('login');
        }
        require 'controllers/MensajesController.php';
        $controller = new MensajesController();
        $controller->sendMessages();
        break;
        
    // Rutas para páginas estáticas
    case '/about':
        require 'controllers/HomeController.php';
        $controller = new HomeController();
        $controller->about();
        break;
        
    case '/contact':
        require 'controllers/HomeController.php';
        $controller = new HomeController();
        $controller->contact();
        break;
        
    case '/contact/process':
        require 'controllers/HomeController.php';
        $controller = new HomeController();
        $controller->processContact();
        break;
        
    // Ruta por defecto - 404
    default:
        http_response_code(404);
        require 'views/404.php';
        break;
}
?>