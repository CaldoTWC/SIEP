<?php
// Archivo: src/Controllers/CompanyController.php (Versión Final y Unificada)

// --- Incluimos TODAS las dependencias que usan los métodos de esta clase ---
require_once(__DIR__ . '/../Models/Vacancy.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Lib/Session.php');
require_once(__DIR__ . '/../Lib/DocumentGenerator.php');
require_once(__DIR__ . '/../Config/Database.php'); // Necesario para el método de ayuda

class CompanyController {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    // ===================================================================
    // --- MÉTODOS PARA EL DASHBOARD Y GESTIÓN DE VACANTES ---
    // ===================================================================

    /**
     * Muestra el dashboard principal de la empresa con la lista de sus vacantes.
     */
    public function dashboard() {
        $this->session->guard(['company']);
        
        $company_profile_id = $this->getCompanyProfileId($_SESSION['user_id']);
        
        $vacancyModel = new Vacancy();
        $vacancies = [];
        if ($company_profile_id) {
            $vacancies = $vacancyModel->getByCompanyId($company_profile_id);
        }

        require_once(__DIR__ . '/../Views/company/dashboard.php');
    }

    /**
     * Muestra el formulario para publicar una nueva vacante.
     */
    public function showPostVacancyForm() {
        $this->session->guard(['company']);
        require_once(__DIR__ . '/../Views/company/post_vacancy.php');
    }

    /**
     * Procesa los datos del formulario de nueva vacante y la guarda.
     */
    public function postVacancy() {
        $this->session->guard(['company']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['title'])) {
            // Manejar error de forma más elegante en el futuro
            die("Error: Faltan datos o método incorrecto.");
        }
        
        $vacancy = new Vacancy();
        $company_profile_id = $this->getCompanyProfileId($_SESSION['user_id']);

        if (!$company_profile_id) {
            die("Error: No se pudo encontrar el perfil de la empresa asociado a este usuario.");
        }

        $vacancy->company_profile_id = $company_profile_id;
        $vacancy->title = $_POST['title'];
        $vacancy->description = $_POST['description'];
        $vacancy->activities = $_POST['activities'];
        $vacancy->modality = $_POST['modality'];
        
