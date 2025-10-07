<?php
/**
 * Modelo de Vacante
 * 
 * Gestiona las operaciones relacionadas con vacantes de estancia profesional
 * 
 * @package SIEP\Models
 * @version 2.0.0 - Corregido fetchAll()
 */

require_once(__DIR__ . '/../Config/Database.php');

class Vacancy {
    
    private $conn;
    
    // Propiedades de vacante
    public $id;
    public $company_profile_id;
    public $title;
    public $description;
    public $activities;
    public $modality;
    public $num_vacancies;
    public $status;
    
    /**
     * Constructor
     */
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Crear una nueva vacante
     * 
     * @return bool
     */
    public function create() {
        $sql = "INSERT INTO vacancies 
                (company_profile_id, title, description, activities, modality, status) 
                VALUES 
                (:company_profile_id, :title, :description, :activities, :modality, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':company_profile_id', $this->company_profile_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':activities', $this->activities);
        $stmt->bindParam(':modality', $this->modality);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener vacantes pendientes de aprobación
     * 
     * @return array
     */
    public function getPendingVacancies() {
        $sql = "SELECT 
                    v.id, v.title, v.description, v.modality, v.posted_at,
                    cp.company_name, cp.commercial_name,
                    u.email as company_email
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                JOIN users u ON cp.contact_person_user_id = u.id
                WHERE v.status = 'pending'
                ORDER BY v.posted_at ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: fetchAll()
    }
    
    /**
     * Obtener vacantes aprobadas (para estudiantes)
     * 
     * @return array
     */
    public function getApprovedVacancies() {
        $sql = "SELECT 
                    v.id, v.title, v.description, v.activities, v.modality,
                    v.economic_support, v.start_date, v.end_date,
                    cp.company_name, cp.commercial_name
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                WHERE v.status = 'approved'
                ORDER BY v.posted_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: fetchAll()
    }
    
    /**
     * Obtener vacantes de una empresa específica
     * 
     * @param int $company_profile_id
     * @return array
     */
    public function getByCompanyId($company_profile_id) {
        $sql = "SELECT * FROM vacancies 
                WHERE company_profile_id = :company_profile_id
                ORDER BY posted_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':company_profile_id', $company_profile_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: fetchAll()
    }
    
    /**
     * Obtener vacante por ID
     * 
     * @param int $vacancy_id
     * @return array|false
     */
    public function findById($vacancy_id) {
        $sql = "SELECT 
                    v.*,
                    cp.company_name, cp.commercial_name, cp.website,
                    u.email as company_email, u.phone_number as company_phone
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                JOIN users u ON cp.contact_person_user_id = u.id
                WHERE v.id = :vacancy_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // ✅ CORRECTO: fetch() (singular)
    }
    
    /**
     * Aprobar una vacante
     * 
     * @param int $vacancy_id
     * @param int $reviewer_id Usuario UPIS que aprueba
     * @return bool
     */
    public function approve($vacancy_id, $reviewer_id = null) {
        $sql = "UPDATE vacancies 
                SET status = 'approved',
                    upis_reviewer_id = :reviewer_id,
                    reviewed_at = NOW()
                WHERE id = :vacancy_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Rechazar una vacante
     * 
     * @param int $vacancy_id
     * @param string $feedback Motivo del rechazo
     * @param int $reviewer_id Usuario UPIS que rechaza
     * @return bool
     */
    public function reject($vacancy_id, $feedback, $reviewer_id = null) {
        $sql = "UPDATE vacancies 
                SET status = 'rejected',
                    rejection_feedback = :feedback,
                    upis_reviewer_id = :reviewer_id,
                    reviewed_at = NOW()
                WHERE id = :vacancy_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->bindParam(':feedback', $feedback);
        $stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar una vacante
     * (Para implementación futura de rechazo con eliminación)
     * 
     * @param int $vacancy_id
     * @return bool
     */
    public function deleteVacancy($vacancy_id) {
        $sql = "DELETE FROM vacancies WHERE id = :vacancy_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener datos de vacante para proceso de rechazo
     * (Para implementación futura con email)
     * 
     * @param int $vacancy_id
     * @return array|false
     */
    public function getVacancyDataForRejection($vacancy_id) {
        $sql = "SELECT 
                    v.id, v.title as vacancy_title,
                    u.email as company_email,
                    cp.company_name
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                JOIN users u ON cp.contact_person_user_id = u.id
                WHERE v.id = :vacancy_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // ✅ CORRECTO: fetch() (singular)
    }
}