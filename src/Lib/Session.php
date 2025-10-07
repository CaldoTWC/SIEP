<?php
/**
 * Clase para manejo de sesiones
 * 
 * Gestiona la autenticación y autorización de usuarios
 * 
 * @package SIEP\Lib
 * @version 2.0.0
 */

class Session {

    /**
     * Constructor - Inicia sesión automáticamente
     */
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Establece los datos de sesión del usuario
     * 
     * NOTA: $user_data['role_name'] viene del método User::login()
     * que ahora retorna users.role AS role_name
     * 
     * @param array $user_data Datos del usuario desde BD
     */
    public function setUser(array $user_data) {
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['user_name'] = $user_data['first_name'];
        $_SESSION['user_role'] = $user_data['role_name']; // ✅ Correcto
    }

    /**
     * Verifica si hay usuario logueado
     * 
     * @return bool
     */
    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        session_unset();
        session_destroy();
    }

    /**
     * Middleware de autorización
     * Verifica rol del usuario antes de permitir acceso
     * 
     * @param array $allowed_roles Roles permitidos ['student', 'upis', etc]
     */
    public function guard(array $allowed_roles) {
        // Verificar si está logueado
        if (!$this->isLoggedIn()) {
            header('Location: /SIEP/public/index.php?action=showLogin');
            exit;
        }

        // Verificar si tiene el rol adecuado
        if (!in_array($_SESSION['user_role'], $allowed_roles)) {
            header('Location: /SIEP/public/index.php?action=home&error=unauthorized');
            exit;
        }
    }
}