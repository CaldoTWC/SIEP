<?php
/**
 * Modelo de Acreditación
 * 
 * Gestiona las acreditaciones finales de estudiantes
 * 
 * @package SIEP\Models
 * @version 4.0.0 - Agregados métodos approve() y reject() para sistema de reportes
 */

require_once(__DIR__ . '/../Config/Database.php');

class Accreditation {
    
    private $conn;
    private $lastInsertId;
    
    // Propiedades
    public $id;
    public $student_user_id;
    public $final_report_path;
    public $validation_letter_path;
    public $metadata;
    public $status;
    
    /**
     * Constructor
     */
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Crear acreditación con todos los campos (Versión Completa)
     * 
     * @param int $student_user_id
     * @param string $boleta
     * @param string $programa_academico
     * @param string $empresa_nombre
     * @param string $tipo_acreditacion ('A' o 'B')
     * @param string $fecha_inicio
     * @param string $fecha_fin
     * @param string $final_report_path
     * @param string $validation_letter_path
     * @param array $metadata
     * @return bool
     */
    public function createSubmissionComplete(
        $student_user_id,
        $boleta,
        $programa_academico,
        $empresa_nombre,
        $tipo_acreditacion,
        $fecha_inicio,
        $fecha_fin,
        $final_report_path,
        $validation_letter_path,
        $metadata = []
    ) {
        $sql = "INSERT INTO accreditation_submissions 
                (student_user_id, boleta, programa_academico, empresa_nombre, 
                 tipo_acreditacion, fecha_inicio, fecha_fin, 
                 final_report_path, validation_letter_path, metadata, 
                 status, submitted_at) 
                VALUES 
                (:student_user_id, :boleta, :programa_academico, :empresa_nombre,
                 :tipo_acreditacion, :fecha_inicio, :fecha_fin,
                 :final_report_path, :validation_letter_path, :metadata,
                 'pending', NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        // Convertir metadata a JSON
        $metadata_json = is_array($metadata) ? json_encode($metadata, JSON_UNESCAPED_UNICODE) : $metadata;
        
        $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':boleta', $boleta);
        $stmt->bindParam(':programa_academico', $programa_academico);
        $stmt->bindParam(':empresa_nombre', $empresa_nombre);
        $stmt->bindParam(':tipo_acreditacion', $tipo_acreditacion);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindParam(':final_report_path', $final_report_path);
        $stmt->bindParam(':validation_letter_path', $validation_letter_path);
        $stmt->bindParam(':metadata', $metadata_json);
        
        $result = $stmt->execute();
        
        if ($result) {
            $this->lastInsertId = $this->conn->lastInsertId();
        }
        
        return $result;
    }
    
    /**
     * Crear una nueva acreditación (Versión Simple - Compatibilidad)
     * 
     * @param int $student_user_id
     * @param string $final_report_path
     * @param string $validation_letter_path
     * @param array $metadata
     * @return bool
     */
    public function createSubmission($student_user_id, $final_report_path, $validation_letter_path, $metadata = null) {
        $sql = "INSERT INTO accreditation_submissions 
                (student_user_id, final_report_path, validation_letter_path, metadata, status, submitted_at) 
                VALUES 
                (:student_user_id, :final_report_path, :validation_letter_path, :metadata, 'pending', NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        // Convertir metadata a JSON si es un array
        $metadata_json = is_array($metadata) ? json_encode($metadata, JSON_UNESCAPED_UNICODE) : $metadata;
        
        $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':final_report_path', $final_report_path);
        $stmt->bindParam(':validation_letter_path', $validation_letter_path);
        $stmt->bindParam(':metadata', $metadata_json);
        
        $result = $stmt->execute();
        
        if ($result) {
            $this->lastInsertId = $this->conn->lastInsertId();
        }
        
        return $result;
    }
    
    /**
     * Obtener el ID de la última inserción
     * 
     * @return int
     */
    public function getLastInsertId() {
        return $this->lastInsertId;
    }
    
    /**
     * Obtener acreditación por ID con metadata decodificada
     * 
     * @param int $accreditation_id
     * @return array|false
     */
    public function getById($accreditation_id) {
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
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Decodificar metadata de JSON a array
        if ($result && isset($result['metadata'])) {
            $result['metadata_decoded'] = json_decode($result['metadata'], true);
        }
        
        return $result;
    }
    
