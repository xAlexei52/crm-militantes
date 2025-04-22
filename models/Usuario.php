<?php
class Usuario {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    // Buscar usuario por email
    public function findByEmail($email) {
        $email = sanitizeInput($email);
        $query = "SELECT * FROM usuarios WHERE email = '$email' LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    // Crear nuevo usuario
    public function create($data) {
        $nombre = sanitizeInput($data['nombre']);
        $email = sanitizeInput($data['email']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = isset($data['role']) ? sanitizeInput($data['role']) : 'militante';
        
        $query = "INSERT INTO usuarios (nombre, email, password, role, created_at) 
                  VALUES ('$nombre', '$email', '$password', '$role', NOW())";
        
        if ($this->conn->query($query)) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    // Validar credenciales de login
    public function validateLogin($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
}
?>