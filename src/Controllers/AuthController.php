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

/**
 * Procesa el registro de una empresa
 */
public function registerCompany() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die("Método no permitido.");
    }

    // Validar campos obligatorios
    $required_fields = [
        'company_name', 'business_area', 'company_type',
        'first_name', 'last_name_p', 'last_name_m',
        'email', 'phone_number', 'password', 'password_confirm'
    ];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            die("Error: El campo {$field} es obligatorio.");
        }
    }

    // Validar que las contraseñas coincidan
    if ($_POST['password'] !== $_POST['password_confirm']) {
        die("Error: Las contraseñas no coinciden.");
    }

    // Validar email único
    $userModel = new User();
    if ($userModel->emailExists($_POST['email'])) {
        die("Error: El correo electrónico ya está registrado.");
    }

    // Preparar datos del usuario (persona de contacto)
    $userData = [
        'email' => trim($_POST['email']),
        'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
        'first_name' => trim($_POST['first_name']),
        'last_name_p' => trim($_POST['last_name_p']),
        'last_name_m' => trim($_POST['last_name_m']),
        'phone_number' => trim($_POST['phone_number']),
        'role' => 'company',
        'status' => 'pending' // Requiere aprobación de UPIS
    ];

    // Crear usuario
    $contact_user_id = $userModel->createUser($userData);

    if (!$contact_user_id) {
        die("Error al crear el usuario de contacto.");
    }

    // Preparar datos del perfil de empresa
    $companyData = [
        'company_name' => trim($_POST['company_name']),
        'commercial_name' => trim($_POST['commercial_name'] ?? ''),
        'business_area' => trim($_POST['business_area']), // ✅ AHORA SÍ SE GUARDA
        'company_type' => trim($_POST['company_type']),   // ✅ AHORA SÍ SE GUARDA
        'rfc' => trim($_POST['rfc'] ?? ''),
        'company_description' => trim($_POST['company_description'] ?? ''), // ✅ AHORA SÍ SE GUARDA
        'website' => trim($_POST['website'] ?? ''),
        'tax_id_url' => trim($_POST['tax_id_url'] ?? ''),
        'employee_count' => $_POST['employee_count'] ?? '1-50',
        'contact_person_user_id' => $contact_user_id,
        'contact_person_position' => trim($_POST['contact_person_position'] ?? '') // ✅ AHORA SÍ SE GUARDA
    ];

    // Procesar programas de desarrollo (checkboxes)
    $student_programs = [];
    if (isset($_POST['student_programs']) && is_array($_POST['student_programs'])) {
        $student_programs = $_POST['student_programs'];
    }
    $companyData['student_programs'] = !empty($student_programs) ? implode(', ', $student_programs) : 'Ninguno'; // ✅ AHORA SÍ SE GUARDA

    // Crear perfil de empresa usando el método público
    if ($userModel->createCompanyProfile($companyData)) {
        // Redirigir a página de confirmación
        header('Location: /SIEP/public/index.php?action=showLogin&status=company_registered');
        exit;
    } else {
        die("Error al crear el perfil de la empresa.");
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