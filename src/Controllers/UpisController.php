<?php
/**
 * Controlador de UPIS
 * 
 * Gestiona las funciones administrativas de la Unidad Politécnica
 * de Integración Social (UPIS)
 * 
 * @package SIEP\Controllers
 * @version 2.0.0 - Actualizado para nueva estructura
 */

require_once(__DIR__ . '/../Models/User.php');
require_once(__DIR__ . '/../Models/Vacancy.php');
require_once(__DIR__ . '/../Models/DocumentApplication.php');
require_once(__DIR__ . '/../Models/Accreditation.php');
require_once(__DIR__ . '/../Models/CompletedProcess.php');
require_once(__DIR__ . '/../Models/StudentDocument.php');
require_once(__DIR__ . '/../Lib/Session.php');
require_once(__DIR__ . '/../Lib/DocumentGenerator.php');

class UpisController {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }
    
    // ========================================================================
    // VISTAS DEL DASHBOARD
    // ========================================================================
    
    /**
     * Dashboard principal de UPIS
     */
    
    public function dashboard() {
        $this->session->guard(['upis', 'admin']);
        
        $userModel = new User();
        $vacancyModel = new Vacancy();
        $applicationModel = new DocumentApplication();
        
        $pendingCompaniesCount = count($userModel->getPendingCompanies());
        $pendingVacanciesCount = count($vacancyModel->getPendingVacancies());
        $pendingLettersCount = count($applicationModel->getPendingPresentationLetters());
        
        require_once(__DIR__ . '/../Views/upis/dashboard_hub.php');
    }

    /**
     * Vista de revisión de empresas pendientes
     */
    public function reviewCompanies() {
        $this->session->guard(['upis', 'admin']);
        
        $userModel = new User();
        $pendingCompanies = $userModel->getPendingCompanies();
        
        require_once(__DIR__ . '/../Views/upis/review_companies.php');
    }
    
    /**
     * Vista de revisión de vacantes pendientes
     */
    public function reviewVacancies() {
        $this->session->guard(['upis', 'admin']);
        $vacancyModel = new Vacancy();
        $pendingVacancies = $vacancyModel->getPendingVacancies();
        require_once(__DIR__ . '/../Views/upis/review_vacancies.php');
    }
    
    /**
     * Vista de revisión de cartas de presentación
     */
    public function reviewLetters() {
        $this->session->guard(['upis', 'admin']);
        $applicationModel = new DocumentApplication();
        $pendingLetters = $applicationModel->getPendingPresentationLetters();
        $approvedLetters = $applicationModel->getApprovedPresentationLetters();
        require_once(__DIR__ . '/../Views/upis/review_letters.php');
    }

    public function showUploadDocumentsForm() {
    $this->session->guard(['upis', 'admin']);
    require_once(__DIR__ . '/../Views/upis/upload_documents.php');
    }

    // ========================================================================
    // ACCIONES DE GESTIÓN DE EMPRESAS
    // ========================================================================

/**
 * Aprueba una empresa (cambia status de 'pending' a 'active')
 */
