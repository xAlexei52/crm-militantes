<?php

$rootPath = dirname(__DIR__);

require_once $rootPath . '/vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable($rootPath); // Ahora apunta al directorio raíz
$dotenv->load();

// Configuración de la base de datos
define('DB_HOST', $_ENV['DB_SERVER']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASSWORD']);
define('DB_NAME', $_ENV['DB_NAME']);

// Conexión a la base de datos
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    // Establecer charset
    $conn->set_charset("utf8");
    
    return $conn;
}

// Función para sanitizar inputs
function sanitizeInput($data) {
    $conn = getDBConnection();
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}
?>