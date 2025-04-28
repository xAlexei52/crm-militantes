<?php
require_once 'models/Usuario.php';

class PasswordResetController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    /**
     * Muestra el formulario para solicitar restablecimiento de contraseña
     */
    public function showRequestForm() {
        require 'views/auth/password-request.php';
    }
    
    /**
     * Procesa la solicitud de restablecimiento de contraseña
     */
    public function processRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('password/reset');
        }
        
        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Error de seguridad. Intente nuevamente.');
            redirect('password/reset');
        }
        
        // Validar email
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlashMessage('error', 'Por favor ingrese un correo electrónico válido.');
            redirect('password/reset');
        }
        
        // Verificar si el email existe
        $user = $this->usuarioModel->findByEmail($email);
        
        if (!$user) {
            // No revelamos si el email existe o no por seguridad
            setFlashMessage('success', 'Si su correo está registrado, recibirá instrucciones para restablecer su contraseña.');
            redirect('login');
            return;
        }
        
        // Generar token para restablecimiento
        $token = $this->usuarioModel->generatePasswordResetToken($email);
        
        if (!$token) {
            setFlashMessage('error', 'Error al generar el token de restablecimiento. Por favor intente más tarde.');
            redirect('password/reset');
            return;
        }
        
        // Enviar email con el link para restablecer contraseña
        $resetUrl = APP_URL . '/password/reset/' . $token . '?email=' . urlencode($email);
        $emailSent = $this->sendPasswordResetEmail($email, $user['nombre'], $resetUrl);
        
        if (!$emailSent) {
            setFlashMessage('error', 'Error al enviar el correo electrónico. Por favor intente más tarde.');
            redirect('password/reset');
            return;
        }
        
        // Redirigir a login con mensaje de éxito
        setFlashMessage('success', 'Se han enviado instrucciones para restablecer su contraseña a su correo electrónico.');
        redirect('login');
    }
    
    /**
     * Muestra el formulario para establecer nueva contraseña
     */
    public function showResetForm($token) {
        // Validar que se proporcionó un email
        if (!isset($_GET['email']) || empty($_GET['email'])) {
            setFlashMessage('error', 'Enlace de restablecimiento de contraseña inválido.');
            redirect('login');
            return;
        }
        
        $email = trim($_GET['email']);
        
        // Verificar que el token es válido
        if (!$this->usuarioModel->verifyPasswordResetToken($email, $token)) {
            setFlashMessage('error', 'El enlace para restablecer la contraseña ha expirado o es inválido.');
            redirect('login');
            return;
        }
        
        // Mostrar formulario para nueva contraseña
        require 'views/auth/password-reset.php';
    }
    
    /**
     * Procesa el formulario de nueva contraseña
     */
    public function processReset($token) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('login');
        }
        
        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Error de seguridad. Intente nuevamente.');
            redirect('login');
        }
        
        // Validar datos
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $passwordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
        
        if (empty($email) || empty($password) || empty($passwordConfirm)) {
            setFlashMessage('error', 'Por favor complete todos los campos.');
            redirect('password/reset/' . $token . '?email=' . urlencode($email));
            return;
        }
        
        if ($password !== $passwordConfirm) {
            setFlashMessage('error', 'Las contraseñas no coinciden.');
            redirect('password/reset/' . $token . '?email=' . urlencode($email));
            return;
        }
        
        if (strlen($password) < 8) {
            setFlashMessage('error', 'La contraseña debe tener al menos 8 caracteres.');
            redirect('password/reset/' . $token . '?email=' . urlencode($email));
            return;
        }
        
        // Verificar que el token sigue siendo válido
        if (!$this->usuarioModel->verifyPasswordResetToken($email, $token)) {
            setFlashMessage('error', 'El enlace para restablecer la contraseña ha expirado o es inválido.');
            redirect('login');
            return;
        }
        
        // Actualizar contraseña
        $reset = $this->usuarioModel->resetPassword($email, $token, $password);
        
        if (!$reset) {
            setFlashMessage('error', 'Error al restablecer la contraseña. Por favor intente nuevamente.');
            redirect('password/reset/' . $token . '?email=' . urlencode($email));
            return;
        }
        
        // Redirigir a login con mensaje de éxito
        setFlashMessage('success', 'Su contraseña ha sido restablecida correctamente. Por favor inicie sesión con su nueva contraseña.');
        redirect('login');
    }
    
    /**
     * Envía email con link para restablecer contraseña
     */
    private function sendPasswordResetEmail($email, $name, $resetUrl) {
        // Cargar librería PHPMailer
        require 'vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            
            // Destinatarios
            $mail->setFrom(ADMIN_EMAIL, APP_NAME);
            $mail->addAddress($email, $name);
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Restablecimiento de Contraseña - ' . APP_NAME;
            
            // Cuerpo del correo HTML
            $mail->Body = '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <div style="background-color: #075985; color: white; padding: 20px; text-align: center;">
                        <h1>' . APP_NAME . '</h1>
                    </div>
                    <div style="padding: 20px; border: 1px solid #ddd; border-top: none;">
                        <h2>Restablecimiento de Contraseña</h2>
                        <p>Hola ' . htmlspecialchars($name) . ',</p>
                        <p>Hemos recibido una solicitud para restablecer la contraseña de su cuenta. Si usted no realizó esta solicitud, puede ignorar este correo.</p>
                        <p>Para restablecer su contraseña, haga clic en el siguiente enlace:</p>
                        <p style="text-align: center; margin: 30px 0;">
                            <a href="' . $resetUrl . '" style="background-color: #075985; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Restablecer Contraseña</a>
                        </p>
                        <p>O copie y pegue la siguiente URL en su navegador:</p>
                        <p style="word-break: break-all; background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">' . $resetUrl . '</p>
                        <p>Este enlace expirará en 24 horas por razones de seguridad.</p>
                        <p>Si tiene alguna pregunta, no dude en contactarnos.</p>
                        <p>Saludos,<br>' . APP_NAME . '</p>
                    </div>
                    <div style="background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666;">
                        &copy; ' . date('Y') . ' ' . APP_NAME . '. Todos los derechos reservados.
                    </div>
                </div>
            ';
            
            // Cuerpo alternativo en texto plano
            $mail->AltBody = "Hola " . $name . ",\n\n" .
                "Hemos recibido una solicitud para restablecer la contraseña de su cuenta.\n\n" .
                "Para restablecer su contraseña, visite el siguiente enlace:\n" . $resetUrl . "\n\n" .
                "Este enlace expirará en 24 horas por razones de seguridad.\n\n" .
                "Si tiene alguna pregunta, no dude en contactarnos.\n\n" .
                "Saludos,\n" . APP_NAME;
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar email de restablecimiento: " . $mail->ErrorInfo);
            return false;
        }
    }
}
?>