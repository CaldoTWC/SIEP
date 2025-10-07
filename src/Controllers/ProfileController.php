<?php
/**
 * Controlador de Perfil de Usuario
 * 
 * Gestiona operaciones de perfil: cambio de contraseña, actualización de datos
 * 
 * @package SIEP\Controllers
 * @version 1.0.0
 */

require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Lib/Session.php');

class ProfileController {
    
    private $session;
    
    public function __construct() {
        $this->session = new Session();
    }
    
    /**
     * Muestra el formulario de cambio de contraseña
     */
    public function showChangePasswordForm() {
        // Cualquier usuario logueado puede acceder
        if (!$this->session->isLoggedIn()) {
            header('Location: /SIEP/public/index.php?action=showLogin');
            exit;
        }
        
        $userModel = new User();
        $user_profile = $userModel->getUserProfile($_SESSION['user_id']);
        
        require_once(__DIR__ . '/../Views/profile/change_password.php');
    }
    
    /**
     * Procesa el cambio de contraseña
     */
    public function changePassword() {
        // Verificar que esté logueado
        if (!$this->session->isLoggedIn()) {
            header('Location: /SIEP/public/index.php?action=showLogin');
            exit;
        }
        
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SIEP/public/index.php?action=showChangePasswordForm');
            exit;
        }
        
        // Obtener datos del formulario
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        // Validaciones
        if (empty($current_password)) {
            $errors[] = "Debe ingresar su contraseña actual.";
        }
        
        if (empty($new_password)) {
            $errors[] = "Debe ingresar una nueva contraseña.";
        }
        
        if (strlen($new_password) < 8) {
            $errors[] = "La nueva contraseña debe tener al menos 8 caracteres.";
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = "Las contraseñas no coinciden.";
        }
        
        if ($current_password === $new_password) {
            $errors[] = "La nueva contraseña debe ser diferente a la actual.";
        }
        
        // Si hay errores, recargar formulario
        if (!empty($errors)) {
            $userModel = new User();
            $user_profile = $userModel->getUserProfile($_SESSION['user_id']);
            require_once(__DIR__ . '/../Views/profile/change_password.php');
            return;
        }
        
        // Intentar cambiar contraseña
        $userModel = new User();
        $result = $userModel->changePassword(
            $_SESSION['user_id'],
            $current_password,
            $new_password
        );
        
        // Redirigir según resultado
        if ($result['success']) {
            // Éxito - Redirigir al dashboard correspondiente
            $dashboard_action = $this->getDashboardAction($_SESSION['user_role']);
            header("Location: /SIEP/public/index.php?action={$dashboard_action}&status=password_changed");
            exit;
        } else {
            // Error - Recargar formulario con mensaje
            $errors[] = $result['message'];
            $user_profile = $userModel->getUserProfile($_SESSION['user_id']);
            require_once(__DIR__ . '/../Views/profile/change_password.php');
        }
    }
    
    /**
     * Obtiene la acción del dashboard según el rol
     * 
     * @param string $role
     * @return string
     */
    private function getDashboardAction($role) {
        switch ($role) {
            case 'student':
                return 'studentDashboard';
            case 'company':
                return 'companyDashboard';
            case 'upis':
            case 'admin':
                return 'upisDashboard';
            default:
                return 'home';
        }
    }
}