        if ($vacancy->create()) {
            header('Location: /SIEP/public/index.php?action=companyDashboard&status=vacancy_posted');
            exit;
        } else {
            die("Hubo un error al publicar la vacante.");
        }
    }

    // ===================================================================
    // --- MÉTODOS PARA LA CARTA DE ACEPTACIÓN ---
    // ===================================================================

    /**
     * Muestra el formulario para generar la Carta de Aceptación.
     */
    public function showAcceptanceLetterForm() {
        $this->session->guard(['company']);
        require_once(__DIR__ . '/../Views/company/acceptance_letter_form.php');
    }

    /**
     * Procesa los datos y genera el PDF de la Carta de Aceptación.
     */
    public function generateAcceptanceLetter() {
        // 1. Proteger la ruta y validar la petición
        $this->session->guard(['company']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['student_boleta'])) {
            // Usamos die() para detener la ejecución y mostrar un error claro en la nueva pestaña.
            die("<h1>Error</h1><p>Solicitud inválida o falta el número de boleta.</p>");
        }
        
        $boleta = trim($_POST['student_boleta']);
        $userModel = new User();

        // 2. Buscamos al estudiante en la base de datos por su boleta
        $student_id = $userModel->findStudentIdByBoleta($boleta);
        if (!$student_id) {
            die("<h1>Estudiante no Encontrado</h1><p>No se encontró ningún estudiante registrado con la boleta: " . htmlspecialchars($boleta) . ". Por favor, verifique el número e inténtelo de nuevo.</p>");
        }

        // 3. Obtenemos los datos completos del estudiante y de la empresa para el PDF y el vínculo
        $student_db_data = $userModel->getStudentProfileForForm($student_id);
        $company_profile_id = $this->getCompanyProfileId($_SESSION['user_id']);
        $company_db_data = $userModel->getCompanyProfileByUserId($_SESSION['user_id']);

        if (!$student_db_data || !$company_profile_id || !$company_db_data) {
            die("<h1>Error Crítico</h1><p>No se pudieron obtener los datos del perfil del estudiante o de la empresa. Contacte al administrador.</p>");
        }

        // 4. Creamos el vínculo en la tabla 'company_student_links'
        require_once(__DIR__ . '/../Models/CompanyStudentLink.php');
        $linkModel = new CompanyStudentLink();
        // Es una buena práctica verificar si el vínculo ya existe para no duplicarlo
        if (!$linkModel->create($company_profile_id, $student_id)) {
            // Este error puede ocurrir si se intenta generar la carta dos veces para el mismo alumno
            die("<h1>Error al Vincular</h1><p>No se pudo crear el vínculo. Es posible que ya exista una relación activa con este estudiante.</p>");
        }

        // 5. Preparamos los arrays de datos para el generador de PDF
        $student_data_for_pdf = [
            'student_name' => $student_db_data['full_name'],
            'student_boleta' => $boleta,
            'student_career' => $student_db_data['career'],
            'gender' => $_POST['gender'],
            'area' => $_POST['area'],
            'project_name' => $_POST['project_name']
        ];
        
        $company_data_for_pdf = [
            'company_name' => $company_db_data['company_name'],
            'contact_person_name' => $company_db_data['first_name'] . ' ' . $company_db_data['last_name_p'],
            'contact_person_position' => $company_db_data['contact_person_position'] ?? 'Representante de la Empresa'
        ];
        
        // 6. Llamamos al servicio para que genere el documento PDF
        $docService = new DocumentService();
        $docService->generateAcceptanceLetter($student_data_for_pdf, $company_data_for_pdf);
    }
    
    // ===================================================================
    // --- MÉTODO DE AYUDA ---
    // ===================================================================
    
    /**
     * Obtiene el ID del perfil de la empresa a partir del ID del usuario.
     * @param int $user_id
     * @return int|null
     */
    private function getCompanyProfileId(int $user_id) { // Quitado ?int para PHP 7.4
        $db = Database::getInstance()->getConnection();
        $query = "SELECT id FROM company_profiles WHERE contact_person_user_id = ? LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['id'];
        } else {
            return null;
        }
    }

    public function showValidationLetterForm() {
        $this->session->guard(['company']);
        require_once(__DIR__ . '/../Views/company/validation_letter_form.php');
    }

    public function generateValidationLetter() {
        $this->session->guard(['company']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { die("Acceso no permitido"); }
        
        $userModel = new User();
        $company_db_data = $userModel->getCompanyProfileByUserId($_SESSION['user_id']);
        if (!$company_db_data) {
            die("Error: No se pudo encontrar el perfil de la empresa.");
        }
        
        // Datos del formulario para la constancia
        $validation_data = $_POST;
        
        // Datos de la empresa de la BD
        $company_data = [
            'company_name' => $company_db_data['company_name'],
            'contact_person_name' => $company_db_data['first_name'] . ' ' . $company_db_data['last_name'],
        ];
        
        $docService = new DocumentService();
        $docService->generateValidationLetter($validation_data, $company_data);
    }

    public function deleteVacancy() {
        $this->session->guard(['company']);

        $vacancy_id = $_GET['id'] ?? null;
        if (!$vacancy_id) { die("ID de vacante no proporcionado."); }

        $company_profile_id = $this->getCompanyProfileId($_SESSION['user_id']);
        if (!$company_profile_id) { die("Error: Perfil de empresa no encontrado."); }

        $vacancyModel = new Vacancy();
        
        if ($vacancyModel->deleteById((int)$vacancy_id, $company_profile_id)) {
            header('Location: /SIEP/public/index.php?action=companyDashboard&status=vacancy_deleted');
        } else {
            header('Location: /SIEP/public/index.php?action=companyDashboard&status=error');
        }
        exit;
    }

}