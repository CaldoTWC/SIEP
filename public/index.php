<?php
// Archivo: public/index.php
// Sistema SIEP - UPIICSA
// Actualización: 2025-11-13 - Sistema de notificaciones integrado


date_default_timezone_set('America/Mexico_City');
$action = $_GET['action'] ?? 'home';


switch ($action) {

    // --- RUTA DE INICIO ---
    case 'home':
        require_once(__DIR__ . '/../src/Views/pages/home.php');
        break;

    // ===================================================================
    // --- RUTAS DE AUTENTICACIÓN ---
    // ===================================================================
    case 'showRegisterSelection':
        require_once(__DIR__ . '/../src/Views/auth/register_selection.php');
        break;
    case 'showStudentRegisterForm':
        require_once(__DIR__ . '/../src/Controllers/AuthController.php');
        $authController = new AuthController();
        $authController->showRegisterForm();
        break;
    case 'registerStudent':
        require_once(__DIR__ . '/../src/Controllers/AuthController.php');
        $authController = new AuthController();
        $authController->registerStudent();
        break;
    case 'showCompanyRegisterForm':
        require_once(__DIR__ . '/../src/Controllers/AuthController.php');
        $authController = new AuthController();
        $authController->showCompanyRegisterForm();
        break;
    case 'registerCompany':
        require_once(__DIR__ . '/../src/Controllers/AuthController.php');
        $authController = new AuthController();
        $authController->registerCompany();
        break;
    case 'showLogin':
        require_once(__DIR__ . '/../src/Controllers/AuthController.php');
        $authController = new AuthController();
        $authController->showLoginForm();
        break;
    case 'login':
        require_once(__DIR__ . '/../src/Controllers/AuthController.php');
        $authController = new AuthController();
        $authController->login();
        break;
    case 'logout':
        require_once(__DIR__ . '/../src/Lib/Session.php');
        $session = new Session();
        $session->logout();
        header('Location: /SIEP/public/index.php?action=showLogin');
        exit;
        break;

    // ===================================================================
    // --- RUTAS DE ESTUDIANTE ---
    // ===================================================================
    case 'studentDashboard':
        require_once(__DIR__ . '/../src/Controllers/StudentController.php');
        $studentController = new StudentController();
        $studentController->dashboard();
        break;
    case 'listVacancies':
        require_once(__DIR__ . '/../src/Controllers/StudentController.php');
        $studentController = new StudentController();
        $studentController->listVacancies();
        break;
    case 'showVacancies':
        require_once(__DIR__ . '/../src/Controllers/StudentController.php');
        $studentController = new StudentController();
        $studentController->showVacancies();
        break;
    case 'showDetailedLetterForm':
        require_once(__DIR__ . '/../src/Controllers/StudentController.php');
        $studentController = new StudentController();
        $studentController->showDetailedLetterForm();
        break;
    case 'submitDetailedLetterRequest':
        require_once(__DIR__ . '/../src/Controllers/StudentController.php');
        $studentController = new StudentController();
        $studentController->submitDetailedLetterRequest();
        break;
    case 'showMyDocuments':
        require_once(__DIR__ . '/../src/Controllers/StudentController.php');
        $studentController = new StudentController();
        $studentController->showMyDocuments();
        break;
    case 'showAccreditationForm':
        require_once(__DIR__ . '/../src/Controllers/StudentController.php');
        $studentController = new StudentController();
        $studentController->showAccreditationForm();
        break;
    case 'submitAccreditation':
        require_once(__DIR__ . '/../src/Controllers/StudentController.php');
        $studentController = new StudentController();
        $studentController->submitAccreditation();
        break;

    // ===================================================================
    // --- RUTAS DE EMPRESA ---
    // ===================================================================
    case 'companyDashboard':
        require_once(__DIR__ . '/../src/Controllers/CompanyController.php');
        $companyController = new CompanyController();
        $companyController->dashboard();
        break;
    case 'showPostVacancyForm':
        require_once(__DIR__ . '/../src/Controllers/CompanyController.php');
        $companyController = new CompanyController();
        $companyController->showPostVacancyForm();
        break;
    case 'postVacancy':
        require_once(__DIR__ . '/../src/Controllers/CompanyController.php');
        $companyController = new CompanyController();
        $companyController->postVacancy();
        break;
    case 'showAcceptanceLetterForm':
        require_once(__DIR__ . '/../src/Controllers/CompanyController.php');
        $companyController = new CompanyController();
        $companyController->showAcceptanceLetterForm();
        break;
    case 'generateAcceptanceLetter':
        require_once(__DIR__ . '/../src/Controllers/CompanyController.php');
        $companyController = new CompanyController();
        $companyController->generateAcceptanceLetter();
        break;
    case 'showValidationLetterForm':
        require_once(__DIR__ . '/../src/Controllers/CompanyController.php');
        $companyController = new CompanyController();
        $companyController->showValidationLetterForm();
        break;
    case 'generateValidationLetter':
        require_once(__DIR__ . '/../src/Controllers/CompanyController.php');
        $companyController = new CompanyController();
        $companyController->generateValidationLetter();
        break;
    case 'deleteVacancy':
        require_once(__DIR__ . '/../src/Controllers/CompanyController.php');
        $companyController = new CompanyController();
        $companyController->deleteVacancy();
        break;
    case 'completeVacancy':
        require_once(__DIR__ . '/../src/Controllers/CompanyController.php');
        $companyController = new CompanyController();
        $companyController->completeVacancy();
        break;
    
    // ===================================================================
    // --- RUTAS DE UPIS ---
    // ===================================================================
    case 'upisDashboard':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->dashboard();
        break;
    
    // --- Gestión de Empresas ---
    case 'companyManagementHub':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->companyManagementHub();
        break;
    case 'reviewCompanies':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->reviewCompanies();
        break;
    case 'approveCompany':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->approveCompany();
        break;
    case 'rejectCompany':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->rejectCompany();
        break;
    
    // --- Gestión de Vacantes ---
    case 'reviewVacancies':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->reviewVacancies();
        break;
    case 'approveVacancy':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->approveVacancy();
        break;
    case 'rejectVacancy':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->rejectVacancy();
        break;
    
    // --- Hub de Vacantes (NUEVO) ---
    case 'vacancyHub':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->vacancyHub();
        break;
    
    // --- Gestionar Vacantes Activas (NUEVO) ---
    case 'manageActiveVacancies':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->manageActiveVacancies();
        break;
    case 'takedownVacancy':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->takedownVacancy();
        break;
    
    // --- Papelera de Vacantes (NUEVO) ---
    case 'vacancyTrash':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->vacancyTrash();
        break;
    case 'restoreVacancy':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->restoreVacancy();
        break;
    case 'hardDeleteVacancy':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->hardDeleteVacancy();
        break;
    
        // --- Reportes y Estadísticas (ACTUALIZADO) ---
    case 'exportActivePDF':
        require_once(__DIR__ . '/../src/Controllers/ReportController.php');
        $reportController = new ReportController();
        $reportController->exportActivePDF();
        break;
    case 'exportCompletedPDF':
        require_once(__DIR__ . '/../src/Controllers/ReportController.php');
        $reportController = new ReportController();
        $reportController->exportCompletedPDF();
        break;
    case 'exportCanceledPDF':
        require_once(__DIR__ . '/../src/Controllers/ReportController.php');
        $reportController = new ReportController();
        $reportController->exportCanceledPDF();
        break;
    case 'exportAllExcel':
        require_once(__DIR__ . '/../src/Controllers/ReportController.php');
        $reportController = new ReportController();
        $reportController->exportAllExcel();
        break;
    case 'exportCompanyAnalysisExcel':
        require_once(__DIR__ . '/../src/Controllers/ReportController.php');
        $reportController = new ReportController();
        $reportController->exportCompanyAnalysisExcel();
        break;
    
        // ===================================================================
    // --- GESTIÓN DE CARTAS DE PRESENTACIÓN (HUB MODERNIZADO) ---
    // ===================================================================
    
    // --- Hub Principal de Cartas ---
    case 'presentationLettersHub':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->presentationLettersHub();
        break;
    
    // --- Ver Detalles de una Solicitud ---
    case 'viewLetterDetails':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->viewLetterDetails();
        break;
    
    // --- Aprobar/Rechazar Individual ---
    case 'approveSingleLetter':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->approveSingleLetter();
        break;
    case 'rejectSingleLetter':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->rejectSingleLetter();
        break;
    
    // --- Descargar Cartas Aprobadas ---
    case 'downloadAllApprovedLettersFromHub':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->downloadAllApprovedLettersFromHub();
        break;
    case 'downloadSingleApprovedLetter':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->downloadSingleApprovedLetter();
        break;
    
    // --- Subir Cartas Firmadas (Nuevo Flujo) ---
    case 'uploadSignedLettersToHub':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->uploadSignedLettersToHub();
        break;
    
    // --- Descargar Cartas Completadas ---
    case 'downloadCompletedLetter':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->downloadCompletedLetter();
        break;
    
    // --- Limpiar Historial de Completadas ---
    case 'clearCompletedLetters':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->clearCompletedLetters();
        break;
    
    // ===================================================================
    // --- GESTIÓN DE CARTAS (RUTAS LEGACY - MANTENER POR COMPATIBILIDAD) ---
    // ===================================================================
    case 'reviewLetters':
        // Redirigir al nuevo hub
        header('Location: /SIEP/public/index.php?action=presentationLettersHub');
        exit;
        break;
    case 'processLetterRequests':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->processLetterRequests();
        break;
    case 'downloadAllApprovedLetters':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->downloadAllApprovedLetters();
        break;
    case 'clearAllApprovedLetters':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->clearAllApprovedLetters();
        break;
    case 'showUploadDocumentsForm':
        // Redirigir al tab de upload del hub
        header('Location: /SIEP/public/index.php?action=presentationLettersHub&tab=upload');
        exit;
        break;
    case 'uploadSignedLetters':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->uploadSignedLetters();
        break;
    case 'completeAccreditation':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->completeAccreditation();
        break;
    case 'showHistory':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->showHistory();
        break;
    case 'downloadHistoryReport':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->downloadHistoryReport();
        break;

    // En public/index.php, después del case 'reviewAccreditations':

case 'viewApprovedAccreditations':
    require_once(__DIR__ . '/../src/Controllers/UpisController.php');
    $upisController = new UpisController();
    $upisController->viewApprovedAccreditations();
    break;
    
    // ===================================================================
    // --- RUTAS DE NOTIFICACIONES ---
    // ===================================================================
    
    // API de Notificaciones
    case 'getNotificationsDropdown':
        require_once(__DIR__ . '/../src/Controllers/NotificationController.php');
        require_once(__DIR__ . '/../src/Config/Database.php');
        $database = Database::getInstance();
        $notificationController = new NotificationController($database->getConnection());
        $notificationController->getNotificationsDropdown();
        break;

    case 'getUnreadCount':
        require_once(__DIR__ . '/../src/Controllers/NotificationController.php');
        require_once(__DIR__ . '/../src/Config/Database.php');
        $database = Database::getInstance();
        $notificationController = new NotificationController($database->getConnection());
        $notificationController->getUnreadCount();
        break;

    case 'markNotificationAsRead':
        require_once(__DIR__ . '/../src/Controllers/NotificationController.php');
        require_once(__DIR__ . '/../src/Config/Database.php');
        $database = Database::getInstance();
        $notificationController = new NotificationController($database->getConnection());
        $notificationController->markAsRead();
        break;

    case 'markAllNotificationsAsRead':
        require_once(__DIR__ . '/../src/Controllers/NotificationController.php');
        require_once(__DIR__ . '/../src/Config/Database.php');
        $database = Database::getInstance();
        $notificationController = new NotificationController($database->getConnection());
        $notificationController->markAllAsRead();
        break;

    // Vista de Notificaciones
    case 'showAllNotifications':
        require_once(__DIR__ . '/../src/Controllers/NotificationController.php');
        require_once(__DIR__ . '/../src/Config/Database.php');
        $database = Database::getInstance();
        $notificationController = new NotificationController($database->getConnection());
        $notificationController->showAllNotifications();
        break;

    case 'deleteNotification':
        require_once(__DIR__ . '/../src/Controllers/NotificationController.php');
        require_once(__DIR__ . '/../src/Config/Database.php');
        $database = Database::getInstance();
        $notificationController = new NotificationController($database->getConnection());
        $notificationController->deleteNotification();
        break;
    
    // --- Gestión de Plantillas ---
    case 'manageTemplates':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->manageTemplates();
        break;
    case 'uploadTemplate':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->uploadTemplate();
        break;
    case 'resetLetterCounters':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->resetLetterCounters();
        break;

    // --- Gestión de Archivos ---
    // Agregar después de las rutas de estudiante en public/index.php

case 'viewDocument':
    require_once(__DIR__ . '/../src/Controllers/FileController.php');
    $fileController = new FileController();
    $fileController->viewDocument();
    break;

case 'downloadDocument':
    require_once(__DIR__ . '/../src/Controllers/FileController.php');
    $fileController = new FileController();
    $fileController->downloadDocument();
    break;

    // ===================================================================
    // --- RUTAS GENÉRICAS ---
    // ===================================================================
    case 'showVacancyDetails':
        require_once(__DIR__ . '/../src/Controllers/VacancyController.php');
        $vacancyController = new VacancyController();
        $vacancyController->showDetails();
        break;

            // --- Reportes de Estudiantes ---
    case 'exportStudentProcessingPDF':
        require_once(__DIR__ . '/../src/Controllers/ReportController.php');
        $reportController = new ReportController();
        $reportController->exportStudentProcessingPDF();
        break;
    case 'exportStudentProcessingExcel':
        require_once(__DIR__ . '/../src/Controllers/ReportController.php');
        $reportController = new ReportController();
        $reportController->exportStudentProcessingExcel();
        break;
    case 'exportCompanyStudentsPDF':
        require_once(__DIR__ . '/../src/Controllers/ReportController.php');
        $reportController = new ReportController();
        $reportController->exportCompanyStudentsPDF();
        break;
    case 'exportCompanyStudentsExcel':
        require_once(__DIR__ . '/../src/Controllers/ReportController.php');
        $reportController = new ReportController();
        $reportController->exportCompanyStudentsExcel();
        break;

    // ===================================================================
    // --- RUTAS DE PERFIL (TODOS LOS USUARIOS) ---
    // ===================================================================
    case 'showChangePasswordForm':
        require_once(__DIR__ . '/../src/Controllers/ProfileController.php');
        $profileController = new ProfileController();
        $profileController->showChangePasswordForm();
        break;
    case 'changePassword':
        require_once(__DIR__ . '/../src/Controllers/ProfileController.php');
        $profileController = new ProfileController();
        $profileController->changePassword();
        break;

        // --- Dashboard de Reportes ---
    case 'reportDashboard':
        require_once(__DIR__ . '/../src/Controllers/ReportController.php');
        $reportController = new ReportController();
        $reportController->dashboard();
        break;

            // --- Gestión de Acreditaciones ---
        // --- Gestión de Acreditaciones ---
    case 'reviewAccreditations':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->reviewAccreditations();
        break;
    case 'approveAccreditation':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->approveAccreditation();
        break;
        case 'rejectAccreditation':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->rejectAccreditation();
        break;

  
    case 'downloadAccreditationPDF':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->downloadAccreditationPDF();
        break;

    // --- RUTA POR DEFECTO ---
    default:
        header("HTTP/1.0 404 Not Found");
        echo "<h1>Error 404 - Página no encontrada</h1>";
        break;
}