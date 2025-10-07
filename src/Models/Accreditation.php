<?php
/**
 * Modelo de Acreditación
 * 
 * Gestiona las acreditaciones finales de estudiantes
 * 
 * @package SIEP\Models
 * @version 2.0.0 - Migrado de MySQLi a PDO
 */

require_once(__DIR__ . '/../Config/Database.php');

class Accreditation {
    
    private $conn;
    
    // Propiedades
    public $id;
    public $student_user_id;
    public $final_report_path;
    public $validation_letter_path;
    public $status;
    
    /**
     * Constructor
     */
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Crear una nueva acreditación
     * 
     * @return bool
     */
    public function create() {
        $sql = "INSERT INTO accreditation_submissions 
                (student_user_id, final_report_path, validation_letter_path, status) 
                VALUES 
                (:student_user_id, :final_report_path, :validation_letter_path, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':student_user_id', $this->student_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':final_report_path', $this->final_report_path);
        $stmt->bindParam(':validation_letter_path', $this->validation_letter_path);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener todas las acreditaciones pendientes
     * 
     * @return array
     */
    public function getPendingAccreditations() {
        $sql = "SELECT 
                    ac.id, ac.final_report_path, ac.validation_letter_path, 
                    ac.submitted_at, ac.status,
                    u.first_name, u.last_name_p, u.last_name_m, u.email,
                    sp.boleta, sp.career
                FROM accreditation_submissions ac
                JOIN users u ON ac.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE ac.status = 'pending'
                ORDER BY ac.submitted_at ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Obtener acreditaciones completadas
     * 
     * @return array
     */
    public function getCompletedAccreditations() {
        $sql = "SELECT 
                    ac.id, ac.submitted_at, ac.status,
                    u.first_name, u.last_name_p, u.last_name_m,
                    sp.boleta, sp.career
                FROM accreditation_submissions ac
                JOIN users u ON ac.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE ac.status = 'completed'
                ORDER BY ac.submitted_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Obtener acreditaciones de un estudiante
     * 
     * @param int $student_user_id
     * @return array
     */
    public function getByStudentId($student_user_id) {
        $sql = "SELECT * FROM accreditation_submissions 
                WHERE student_user_id = :student_user_id
                ORDER BY submitted_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Obtener acreditación por ID
     * 
     * @param int $accreditation_id
     * @return array|false
     */
    public function findById($accreditation_id) {
        $sql = "SELECT 
                    ac.*,
                    u.first_name, u.last_name_p, u.last_name_m, u.email,
                    sp.boleta, sp.career
                FROM accreditation_submissions ac
                JOIN users u ON ac.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE ac.id = :accreditation_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':accreditation_id', $accreditation_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Marcar acreditación como completada
     * 
     * @param int $accreditation_id
     * @return bool
     */
    public function markAsCompleted($accreditation_id) {
        $sql = "UPDATE accreditation_submissions 
                SET status = 'completed'
                WHERE id = :accreditation_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':accreditation_id', $accreditation_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}