public function approveCompany() {
    $this->session->guard(['upis', 'admin']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Método no permitido.";
        header('Location: /SIEP/public/index.php?action=reviewCompanies');
        exit;
    }
    
    $company_id = (int)($_POST['company_id'] ?? 0);
    $comments = trim($_POST['comments'] ?? '');
    
    if (!$company_id) {
        $_SESSION['error'] = "ID de empresa no válido.";
        header('Location: /SIEP/public/index.php?action=reviewCompanies');
        exit;
    }
    
    $userModel = new User();
    
    // Aprobar la empresa (cambia status de 'pending' a 'active')
    if ($userModel->approveUser($company_id)) {
        
        // ========================================
        // OPCIONAL: ENVIAR NOTIFICACIÓN POR EMAIL
        // ========================================
        // Si quieres enviar un correo de confirmación, descomenta esto:
        /*
        try {
            $company_data = $userModel->getCompanyProfileByUserId($company_id);
            
            if ($company_data && !empty($company_data['email'])) {
                require_once(__DIR__ . '/../Lib/Mailer.php');
                
                $subject = "✅ Su empresa ha sido aprobada - SIEP UPIS";
                
                $message = "
                    <h2 style='color: #4caf50;'>¡Felicidades!</h2>
                    <p>Estimado/a <strong>{$company_data['first_name']} {$company_data['last_name_p']}</strong>,</p>
                    
                    <p>Nos complace informarle que su empresa <strong>{$company_data['company_name']}</strong> 
                    ha sido <strong style='color: #4caf50;'>APROBADA</strong> en el sistema SIEP.</p>
                    
                    <p>Ya puede iniciar sesión y comenzar a publicar vacantes para estudiantes.</p>
                    
                    " . (!empty($comments) ? "<p><strong>Comentarios de UPIS:</strong><br>" . nl2br(htmlspecialchars($comments)) . "</p>" : "") . "
                    
                    <p>
                        <a href='http://localhost/SIEP/public/index.php?action=showLogin' 
                           style='display: inline-block; padding: 12px 24px; background: #4caf50; color: white; 
                                  text-decoration: none; border-radius: 5px; margin-top: 10px;'>
                            Iniciar Sesión
                        </a>
                    </p>
                    
                    <hr style='margin: 20px 0; border: none; border-top: 1px solid #ddd;'>
                    <p style='color: #666; font-size: 12px;'>
                        Este es un mensaje automático del Sistema Integral de Estancias Profesionales (SIEP) - UPIS ESCOM IPN
                    </p>
                ";
                
                mailer_send($company_data['email'], $subject, $message);
            }
        } catch (Exception $e) {
            error_log("Error al enviar email de aprobación: " . $e->getMessage());
            // No detenemos el proceso si falla el email
        }
        */
        // ========================================
        // FIN DE NOTIFICACIONES
        // ========================================
        
        $_SESSION['success'] = "✅ Empresa aprobada correctamente.";
        header('Location: /SIEP/public/index.php?action=reviewCompanies');
        exit;
        
    } else {
        $_SESSION['error'] = "❌ Error al aprobar la empresa. Intente nuevamente.";
        header('Location: /SIEP/public/index.php?action=reviewCompanies');
        exit;
    }
}

/**
 * Rechaza una empresa (cambia status de 'pending' a 'rejected')
 */
public function rejectCompany() {
    $this->session->guard(['upis', 'admin']);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $company_id = (int)$_POST['company_id'];
        $comments = trim($_POST['comments'] ?? '');
        
        if (empty($company_id)) {
            $_SESSION['error'] = "ID de empresa inválido.";
            header('Location: /SIEP/public/index.php?action=reviewCompanies');
            exit;
        }
        
        if (empty($comments)) {
            $_SESSION['error'] = "Debes proporcionar una razón para el rechazo.";
            header('Location: /SIEP/public/index.php?action=reviewCompanies');
            exit;
        }
        
        $userModel = new User();
        
        if ($userModel->rejectUser($company_id)) {
            
            // ========================================
            // 🆕 ENVIAR NOTIFICACIÓN A LA EMPRESA
            // ========================================
            
            require_once(__DIR__ . '/../Services/EmailService.php');
            $emailService = new EmailService();
            
            // Obtener datos de la empresa
            $company = $userModel->findById($company_id);
            $company_profile = $userModel->getCompanyProfileByUserId($company_id);
            
            $company_data = [
                'user_id' => $company_id,
                'contact_name' => $company['first_name'] . ' ' . 
                                 $company['last_name_p'] . ' ' . 
                                 $company['last_name_m'],
                'company_name' => $company_profile['company_name'] ?? 'N/A',
                'rfc' => $company_profile['rfc'] ?? 'N/A',
                'email' => $company['email']
            ];
            
            // Enviar notificación de rechazo con comentarios
            $emailService->notifyCompanyStatus($company_data, 'rejected', $comments);
            
            // ========================================
            // FIN DE NOTIFICACIONES
            // ========================================
            
            $_SESSION['success'] = "Empresa rechazada y notificada por email.";
            header('Location: /SIEP/public/index.php?action=reviewCompanies');
            exit;
        }
    }
    
    $_SESSION['error'] = "Error al rechazar la empresa.";
    header('Location: /SIEP/public/index.php?action=reviewCompanies');
    exit;
}

    /**
 * Aprobar una vacante
 */
/**
 * Aprobar una vacante
 */
