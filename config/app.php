<?php

// Agregar esta línea al principio del archivo config/app.php, después de los demás requires
require_once __DIR__ . '/email.php';
// Configuración general de la aplicación
define('APP_NAME', 'Sistema de Afiliación');
define('APP_URL', 'http://localhost/militantes-sistema');
define('ADMIN_EMAIL', 'admin@ejemplo.com');

// Directorio para uploads
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Configuración de sesión
session_start();

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función para verificar si es administrador
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
}

// Función para redirigir
function redirect($path) {
    $path = ltrim($path, '/');
    header("Location: " . APP_URL . "/" . $path);
    exit();
}

// Función para mensajes flash
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Generar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verificar token CSRF
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die("Error de validación CSRF");
    }
    return true;
}
?>