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
require_once(__DIR__ . '/../Services/DocumentService.php'); // ✅ CAMBIO: Usar DocumentService en lugar de DocumentGenerator

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
     * Hub de gestión de empresas
     * Muestra 3 secciones: Pendientes, Activas, Historial de Rechazos
     */
    public function companyManagementHub() {
        $this->session->guard(['upis', 'admin']);
        
        $userModel = new User();
        require_once(__DIR__ . '/../Models/CompanyRejection.php');
        $rejectionModel = new CompanyRejection();
        
        // Obtener datos para las 3 secciones
        $pendingCompanies = $userModel->getPendingCompanies();
        $activeCompanies = $userModel->getActiveCompanies();
        $rejectionHistory = $rejectionModel->getAll();
        
        require_once(__DIR__ . '/../Views/upis/company_management_hub.php');
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
     * ✅ CORREGIDO: Ahora obtiene correctamente las cartas pendientes y aprobadas
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
            
            // ========================================
            // PASO 1: Obtener datos de la empresa ANTES de eliminar
            // ========================================
            $company = $userModel->findById($company_id);
            $company_profile = $userModel->getCompanyProfileByUserId($company_id);
            
            if (!$company || !$company_profile) {
                $_SESSION['error'] = "No se encontró la información de la empresa.";
                header('Location: /SIEP/public/index.php?action=reviewCompanies');
                exit;
            }
            
            $company_data = [
                'contact_name' => $company['first_name'] . ' ' . 
                                 $company['last_name_p'] . ' ' . 
                                 $company['last_name_m'],
                'company_name' => $company_profile['company_name'] ?? 'N/A',
                'rfc' => $company_profile['rfc'] ?? 'N/A',
                'commercial_name' => $company_profile['commercial_name'] ?? 'N/A',
                'email' => $company['email']
            ];
            
            // ========================================
            // PASO 2: Guardar en historial de rechazos
            // ========================================
            require_once(__DIR__ . '/../Models/CompanyRejection.php');
            $rejectionModel = new CompanyRejection();
            
            $rejection_saved = $rejectionModel->createRejection(
                $company_data['company_name'],
                $company_data['email'],
                $company_data['contact_name'],
                $comments,
                $company_data['rfc'],
                $company_data['commercial_name']
            );
            
            if (!$rejection_saved) {
                $_SESSION['error'] = "Error al guardar el historial de rechazo.";
                header('Location: /SIEP/public/index.php?action=reviewCompanies');
                exit;
            }
            
            // ========================================
            // PASO 3: Enviar email FORZOSO
            // ========================================
            require_once(__DIR__ . '/../Services/EmailService.php');
            $emailService = new EmailService();
            $emailService->notifyCompanyRejection($company_data, $comments);
            
            // ========================================
            // PASO 4: ELIMINAR empresa de la base de datos
            // ========================================
            if ($userModel->deleteCompany($company_id)) {
                $_SESSION['success'] = "✅ Empresa rechazada, notificada por email y guardada en historial. El email ahora está disponible para re-registro.";
            } else {
                $_SESSION['error'] = "❌ Error al eliminar la empresa del sistema.";
            }
            
            header('Location: /SIEP/public/index.php?action=reviewCompanies');
            exit;
        }
        
        $_SESSION['error'] = "Error al rechazar la empresa.";
        header('Location: /SIEP/public/index.php?action=reviewCompanies');
        exit;
    }

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
            
           $_SESSION['success'] = "✅ Vacante aprobada correctamente.";
        } else {
            $_SESSION['error'] = "❌ Error al aprobar la vacante.";
        }
        
        header('Location: /SIEP/public/index.php?action=reviewVacancies');
        exit;
    }

    /**
     * Rechazar una vacante durante revisión inicial
     */
    public function rejectVacancy() {
        $this->session->guard(['upis', 'admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Método no permitido.";
            header('Location: /SIEP/public/index.php?action=reviewVacancies');
            exit;
        }
        
        $vacancy_id = (int)($_POST['vacancy_id'] ?? 0);
        $rejection_reason = trim($_POST['rejection_reason'] ?? '');
        $rejection_notes = trim($_POST['rejection_notes'] ?? $_POST['comments'] ?? '');
        
        if (!$vacancy_id || empty($rejection_reason) || empty($rejection_notes)) {
            $_SESSION['error'] = "Debes proporcionar un motivo y justificación para el rechazo.";
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
        
        // Llamar al método actualizado con los nuevos parámetros
        if ($vacancyModel->reject($vacancy_id, $reviewer_id, $rejection_reason, $rejection_notes)) {
            
            // Enviar email con razón del rechazo
            require_once(__DIR__ . '/../Services/EmailService.php');
            $emailService = new EmailService();
            
            $company_data = [
                'email' => $vacancy['company_email'],
                'company_name' => $vacancy['company_name']
            ];
            
            $emailService->notifyVacancyRejected($vacancy, $company_data, $rejection_notes);
            
            $_SESSION['success'] = "❌ Vacante rechazada y notificación enviada por email.";
        } else {
            $_SESSION['error'] = "Error al rechazar la vacante.";
        }
        
        header('Location: /SIEP/public/index.php?action=reviewVacancies');
        exit;
    }

    // ========================================================================
    // GESTIÓN DE CARTAS DE PRESENTACIÓN
    // ========================================================================

    /**
     * Procesar solicitudes de cartas de presentación (aprobar/rechazar masivamente)
     * ✅ CORREGIDO: Mejor manejo de errores y validaciones
     */
    public function processLetterRequests() {
        $this->session->guard(['upis', 'admin']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Método no permitido.";
            header('Location: /SIEP/public/index.php?action=reviewLetters');
            exit;
        }
        
        if (empty($_POST['bulk_action']) || empty($_POST['request_ids'])) {
            $_SESSION['error'] = "⚠️ No se seleccionaron solicitudes o acción.";
            header('Location: /SIEP/public/index.php?action=reviewLetters');
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
            $_SESSION['error'] = "Acción no válida.";
            header('Location: /SIEP/public/index.php?action=reviewLetters');
            exit;
        }
        
        $applicationModel = new DocumentApplication();
        
        if ($applicationModel->updateStatusForMultipleIds($request_ids, $new_status, $reviewer_id)) {
            $_SESSION['success'] = "✅ Se procesaron {$count} solicitudes correctamente.";
            header('Location: /SIEP/public/index.php?action=reviewLetters&status=' . $redirect_status . '&count=' . $count);
        } else {
            $_SESSION['error'] = "❌ Error al procesar las solicitudes.";
            header('Location: /SIEP/public/index.php?action=reviewLetters&status=error');
        }
        
        exit;
    }

    /**
     * Descargar todas las cartas aprobadas como ZIP
     * VERSIÓN 2.0: Con sistema de plantillas y numeración automática
     * ✅ CORREGIDO: Usa DocumentService correctamente
     */
    public function downloadAllApprovedLetters() {
        $this->session->guard(['upis', 'admin']);
        
        $applicationModel = new DocumentApplication();
        $approved_ids = $applicationModel->getAllApprovedLetterIds();
        
        if (empty($approved_ids)) {
            $_SESSION['error'] = "⚠️ No hay cartas aprobadas para descargar.";
            header('Location: /SIEP/public/index.php?action=reviewLetters');
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
            $_SESSION['error'] = "❌ Error al obtener datos de las solicitudes aprobadas.";
            header('Location: /SIEP/public/index.php?action=reviewLetters');
            exit;
        }
        
        // ✅ PASO 3: Crear ZIP
        $zip = new ZipArchive();
        $zipFileName = 'Cartas_Presentacion_' . date('Y-m-d_His') . '.zip';
        $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;
        
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            $_SESSION['error'] = "❌ Error al crear archivo ZIP.";
            header('Location: /SIEP/public/index.php?action=reviewLetters');
            exit;
        }
        
        // ✅ PASO 4: Generar cada PDF
        require_once(__DIR__ . '/../Services/DocumentService.php');
        $docGenerator = new DocumentService();
        
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

    /**
     * Limpiar todas las cartas aprobadas
     */
    public function clearAllApprovedLetters() {
        $this->session->guard(['upis', 'admin']);
        
        $applicationModel = new DocumentApplication();
        
        // Obtenemos los IDs de TODAS las solicitudes aprobadas.
        $approved_ids = $applicationModel->getAllApprovedLetterIds();

        if (empty($approved_ids)) {
            $_SESSION['warning'] = "⚠️ No hay cartas aprobadas para limpiar.";
            header('Location: /SIEP/public/index.php?action=reviewLetters');
            exit;
        }

        // 2. Llamamos al método para eliminar esos IDs.
        $count = count($approved_ids);
        if ($applicationModel->deleteByIds($approved_ids)) {
            $_SESSION['success'] = "✅ Se limpiaron {$count} cartas aprobadas correctamente.";
            header('Location: /SIEP/public/index.php?action=reviewLetters&status=cleared_ok&count=' . $count);
        } else {
            $_SESSION['error'] = "❌ Error al limpiar las cartas aprobadas.";
            header('Location: /SIEP/public/index.php?action=reviewLetters&status=error');
        }
        exit;
    }

    /**
     * Subir cartas firmadas por UPIS
     * ✅ CORREGIDO: Mejor validación y manejo de errores
     */
    public function uploadSignedLetters() {
        $this->session->guard(['upis', 'admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['signed_letters'])) {
            $_SESSION['error'] = "⚠️ No se subieron archivos o método no permitido.";
            header('Location: /SIEP/public/index.php?action=showUploadDocumentsForm');
            exit;
        }

        require_once(__DIR__ . '/../Lib/FileHelper.php');
        $userModel = new User();
        $docModel = new StudentDocument();

        $success_count = 0;
        $error_count = 0;
        $errors = [];

        foreach ($_FILES['signed_letters']['name'] as $key => $filename) {
            if ($_FILES['signed_letters']['error'][$key] !== UPLOAD_ERR_OK) {
                $error_count++;
                $errors[] = "Error al subir archivo: {$filename}";
                continue;
            }

            $filename_no_ext = pathinfo($filename, PATHINFO_FILENAME);
            $parts = explode('_', $filename_no_ext);
            
            if (count($parts) !== 2) {
                $error_count++;
                $errors[] = "Formato de nombre incorrecto: {$filename} (debe ser BOLETA_CODIGO.pdf)";
                continue;
            }

            $boleta = $parts[0];
            $doc_code = $parts[1];

            // ✅ Buscar datos completos del estudiante
            $student_data = $userModel->getStudentDataByBoleta($boleta);
            if (!$student_data) {
                $error_count++;
                $errors[] = "Estudiante no encontrado para boleta: {$boleta}";
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
                $doc_type = ($doc_code === 'CPSF') ? 'presentation_letter' : 'unknown';
                $relative_path = FileHelper::getRelativePath($destination);
                
                if ($docModel->create($student_data['id'], $doc_type, $relative_path, $filename)) {
                    $success_count++;
                } else {
                    $error_count++;
                    $errors[] = "Error al registrar en BD: {$filename}";
                    unlink($destination);
                }
            } else {
                $error_count++;
                $errors[] = "Error al mover archivo: {$filename}";
            }
        }
        
        if ($success_count > 0) {
            $_SESSION['success'] = "✅ Se subieron {$success_count} cartas correctamente.";
        }
        
        if ($error_count > 0) {
            $_SESSION['error'] = "⚠️ {$error_count} archivos tuvieron errores.";
            $_SESSION['upload_errors'] = $errors;
        }
        
        header('Location: /SIEP/public/index.php?action=showUploadDocumentsForm');
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
     * Aprobar solicitud de acreditación
     * ACTUALIZADO: Ahora registra revisor, fecha y comentarios
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
        
        // ✅ USAR EL NUEVO MÉTODO approve() con revisor, fecha y comentarios
        $reviewer_id = $_SESSION['user_id'];
        
        if ($accreditationModel->approve($submission_id, $reviewer_id, $comments)) {
            
            $_SESSION['success'] = "✅ Solicitud aprobada correctamente.";
        } else {
            $_SESSION['error'] = "❌ Error al aprobar la solicitud.";
        }
        
        header('Location: /SIEP/public/index.php?action=reviewAccreditations');
        exit;
    }

    /**
     * Rechazar solicitud de acreditación
     * ACTUALIZADO: Ahora registra revisor, fecha y comentarios obligatorios
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
            $_SESSION['error'] = "⚠️ Debes proporcionar una razón para el rechazo.";
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
        
        // ✅ USAR EL NUEVO MÉTODO reject() con revisor, fecha y comentarios
        $reviewer_id = $_SESSION['user_id'];
        
        if ($accreditationModel->reject($submission_id, $reviewer_id, $comments)) {
            $_SESSION['success'] = "❌ Solicitud rechazada correctamente.";
        } else {
            $_SESSION['error'] = "Error al rechazar la solicitud.";
        }
        
        header('Location: /SIEP/public/index.php?action=reviewAccreditations');
        exit;
    }

    public function showHistory() {
        $this->session->guard(['upis', 'admin']);
        
        // Temporalmente comentado hasta crear el modelo CompletedProcess
        // require_once(__DIR__ . '/../Models/CompletedProcess.php');
        // $completedModel = new CompletedProcess();
        // $completedProcesses = $completedModel->getAll();
        
        $completedProcesses = []; // Array vacío temporal
        
        require_once(__DIR__ . '/../Views/upis/history_report.php');
    }

    public function downloadHistoryReport() {
        $this->session->guard(['upis', 'admin']);
        
        require_once(__DIR__ . '/../Models/CompletedProcess.php');
        $completedModel = new CompletedProcess();
        $processes = $completedModel->getAll();
        
        require_once(__DIR__ . '/../Services/DocumentService.php');
        $docService = new DocumentService();
        $docService->generateHistoryReport($processes);
    }

    // ========================================================================
    // GESTIÓN DE PLANTILLAS
    // ========================================================================

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
    public function vacancyHub() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $stats = $vacancyModel->getGlobalStatistics();
        
        require_once(__DIR__ . '/../Views/upis/vacancy_hub.php');
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

    // ========================================================================
// HUB DE GESTIÓN DE CARTAS DE PRESENTACIÓN (NUEVO)
// ========================================================================

/**
 * Hub principal de cartas de presentación
 * Muestra 4 secciones: Pendientes, Aprobadas, Subir Firmadas, Completadas
 */
public function presentationLettersHub() {
    $this->session->guard(['upis', 'admin']);
    
    $applicationModel = new DocumentApplication();
    
    // Obtener cartas por estado
    $pendingLetters = $applicationModel->getLettersByStatus('pending');
    $approvedLetters = $applicationModel->getLettersByStatus('approved');
    $completedLetters = $applicationModel->getLettersByStatus('completed');
    
    // Estadísticas
    $stats = [
        'pending_count' => count($pendingLetters),
        'approved_count' => count($approvedLetters),
        'completed_count' => count($completedLetters)
    ];
    
    require_once(__DIR__ . '/../Views/upis/presentation_letters_hub.php');
}

/**
 * Ver detalles de una solicitud específica
 */
public function viewLetterDetails() {
    $this->session->guard(['upis', 'admin']);
    
    $application_id = (int)($_GET['id'] ?? 0);
    
    if (!$application_id) {
        $_SESSION['error'] = "ID de solicitud inválido.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub');
        exit;
    }
    
    $applicationModel = new DocumentApplication();
    $letter = $applicationModel->getLetterDetailsById($application_id);
    
    if (!$letter) {
        $_SESSION['error'] = "Solicitud no encontrada.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub');
        exit;
    }
    
    require_once(__DIR__ . '/../Views/upis/letter_details.php');
}

/**
 * Aprobar UNA carta de presentación individual
 */
public function approveSingleLetter() {
    $this->session->guard(['upis', 'admin']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Método no permitido.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub');
        exit;
    }
    
    $application_id = (int)($_POST['application_id'] ?? 0);
    $comments = trim($_POST['comments'] ?? '');
    
    if (!$application_id) {
        $_SESSION['error'] = "ID de solicitud inválido.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub');
        exit;
    }
    
    $applicationModel = new DocumentApplication();
    $reviewer_id = $_SESSION['user_id'];
    
    // Aprobar carta individual
    if ($applicationModel->approveSingle($application_id, $reviewer_id, $comments)) {
        $_SESSION['success'] = "✅ Carta de presentación aprobada correctamente.";
    } else {
        $_SESSION['error'] = "❌ Error al aprobar la carta.";
    }
    
    header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=pending');
    exit;
}

/**
 * Rechazar UNA carta de presentación individual
 */
public function rejectSingleLetter() {
    $this->session->guard(['upis', 'admin']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Método no permitido.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub');
        exit;
    }
    
    $application_id = (int)($_POST['application_id'] ?? 0);
    $rejection_reason = trim($_POST['rejection_reason'] ?? '');
    
    if (!$application_id || empty($rejection_reason)) {
        $_SESSION['error'] = "⚠️ Debes proporcionar una razón para el rechazo.";
        header('Location: /SIEP/public/index.php?action=viewLetterDetails&id=' . $application_id);
        exit;
    }
    
    $applicationModel = new DocumentApplication();
    $reviewer_id = $_SESSION['user_id'];
    
    // Rechazar carta individual
    if ($applicationModel->rejectSingle($application_id, $reviewer_id, $rejection_reason)) {
        
        // Opcional: Notificar al estudiante por email
        // TODO: Implementar notificación
        
        $_SESSION['success'] = "❌ Carta rechazada. El estudiante será notificado.";
    } else {
        $_SESSION['error'] = "Error al rechazar la carta.";
    }
    
    header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=pending');
    exit;
}

/**
 * Descargar ZIP con TODAS las cartas aprobadas
 * ✅ LAS CARTAS SE QUEDAN EN "approved" (no se mueven automáticamente)
 */
public function downloadAllApprovedLettersFromHub() {
    $this->session->guard(['upis', 'admin']);
    
    $applicationModel = new DocumentApplication();
    $approved_letters = $applicationModel->getLettersByStatus('approved');
    
    if (empty($approved_letters)) {
        $_SESSION['error'] = "⚠️ No hay cartas aprobadas para descargar.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=approved');
        exit;
    }
    
    $approved_ids = array_column($approved_letters, 'id');
    
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
        $_SESSION['error'] = "❌ Error al obtener datos de las solicitudes aprobadas.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=approved');
        exit;
    }
    
    // ✅ PASO 3: Crear ZIP
    $zip = new ZipArchive();
    $zipFileName = 'Cartas_Presentacion_' . date('Y-m-d_His') . '.zip';
    $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;
    
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        $_SESSION['error'] = "❌ Error al crear archivo ZIP.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=approved');
        exit;
    }
    
    // ✅ PASO 4: Generar cada PDF
    require_once(__DIR__ . '/../Services/DocumentService.php');
    $docGenerator = new DocumentService();
    
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
    
    // ✅ PASO 5: Descargar ZIP (SIN mover a completadas)
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
    header('Content-Length: ' . filesize($zipFilePath));
    header('Pragma: no-cache');
    header('Expires: 0');
    
    readfile($zipFilePath);
    unlink($zipFilePath);
    exit;
}

/**
 * Descargar PDF individual de una carta aprobada
 * ✅ NO cambia el estado, solo descarga
 */
public function downloadSingleApprovedLetter() {
    $this->session->guard(['upis', 'admin']);
    
    $application_id = (int)($_GET['id'] ?? 0);
    
    if (!$application_id) {
        $_SESSION['error'] = "ID de solicitud inválido.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub');
        exit;
    }
    
    $applicationModel = new DocumentApplication();
    $application = $applicationModel->findById($application_id);
    
    if (!$application || $application['status'] !== 'approved') {
        $_SESSION['error'] = "Carta no encontrada o no está aprobada.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub');
        exit;
    }
    
    // Generar número de oficio si no tiene
    if (empty($application['letter_number'])) {
        require_once(__DIR__ . '/../Models/LetterTemplate.php');
        $templateModel = new LetterTemplate();
        $letter_number = $templateModel->generateNextLetterNumber();
        if ($letter_number) {
            $applicationModel->assignLetterNumber($application_id, $letter_number);
            $application['letter_number'] = $letter_number;
        }
    }
    
    // Obtener datos del estudiante
    $student_data = $applicationModel->getStudentDataForLetter($application_id);
    
    if (!$student_data) {
        $_SESSION['error'] = "Error al obtener datos del estudiante.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub');
        exit;
    }
    
    // Generar PDF
    require_once(__DIR__ . '/../Services/DocumentService.php');
    $docService = new DocumentService();
    
    $student_data_for_pdf = [
        'full_name' => $student_data['first_name'] . ' ' . $student_data['last_name_p'] . ' ' . $student_data['last_name_m'],
        'boleta' => $student_data['boleta'],
        'career' => $student_data['career'],
        'percentage_progress' => $student_data['percentage_progress'],
        'has_specific_recipient' => (bool)($student_data['has_specific_recipient'] ?? 0),
        'recipient_name' => $student_data['recipient_name'] ?? null,
        'recipient_position' => $student_data['recipient_position'] ?? null,
        'requires_hours' => (bool)($student_data['requires_hours'] ?? 0),
        'letter_template_type' => $student_data['letter_template_type'] ?? 'normal'
    ];
    
    $pdfContent = $docService->generatePresentationLetter($student_data_for_pdf, $application['letter_number'], true);
    
    // Descargar PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $student_data['boleta'] . '_CPSF.pdf"');
    header('Content-Length: ' . strlen($pdfContent));
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo $pdfContent;
    exit;
}

/**
 * Descargar PDF individual de una carta completada (re-descarga)
 */
public function downloadCompletedLetter() {
    $this->session->guard(['upis', 'admin']);
    
    $application_id = (int)($_GET['id'] ?? 0);
    
    if (!$application_id) {
        $_SESSION['error'] = "ID de solicitud inválido.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=completed');
        exit;
    }
    
    $applicationModel = new DocumentApplication();
    $application = $applicationModel->findById($application_id);
    
    if (!$application || $application['status'] !== 'completed') {
        $_SESSION['error'] = "Carta no encontrada o no está completada.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=completed');
        exit;
    }
    
    // Obtener datos del estudiante
    $student_data = $applicationModel->getStudentDataForLetter($application_id);
    
    if (!$student_data) {
        $_SESSION['error'] = "Error al obtener datos del estudiante.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=completed');
        exit;
    }
    
    // Generar PDF
    require_once(__DIR__ . '/../Services/DocumentService.php');
    $docService = new DocumentService();
    
    $student_data_for_pdf = [
        'full_name' => $student_data['first_name'] . ' ' . $student_data['last_name_p'] . ' ' . $student_data['last_name_m'],
        'boleta' => $student_data['boleta'],
        'career' => $student_data['career'],
        'percentage_progress' => $student_data['percentage_progress'],
        'has_specific_recipient' => (bool)($student_data['has_specific_recipient'] ?? 0),
        'recipient_name' => $student_data['recipient_name'] ?? null,
        'recipient_position' => $student_data['recipient_position'] ?? null,
        'requires_hours' => (bool)($student_data['requires_hours'] ?? 0),
        'letter_template_type' => $student_data['letter_template_type'] ?? 'normal'
    ];
    
    $letter_number = $application['letter_number'] ?? 'No. 00-2025/2';
    $pdfContent = $docService->generatePresentationLetter($student_data_for_pdf, $letter_number, true);
    
    // Descargar PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $student_data['boleta'] . '_CPSF.pdf"');
    header('Content-Length: ' . strlen($pdfContent));
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo $pdfContent;
    exit;
}

/**
 * Subir cartas firmadas y marcarlas como completadas AUTOMÁTICAMENTE
 * ✅ Al subir una carta firmada, se marca como "completed"
 */
public function uploadSignedLettersToHub() {
    $this->session->guard(['upis', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['signed_letters'])) {
        $_SESSION['error'] = "⚠️ No se subieron archivos o método no permitido.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=upload');
        exit;
    }

    require_once(__DIR__ . '/../Lib/FileHelper.php');
    $userModel = new User();
    $docModel = new StudentDocument();
    $applicationModel = new DocumentApplication();

    $success_count = 0;
    $error_count = 0;
    $errors = [];

    foreach ($_FILES['signed_letters']['name'] as $key => $filename) {
        if ($_FILES['signed_letters']['error'][$key] !== UPLOAD_ERR_OK) {
            $error_count++;
            $errors[] = "Error al subir archivo: {$filename}";
            continue;
        }

        // Formato esperado: BOLETA_CPSF.pdf
        $filename_no_ext = pathinfo($filename, PATHINFO_FILENAME);
        $parts = explode('_', $filename_no_ext);
        
        if (count($parts) !== 2 || $parts[1] !== 'CPSF') {
            $error_count++;
            $errors[] = "Formato de nombre incorrecto: {$filename} (debe ser BOLETA_CPSF.pdf)";
            continue;
        }

        $boleta = $parts[0];

        // Buscar estudiante
        $student_data = $userModel->getStudentDataByBoleta($boleta);
        if (!$student_data) {
            $error_count++;
            $errors[] = "Estudiante no encontrado para boleta: {$boleta}";
            continue;
        }

        // Buscar solicitud aprobada del estudiante
        $application = $applicationModel->getApprovedLetterByStudentId($student_data['id']);
        if (!$application) {
            $error_count++;
            $errors[] = "No hay carta aprobada para boleta: {$boleta}";
            continue;
        }

        // Subir archivo
        $upload_dir = FileHelper::getStudentSubfolder(
            $boleta, 
            $student_data['first_name'], 
            $student_data['last_name_p'], 
            'signed_documents'
        );

        $destination = $upload_dir . '/' . $filename;
        if (move_uploaded_file($_FILES['signed_letters']['tmp_name'][$key], $destination)) {
            $relative_path = FileHelper::getRelativePath($destination);
            
            // Guardar en student_documents
            if ($docModel->create($student_data['id'], 'presentation_letter', $relative_path, $filename)) {
                
                // ✅ MARCAR COMO COMPLETADA AUTOMÁTICAMENTE
                if ($applicationModel->markAsCompleted($application['id'])) {
                    $success_count++;
                } else {
                    $error_count++;
                    $errors[] = "Error al marcar como completada: {$filename}";
                }
            } else {
                $error_count++;
                $errors[] = "Error al registrar en BD: {$filename}";
                unlink($destination);
            }
        } else {
            $error_count++;
            $errors[] = "Error al mover archivo: {$filename}";
        }
    }
    
    if ($success_count > 0) {
        $_SESSION['success'] = "✅ Se subieron y completaron {$success_count} cartas correctamente.";
    }
    
    if ($error_count > 0) {
        $_SESSION['error'] = "⚠️ {$error_count} archivos tuvieron errores.";
        $_SESSION['upload_errors'] = $errors;
    }
    
    header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=completed');
    exit;
}

/**
 * Limpiar cartas completadas (eliminación masiva)
 * ✅ OPCIONAL: Para limpiar el historial de cartas ya entregadas
 */
public function clearCompletedLetters() {
    $this->session->guard(['upis', 'admin']);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error'] = "Método no permitido.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=completed');
        exit;
    }
    
    $applicationModel = new DocumentApplication();
    $completed_letters = $applicationModel->getLettersByStatus('completed');
    
    if (empty($completed_letters)) {
        $_SESSION['warning'] = "⚠️ No hay cartas completadas para limpiar.";
        header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=completed');
        exit;
    }
    
    $completed_ids = array_column($completed_letters, 'id');
    $count = count($completed_ids);
    
    if ($applicationModel->deleteByIds($completed_ids)) {
        $_SESSION['success'] = "✅ Se limpiaron {$count} cartas completadas del historial.";
    } else {
        $_SESSION['error'] = "❌ Error al limpiar las cartas completadas.";
    }
    
    header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=completed');
    exit;
}
}