public function approveVacancy() {
    $this->session->guard(['upis', 'admin']);
    
    $vacancy_id = (int)($_GET['id'] ?? $_POST['vacancy_id'] ?? 0);
    $comments = trim($_POST['comments'] ?? '');
    
    if (!$vacancy_id) {
        $_SESSION['error'] = "ID de vacante inválido.";
        header('Location: /SIEP/public/index.php?action=reviewVacancies');
        exit;
    }
    
    $vacancyModel = new Vacancy();
    $vacancy = $vacancyModel->getVacancyById($vacancy_id);
    
    if (!$vacancy) {
        $_SESSION['error'] = "Vacante no encontrada.";
        header('Location: /SIEP/public/index.php?action=reviewVacancies');
        exit;
    }
    
    $reviewer_id = $_SESSION['user_id'];
    
    if ($vacancyModel->approve($vacancy_id, $reviewer_id)) {
        
        // Enviar email de aprobación
        require_once(__DIR__ . '/../Services/EmailService.php');
        $emailService = new EmailService();
        
        $company_data = [
            'email' => $vacancy['company_email'],
            'company_name' => $vacancy['company_name']
        ];
        
        $emailService->notifyVacancyApproved($vacancy, $company_data, $comments);
        
        $_SESSION['success'] = "✅ Vacante aprobada y notificación enviada por email.";
    } else {
        $_SESSION['error'] = "❌ Error al aprobar la vacante.";
    }
    
    header('Location: /SIEP/public/index.php?action=reviewVacancies');
    exit;
}

/**
 * Rechazar una vacante
 */
