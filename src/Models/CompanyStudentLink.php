<?php
/**
 * Modelo de Vínculo Empresa-Estudiante
 * 
 * Gestiona las relaciones activas entre empresas y estudiantes
 * durante la estancia profesional
 * 
 * @package SIEP\Models
 * @version 2.0.0 - Migrado de MySQLi a PDO
 */

require_once(__DIR__ . '/../Config/Database.php');

class CompanyStudentLink {
    
    private $conn;
    
    // Propiedades
    public $id;
    public $company_profile_id;
    public $student_user_id;
    public $status;
    public $acceptance_date;
    public $completion_date;
    
    /**
     * Constructor
     */
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Crear un nuevo vínculo empresa-estudiante
     * 
     * @return bool
     */
    public function create() {
        $sql = "INSERT INTO company_student_links 
                (company_profile_id, student_user_id, status, acceptance_date) 
                VALUES 
                (:company_profile_id, :student_user_id, 'active', :acceptance_date)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':company_profile_id', $this->company_profile_id, PDO::PARAM_INT);
        $stmt->bindParam(':student_user_id', $this->student_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':acceptance_date', $this->acceptance_date);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener vínculos activos de una empresa
     * 
     * @param int $company_profile_id
     * @return array
     */
    public function getActiveByCompanyId($company_profile_id) {
        $sql = "SELECT 
                    csl.*,
                    u.first_name, u.last_name_p, u.last_name_m, u.email,
                    sp.boleta, sp.career
                FROM company_student_links csl
                JOIN users u ON csl.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE csl.company_profile_id = :company_profile_id
                  AND csl.status = 'active'
                ORDER BY csl.acceptance_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':company_profile_id', $company_profile_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Obtener vínculos completados de una empresa
     * 
     * @param int $company_profile_id
     * @return array
     */
    public function getCompletedByCompanyId($company_profile_id) {
        $sql = "SELECT 
                    csl.*,
                    u.first_name, u.last_name_p, u.last_name_m,
                    sp.boleta, sp.career
                FROM company_student_links csl
                JOIN users u ON csl.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE csl.company_profile_id = :company_profile_id
                  AND csl.status = 'completed'
                ORDER BY csl.completion_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':company_profile_id', $company_profile_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Obtener vínculo activo de un estudiante
     * 
     * @param int $student_user_id
     * @return array|false
     */
    public function getActiveByStudentId($student_user_id) {
        $sql = "SELECT 
                    csl.*,
                    cp.company_name, cp.commercial_name, cp.website,
                    u.email as company_email, u.phone_number as company_phone
                FROM company_student_links csl
                JOIN company_profiles cp ON csl.company_profile_id = cp.id
                JOIN users u ON cp.contact_person_user_id = u.id
                WHERE csl.student_user_id = :student_user_id
                  AND csl.status = 'active'
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Verificar si existe un vínculo activo
     * 
     * @param int $company_profile_id
     * @param int $student_user_id
     * @return bool
     */
    public function existsActiveLink($company_profile_id, $student_user_id) {
        $sql = "SELECT COUNT(*) as count 
                FROM company_student_links 
                WHERE company_profile_id = :company_profile_id
                  AND student_user_id = :student_user_id
                  AND status = 'active'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':company_profile_id', $company_profile_id, PDO::PARAM_INT);
        $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Marcar vínculo como completado
     * 
     * @param int $link_id
     * @param string $completion_date Fecha de finalización
     * @return bool
     */
    public function markAsCompleted($link_id, $completion_date = null) {
        if ($completion_date === null) {
            $completion_date = date('Y-m-d');
        }
        
        $sql = "UPDATE company_student_links 
                SET status = 'completed',
                    completion_date = :completion_date
                WHERE id = :link_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':link_id', $link_id, PDO::PARAM_INT);
        $stmt->bindParam(':completion_date', $completion_date);
        
        return $stmt->execute();
    }
}