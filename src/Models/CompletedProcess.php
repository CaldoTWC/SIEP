<?php
/**
 * Modelo de Procesos Completados
 * 
 * Gestiona el registro histórico de procesos de estancia completados
 * 
 * @package SIEP\Models
 * @version 2.0.0 - Migrado de MySQLi a PDO
 */

require_once(__DIR__ . '/../Config/Database.php');

class CompletedProcess {
    
    private $conn;
    
    // Propiedades
    public $id;
    public $student_user_id;
    public $presentation_letter_date;
    public $accreditation_completed_date;
    public $total_duration_days;
    
    /**
     * Constructor
     */
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Registrar un proceso completado
     * 
     * @return bool
     */
    public function create() {
        $sql = "INSERT INTO completed_processes 
                (student_user_id, presentation_letter_date, accreditation_completed_date, total_duration_days) 
                VALUES 
                (:student_user_id, :presentation_letter_date, :accreditation_completed_date, :total_duration_days)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':student_user_id', $this->student_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':presentation_letter_date', $this->presentation_letter_date);
        $stmt->bindParam(':accreditation_completed_date', $this->accreditation_completed_date);
        $stmt->bindParam(':total_duration_days', $this->total_duration_days, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener todos los procesos completados (con datos del estudiante)
     * Usa la vista SQL v_completed_processes_full
     * 
     * @return array
     */
    public function getAllCompleted() {
        $sql = "SELECT * FROM v_completed_processes_full 
                ORDER BY accreditation_completed_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Obtener procesos completados con filtros
     * 
     * @param array $filters Filtros opcionales (career, year, etc)
     * @return array
     */
    public function getFilteredCompleted($filters = []) {
        $sql = "SELECT 
                    cp.*,
                    u.first_name, u.last_name_p, u.last_name_m,
                    sp.boleta, sp.career
                FROM completed_processes cp
                JOIN users u ON cp.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por carrera
        if (!empty($filters['career'])) {
            $sql .= " AND sp.career = :career";
            $params[':career'] = $filters['career'];
        }
        
        // Filtro por año
        if (!empty($filters['year'])) {
            $sql .= " AND YEAR(cp.accreditation_completed_date) = :year";
            $params[':year'] = $filters['year'];
        }
        
        $sql .= " ORDER BY cp.accreditation_completed_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Obtener estadísticas de procesos completados
     * 
     * @return array
     */
    public function getStatistics() {
        $sql = "SELECT 
                    COUNT(*) as total_completed,
                    AVG(total_duration_days) as avg_duration,
                    MIN(total_duration_days) as min_duration,
                    MAX(total_duration_days) as max_duration,
                    YEAR(accreditation_completed_date) as year
                FROM completed_processes
                GROUP BY YEAR(accreditation_completed_date)
                ORDER BY year DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Verificar si un estudiante ya tiene un proceso completado
     * 
     * @param int $student_user_id
     * @return bool
     */
    public function hasCompletedProcess($student_user_id) {
        $sql = "SELECT COUNT(*) as count 
                FROM completed_processes 
                WHERE student_user_id = :student_user_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}