public function rejectVacancy() {
    $this->session->guard(['upis', 'admin']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Método no permitido.";
        header('Location: /SIEP/public/index.php?action=reviewVacancies');
        exit;
    }
    
    $vacancy_id = (int)($_POST['vacancy_id'] ?? 0);
    $rejection_reason = trim($_POST['rejection_reason'] ?? $_POST['comments'] ?? '');
    
    if (!$vacancy_id || empty($rejection_reason)) {
        $_SESSION['error'] = "Debe proporcionar una razón para el rechazo.";
        header('Location: /SIEP/public/index.php?action=reviewVacancies');
        exit;
    }
    
    $vacancyModel = new Vacancy();
    $vacancy = $vacancyModel->getVacancyById($vacancy_id);
    
    if (!$vacancy) {
        $_SESSION['error'] = "Vacante no encontrada.";
        header('Location: /SIEP/public/index.php?action=reviewVacancies');
        exit;
    }
    
    $reviewer_id = $_SESSION['user_id'];
    
    if ($vacancyModel->reject($vacancy_id, $reviewer_id)) {
        
        // Enviar email con razón del rechazo
        require_once(__DIR__ . '/../Services/EmailService.php');
        $emailService = new EmailService();
        
        $company_data = [
            'email' => $vacancy['company_email'],
            'company_name' => $vacancy['company_name']
        ];
        
        $emailService->notifyVacancyRejected($vacancy, $company_data, $rejection_reason);
        
        $_SESSION['success'] = "❌ Vacante rechazada y notificación enviada por email.";
    } else {
        $_SESSION['error'] = "Error al rechazar la vacante.";
    }
    
    header('Location: /SIEP/public/index.php?action=reviewVacancies');
    exit;
}


    public function processLetterRequests() {
        $this->session->guard(['upis', 'admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['bulk_action']) || empty($_POST['request_ids'])) {
            header('Location: /SIEP/public/index.php?action=upisDashboard&status=no_selection');
            exit;
        }
        $action = $_POST['bulk_action'];
        $request_ids = $_POST['request_ids'];
        $count = count($request_ids);
        $reviewer_id = $_SESSION['user_id'];
        $new_status = '';
        $redirect_status = '';
        if ($action === 'approve') {
            $new_status = 'approved';
            $redirect_status = 'letters_approved';
        } elseif ($action === 'reject') {
            $new_status = 'rejected';
            $redirect_status = 'letters_rejected';
        } else {
            header('Location: /SIEP/public/index.php?action=upisDashboard&status=error');
            exit;
        }
        $applicationModel = new DocumentApplication();
        if ($applicationModel->updateStatusForMultipleIds($request_ids, $new_status, $reviewer_id)) {
            header('Location: /SIEP/public/index.php?action=reviewLetters&status=' . $redirect_status . '&count=' . $count);        } else {
            header('Location: /SIEP/public/index.php?action=reviewLetters&status=error');        }
        exit;
    }

    /**
 * Descargar todas las cartas aprobadas como ZIP
 * VERSIÓN 2.0: Con sistema de plantillas y numeración automática
 */
/**
 * Descargar todas las cartas aprobadas como ZIP
 * VERSIÓN 2.0: Con sistema de plantillas y numeración automática
 */
public function downloadAllApprovedLetters() {
    $this->session->guard(['upis', 'admin']);
    
    $applicationModel = new DocumentApplication();
    $approved_ids = $applicationModel->getAllApprovedLetterIds();
    
    if (empty($approved_ids)) {
        header('Location: /SIEP/public/index.php?action=reviewLetters&status=no_approved_letters');
        exit;
    }
    
    // ✅ PASO 1: Generar números de oficio para las cartas que no lo tengan
    require_once(__DIR__ . '/../Models/LetterTemplate.php');
    $templateModel = new LetterTemplate();
    
    foreach ($approved_ids as $app_id) {
        $application = $applicationModel->findById($app_id);
        
        // Si no tiene número de oficio, generarlo
        if (empty($application['letter_number'])) {
            $letter_number = $templateModel->generateNextLetterNumber();
            if ($letter_number) {
                $applicationModel->assignLetterNumber($app_id, $letter_number);
            }
        }
    }
    
    // ✅ PASO 2: Obtener datos actualizados con números de oficio
    $students_data = $applicationModel->getApprovedStudentDataForLetters($approved_ids);
    
    if (empty($students_data)) {
        die("Error: No se encontraron datos para las solicitudes aprobadas.");
    }
    
    // ✅ PASO 3: Crear ZIP
    $zip = new ZipArchive();
    $zipFileName = 'Cartas_Presentacion_' . date('Y-m-d_His') . '.zip';
    $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;
    
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        die("Error al crear archivo ZIP.");
    }
    
    // ✅ PASO 4: Generar cada PDF
    $docGenerator = new DocumentService(); // ✅ NOMBRE CORRECTO DE LA CLASE
    
    foreach ($students_data as $student) {
        $student_data_for_pdf = [
            'full_name' => $student['first_name'] . ' ' . $student['last_name_p'] . ' ' . $student['last_name_m'],
            'boleta' => $student['boleta'],
            'career' => $student['career'],
            'percentage_progress' => $student['percentage_progress'],
            'has_specific_recipient' => (bool)($student['has_specific_recipient'] ?? 0),
            'recipient_name' => $student['recipient_name'] ?? null,
            'recipient_position' => $student['recipient_position'] ?? null,
            'requires_hours' => (bool)($student['requires_hours'] ?? 0),
            'letter_template_type' => $student['letter_template_type'] ?? 'normal'
        ];
        
        // Obtener número de oficio
        $application = $applicationModel->findById($student['application_id']);
        $letter_number = $application['letter_number'] ?? 'No. 00-2025/2';
        
        // Generar PDF
        $pdfContent = $docGenerator->generatePresentationLetter($student_data_for_pdf, $letter_number, true);
        
        // Nombre del archivo
        $pdfFileName = $student['boleta'] . '_CPSF.pdf';
        
        // Agregar al ZIP
        $zip->addFromString($pdfFileName, $pdfContent);
    }
    
    $zip->close();
    
    // ✅ PASO 5: Descargar ZIP
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
    header('Content-Length: ' . filesize($zipFilePath));
    header('Pragma: no-cache');
    header('Expires: 0');
    
    readfile($zipFilePath);
    unlink($zipFilePath);
    exit;
}

    public function clearAllApprovedLetters() {
        // CORRECCIÓN: Usar '$this->session' para acceder a la propiedad de la clase.
        $this->session->guard(['upis', 'admin']);
        
        $applicationModel = new DocumentApplication();
        
        // Obtenemos los IDs de TODAS las solicitudes aprobadas.
        $approved_ids = $applicationModel->getAllApprovedLetterIds();

        if (empty($approved_ids)) {
            header('Location: /SIEP/public/index.php?action=upisDashboard&status=no_approved_letters');
            exit;
        }

        // 2. Llamamos al método para eliminar esos IDs.
        $count = count($approved_ids);
        if ($applicationModel->deleteByIds($approved_ids)) {
            header('Location: /SIEP/public/index.php?action=upisDashboard&status=cleared_ok&count=' . $count);
        } else {
            header('Location: /SIEP/public/index.php?action=upisDashboard&status=error');
        }
        exit;
    }

    public function uploadSignedLetters() {
    $this->session->guard(['upis', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['signed_letters'])) {
        die("Acceso no válido o no se subieron archivos.");
    }

    require_once(__DIR__ . '/../Lib/FileHelper.php');
    $userModel = new User();
    $docModel = new StudentDocument();

    $success_count = 0;
    $error_count = 0;

    foreach ($_FILES['signed_letters']['name'] as $key => $filename) {
        if ($_FILES['signed_letters']['error'][$key] !== UPLOAD_ERR_OK) {
            $error_count++;
            continue;
        }

        $filename_no_ext = pathinfo($filename, PATHINFO_FILENAME);
        $parts = explode('_', $filename_no_ext);
        
        if (count($parts) !== 2) {
            $error_count++;
            continue;
        }

        $boleta = $parts[0];
        $doc_code = $parts[1];

        // ✅ Buscar datos completos del estudiante
        $student_data = $userModel->getStudentDataByBoleta($boleta);
        if (!$student_data) {
            $error_count++;
            continue;
        }

        // ✅ USAR FileHelper
        $upload_dir = FileHelper::getStudentSubfolder(
            $boleta, 
            $student_data['first_name'], 
            $student_data['last_name_p'], 
            'signed_documents'
        );

        $destination = $upload_dir . '/' . $filename;
        if (move_uploaded_file($_FILES['signed_letters']['tmp_name'][$key], $destination)) {
            $doc_type = ($doc_code === 'CP') ? 'presentation_letter' : 'unknown';
            $relative_path = FileHelper::getRelativePath($destination);
            
            if ($docModel->create($student_data['id'], $doc_type, $relative_path, $filename)) {
                $success_count++;
            } else {
                $error_count++;
                unlink($destination);
            }
        } else {
            $error_count++;
        }
    }
    
    header('Location: /SIEP/public/index.php?action=upisDashboard&status=upload_complete&success=' . $success_count . '&errors=' . $error_count);
    exit;
}

    // ========================================================================
