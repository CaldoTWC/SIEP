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
    /**
 * Dashboard principal de la empresa
 */
public function dashboard() {
    $this->session->guard(['company']);
    
    // Obtener el perfil de la empresa
    $user_id = $_SESSION['user_id'];
    $userModel = new User();
    $company_profile = $userModel->getCompanyProfileByUserId($user_id);
    
    if (!$company_profile) {
        die("Error: No se encontró el perfil de empresa para este usuario.");
    }
    
    $company_profile_id = $company_profile['company_profile_id'];
    
    // Obtener las vacantes de esta empresa
    $vacancyModel = new Vacancy();
    $vacancies = $vacancyModel->getVacanciesByCompany($company_profile_id); // ← CAMBIO AQUÍ
    
    // Cargar la vista del dashboard
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
 * Procesa la publicación de una nueva vacante
 */
public function postVacancy() {
    $this->session->guard(['company']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die("Método no permitido.");
    }
    
    // Validar campos obligatorios básicos
    $required_fields = [
        'title',                // Nombre de la vacante
        'num_vacancies',        // Número de vacantes
        'economic_support',     // Apoyo económico
        'start_date',           // Fecha de inicio
        'end_date',             // Fecha de conclusión
        'related_career',       // Carrera relacionada
        'required_knowledge',   // Conocimientos requeridos
        'activities_list',      // Lista de actividades (NUEVO SISTEMA)
        'modality',             // Modalidad
        'privacy_accepted'      // Aviso de privacidad
    ];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "❌ El campo {$field} es obligatorio.";
            header('Location: /SIEP/public/index.php?action=showPostVacancyForm');
            exit;
        }
    }
    
    // Validar dirección si es Presencial o Híbrida
    if (in_array($_POST['modality'], ['Presencial', 'Híbrida']) && empty($_POST['work_location_address'])) {
        $_SESSION['error'] = "❌ La dirección es obligatoria para modalidad Presencial o Híbrida.";
        header('Location: /SIEP/public/index.php?action=showPostVacancyForm');
        exit;
    }
    
    // Obtener el company_profile_id del usuario autenticado
    $user_id = $_SESSION['user_id'];
    $userModel = new User();
    $company_profile = $userModel->getCompanyProfileByUserId($user_id);
    
    if (!$company_profile) {
        $_SESSION['error'] = "❌ No se encontró el perfil de empresa.";
        header('Location: /SIEP/public/index.php?action=companyDashboard');
        exit;
    }
    
    $company_profile_id = $company_profile['company_profile_id'];
    
    // Crear instancia del modelo Vacancy
    $vacancy = new Vacancy();
    
    // DATOS BÁSICOS
    $vacancy->company_profile_id = $company_profile_id;
    $vacancy->title = trim($_POST['title']);
    $vacancy->description = trim($_POST['key_information'] ?? ''); // Usar key_information como description
    $vacancy->activities = ''; // Campo legacy, dejar vacío
    $vacancy->modality = $_POST['modality'];
    
    // ATENCIÓN A ESTUDIANTES INTERESADOS
    $attention_days = isset($_POST['attention_days']) && is_array($_POST['attention_days']) 
        ? json_encode($_POST['attention_days']) 
        : json_encode([]);
    $vacancy->attention_days = $attention_days;
    $vacancy->attention_schedule = trim($_POST['attention_schedule'] ?? '');
    
    // GENERALIDADES DE LA POSTULACIÓN
    $vacancy->num_vacancies = (int)$_POST['num_vacancies'];
    $vacancy->vacancy_names = trim($_POST['vacancy_names'] ?? '');
    $vacancy->economic_support = (float)$_POST['economic_support'];
    $vacancy->start_date = $_POST['start_date'];
    $vacancy->end_date = $_POST['end_date'];
    
    // Validar que la fecha de fin sea posterior a la de inicio
    if (strtotime($vacancy->end_date) <= strtotime($vacancy->start_date)) {
        $_SESSION['error'] = "❌ La fecha de conclusión debe ser posterior a la fecha de inicio.";
        header('Location: /SIEP/public/index.php?action=showPostVacancyForm');
        exit;
    }
    
    // PERFIL PARA OCUPAR LA VACANTE
    $vacancy->key_information = trim($_POST['key_information'] ?? '');
    $vacancy->related_career = trim($_POST['related_career']);
    $vacancy->required_knowledge = trim($_POST['required_knowledge']);
    $vacancy->required_competencies = trim($_POST['required_competencies'] ?? '');
    
    // Idiomas requeridos (checkbox array)
    $languages = isset($_POST['required_languages']) && is_array($_POST['required_languages']) 
        ? json_encode($_POST['required_languages']) 
        : json_encode(['Ninguno']);
    $vacancy->required_languages = $languages;
    
    // ACTIVIDADES A REALIZAR (NUEVO SISTEMA SIMPLIFICADO)
    $vacancy->activities_list = trim($_POST['activities_list']); // OBLIGATORIO
    $vacancy->activity_details = trim($_POST['activity_details'] ?? ''); // OPCIONAL
    
    // MODALIDAD EN QUE SE DESARROLLARÁN LAS ACTIVIDADES
    $vacancy->work_location_address = trim($_POST['work_location_address'] ?? '');
    
    // Días de trabajo (checkbox array)
    $work_days = isset($_POST['work_days']) && is_array($_POST['work_days']) 
        ? json_encode($_POST['work_days']) 
        : json_encode([]);
    $vacancy->work_days = $work_days;
    
    $vacancy->start_time = !empty($_POST['start_time']) ? $_POST['start_time'] : null;
    $vacancy->end_time = !empty($_POST['end_time']) ? $_POST['end_time'] : null;
    
    // PUBLICACIÓN DE LOGOTIPOS
    $vacancy->logo_auth = isset($_POST['logo_auth']) && $_POST['logo_auth'] == '1' ? 1 : 0;
    $vacancy->logo_url = trim($_POST['logo_url'] ?? '');
    
    // AVISO DE PRIVACIDAD
    $vacancy->privacy_accepted = isset($_POST['privacy_accepted']) ? 1 : 0;
    
    // Crear la vacante en la base de datos
    if ($vacancy->create()) {
        
        // ============================================
        // NOTIFICAR A UPIS QUE HAY NUEVA VACANTE
        // ============================================
        try {
            require_once(__DIR__ . '/../Services/EmailService.php');
            $emailService = new EmailService();
            
            // Preparar datos de la vacante para el email
            $vacancy_data = [
                'title' => $vacancy->title,
                'num_vacancies' => $vacancy->num_vacancies,
                'economic_support' => $vacancy->economic_support,
                'start_date' => $vacancy->start_date,
                'end_date' => $vacancy->end_date,
                'modality' => $vacancy->modality
            ];
            
            // Preparar datos de la empresa para el email
            $company_data = [
                'company_name' => $company_profile['company_name'],
                'email' => $company_profile['email']
            ];
            
            // Enviar email a UPIS
            $emailService->notifyUPISNewVacancy($vacancy_data, $company_data);
            
            error_log("✅ Notificación enviada a UPIS sobre nueva vacante: {$vacancy->title}");
            
        } catch (Exception $e) {
            // No detener el proceso si falla el email, solo registrar
            error_log("⚠️ Error al enviar notificación a UPIS: " . $e->getMessage());
        }
        // ============================================
        // FIN DE NOTIFICACIONES
        // ============================================
        
        $_SESSION['success'] = "✅ Vacante publicada exitosamente. Está pendiente de aprobación por UPIS.";
        header('Location: /SIEP/public/index.php?action=companyDashboard');
        exit;
        
    } else {
        $_SESSION['error'] = "❌ Error al publicar la vacante. Por favor, intente nuevamente.";
        header('Location: /SIEP/public/index.php?action=showPostVacancyForm');
        exit;
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

    /**
 * Eliminar una vacante (solo si está en status 'pending')
 */
public function deleteVacancy() {
    $this->session->guard(['company']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['id'])) {
        $_SESSION['error'] = "Solicitud inválida.";
        header('Location: /SIEP/public/index.php?action=companyDashboard');
        exit;
    }
    
    // Obtener ID de la vacante
    $vacancy_id = isset($_POST['vacancy_id']) ? (int)$_POST['vacancy_id'] : (int)$_GET['id'];
    
    if (!$vacancy_id) {
        $_SESSION['error'] = "ID de vacante inválido.";
        header('Location: /SIEP/public/index.php?action=companyDashboard');
        exit;
    }
    
    // Obtener información de la empresa actual
    $user_id = $_SESSION['user_id'];
    $userModel = new User();
    $company_profile = $userModel->getCompanyProfileByUserId($user_id);
    
    if (!$company_profile) {
        $_SESSION['error'] = "No se encontró el perfil de empresa.";
        header('Location: /SIEP/public/index.php?action=companyDashboard');
        exit;
    }
    
    $company_profile_id = $company_profile['company_profile_id'];
    
    // Verificar que la vacante pertenece a esta empresa
    $vacancyModel = new Vacancy();
    $vacancy = $vacancyModel->getVacancyById($vacancy_id);
    
    if (!$vacancy) {
        $_SESSION['error'] = "Vacante no encontrada.";
        header('Location: /SIEP/public/index.php?action=companyDashboard');
        exit;
    }
    
    // Verificar que la vacante pertenece a esta empresa
    if ($vacancy['company_profile_id'] != $company_profile_id) {
        $_SESSION['error'] = "No tienes permiso para eliminar esta vacante.";
        header('Location: /SIEP/public/index.php?action=companyDashboard');
        exit;
    }
    
    // Solo permitir eliminar vacantes pendientes o rechazadas
    if ($vacancy['status'] === 'approved') {
        $_SESSION['error'] = "No puedes eliminar una vacante que ya ha sido aprobada. Contacta a UPIS si necesitas darla de baja.";
        header('Location: /SIEP/public/index.php?action=companyDashboard');
        exit;
    }
    
    // Eliminar la vacante (soft delete - cambia a rejected)
    if ($vacancyModel->delete($vacancy_id)) {
        $_SESSION['success'] = "✅ Vacante eliminada correctamente.";
    } else {
        $_SESSION['error'] = "❌ Error al eliminar la vacante. Intente nuevamente.";
    }
    
    header('Location: /SIEP/public/index.php?action=companyDashboard');
    exit;
}

}