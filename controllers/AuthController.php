<?php
require_once 'models/Usuario.php';

class AuthController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    // Muestra la página de login
    public function showLogin() {
        if (isLoggedIn()) {
            redirect(isAdmin() ? 'admin/dashboard' : 'home');
        }
        
        require 'views/auth/login.php';
    }
    
    // Procesa el formulario de login
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('login');
        }
        
        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Error de seguridad. Intente nuevamente.');
            redirect('login');
        }
        
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Validar campos
        if (empty($email) || empty($password)) {
            setFlashMessage('error', 'Por favor complete todos los campos.');
            redirect('login');
        }
        
        // Validar credenciales
        $user = $this->usuarioModel->validateLogin($email, $password);
        
        if ($user) {
            // Iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            setFlashMessage('success', 'Bienvenido/a ' . $user['nombre']);
            
            // Redirigir según rol
            if ($user['role'] == 'admin') {
                redirect('admin/dashboard');
            } else {
                redirect('home');
            }
        } else {
            setFlashMessage('error', 'Credenciales inválidas. Intente nuevamente.');
            redirect('login');
        }
    }
    
    // Cierra la sesión
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la sesión
        session_destroy();
        
        setFlashMessage('success', 'Ha cerrado sesión correctamente.');
        redirect('login');
    }
}
?>