// ACCIONES DE GESTIÓN DE ACREDITACIONES
// ========================================================================

/**
 * Vista de revisión de acreditaciones pendientes
 */
public function reviewAccreditations() {
    $this->session->guard(['upis', 'admin']);
    
    require_once(__DIR__ . '/../Models/Accreditation.php');
    $accreditationModel = new Accreditation();
    $pendingAccreditations = $accreditationModel->getPendingSubmissions();
    
    require_once(__DIR__ . '/../Views/upis/review_accreditations.php');
}

/**
 * Vista detallada de una acreditación
 */
public function reviewAccreditation() {
    $this->session->guard(['upis', 'admin']);
    
    $submission_id = (int)($_GET['id'] ?? 0);
    
    if (!$submission_id) {
        $_SESSION['error'] = "ID de solicitud inválido.";
        header('Location: /SIEP/public/index.php?action=upisDashboard');
        exit;
    }
    
    require_once(__DIR__ . '/../Models/Accreditation.php');
    $accreditationModel = new Accreditation();
    $submission = $accreditationModel->getById($submission_id);
    
    if (!$submission) {
        $_SESSION['error'] = "Solicitud no encontrada.";
        header('Location: /SIEP/public/index.php?action=upisDashboard');
        exit;
    }
    
    require_once(__DIR__ . '/../Views/upis/review_accreditation.php');
}

/**
 * Aprobar solicitud de acreditación
 */
public function approveAccreditation() {
    $this->session->guard(['upis', 'admin']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Método no permitido.";
        header('Location: /SIEP/public/index.php?action=upisDashboard');
        exit;
    }
    
    $submission_id = (int)$_POST['submission_id'];
    $comments = trim($_POST['comments'] ?? '');
    
    if (empty($submission_id)) {
        $_SESSION['error'] = "ID de solicitud inválido.";
        header('Location: /SIEP/public/index.php?action=reviewAccreditations');
        exit;
    }
    
    require_once(__DIR__ . '/../Models/Accreditation.php');
    $accreditationModel = new Accreditation();
    $submission = $accreditationModel->getById($submission_id);
    
    if (!$submission) {
        $_SESSION['error'] = "Solicitud no encontrada.";
        header('Location: /SIEP/public/index.php?action=reviewAccreditations');
        exit;
    }
    
    // Actualizar estado
    if ($accreditationModel->updateStatus($submission_id, 'approved')) {
        
        // Enviar notificación al estudiante
        require_once(__DIR__ . '/../Services/EmailService.php');
        $emailService = new EmailService();
        
        $student_data = [
            'user_id' => $submission['student_user_id'],
            'full_name' => $submission['first_name'] . ' ' . 
                           $submission['last_name_p'] . ' ' . 
                           $submission['last_name_m'],
            'boleta' => $submission['boleta'],
            'career' => $submission['career'],
            'email' => $submission['email']
        ];
        
        $emailService->notifyStudentAccreditationStatus($student_data, 'approved', $comments);
        
        $_SESSION['success'] = "Solicitud aprobada y estudiante notificado.";
    } else {
        $_SESSION['error'] = "Error al aprobar la solicitud.";
    }
    
    header('Location: /SIEP/public/index.php?action=reviewAccreditations');
    exit;
}

