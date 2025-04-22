<?php
/**
 * Controlador para la página principal y páginas públicas
 */
class HomeController {
    
    /**
     * Muestra la página principal
     */
    public function index() {
        // Verificar si el usuario está logueado
        $loggedIn = isLoggedIn();
        
        // Si el usuario es admin, redirigir al dashboard
        if ($loggedIn && isAdmin()) {
            redirect('admin/dashboard');
        }
        
        // Obtener datos para la vista
        $username = $loggedIn ? $_SESSION['user_name'] : null;
        
        // Cargar la vista de la página principal
        require 'views/home/index.php';
    }
    
    /**
     * Muestra la página "Acerca de"
     */
    public function about() {
        require 'views/home/about.php';
    }
    
    /**
     * Muestra la página de contacto
     */
    public function contact() {
        require 'views/home/contact.php';
    }
    
    /**
     * Procesa el formulario de contacto
     */
    public function processContact() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('contact');
        }
        
        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Error de seguridad. Intente nuevamente.');
            redirect('contact');
        }
        
        // Validar campos
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        
        if (empty($name) || empty($email) || empty($message)) {
            setFlashMessage('error', 'Por favor complete todos los campos.');
            redirect('contact');
        }
        
        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlashMessage('error', 'Por favor ingrese un correo electrónico válido.');
            redirect('contact');
        }
        
        // Enviar email (usando la función mail de PHP)
        $to = ADMIN_EMAIL;
        $subject = "Mensaje de contacto desde " . APP_NAME;
        $mailContent = "Nombre: $name\n";
        $mailContent .= "Email: $email\n\n";
        $mailContent .= "Mensaje:\n$message";
        
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        
        $mailSent = mail($to, $subject, $mailContent, $headers);
        
        if ($mailSent) {
            setFlashMessage('success', 'Su mensaje ha sido enviado. Nos pondremos en contacto con usted pronto.');
        } else {
            setFlashMessage('error', 'Ha ocurrido un error al enviar su mensaje. Por favor intente nuevamente.');
        }
        
        redirect('contact');
    }
}
?>