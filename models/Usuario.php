<?php
class Usuario {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    /**
     * Obtener todos los usuarios
     * @return array Lista de usuarios
     */
    public function getAll() {
        $query = "SELECT * FROM usuarios ORDER BY id ASC";
        $result = $this->conn->query($query);
        
        $usuarios = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // No devolver la contraseña
                unset($row['password']);
                $usuarios[] = $row;
            }
        }
        
        return $usuarios;
    }
    
    /**
     * Obtener un usuario por su ID
     * @param int $id ID del usuario
     * @return array|null Datos del usuario o null si no existe
     */
    public function getById($id) {
        $id = (int)$id;
        $query = "SELECT * FROM usuarios WHERE id = $id LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // No devolver la contraseña
            unset($user['password']);
            return $user;
        }
        
        return null;
    }
    
    /**
     * Buscar usuario por email
     * @param string $email Email del usuario
     * @return array|null Datos del usuario o null si no existe
     */
    public function findByEmail($email) {
        $email = sanitizeInput($email);
        $query = "SELECT * FROM usuarios WHERE email = '$email' LIMIT 1";
        $result = $this->conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Crear nuevo usuario
     * @param array $data Datos del usuario
     * @return int|false ID del usuario creado o false si hubo error
     */
    public function create($data) {
        $nombre = sanitizeInput($data['nombre']);
        $email = sanitizeInput($data['email']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = isset($data['role']) ? sanitizeInput($data['role']) : 'operador';
        
        $query = "INSERT INTO usuarios (nombre, email, password, role, created_at) 
                  VALUES ('$nombre', '$email', '$password', '$role', NOW())";
        
        if ($this->conn->query($query)) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Actualizar usuario existente
     * @param int $id ID del usuario
     * @param array $data Datos a actualizar
     * @return bool Resultado de la operación
     */
    public function update($id, $data) {
        $id = (int)$id;
        $sets = [];
        
        if (isset($data['nombre'])) {
            $nombre = sanitizeInput($data['nombre']);
            $sets[] = "nombre = '$nombre'";
        }
        
        if (isset($data['email'])) {
            $email = sanitizeInput($data['email']);
            $sets[] = "email = '$email'";
        }
        
        if (isset($data['password'])) {
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $sets[] = "password = '$password'";
        }
        
        if (isset($data['role'])) {
            $role = sanitizeInput($data['role']);
            $sets[] = "role = '$role'";
        }
        
        // Agregar campos para recuperación de contraseña
        if (isset($data['reset_token'])) {
            $reset_token = sanitizeInput($data['reset_token']);
            $sets[] = "reset_token = '$reset_token'";
        }
        
        if (isset($data['reset_expires'])) {
            $reset_expires = sanitizeInput($data['reset_expires']);
            $sets[] = "reset_expires = '$reset_expires'";
        }
        
        if (empty($sets)) {
            return false;
        }
        
        // Actualizar fecha de modificación
        $sets[] = "updated_at = NOW()";
        
        $setString = implode(', ', $sets);
        $query = "UPDATE usuarios SET $setString WHERE id = $id";
        
        return $this->conn->query($query);
    }
    
    /**
     * Eliminar usuario
     * @param int $id ID del usuario
     * @return bool Resultado de la operación
     */
    public function delete($id) {
        $id = (int)$id;
        // No permitir eliminar al administrador principal (ID 1)
        if ($id === 1) {
            return false;
        }
        
        $query = "DELETE FROM usuarios WHERE id = $id";
        return $this->conn->query($query);
    }
    
    /**
     * Validar credenciales de login
     * @param string $email Email del usuario
     * @param string $password Contraseña
     * @return array|false Datos del usuario o false si son inválidas
     */
    public function validateLogin($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            // Actualizar fecha de último login
            $id = (int)$user['id'];
            $this->conn->query("UPDATE usuarios SET last_login = NOW() WHERE id = $id");
            
            return $user;
        }
        
        return false;
    }
    
    /**
     * Generar token para recuperación de contraseña
     * @param string $email Email del usuario
     * @return string|false Token generado o false si el usuario no existe
     */
    public function generatePasswordResetToken($email) {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Generar token aleatorio
        $token = bin2hex(random_bytes(32));
        // Establecer expiración a 24 horas
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $id = (int)$user['id'];
        $updateData = [
            'reset_token' => $token,
            'reset_expires' => $expires
        ];
        
        if ($this->update($id, $updateData)) {
            return $token;
        }
        
        return false;
    }
    
    /**
     * Verificar token de recuperación de contraseña
     * @param string $email Email del usuario
     * @param string $token Token a verificar
     * @return bool Resultado de la verificación
     */
    public function verifyPasswordResetToken($email, $token) {
        $email = sanitizeInput($email);
        $token = sanitizeInput($token);
        
        $query = "SELECT * FROM usuarios WHERE email = '$email' AND reset_token = '$token' AND reset_expires > NOW() LIMIT 1";
        $result = $this->conn->query($query);
        
        return ($result && $result->num_rows > 0);
    }
    
    /**
     * Restablecer contraseña usando token
     * @param string $email Email del usuario
     * @param string $token Token de recuperación
     * @param string $newPassword Nueva contraseña
     * @return bool Resultado de la operación
     */
    public function resetPassword($email, $token, $newPassword) {
        if (!$this->verifyPasswordResetToken($email, $token)) {
            return false;
        }
        
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        $id = (int)$user['id'];
        $updateData = [
            'password' => $newPassword,
            'reset_token' => null,
            'reset_expires' => null
        ];
        
        return $this->update($id, $updateData);
    }
}
?>