/**
 * Rechazar solicitud de acreditación
 */
public function rejectAccreditation() {
    $this->session->guard(['upis', 'admin']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Método no permitido.";
        header('Location: /SIEP/public/index.php?action=upisDashboard');
        exit;
    }
    
    $submission_id = (int)$_POST['submission_id'];
    $comments = trim($_POST['comments'] ?? '');
    
    if (empty($submission_id)) {
        $_SESSION['error'] = "ID de solicitud inválido.";
        header('Location: /SIEP/public/index.php?action=reviewAccreditations');
        exit;
    }
    
    if (empty($comments)) {
        $_SESSION['error'] = "Debes proporcionar una razón para el rechazo.";
        header('Location: /SIEP/public/index.php?action=reviewAccreditation&id=' . $submission_id);
        exit;
    }
    
    require_once(__DIR__ . '/../Models/Accreditation.php');
    $accreditationModel = new Accreditation();
    $submission = $accreditationModel->getById($submission_id);
    
    if (!$submission) {
        $_SESSION['error'] = "Solicitud no encontrada.";
        header('Location: /SIEP/public/index.php?action=reviewAccreditations');
        exit;
    }
    
    // Actualizar estado
    if ($accreditationModel->updateStatus($submission_id, 'rejected')) {
        
        // Enviar notificación al estudiante con comentarios
        require_once(__DIR__ . '/../Services/EmailService.php');
        $emailService = new EmailService();
        
        $student_data = [
            'user_id' => $submission['student_user_id'],
            'full_name' => $submission['first_name'] . ' ' . 
                           $submission['last_name_p'] . ' ' . 
                           $submission['last_name_m'],
            'boleta' => $submission['boleta'],
            'career' => $submission['career'],
            'email' => $submission['email']
        ];
        
        $emailService->notifyStudentAccreditationStatus($student_data, 'rejected', $comments);
        
        $_SESSION['success'] = "Solicitud rechazada y estudiante notificado.";
    } else {
        $_SESSION['error'] = "Error al rechazar la solicitud.";
    }
    
    header('Location: /SIEP/public/index.php?action=reviewAccreditations');
    exit;
}

    public function showHistory() {
        $this->session->guard(['upis', 'admin']);
        
        require_once(__DIR__ . '/../Models/CompletedProcess.php');
        $completedModel = new CompletedProcess();
        $completedProcesses = $completedModel->getAll();
        
        require_once(__DIR__ . '/../Views/upis/history_report.php');
    }

    public function downloadHistoryReport() {
        $this->session->guard(['upis', 'admin']);
        
        require_once(__DIR__ . '/../Models/CompletedProcess.php');
        $completedModel = new CompletedProcess();
        $processes = $completedModel->getAll();
        
        $docService = new DocumentService();
        $docService->generateHistoryReport($processes);
    }

/**
 * Mostrar vista de gestión de plantillas
 */
public function manageTemplates() {
    $this->session->guard(['upis', 'admin']);
    
    require_once(__DIR__ . '/../Models/LetterTemplate.php');
    $templateModel = new LetterTemplate();
    $templates = $templateModel->getAllActiveTemplates();
    
    require_once(__DIR__ . '/../Views/upis/manage_templates.php');
}

/**
 * Subir nueva plantilla y actualizar periodo académico
 * El archivo se renombra automáticamente a "Plantilla_CP.pdf"
 * SOBRESCRIBE la plantilla actual (sin backup automático)
 */
