<?php
require_once 'models/Usuario.php';

class UsuariosController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    /**
     * Muestra la lista de usuarios (solo para administradores)
     */
    public function index() {
        // Verificar que el usuario sea administrador
        if (!isAdmin()) {
            setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            redirect('admin/dashboard');
        }
        
        // Obtener lista de usuarios
        $usuarios = $this->usuarioModel->getAll();
        
        // Cargar vista
        require 'views/admin/usuarios/list.php';
    }
    
    /**
     * Muestra el formulario para crear un nuevo usuario
     */
    public function create() {
        // Verificar que el usuario sea administrador
        if (!isAdmin()) {
            setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            redirect('admin/dashboard');
        }
        
        // Cargar vista
        require 'views/admin/usuarios/create.php';
    }
    
    /**
     * Procesa el formulario para crear un nuevo usuario
     */
    public function store() {
        // Verificar que el usuario sea administrador
        if (!isAdmin()) {
            setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            redirect('admin/dashboard');
        }
        
        // Verificar que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/usuarios');
        }
        
        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Error de seguridad. Intente nuevamente.');
            redirect('admin/usuarios/create');
        }
        
        // Validar campos requeridos
        $requiredFields = ['nombre', 'email', 'password', 'role'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                setFlashMessage('error', 'Por favor complete todos los campos requeridos.');
                redirect('admin/usuarios/create');
            }
        }
        
        // Validar email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            setFlashMessage('error', 'Por favor ingrese un correo electrónico válido.');
            redirect('admin/usuarios/create');
        }
        
        // Verificar que el email no esté en uso
        if ($this->usuarioModel->findByEmail($_POST['email'])) {
            setFlashMessage('error', 'Este correo electrónico ya está registrado.');
            redirect('admin/usuarios/create');
        }
        
        // Preparar datos
        $userData = [
            'nombre' => sanitizeInput($_POST['nombre']),
            'email' => sanitizeInput($_POST['email']),
            'password' => $_POST['password'],
            'role' => sanitizeInput($_POST['role'])
        ];
        
        // Crear usuario
        $userId = $this->usuarioModel->create($userData);
        
        if ($userId) {
            setFlashMessage('success', 'Usuario creado correctamente.');
            redirect('admin/usuarios');
        } else {
            setFlashMessage('error', 'Error al crear el usuario. Por favor intente nuevamente.');
            redirect('admin/usuarios/create');
        }
    }
    
    /**
     * Muestra el formulario para editar un usuario
     */
    public function edit() {
        // Verificar que el usuario sea administrador
        if (!isAdmin()) {
            setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            redirect('admin/dashboard');
        }
        
        // Verificar que se proporcionó un ID
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de usuario no proporcionado');
            redirect('admin/usuarios');
        }
        
        $id = (int)$_GET['id'];
        
        // Obtener datos del usuario
        $usuario = $this->usuarioModel->getById($id);
        
        if (!$usuario) {
            setFlashMessage('error', 'Usuario no encontrado');
            redirect('admin/usuarios');
        }
        
        // No permitir editar al usuario administrador principal con ID 1
        if ($id === 1 && $_SESSION['user_id'] !== 1) {
            setFlashMessage('error', 'No puedes editar al administrador principal');
            redirect('admin/usuarios');
        }
        
        // Cargar vista
        require 'views/admin/usuarios/edit.php';
    }
    
    /**
     * Procesa el formulario para actualizar un usuario
     */
    public function update() {
        // Verificar que el usuario sea administrador
        if (!isAdmin()) {
            setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            redirect('admin/dashboard');
        }
        
        // Verificar que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/usuarios');
        }
        
        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Error de seguridad. Intente nuevamente.');
            redirect('admin/usuarios');
        }
        
        // Verificar que se proporcionó un ID
        if (!isset($_POST['id'])) {
            setFlashMessage('error', 'ID de usuario no proporcionado');
            redirect('admin/usuarios');
        }
        
        $id = (int)$_POST['id'];
        
        // No permitir editar al usuario administrador principal con ID 1 (excepto si es uno mismo)
        if ($id === 1 && $_SESSION['user_id'] !== 1) {
            setFlashMessage('error', 'No puedes editar al administrador principal');
            redirect('admin/usuarios');
        }
        
        // Validar campos requeridos
        $requiredFields = ['nombre', 'email', 'role'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                setFlashMessage('error', 'Por favor complete todos los campos requeridos.');
                redirect('admin/usuarios/edit?id=' . $id);
            }
        }
        
        // Validar email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            setFlashMessage('error', 'Por favor ingrese un correo electrónico válido.');
            redirect('admin/usuarios/edit?id=' . $id);
        }
        
        // Verificar que el email no esté en uso por otro usuario
        $existingUser = $this->usuarioModel->findByEmail($_POST['email']);
        if ($existingUser && $existingUser['id'] != $id) {
            setFlashMessage('error', 'Este correo electrónico ya está registrado para otro usuario.');
            redirect('admin/usuarios/edit?id=' . $id);
        }
        
        // Obtener usuario actual
        $usuario = $this->usuarioModel->getById($id);
        
        if (!$usuario) {
            setFlashMessage('error', 'Usuario no encontrado');
            redirect('admin/usuarios');
        }
        
        // Preparar datos
        $userData = [
            'nombre' => sanitizeInput($_POST['nombre']),
            'email' => sanitizeInput($_POST['email']),
            'role' => sanitizeInput($_POST['role'])
        ];
        
        // Actualizar contraseña solo si se proporcionó una nueva
        if (!empty($_POST['password'])) {
            $userData['password'] = $_POST['password'];
        }
        
        // Actualizar usuario
        $success = $this->usuarioModel->update($id, $userData);
        
        if ($success) {
            // Si el usuario actualizado es el usuario actual, actualizar datos de sesión
            if ($_SESSION['user_id'] == $id) {
                $_SESSION['user_name'] = $userData['nombre'];
                $_SESSION['user_email'] = $userData['email'];
                $_SESSION['user_role'] = $userData['role'];
            }
            
            setFlashMessage('success', 'Usuario actualizado correctamente.');
            redirect('admin/usuarios');
        } else {
            setFlashMessage('error', 'Error al actualizar el usuario. Por favor intente nuevamente.');
            redirect('admin/usuarios/edit?id=' . $id);
        }
    }
    
    /**
     * Muestra la confirmación para eliminar un usuario
     */
    public function delete() {
        // Verificar que el usuario sea administrador
        if (!isAdmin()) {
            setFlashMessage('error', 'No tienes permisos para acceder a esta sección');
            redirect('admin/dashboard');
        }
        
        // Verificar que se proporcionó un ID
        if (!isset($_GET['id'])) {
            setFlashMessage('error', 'ID de usuario no proporcionado');
            redirect('admin/usuarios');
        }
        
        $id = (int)$_GET['id'];
        
        // No permitir eliminar al usuario administrador principal con ID 1
        if ($id === 1) {
            setFlashMessage('error', 'No puedes eliminar al administrador principal');
            redirect('admin/usuarios');
        }
        
        // No permitir eliminar al usuario actual
        if ($id === $_SESSION['user_id']) {
            setFlashMessage('error', 'No puedes eliminar tu propio usuario');
            redirect('admin/usuarios');
        }
        
        // Obtener datos del usuario
        $usuario = $this->usuarioModel->getById($id);
        
        if (!$usuario) {
            setFlashMessage('error', 'Usuario no encontrado');
            redirect('admin/usuarios');
        }
        
        // Confirmar eliminación si es GET
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require 'views/admin/usuarios/delete.php';
            return;
        }
        
        // Procesar eliminación si es POST
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Error de seguridad. Intente nuevamente.');
            redirect('admin/usuarios');
        }
        
        // Eliminar usuario
        $success = $this->usuarioModel->delete($id);
        
        if ($success) {
            setFlashMessage('success', 'Usuario eliminado correctamente.');
        } else {
            setFlashMessage('error', 'Error al eliminar el usuario. Por favor intente nuevamente.');
        }
        
        redirect('admin/usuarios');
    }
}
?>