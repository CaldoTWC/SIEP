<?php
/**
 * Modelo de Acreditaciones
 * Gestiona las solicitudes de acreditación de estudiantes
 */

require_once(__DIR__ . '/../Config/Database.php');

class Accreditation {
    
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Obtener todas las solicitudes pendientes
     */
    public function getPendingSubmissions() {
        $sql = "SELECT 
                    acc.*,
                    u.first_name,
                    u.last_name_p,
                    u.last_name_m,
                    u.email,
                    sp.boleta,
                    sp.career
                FROM accreditation_submissions acc
                INNER JOIN users u ON acc.student_user_id = u.id
                INNER JOIN student_profiles sp ON u.id = sp.user_id
                WHERE acc.status = 'pending'
                ORDER BY acc.submitted_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener solicitud por ID
     */
    public function getById($id) {
        $sql = "SELECT 
                    acc.*,
                    u.first_name,
                    u.last_name_p,
                    u.last_name_m,
                    u.email,
                    sp.boleta,
                    sp.career
                FROM accreditation_submissions acc
                INNER JOIN users u ON acc.student_user_id = u.id
                INNER JOIN student_profiles sp ON u.id = sp.user_id
                WHERE acc.id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Aprobar solicitud de acreditación
     */
    public function approve($submission_id, $reviewer_id, $comments = null) {
        $sql = "UPDATE accreditation_submissions 
                SET status = 'approved',
                    upis_reviewer_id = :reviewer_id,
                    reviewed_at = NOW(),
                    upis_comments = :comments
                WHERE id = :submission_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
        $stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
        $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
    /**
     * Rechazar solicitud de acreditación
     */
    public function reject($submission_id, $reviewer_id, $comments) {
        $sql = "UPDATE accreditation_submissions 
                SET status = 'rejected',
                    upis_reviewer_id = :reviewer_id,
                    reviewed_at = NOW(),
                    upis_comments = :comments
                WHERE id = :submission_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':submission_id', $submission_id, PDO::PARAM_INT);
        $stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
        $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar estado (método legacy - mantener por compatibilidad)
     */
    public function updateStatus($submission_id, $new_status) {
        $sql = "UPDATE accreditation_submissions 
                SET status = :status
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $submission_id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
}