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

    // ========================================================================
    // ACCIONES DE GESTIÓN DE EMPRESAS
    // ========================================================================

    /**
     * Aprueba una empresa (cambia status de 'pending' a 'active')
     */
    public function approveCompany() {
        $this->session->guard(['upis', 'admin']);
        
        $company_id = $_GET['id'] ?? null;
        
        if ($company_id) {
            $userModel = new User();
            
            if ($userModel->approveUser((int)$company_id)) {
                header('Location: /SIEP/public/index.php?action=reviewCompanies&status=approved');
                exit;
            }
        }
        
        header('Location: /SIEP/public/index.php?action=reviewCompanies&status=error');
        exit;
    }
    
    /**
     * Rechaza una empresa
     * 
     * VERSIÓN ACTUAL: Solo marca como 'inactive'
     * VERSIÓN FUTURA (PINEADA): Eliminar + enviar email + blacklist
     */
    public function rejectCompany() {
        $this->session->guard(['upis', 'admin']);
        
        $company_id = $_GET['id'] ?? null;
        
        if ($company_id) {
            $userModel = new User();
            
            // VERSIÓN ACTUAL: Solo cambia status a 'inactive'
            if ($userModel->rejectUser((int)$company_id)) {
                header('Location: /SIEP/public/index.php?action=reviewCompanies&status=rejected');
                exit;
            }
            
            /* ============================================================
             * VERSIÓN FUTURA CON EMAIL Y BLACKLIST (PINEADA):
             * ============================================================
             * 
             * $rejection_reason = $_POST['rejection_reason'] ?? 'No especificado';
             * 
             * // 1. Obtener datos antes de eliminar
             * $company_data = $userModel->getCompanyDataForRejection((int)$company_id);
             * 
             * // 2. Agregar a blacklist
             * $blacklist_result = $userModel->addOrUpdateBlacklist(
             *     $company_data['email'],
             *     $company_data['company_name'],
             *     $rejection_reason,
             *     $_SESSION['user_id']
             * );
             * 
             * // 3. Enviar email
             * $emailService = new EmailService();
             * $emailService->sendCompanyRejectionEmail(
             *     $company_data['email'],
             *     $company_data['company_name'],
             *     $rejection_reason,
             *     $blacklist_result['rejection_count'],
             *     $blacklist_result['is_banned']
             * );
             * 
             * // 4. Eliminar de BD
             * $userModel->deleteCompany((int)$company_id);
             * 
             * ============================================================ */
        }
        
        header('Location: /SIEP/public/index.php?action=reviewCompanies&status=error');
        exit;
    }

    public function approveVacancy() {
        $this->session->guard(['upis', 'admin']); // <-- CORREGIDO
        $vacancy_id = $_GET['id'] ?? null;
        if ($vacancy_id) {
            $vacancyModel = new Vacancy();
            if ($vacancyModel->approve((int)$vacancy_id, $_SESSION['user_id'])) {
                header('Location: /SIEP/public/index.php?action=reviewVacancies&status=vacancy_approved');
                exit;
            }
        }
        header('Location: /SIEP/public/index.php?action=reviewVacancies&status=error');
        exit;
    }

    public function rejectVacancy() {
        $this->session->guard(['upis', 'admin']); // <-- CORREGIDO
        $vacancy_id = $_GET['id'] ?? null;
        if ($vacancy_id) {
            $vacancyModel = new Vacancy();
            if ($vacancyModel->deleteById_admin((int)$vacancy_id)) {
                header('Location: /SIEP/public/index.php?action=reviewVacancies&status=vacancy_rejected');
                exit;
            }
        }
        header('Location: /SIEP/public/index.php?action=reviewVacancies&status=error');
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
            header('Location: /SIEP/public/index.php?action=upisDashboard&status=' . $redirect_status . '&count=' . $count);
        } else {
            header('Location: /SIEP/public/index.php?action=upisDashboard&status=error');
        }
        exit;
    }

    public function downloadAllApprovedLetters() {
        $this->session->guard(['upis', 'admin']);
        $applicationModel = new DocumentApplication();
        $approved_ids = $applicationModel->getAllApprovedLetterIds();
        if (empty($approved_ids)) {
            header('Location: /SIEP/public/index.php?action=upisDashboard&status=no_approved_letters');
            exit;
        }
        $docService = new DocumentService();
        $students_data = $applicationModel->getApprovedStudentDataForLetters($approved_ids);
        if (empty($students_data)) { die("Error: No se encontraron datos para las solicitudes aprobadas."); }
        $zip = new ZipArchive();
        $zipFileName = 'Todas_Cartas_Presentacion_' . date('Y-m-d') . '.zip';
        $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) { die("Error al crear ZIP."); }
        foreach ($students_data as $student) {
            $student_data_for_pdf = [
                'full_name' => $student['first_name'] . ' ' . $student['last_name'],
                'boleta' => $student['boleta'],
                'career' => $student['career'],
                'percentage_progress' => $student['percentage_progress']
            ];
            $pdfContent = $docService->generatePresentationLetter($student_data_for_pdf, true);
            $pdfFileName = 'Carta_Presentacion_' . $student['boleta'] . '.pdf';
            $zip->addFromString($pdfFileName, $pdfContent);
        }
        $zip->close();
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

        // Definimos la carpeta de destino para los archivos
        $upload_dir = __DIR__ . '/../../public/uploads/signed_documents/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // Creamos la carpeta si no existe
        }

        $userModel = new User();
        require_once(__DIR__ . '/../Models/StudentDocument.php');
        $docModel = new StudentDocument();

        $success_count = 0;
        $error_count = 0;

        // Iteramos sobre cada archivo subido
        foreach ($_FILES['signed_letters']['name'] as $key => $filename) {
            if ($_FILES['signed_letters']['error'][$key] !== UPLOAD_ERR_OK) {
                $error_count++;
                continue; // Saltar al siguiente archivo si hay un error de subida
            }

            // 1. Parsear el nombre del archivo para obtener boleta y tipo
            $filename_no_ext = pathinfo($filename, PATHINFO_FILENAME);
            $parts = explode('_', $filename_no_ext);
            
            if (count($parts) !== 2) {
                $error_count++;
                continue; // Formato de nombre de archivo incorrecto
            }

            $boleta = $parts[0];
            $doc_code = $parts[1];

            // 2. Buscar al estudiante por boleta
            $student_id = $userModel->findStudentIdByBoleta($boleta);
            if (!$student_id) {
                $error_count++;
                continue; // Estudiante no encontrado
            }

            // 3. Mover el archivo a su destino final
            $destination = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['signed_letters']['tmp_name'][$key], $destination)) {
                // 4. Guardar el registro en la base de datos
                $doc_type = ($doc_code === 'CP') ? 'presentation_letter' : 'unknown';
                if ($docModel->create($student_id, $doc_type, 'public/uploads/signed_documents/' . $filename, $filename)) {
                    $success_count++;
                    // Aquí iría la lógica para enviar el correo de notificación
                } else {
                    $error_count++;
                    unlink($destination); // Borrar archivo si falla el registro en BD
                }
            } else {
                $error_count++;
            }
        }
        
        // 5. Redirigir con un mensaje de resumen
        header('Location: /SIEP/public/index.php?action=upisDashboard&status=upload_complete&success=' . $success_count . '&errors=' . $error_count);
        exit;
    }

    public function completeAccreditation() {
        $this->session->guard(['upis', 'admin']);

        $submission_id = $_GET['id'] ?? null;
        if (!$submission_id) { die("ID de la entrega no proporcionado."); }

        // --- 1. Obtener los datos del estudiante de esta entrega ---
        // (Necesitamos un método para obtener una submission por su ID)
        $accreditationModel = new Accreditation();
        $submission_data = $accreditationModel->getSubmissionById((int)$submission_id); // Lo crearemos ahora
        
        if (!$submission_data) { die("Entrega no encontrada."); }
        
        // --- 2. Obtener la fecha de la solicitud de la carta ---
        $applicationModel = new DocumentApplication();
        $letter_date = $applicationModel->getRequestDateByStudentId($submission_data['student_user_id']);

        // --- 3. Crear el registro histórico ---
        require_once(__DIR__ . '/../Models/CompletedProcess.php');
        $completedModel = new CompletedProcess();
        $student_full_name = $submission_data['first_name'] . ' ' . $submission_data['last_name'];

        if ($completedModel->create($submission_data['student_user_id'], $student_full_name, $submission_data['boleta'], $letter_date)) {
            // --- 4. Si el registro histórico se creó, eliminamos la solicitud pendiente ---
            if ($accreditationModel->deleteSubmission((int)$submission_id)) {
                header('Location: /SIEP/public/index.php?action=upisDashboard&status=accreditation_completed');
            } else {
                // Error crítico: se creó el histórico pero no se borró el pendiente
                header('Location: /SIEP/public/index.php?action=upisDashboard&status=critical_error');
            }
        } else {
            header('Location: /SIEP/public/index.php?action=upisDashboard&status=error');
        }
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

}