    /**
     * Obtener todas las acreditaciones pendientes con metadata
     * 
     * @return array
     */
// En src/Models/Accreditation.php
// REEMPLAZAR el método getPendingSubmissions() existente

public function getPendingSubmissions() {
    $sql = "SELECT 
                ac.*,
                u.first_name, u.last_name_p, u.last_name_m, u.email,
                sp.boleta, sp.career
            FROM accreditation_submissions ac
            JOIN users u ON ac.student_user_id = u.id
            JOIN student_profiles sp ON u.id = sp.user_id
            WHERE ac.status = 'pending'
            ORDER BY ac.submitted_at DESC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ✅ AGREGAR: Decodificar metadata para cada resultado
    foreach ($results as &$result) {
        if (isset($result['metadata'])) {
            $result['metadata_decoded'] = json_decode($result['metadata'], true);
        }
    }
    
    return $results;
}
// En src/Models/Accreditation.php
// Agregar después del método getPendingSubmissions()

/**
 * Obtener todas las acreditaciones aprobadas con información completa
 * 
 * @return array
 */
public function getApprovedSubmissions() {
    $sql = "SELECT 
                ac.*,
                u.first_name, u.last_name_p, u.last_name_m, u.email,
                sp.boleta, sp.career,
                upis.first_name as upis_first_name,
                upis.last_name_p as upis_last_name_p
            FROM accreditation_submissions ac
            JOIN users u ON ac.student_user_id = u.id
            JOIN student_profiles sp ON u.id = sp.user_id
            LEFT JOIN users upis ON ac.upis_reviewer_id = upis.id
            WHERE ac.status = 'approved'
            ORDER BY ac.reviewed_at DESC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ✅ Decodificar metadata para cada resultado
    foreach ($results as &$result) {
        if (isset($result['metadata'])) {
            $result['metadata_decoded'] = json_decode($result['metadata'], true);
        }
    }
    
    return $results;
}
    
    /**
     * Actualizar estado de acreditación (método legacy)
     * 
     * @param int $accreditation_id
     * @param string $status ('pending', 'approved', 'rejected', 'completed')
     * @return bool
     */
    public function updateStatus($accreditation_id, $status) {
        $sql = "UPDATE accreditation_submissions 
                SET status = :status
                WHERE id = :accreditation_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':accreditation_id', $accreditation_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // ========================================================================
    // NUEVOS MÉTODOS PARA SISTEMA DE REPORTES (v4.0.0)
    // ========================================================================
    
    /**
     * Aprobar solicitud de acreditación
     * Actualiza el estado a 'approved' y registra revisor, fecha y comentarios
     * 
     * @param int $submission_id ID de la acreditación
     * @param int $reviewer_id ID del usuario UPIS que aprueba
     * @param string|null $comments Comentarios opcionales de UPIS
     * @return bool
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
     * Actualiza el estado a 'rejected' y registra revisor, fecha y comentarios obligatorios
     * 
     * @param int $submission_id ID de la acreditación
     * @param int $reviewer_id ID del usuario UPIS que rechaza
     * @param string $comments Comentarios OBLIGATORIOS explicando el rechazo
     * @return bool
     */
    public function reject($submission_id, $reviewer_id, $comments) {
        if (empty($comments)) {
            throw new Exception("Los comentarios son obligatorios al rechazar una acreditación");
        }
        
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
    
    // ========================================================================
    // MÉTODOS ADICIONALES
    // ========================================================================
    
    /**
     * Eliminar acreditación
     * 
     * @param int $accreditation_id
     * @return bool
     */
    public function deleteSubmission($accreditation_id) {
        $sql = "DELETE FROM accreditation_submissions WHERE id = :accreditation_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':accreditation_id', $accreditation_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener acreditación por estudiante
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
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decodificar metadata
        foreach ($results as &$result) {
            if (isset($result['metadata'])) {
                $result['metadata_decoded'] = json_decode($result['metadata'], true);
            }
        }
        
        return $results;
    }

    // ========================================================================
    // MÉTODOS DE COMPATIBILIDAD (legacy)
    // ========================================================================
    
    /**
     * Obtener acreditaciones pendientes (alias)
     */
    public function getPendingAccreditations() {
        return $this->getPendingSubmissions();
    }
    
    /**
     * Obtener por ID (alias)
     */
    public function findById($accreditation_id) {
        return $this->getById($accreditation_id);
    }
    
    /**
     * Marcar como completada
     */
    public function markAsCompleted($accreditation_id) {
        return $this->updateStatus($accreditation_id, 'completed');
    }

        /**
     * Obtener detalles completos de acreditación para PDF
     * Incluye toda la información necesaria para generar el expediente
     * 
     * @param int $accreditation_id
     * @return array|false
     */
    public function getAccreditationDetailsById($accreditation_id) {
        $sql = "SELECT 
                    ac.*,
                    u.first_name, u.last_name_p, u.last_name_m, u.email,
                    sp.boleta, sp.career,
                    upis.first_name as upis_first_name,
                    upis.last_name_p as upis_last_name_p
                FROM accreditation_submissions ac
                JOIN users u ON ac.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                LEFT JOIN users upis ON ac.upis_reviewer_id = upis.id
                WHERE ac.id = :accreditation_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':accreditation_id', $accreditation_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