public function uploadTemplate() {
    $this->session->guard(['upis', 'admin']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /SIEP/public/index.php?action=manageTemplates&status=error');
        exit;
    }
    
    // Validar periodo académico
    $academic_period = trim($_POST['academic_period'] ?? '');
    if (!preg_match('/^\d{4}\/[12]$/', $academic_period)) {
        header('Location: /SIEP/public/index.php?action=manageTemplates&status=invalid_period');
        exit;
    }
    
    // Validar archivo PDF
    if (!isset($_FILES['template_file']) || $_FILES['template_file']['error'] !== UPLOAD_ERR_OK) {
        header('Location: /SIEP/public/index.php?action=manageTemplates&status=error');
        exit;
    }
    
    $file = $_FILES['template_file'];
    
    // Validar que sea PDF
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if ($mime_type !== 'application/pdf') {
        header('Location: /SIEP/public/index.php?action=manageTemplates&status=invalid_file');
        exit;
    }
    
    // Validar tamaño (máximo 10 MB)
    $max_size = 10 * 1024 * 1024; // 10 MB
    if ($file['size'] > $max_size) {
        header('Location: /SIEP/public/index.php?action=manageTemplates&status=invalid_file');
        exit;
    }
    
    // Definir ruta destino (SIEMPRE se llama Plantilla_CP.pdf)
    $templates_dir = __DIR__ . '/../../templates';
    
    // Crear directorio si no existe
    if (!is_dir($templates_dir)) {
        mkdir($templates_dir, 0755, true);
    }
    
    // ✅ NOMBRE FIJO: Plantilla_CP.pdf
    $destination = $templates_dir . '/Plantilla_CP.pdf';
    
    // ✅ SOBRESCRIBIR directamente (sin backup)
    // Si existe, se elimina y se reemplaza por el nuevo
    if (file_exists($destination)) {
        unlink($destination); // Eliminar el archivo anterior
    }
    
    // Mover archivo nuevo (se renombra automáticamente)
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        header('Location: /SIEP/public/index.php?action=manageTemplates&status=error');
        exit;
    }
    
    // Actualizar en la base de datos
    require_once(__DIR__ . '/../Models/LetterTemplate.php');
    $templateModel = new LetterTemplate();
    
    // Actualizar periodo académico para todas las plantillas
    $upis_user_id = $_SESSION['user_id'];
    $success1 = $templateModel->updateAcademicPeriodForAll($academic_period, $upis_user_id);
    
    // Actualizar la ruta del archivo en todas las plantillas
    $success2 = $templateModel->updateAllTemplatePaths('templates/Plantilla_CP.pdf');
    
    if ($success1 && $success2) {
        header('Location: /SIEP/public/index.php?action=manageTemplates&status=success');
    } else {
        header('Location: /SIEP/public/index.php?action=manageTemplates&status=error');
    }
    exit;
}

/**
 * Reiniciar contadores de numeración de cartas
 */
