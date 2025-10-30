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

    /**
 * Muestra las vacantes disponibles (alias de listVacancies)
 */
public function showVacancies() {
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
/**
 * Procesa el formulario de solicitud de carta de presentación
 * VERSIÓN ACTUALIZADA: Soporte para 4 variantes de plantillas
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
    
    // ✅ NUEVOS CAMPOS: Determinar tipo de carta solicitada
    $has_specific_recipient = isset($_POST['has_specific_recipient']) ? (int)$_POST['has_specific_recipient'] : 0;
    $requires_hours = isset($_POST['requires_hours']) ? (int)$_POST['requires_hours'] : 0;
    
    // Datos del destinatario (si aplica)
    $recipient_name = null;
    $recipient_position = null;
    if ($has_specific_recipient) {
        $recipient_name = trim($_POST['recipient_name'] ?? '');
        $recipient_position = trim($_POST['recipient_position'] ?? '');
        
        // Validar que se proporcionaron los datos del destinatario
        if (empty($recipient_name) || empty($recipient_position)) {
            header('Location: /SIEP/public/index.php?action=showDetailedLetterForm&status=recipient_required');
            exit;
        }
    }
    
    // ✅ DETERMINAR TIPO DE PLANTILLA
    require_once(__DIR__ . '/../Models/LetterTemplate.php');
    $templateModel = new LetterTemplate();
    $letter_template_type = $templateModel->determineTemplateType($has_specific_recipient, $requires_hours);
    
    // Validar que la plantilla existe
    if (!$templateModel->templateExists($letter_template_type)) {
        header('Location: /SIEP/public/index.php?action=showDetailedLetterForm&status=template_not_found');
        exit;
    }
    
    // ✅ PROCESAR ARCHIVO DE KÁRDEX
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
    
    $transcript_new_name = $boleta . '_Kardex_' . time() . '.pdf';
    $full_path = $upload_dir . '/' . $transcript_new_name;
    
    if (!move_uploaded_file($transcript_file['tmp_name'], $full_path)) {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=upload_error');
        exit;
    }
    
    // ✅ Guardar ruta relativa
    $transcript_path = FileHelper::getRelativePath($full_path);
    
    // ✅ PREPARAR DATOS PARA INSERCIÓN
    $applicationModel = new DocumentApplication();
    $data = [
        'student_user_id' => $_SESSION['user_id'],
        'credits_percentage' => $_POST['credits_percentage'],
        'current_semester' => $_POST['semester'],
        'transcript_path' => $transcript_path,
        'target_company_name' => trim($_POST['target_company_name'] ?? ''),
        'has_specific_recipient' => $has_specific_recipient,
        'recipient_name' => $recipient_name,
        'recipient_position' => $recipient_position,
        'requires_hours' => $requires_hours,
        'letter_template_type' => $letter_template_type
    ];

    // ✅ GUARDAR SOLICITUD
    $application_id = $applicationModel->createWithTemplate($data);
    
    if ($application_id) {
        // Redirigir con éxito indicando el tipo de carta solicitada
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=request_sent&template=' . $letter_template_type);
    } else {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=request_failed');
    }
    exit;
}

/**
 * Procesa la subida de documentos de acreditación
 */
/**
 * Procesa la subida de documentos de acreditación (VERSIÓN COMPLETA)
 */
/**
 * Procesa la subida de documentos de acreditación (VERSIÓN COMPLETA)
 */
public function submitAccreditation() {
    $this->session->guard(['student']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /SIEP/public/index.php?action=showAccreditationForm&status=invalid_request');
        exit;
    }

    $student_id = $_SESSION['user_id'];
    
    // ✅ USAR EL MÉTODO CORRECTO
    $profile = $this->userModel->getStudentProfileForForm($student_id);
    
    if (!$profile) {
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=profile_error');
        exit;
    }
    
    $boleta = $profile['boleta'];
    
    // ========================================
    // 1. VALIDAR CHECKBOX DE PRIVACIDAD
    // ========================================
    if (!isset($_POST['privacy_accept']) || $_POST['privacy_accept'] !== 'on') {
        header('Location: /SIEP/public/index.php?action=showAccreditationForm&status=privacy_required');
        exit;
    }
    
    // ========================================
    // 2. DETECTAR TIPO A o B AUTOMÁTICAMENTE
    // ========================================
    $empresa_registrada = $_POST['empresa_registrada'] ?? '';
    $tipo_acreditacion = ($empresa_registrada === 'si') ? 'B' : 'A';
    
    // ========================================
    // 3. PREPARAR METADATA (JSON)
    // ========================================
    $metadata = [
        'student_info' => [
            'nombres' => $_POST['nombres'] ?? '',
            'apellido_paterno' => $_POST['apellido_paterno'] ?? '',
            'apellido_materno' => $_POST['apellido_materno'] ?? '',
            'boleta' => $_POST['boleta'] ?? '',
            'email_institucional' => $_POST['email_institucional'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'programa_academico' => $_POST['programa_academico'] ?? '',
            'semestre' => $_POST['semestre'] ?? ''
        ],
        'company_info' => [
            'agencia_colocacion' => $_POST['agencia_colocacion'] ?? '',
            'nombre_comercial' => $_POST['nombre_comercial'] ?? '',
            'tipo_empresa' => $_POST['tipo_empresa'] ?? '',
            'giro' => $_POST['giro'] ?? '',
            'razon_social' => $_POST['razon_social'] ?? '',
            'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
            'fecha_fin' => $_POST['fecha_fin'] ?? '',
            'dias_estancia' => $_POST['dias_estancia'] ?? [],
            'nombre_contacto' => $_POST['nombre_contacto'] ?? '',
            'email_contacto' => $_POST['email_contacto'] ?? '',
            'telefono_contacto' => $_POST['telefono_contacto'] ?? '',
            'empresa_registrada' => $empresa_registrada
        ],
        'tipo_acreditacion' => $tipo_acreditacion,
        'privacy_accepted' => true,
        'submission_date' => date('Y-m-d H:i:s')
    ];
    
    // ========================================
    // 4. PROCESAR ARCHIVOS
    // ========================================
    require_once(__DIR__ . '/../Lib/FileHelper.php');
    $upload_dir = FileHelper::getStudentSubfolder($boleta, $profile['first_name'], $profile['last_name_p'], 'accreditation');
    
    $archivos_subidos = [];
    
    // 4.1 Boleta Global (OBLIGATORIO PARA TODOS)
    if (isset($_FILES['boleta_global']) && $_FILES['boleta_global']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['boleta_global'];
        $filename = $boleta . '_BoletaGlobal_' . time() . '.pdf';
        $full_path = $upload_dir . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $full_path)) {
            $archivos_subidos['boleta_global'] = FileHelper::getRelativePath($full_path);
        }
    } else {
        header('Location: /SIEP/public/index.php?action=showAccreditationForm&status=missing_boleta');
        exit;
    }
    
    // 4.2 Archivos según TIPO A o B
    if ($tipo_acreditacion === 'A') {
        // TIPO A: Empresa NO Registrada
        
        // Recibos de nómina (múltiples archivos)
        if (isset($_FILES['recibos_nomina']) && !empty($_FILES['recibos_nomina']['name'][0])) {
            $recibos = [];
            $total_recibos = count($_FILES['recibos_nomina']['name']);
            
            for ($i = 0; $i < $total_recibos; $i++) {
                if ($_FILES['recibos_nomina']['error'][$i] === UPLOAD_ERR_OK) {
                    $filename = $boleta . '_ReciboNomina_' . ($i + 1) . '_' . time() . '.pdf';
                    $full_path = $upload_dir . '/' . $filename;
                    
                    if (move_uploaded_file($_FILES['recibos_nomina']['tmp_name'][$i], $full_path)) {
                        $recibos[] = FileHelper::getRelativePath($full_path);
                    }
                }
            }
            
            $archivos_subidos['recibos_nomina'] = $recibos;
        }
        
        // Constancia laboral
        if (isset($_FILES['constancia_laboral']) && $_FILES['constancia_laboral']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['constancia_laboral'];
            $filename = $boleta . '_ConstanciaLaboral_' . time() . '.pdf';
            $full_path = $upload_dir . '/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $full_path)) {
                $archivos_subidos['constancia_laboral'] = FileHelper::getRelativePath($full_path);
            }
        }
        
        // Reporte final
        if (isset($_FILES['reporte_final']) && $_FILES['reporte_final']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['reporte_final'];
            $filename = $boleta . '_ReporteFinal_' . time() . '.pdf';
            $full_path = $upload_dir . '/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $full_path)) {
                $archivos_subidos['reporte_final'] = FileHelper::getRelativePath($full_path);
            }
        }
        
    } else {
        // TIPO B: Empresa Registrada
        
        // Carta de aceptación
        if (isset($_FILES['carta_aceptacion']) && $_FILES['carta_aceptacion']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['carta_aceptacion'];
            $filename = $boleta . '_CartaAceptacion_' . time() . '.pdf';
            $full_path = $upload_dir . '/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $full_path)) {
                $archivos_subidos['carta_aceptacion'] = FileHelper::getRelativePath($full_path);
            }
        }
        
        // Constancia de validación
        if (isset($_FILES['constancia_validacion']) && $_FILES['constancia_validacion']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['constancia_validacion'];
            $filename = $boleta . '_ConstanciaValidacion_' . time() . '.pdf';
            $full_path = $upload_dir . '/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $full_path)) {
                $archivos_subidos['constancia_validacion'] = FileHelper::getRelativePath($full_path);
            }
        }
        
        // Reporte final
        if (isset($_FILES['reporte_final']) && $_FILES['reporte_final']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['reporte_final'];
            $filename = $boleta . '_ReporteFinal_' . time() . '.pdf';
            $full_path = $upload_dir . '/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $full_path)) {
                $archivos_subidos['reporte_final'] = FileHelper::getRelativePath($full_path);
            }
        }
    }
    
    // Agregar archivos a metadata
    $metadata['documents'] = $archivos_subidos;
    
    // ========================================
    // 5. GUARDAR EN BASE DE DATOS
    // ========================================
    require_once(__DIR__ . '/../Models/Accreditation.php');
    $accreditationModel = new Accreditation();
    
    // Preparar datos principales
    $boleta_estudiante = $_POST['boleta'] ?? $boleta;
    $programa = $_POST['programa_academico'] ?? '';
    $empresa = $_POST['nombre_comercial'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;
    
    // Usar el método createSubmissionComplete
    $result = $accreditationModel->createSubmissionComplete(
        $student_id,
        $boleta_estudiante,
        $programa,
        $empresa,
        $tipo_acreditacion,
        $fecha_inicio,
        $fecha_fin,
        $archivos_subidos['reporte_final'] ?? '',
        $archivos_subidos['constancia_validacion'] ?? $archivos_subidos['constancia_laboral'] ?? '',
        $metadata
    );
    
    if ($result) {
        // ========================================
        // 6. ENVIAR NOTIFICACIONES POR EMAIL
        // ========================================
        require_once(__DIR__ . '/../Services/EmailService.php');
        $emailService = new EmailService();
        
        // Datos del estudiante
        $student_data = [
            'user_id' => $student_id,
            'full_name' => $profile['first_name'] . ' ' . 
                           $profile['last_name_p'] . ' ' . 
                           $profile['last_name_m'],
            'boleta' => $boleta,
            'career' => $programa,
            'email' => $profile['email']
        ];
        
        // Datos de la acreditación
        $submission_data = [
            'id' => $accreditationModel->getLastInsertId(),
            'tipo' => $tipo_acreditacion,
            'empresa_nombre' => $empresa,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'created_at' => date('Y-m-d H:i:s'),
            'review_url' => getenv('SITE_URL') . '/index.php?action=reviewAccreditation&id=' . $accreditationModel->getLastInsertId()
        ];
        
        // Enviar confirmación al estudiante
        $emailService->notifyStudentAccreditationReceived($student_data, $submission_data);
        
        header('Location: /SIEP/public/index.php?action=studentDashboard&status=accreditation_sent&tipo=' . $tipo_acreditacion);
    } else {
        header('Location: /SIEP/public/index.php?action=showAccreditationForm&status=upload_error');
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