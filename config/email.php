<?php
// Configuración del servidor SMTP para envío de correos
define('SMTP_HOST', 'smtp.gmail.com'); // Cambia esto según tu proveedor de correo
define('SMTP_USER', 'tu_correo@gmail.com'); // Cambia esto por tu correo
define('SMTP_PASS', 'tu_contraseña_de_aplicacion'); // Cambia esto por tu contraseña
define('SMTP_SECURE', 'tls'); // tls o ssl
define('SMTP_PORT', 587); // Puerto (587 para TLS, 465 para SSL)

// Si utilizas Gmail, necesitas crear una "Contraseña de aplicación" 
// en la configuración de seguridad de tu cuenta de Google
// https://myaccount.google.com/apppasswords
?>