public function resetLetterCounters() {
    $this->session->guard(['upis', 'admin']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /SIEP/public/index.php?action=manageTemplates&status=error');
        exit;
    }
    
    require_once(__DIR__ . '/../Models/LetterTemplate.php');
    $templateModel = new LetterTemplate();
    
    if ($templateModel->resetAllCounters()) {
        header('Location: /SIEP/public/index.php?action=manageTemplates&status=counters_reset');
    } else {
        header('Location: /SIEP/public/index.php?action=manageTemplates&status=error');
    }
    exit;
}

    // ========================================================================
    // GESTIÓN DE CICLO DE VIDA DE VACANTES (NUEVO)
    // ========================================================================
    
    /**
     * Hub principal de gestión de vacantes
     */
    public function hub() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $stats = $vacancyModel->getGlobalStatistics();
        
        require_once(__DIR__ . '/../Views/upis/hub.php');
    }
    
    /**
     * Gestionar vacantes activas (approved)
     */
    public function manageActiveVacancies() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $activeVacancies = $vacancyModel->getVacanciesByStatus('approved');
        
        require_once(__DIR__ . '/../Views/upis/manage_active_vacancies.php');
    }
    
    /**
     * Tumbar una vacante activa (solo UPIS)
     */
    public function takedownVacancy() {
        $this->session->guard(['upis', 'admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Método no permitido.";
            header('Location: /SIEP/public/index.php?action=manageActiveVacancies');
            exit;
        }
        
        $vacancy_id = (int)($_POST['vacancy_id'] ?? 0);
        $rejection_notes = trim($_POST['rejection_notes'] ?? '');
        
        if (!$vacancy_id || empty($rejection_notes)) {
            $_SESSION['error'] = "❌ Debes proporcionar una justificación para tumbar la vacante.";
            header('Location: /SIEP/public/index.php?action=manageActiveVacancies');
            exit;
        }
        
        $vacancyModel = new Vacancy();
        $vacancy = $vacancyModel->getVacancyById($vacancy_id);
        
        if (!$vacancy || $vacancy['status'] !== 'approved') {
            $_SESSION['error'] = "Vacante no encontrada o no está activa.";
            header('Location: /SIEP/public/index.php?action=manageActiveVacancies');
            exit;
        }
        
        $reviewer_id = $_SESSION['user_id'];
        
        if ($vacancyModel->takedown($vacancy_id, $reviewer_id, $rejection_notes)) {
            
            // Notificar a la empresa
            require_once(__DIR__ . '/../Services/EmailService.php');
            $emailService = new EmailService();
            
            $company_data = [
                'email' => $vacancy['company_email'],
                'company_name' => $vacancy['company_name']
            ];
            
            $emailService->notifyVacancyTakenDown($vacancy, $company_data, $rejection_notes);
            
            $_SESSION['success'] = "⚠️ Vacante desactivada y empresa notificada.";
        } else {
            $_SESSION['error'] = "❌ Error al desactivar la vacante.";
        }
        
        header('Location: /SIEP/public/index.php?action=manageActiveVacancies');
        exit;
    }
    
    /**
     * Papelera de vacantes rechazadas
     */
    public function vacancyTrash() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $rejectedVacancies = $vacancyModel->getVacanciesByStatus('rejected');
        $stats = $vacancyModel->getTrashStatistics();
        
        require_once(__DIR__ . '/../Views/upis/vacancy_trash.php');
    }
    
    /**
     * Restaurar una vacante rechazada
     */
    public function restoreVacancy() {
        $this->session->guard(['upis', 'admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Método no permitido.";
            header('Location: /SIEP/public/index.php?action=vacancyTrash');
            exit;
        }
        
        $vacancy_id = (int)($_POST['vacancy_id'] ?? 0);
        
        if (!$vacancy_id) {
            $_SESSION['error'] = "ID de vacante inválido.";
            header('Location: /SIEP/public/index.php?action=vacancyTrash');
            exit;
        }
        
        $vacancyModel = new Vacancy();
        
        if ($vacancyModel->restore($vacancy_id)) {
            $_SESSION['success'] = "♻️ Vacante restaurada a estado pendiente.";
        } else {
            $_SESSION['error'] = "❌ Error al restaurar la vacante.";
        }
        
        header('Location: /SIEP/public/index.php?action=vacancyTrash');
        exit;
    }
    
    /**
     * Eliminar permanentemente una vacante (hard delete)
     */
    public function hardDeleteVacancy() {
        $this->session->guard(['upis', 'admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Método no permitido.";
            header('Location: /SIEP/public/index.php?action=vacancyTrash');
            exit;
        }
        
        $vacancy_id = (int)($_POST['vacancy_id'] ?? 0);
        
        if (!$vacancy_id) {
            $_SESSION['error'] = "ID de vacante inválido.";
            header('Location: /SIEP/public/index.php?action=vacancyTrash');
            exit;
        }
        
        $vacancyModel = new Vacancy();
        
        if ($vacancyModel->hardDelete($vacancy_id)) {
            $_SESSION['success'] = "💀 Vacante eliminada permanentemente.";
        } else {
            $_SESSION['error'] = "❌ Error al eliminar la vacante.";
        }
        
        header('Location: /SIEP/public/index.php?action=vacancyTrash');
        exit;
    }
    
    /**
     * Vista de reportes y estadísticas
     */
    public function vacancyReports() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $stats = $vacancyModel->getGlobalStatistics();
        
        require_once(__DIR__ . '/../Views/upis/vacancy_reports.php');
    }
    
    /**
     * Exportar PDF de vacantes activas
     */
    public function exportActivePDF() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $vacancies = $vacancyModel->getVacanciesByStatus('approved');
        
        require_once(__DIR__ . '/../Services/ExportService.php');
        $exportService = new ExportService();
        $exportService->generateActivePDF($vacancies);
    }
    
    /**
     * Exportar PDF de vacantes completadas
     */
    public function exportCompletedPDF() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $vacancies = $vacancyModel->getVacanciesByStatus('completed');
        
        require_once(__DIR__ . '/../Services/ExportService.php');
        $exportService = new ExportService();
        $exportService->generateCompletedPDF($vacancies);
    }
    
    /**
     * Exportar PDF de vacantes canceladas
     */
    public function exportCanceledPDF() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $vacancies = $vacancyModel->getVacanciesByStatus('rejected');
        
        require_once(__DIR__ . '/../Services/ExportService.php');
        $exportService = new ExportService();
        $exportService->generateCanceledPDF($vacancies);
    }
    
    /**
     * Exportar Excel de todas las vacantes
     */
    public function exportAllExcel() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $vacancies = $vacancyModel->getAllVacanciesForReports();
        
        require_once(__DIR__ . '/../Services/ExportService.php');
        $exportService = new ExportService();
        $exportService->generateAllVacanciesExcel($vacancies);
    }
    
    /**
     * Exportar Excel de análisis de empresas
     */
    public function exportCompanyAnalysisExcel() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $companies = $vacancyModel->getVacanciesGroupedByCompany();
        
        require_once(__DIR__ . '/../Services/ExportService.php');
        $exportService = new ExportService();
        $exportService->generateCompanyAnalysisExcel($companies);
    }


}
