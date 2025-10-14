<?php
// Archivo: src/Controllers/StudentController.php (Versión Completa y Corregida)

// Incluimos todos los modelos y librerías que este controlador necesita.
require_once(__DIR__ . '/../Models/Vacancy.php');
require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/DocumentApplication.php'); // ¡Importante!
require_once(__DIR__ . '/../Lib/Session.php');

class StudentController {

    private $session;
    private $userModel;

    public function __construct() {
        $this->session = new Session();
        $this->userModel = new User();
    }
    
    /**
     * Muestra el dashboard principal del estudiante.
     */
    public function dashboard() {
        $this->session->guard(['student']);
        require_once(__DIR__ . '/../Views/student/dashboard.php');
    }

    /**
     * Muestra la lista de vacantes aprobadas.
     */
    public function listVacancies() {
        $this->session->guard(['student']);
        $vacancyModel = new Vacancy();
        $approvedVacancies = $vacancyModel->getApprovedVacancies();
        require_once(__DIR__ . '/../Views/student/vacancies.php');
    }

    // ====================================================================
    // --- MÉTODO QUE ESTABA FALTANDO ---
    // ====================================================================
    /**
     * Procesa la solicitud de una Carta de Presentación y la guarda en la base de datos
     * para que sea revisada por la UPIS.
     */
    public function showDetailedLetterForm() {
        $this->session->guard(['student']);
        
        // Obtenemos los datos del perfil del usuario logueado para autocompletar el formulario
        $profile_data = $this->userModel->getStudentProfileForForm($_SESSION['user_id']);
        
        // Cargamos la vista y le pasamos los datos del perfil
        require_once(__DIR__ . '/../Views/student/request_presentation_letter.php');
    }

    /**
 * Procesa el formulario de solicitud de carta de presentación
 */
public function submitDetailedLetterRequest() {
    $this->session->guard(['student']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['transcript'])) {
        header('Location: /SIEP/public/index.php?action=showDetailedLetterForm&status=error');
        exit;
    }
    
    // Obtener datos del estudiante
    $profile = $this->userModel->getStudentProfileForForm($_SESSION['user_id']);
    $boleta = $profile['boleta'];
    $first_name = $profile['first_name'];
    $last_name_p = $profile['last_name_p'];
    
    // ✅ USAR FileHelper
    require_once(__DIR__ . '/../Lib/FileHelper.php');
    $upload_dir = FileHelper::getStudentSubfolder($boleta, $first_name, $last_name_p, 'transcripts');
    
    $transcript_file = $_FILES['transcript'];
    
    if ($transcript_file['error'] !== UPLOAD_ERR_OK) {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=upload_error');
        exit;
    }
    
    $allowed_ext = ['pdf'];
    $transcript_ext = strtolower(pathinfo($transcript_file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($transcript_ext, $allowed_ext)) {
        header('Location: /SIEP/public/index.php?action=showDetailedLetterForm&status=invalid_file');
        exit;
    }
    
    $transcript_new_name = $boleta . '_BoletaGlobal_' . time() . '.pdf';
    $full_path = $upload_dir . '/' . $transcript_new_name;
    
    if (!move_uploaded_file($transcript_file['tmp_name'], $full_path)) {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=upload_error');
        exit;
    }
    
    // ✅ Guardar ruta relativa
    $transcript_path = FileHelper::getRelativePath($full_path);
    
    $applicationModel = new DocumentApplication();
    $data = [
        'student_user_id' => $_SESSION['user_id'],
        'credits_percentage' => $_POST['credits_percentage'],
        'current_semester' => $_POST['semester'],
        'transcript_path' => $transcript_path,
        'target_company_name' => null,
        'target_recipient_name' => null,
        'target_recipient_position' => null,
        'show_required_hours' => 1
    ];

    if ($applicationModel->create($data)) {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=request_sent');
    } else {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=request_failed');
    }
    exit;
}

/**
 * Procesa la subida de documentos de acreditación
 */
public function submitAccreditation() {
    $this->session->guard(['student']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['final_report']) || empty($_FILES['signed_validation_letter'])) {
        die("Acceso no válido o faltan archivos.");
    }

    $student_id = $_SESSION['user_id'];
    $profile = $this->userModel->getStudentProfileById($student_id);
    $boleta = $profile['boleta'];
    
    // ✅ USAR FileHelper
    require_once(__DIR__ . '/../Lib/FileHelper.php');
    $upload_dir = FileHelper::getStudentSubfolder($boleta, $profile['first_name'], $profile['last_name_p'], 'accreditation');

    // Procesar archivos
    $report_file = $_FILES['final_report'];
    $report_ext = pathinfo($report_file['name'], PATHINFO_EXTENSION);
    $report_new_name = $boleta . '_ReporteFinal_' . time() . '.' . $report_ext;
    $report_full_path = $upload_dir . '/' . $report_new_name;
    
    if (!move_uploaded_file($report_file['tmp_name'], $report_full_path)) {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=upload_error'); 
        exit;
    }

    $letter_file = $_FILES['signed_validation_letter'];
    $letter_ext = pathinfo($letter_file['name'], PATHINFO_EXTENSION);
    $letter_new_name = $boleta . '_ConstanciaValidacion_' . time() . '.' . $letter_ext;
    $letter_full_path = $upload_dir . '/' . $letter_new_name;
    
    if (!move_uploaded_file($letter_file['tmp_name'], $letter_full_path)) {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=upload_error'); 
        exit;
    }

    // ✅ Guardar rutas relativas
    $report_path = FileHelper::getRelativePath($report_full_path);
    $letter_path = FileHelper::getRelativePath($letter_full_path);

    require_once(__DIR__ . '/../Models/Accreditation.php');
    $accreditationModel = new Accreditation();

    if ($accreditationModel->createSubmission($student_id, $report_path, $letter_path)) {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=accreditation_sent');
    } else {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=upload_error');
    }
    exit;
}

    public function showMyDocuments() {
    $this->session->guard(['student']);
    
    require_once(__DIR__ . '/../Models/StudentDocument.php');
    $docModel = new StudentDocument();
    $myDocuments = $docModel->getByStudentId($_SESSION['user_id']); // ✅ CORRECTO
    
    require_once(__DIR__ . '/../Views/student/my_documents.php');
    }

     public function showAccreditationForm() {
        $this->session->guard(['student']);
        require_once(__DIR__ . '/../Views/student/accreditation_form.php');
    }

    
}