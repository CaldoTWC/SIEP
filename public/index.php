<?php
// Archivo: public/index.php (Versión Completa y Corregida)

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
    
    // ===================================================================
    // --- RUTAS DE UPIS ---
    // ===================================================================
    case 'upisDashboard':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->dashboard();
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
    case 'reviewLetters':
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->reviewLetters();
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
        require_once(__DIR__ . '/../src/Controllers/UpisController.php');
        $upisController = new UpisController();
        $upisController->showUploadDocumentsForm();
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

    // ===================================================================
    // --- RUTAS GENÉRICAS ---
    // ===================================================================
    case 'showVacancyDetails':
        require_once(__DIR__ . '/../src/Controllers/VacancyController.php');
        $vacancyController = new VacancyController();
        $vacancyController->showDetails();
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

    // --- RUTA POR DEFECTO ---
    default:
        header("HTTP/1.0 404 Not Found");
        echo "<h1>Error 404 - Página no encontrada</h1>";
        break;
}