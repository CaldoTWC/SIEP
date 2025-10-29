<?php
// Archivo: src/Controllers/CompanyController.php (Versión Final Corregida)

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

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die("Error: Método incorrecto.");
    }
    
    // Validar campos obligatorios básicos
    $required_fields = ['title', 'description', 'num_vacancies', 'economic_support', 
                       'start_date', 'end_date', 'related_career', 'required_knowledge',
                       'activity_1', 'activity_2', 'activity_3', 'activity_4', 'activity_5',
                       'modality', 'privacy_accepted'];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field]) && $_POST[$field] !== '0') {
            die("Error: El campo {$field} es obligatorio.");
        }
    }
    
    // Validar que se hayan aceptado las políticas
    if (!isset($_POST['privacy_accepted']) || $_POST['privacy_accepted'] !== 'on') {
        die("Error: Debe aceptar el aviso de privacidad.");
    }
    
    $vacancy = new Vacancy();
    $company_profile_id = $this->getCompanyProfileId($_SESSION['user_id']);

    if (!$company_profile_id) {
        die("Error: No se pudo encontrar el perfil de la empresa asociado a este usuario.");
    }

    // Asignar datos básicos
    $vacancy->company_profile_id = $company_profile_id;
    $vacancy->title = trim($_POST['title']);
    $vacancy->description = trim($_POST['description']);
    $vacancy->activities = trim($_POST['activities'] ?? ''); // Campo legacy, opcional
    $vacancy->modality = $_POST['modality'];
    
    // ATENCIÓN A ESTUDIANTES INTERESADOS
    $attention_days_array = $_POST['attention_days'] ?? [];
    $vacancy->attention_days = !empty($attention_days_array) ? json_encode($attention_days_array) : null;
    $vacancy->attention_schedule = trim($_POST['attention_schedule'] ?? '');
    
    // GENERALIDADES DE LA POSTULACIÓN
    $vacancy->num_vacancies = (int)$_POST['num_vacancies'];
    $vacancy->vacancy_names = trim($_POST['vacancy_names'] ?? '');
    $vacancy->economic_support = !empty($_POST['economic_support']) ? (float)$_POST['economic_support'] : null;
    $vacancy->start_date = $_POST['start_date'];
    $vacancy->end_date = $_POST['end_date'];
    
    // PERFIL PARA OCUPAR LA VACANTE
    $vacancy->key_information = trim($_POST['key_information'] ?? '');
    $vacancy->related_career = trim($_POST['related_career']);
    $vacancy->required_knowledge = trim($_POST['required_knowledge']);
    $vacancy->required_competencies = trim($_POST['required_competencies'] ?? '');
    
    // Idiomas requeridos (array a JSON)
    $languages_array = $_POST['required_languages'] ?? [];
    $vacancy->required_languages = !empty($languages_array) ? json_encode($languages_array) : null;
    
    // ACTIVIDADES A REALIZAR (CAMPO ÚNICO)
$vacancy->activities_list = trim($_POST['activities_list']);
$vacancy->activity_details = trim($_POST['activity_details'] ?? ''); // Opcional
    
    // MODALIDAD EN QUE SE DESARROLLARÁN LAS ACTIVIDADES
    if ($_POST['modality'] === 'Presencial' || $_POST['modality'] === 'Hibrida') {
        $vacancy->work_location_address = trim($_POST['work_location_address'] ?? '');
    } else {
        $vacancy->work_location_address = null;
    }
    
    // Días de trabajo (array a JSON)
    $work_days_array = $_POST['work_days'] ?? [];
    $vacancy->work_days = !empty($work_days_array) ? json_encode($work_days_array) : null;
    
    $vacancy->start_time = !empty($_POST['start_time']) ? $_POST['start_time'] : null;
    $vacancy->end_time = !empty($_POST['end_time']) ? $_POST['end_time'] : null;
    
    // PUBLICACIÓN DE LOGOTIPOS
    $vacancy->logo_auth = isset($_POST['logo_auth']) && $_POST['logo_auth'] === 'on' ? 1 : 0;
    $vacancy->logo_url = trim($_POST['logo_url'] ?? '');
    
    // AVISO DE PRIVACIDAD
    $vacancy->privacy_accepted = 1; // Ya validamos arriba que fue aceptado
    
    // Intentar crear la vacante
    if ($vacancy->create()) {
        header('Location: /SIEP/public/index.php?action=companyDashboard&status=vacancy_posted');
        exit;
    } else {
        die("Hubo un error al publicar la vacante. Por favor, intente nuevamente.");
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

        // Verificar si el vínculo ya existe para no duplicarlo
        if ($linkModel->existsActiveLink($company_profile_id, $student_id)) {
            die("<h1>Vínculo Existente</h1><p>Ya existe una relación activa con este estudiante. No es necesario generar la carta nuevamente.</p>");
        }

        // Configurar las propiedades del objeto antes de crear
        $linkModel->company_profile_id = $company_profile_id;
        $linkModel->student_user_id = $student_id;
        $linkModel->acceptance_date = date('Y-m-d'); // Fecha actual

        // Crear el vínculo
        if (!$linkModel->create()) {
            die("<h1>Error al Vincular</h1><p>No se pudo crear el vínculo. Por favor, contacte al administrador.</p>");
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
    private function getCompanyProfileId($user_id) {
        $db = Database::getInstance()->getConnection();
        $query = "SELECT id FROM company_profiles WHERE contact_person_user_id = :user_id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['id'] : null;
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
            'contact_person_name' => $company_db_data['first_name'] . ' ' . $company_db_data['last_name_p'],
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