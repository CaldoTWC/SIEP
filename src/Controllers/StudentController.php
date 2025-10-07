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
     * Procesa el formulario detallado y la subida de la boleta global.
     */
    /**
 * Procesa el formulario detallado y la subida de la boleta global.
 */
public function submitDetailedLetterRequest() {
    $this->session->guard(['student']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['transcript'])) {
        header('Location: /SIEP/public/index.php?action=showDetailedLetterForm&status=error');
        exit;
    }
    
    // --- Manejo de la subida del archivo (Boleta Global) ---
    $upload_dir = __DIR__ . '/../../public/uploads/transcripts/';
    if (!is_dir($upload_dir)) { 
        mkdir($upload_dir, 0755, true); 
    }

    $transcript_file = $_FILES['transcript'];
    
    // Validar archivo
    if ($transcript_file['error'] !== UPLOAD_ERR_OK) {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=upload_error');
        exit;
    }
    
    // Validar tipo de archivo (solo PDF)
    $allowed_ext = ['pdf'];
    $transcript_ext = strtolower(pathinfo($transcript_file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($transcript_ext, $allowed_ext)) {
        header('Location: /SIEP/public/index.php?action=showDetailedLetterForm&status=invalid_file');
        exit;
    }
    
    // Obtener boleta del estudiante
    $profile = $this->userModel->getStudentProfileForForm($_SESSION['user_id']);
    $boleta = $profile['boleta'];

    // Generar nombre único para el archivo
    $transcript_new_name = $boleta . '_BoletaGlobal_' . time() . '.' . $transcript_ext;

    // Subir archivo
    if (!move_uploaded_file($transcript_file['tmp_name'], $upload_dir . $transcript_new_name)) {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=upload_error');
        exit;
    }

    // --- Guardar la solicitud en la Base de Datos ---
    $applicationModel = new DocumentApplication();
    
    // Preparar datos según lo que espera el método create()
    $data = [
        'student_user_id' => $_SESSION['user_id'],
        'credits_percentage' => $_POST['credits_percentage'],
        'current_semester' => $_POST['semester'],
        'transcript_path' => 'public/uploads/transcripts/' . $transcript_new_name,
        'target_company_name' => null,  // Opcional por ahora
        'target_recipient_name' => null,  // Opcional por ahora
        'target_recipient_position' => null,  // Opcional por ahora
        'show_required_hours' => 1  // Por defecto mostrar horas
    ];

    // Llamar al método correcto: create()
    if ($applicationModel->create($data)) {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=request_sent');
    } else {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=request_failed');
    }
    exit;
    }
    // ====================================================================

    // En el futuro, aquí irían otros métodos como el que muestra el formulario
    // para subir documentos de acreditación.
    public function showMyDocuments() {
        $this->session->guard(['student']);
        
        // Incluimos el modelo y obtenemos los documentos
        require_once(__DIR__ . '/../Models/StudentDocument.php');
        $docModel = new StudentDocument();
        $myDocuments = $docModel->getDocumentsByStudentId($_SESSION['user_id']);
        
        // Cargamos la vista y le pasamos los datos
        require_once(__DIR__ . '/../Views/student/my_documents.php');
    }

     public function showAccreditationForm() {
        $this->session->guard(['student']);
        require_once(__DIR__ . '/../Views/student/accreditation_form.php');
    }

     public function submitAccreditation() {
        $this->session->guard(['student']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['final_report']) || empty($_FILES['signed_validation_letter'])) {
            die("Acceso no válido o faltan archivos.");
        }

        // --- Manejo de la subida de archivos ---
        $upload_dir = __DIR__ . '/../../public/uploads/accreditation_docs/';
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }

        $student_id = $_SESSION['user_id'];
        $boleta = $this->userModel->getStudentProfileById($student_id)['boleta'];

        // Procesar Reporte Final
        $report_file = $_FILES['final_report'];
        $report_ext = pathinfo($report_file['name'], PATHINFO_EXTENSION);
        $report_new_name = $boleta . '_ReporteFinal_' . time() . '.' . $report_ext;
        if (!move_uploaded_file($report_file['tmp_name'], $upload_dir . $report_new_name)) {
            header('Location: /SIEP/public/index.php?action=studentDashboard&status=upload_error'); exit;
        }

        // Procesar Constancia de Validación
        $letter_file = $_FILES['signed_validation_letter'];
        $letter_ext = pathinfo($letter_file['name'], PATHINFO_EXTENSION);
        $letter_new_name = $boleta . '_ConstanciaValidacion_' . time() . '.' . $letter_ext;
        if (!move_uploaded_file($letter_file['tmp_name'], $upload_dir . $letter_new_name)) {
            header('Location: /SIEP/public/index.php?action=studentDashboard&status=upload_error'); exit;
        }

        // --- Guardar en la Base de Datos ---
        require_once(__DIR__ . '/../Models/Accreditation.php');
        $accreditationModel = new Accreditation();
        
        $report_path_for_db = 'public/uploads/accreditation_docs/' . $report_new_name;
        $letter_path_for_db = 'public/uploads/accreditation_docs/' . $letter_new_name;

        if ($accreditationModel->createSubmission($student_id, $report_path_for_db, $letter_path_for_db)) {
            header('Location: /SIEP/public/index.php?action=studentDashboard&status=accreditation_sent');
        } else {
            header('Location: /SIEP/public/index.php?action=studentDashboard&status=upload_error');
        }
        exit;
    }
}