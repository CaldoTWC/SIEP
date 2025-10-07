
<?php
require_once(__DIR__ . '/../Models/Vacancy.php');
require_once(__DIR__ . '/../Lib/Session.php');

class VacancyController {
    private $session;
    public function __construct() { $this->session = new Session(); }
    
    public function showDetails() {
        $this->session->guard(['student', 'company']);
        $vacancy_id = $_GET['id'] ?? 0;
        $vacancyModel = new Vacancy();
        $vacancy = $vacancyModel->findById((int)$vacancy_id);
        require_once(__DIR__ . '/../Views/vacancies/details.php');
    }
}