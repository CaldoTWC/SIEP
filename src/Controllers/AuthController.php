<?php
// Archivo: src/Controllers/AuthController.php

// Incluimos el modelo User que acabamos de crear
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Lib/Session.php'); 

class AuthController {

    public function registerStudent() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Método no permitido.";
            return;
        }

        $errors = [];
        $input = $_POST;

        // --- Validaciones (email, boleta, contraseña) se mantienen igual ---
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "El formato del correo electrónico no es válido."; }
        // ... (resto de validaciones que ya tenías) ...

        // --- Si hay errores, mostrar el formulario de nuevo ---
        if (!empty($errors)) {
            require_once(__DIR__ . '/../Views/auth/register_student.php');
            return;
        }

        // --- Si no hay errores, procedemos con el registro ---
        $user = new User();
        $user->email = $email;
        $user->password = $_POST['password']; // Asumimos que la validación ya pasó
        $user->first_name = trim($_POST['first_name']);
        // CORRECCIÓN: Usamos los campos de apellidos separados
        $user->last_name_p = trim($_POST['last_name_p']);
        $user->last_name_m = trim($_POST['last_name_m']);
        $user->boleta = trim($_POST['boleta']);
        $user->career = $_POST['career'];
        $phone = trim($_POST['phone_number'] ?? '');
            if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "El número de teléfono debe tener 10 dígitos numéricos.";
        }
        $user->phone_number = $phone; 
        // Guardamos la carrera
        $user->career = $_POST['career'];

        if (empty($_POST['accept_privacy']) || $_POST['accept_privacy'] !== 'accepted') {
        $errors[] = "Debes aceptar el aviso de privacidad para continuar.";
        }
        
        // YA NO guardamos 'phone_number' ni 'percentage_progress' en el registro
        
        if ($user->createStudent()) {
            require_once(__DIR__ . '/../Views/auth/register_success.php');
        } else {
            $errors[] = "El correo electrónico o la boleta ya están registrados en el sistema.";
            require_once(__DIR__ . '/../Views/auth/register_student.php');
        }
    }

    public function showCompanyRegisterForm() {
        // Carga la vista del formulario de registro de empresa
        require_once(__DIR__ . '/../Views/auth/register_company.php');
    }

    public function registerCompany() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "Método no permitido.";
        return;
    }

    // Validación (simple por ahora)
    if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['company_name'])) {
        // Podríamos añadir una validación más robusta como en el registro de estudiantes
        echo "Faltan campos obligatorios.";
        return;
    }
    
    $companyUser = new User();

    // Datos del usuario de contacto
    $companyUser->email = $_POST['email'];
    $companyUser->password = $_POST['password'];
    $companyUser->first_name = $_POST['first_name'];
    $companyUser->last_name_p = $_POST['last_name_p'];
    $companyUser->last_name_m = $_POST['last_name_m'];
    $companyUser->phone_number = $_POST['phone_number'];
    
    // Datos del perfil de la empresa
    $companyUser->company_name = trim($_POST['company_name']);
    $companyUser->commercial_name = trim($_POST['commercial_name']);
    $companyUser->rfc = trim($_POST['rfc']);
    $companyUser->company_address = trim($_POST['company_address']);
    $companyUser->company_sector = $_POST['company_sector'];
    $companyUser->company_size = $_POST['company_size'];
    
    if ($companyUser->createCompany()) {
        
        // ========================================
        // 🆕 ENVIAR NOTIFICACIÓN DE CONFIRMACIÓN
        // ========================================
        
        require_once(__DIR__ . '/../Services/EmailService.php');
        $emailService = new EmailService();
        
        // Preparar datos para el email
        $company_data = [
            'user_id' => $companyUser->conn->lastInsertId(), // ID del usuario recién creado
            'contact_name' => $_POST['first_name'] . ' ' . $_POST['last_name_p'] . ' ' . $_POST['last_name_m'],
            'company_name' => $_POST['company_name'],
            'rfc' => $_POST['rfc'],
            'email' => $_POST['email'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Enviar confirmación a la empresa
        $emailService->notifyCompanyRegistered($company_data);
        
        // ========================================
        // FIN DE NOTIFICACIONES
        // ========================================
        
        require_once(__DIR__ . '/../Views/auth/register_success_company.php');
    } else {
        echo "Error: El correo electrónico o el RFC ya están registrados en el sistema.";
    }
}

    public function showRegisterForm() {
        // Esta función simplemente carga la vista del formulario
        require_once(__DIR__ . '/../Views/auth/register_student.php');
    }

    // (Aquí irían otras funciones como login(), logout(), etc. en el futuro)

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Método no permitido.";
            return;
        }

        $user = new User();
        $user->email = $_POST['email'];
        $user->password = $_POST['password'];
        
        $user_data = $user->login();

        if ($user_data) {
            // Verificamos si la cuenta está activa ANTES de iniciar sesión
            if ($user_data['status'] !== 'active') {
                echo "<h1>Cuenta no activada</h1> <p>Tu cuenta está pendiente de aprobación o ha sido desactivada. Por favor, contacta al administrador.</p>";
                return;
            }

            // Usamos nuestra clase Session para manejar la sesión.
            $session = new Session();
            $session->setUser($user_data);

            // ====================================================================
            // --- ESTA ES LA SECCIÓN QUE SE ACTUALIZA ---
            // Redirigimos al dashboard correspondiente según el rol del usuario.
            // ====================================================================
            if ($_SESSION['user_role'] == 'student') {
                header('Location: /SIEP/public/index.php?action=studentDashboard');
                exit;
            
            } else if ($_SESSION['user_role'] == 'upis' || $_SESSION['user_role'] == 'admin') {
                header('Location: /SIEP/public/index.php?action=upisDashboard');
                exit;

            } else if ($_SESSION['user_role'] == 'company') {
                // --- ACTIVAR ESTA REDIRECCIÓN ---
                header('Location: /SIEP/public/index.php?action=companyDashboard');
                exit;
            
            } else {
                // Si el rol no se reconoce por alguna razón
                echo "Rol de usuario desconocido.";
            }

        } else {
            echo "<h1>Error de Acceso</h1> <p>El correo electrónico o la contraseña son incorrectos.</p>";
        }
    }

    public function showLoginForm() {
        // Esta función simplemente carga la vista del formulario de login
        require_once(__DIR__ . '/../Views/auth/login.php');
    }
}