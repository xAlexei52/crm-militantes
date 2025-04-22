-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS partido_afiliados;

-- Usar la base de datos
USE partido_afiliados;

-- Tabla de usuarios (administradores y operadores del sistema)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'operador') NOT NULL DEFAULT 'operador',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de militantes
CREATE TABLE IF NOT EXISTS militantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100),
    fecha_nacimiento DATE NOT NULL,
    genero ENUM('M', 'F', 'O') NOT NULL,
    clave_elector VARCHAR(18) NOT NULL UNIQUE,
    curp VARCHAR(18),
    domicilio TEXT,
    estado VARCHAR(50) NOT NULL,
    municipio VARCHAR(100) NOT NULL,
    seccion VARCHAR(10),
    telefono VARCHAR(15),
    email VARCHAR(100),
    imagen_ine VARCHAR(255),
    status ENUM('activo', 'inactivo', 'pendiente') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para el registro de envío de mensajes
CREATE TABLE IF NOT EXISTS mensajes_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    militante_id INT,
    mensaje TEXT NOT NULL,
    status ENUM('enviado', 'fallido', 'pendiente') NOT NULL,
    proveedor VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (militante_id) REFERENCES militantes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para campañas de mensajes
CREATE TABLE IF NOT EXISTS campanas_mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    filtro_estado VARCHAR(50),
    filtro_municipio VARCHAR(100),
    filtro_genero ENUM('M', 'F', 'O'),
    total_enviados INT DEFAULT 0,
    total_fallidos INT DEFAULT 0,
    usuario_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para historial de actividades (logs)
CREATE TABLE IF NOT EXISTS actividades_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(50) NOT NULL,
    descripcion TEXT,
    tipo_entidad VARCHAR(50),
    entidad_id INT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear usuario administrador por defecto (password: admin123)
-- La contraseña está hasheada con password_hash() usando PASSWORD_DEFAULT
INSERT INTO usuarios (nombre, email, password, role) VALUES 
('Administrador', 'admin@ejemplo.com', '$2y$10$rFJ.d.YUXQ9dcbWZaqZJUeS1yXUVgE7dxKBY0e0YeYF0ROQh7Z5G2', 'admin');