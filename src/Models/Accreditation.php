<?php
/**
 * Modelo de Acreditación
 * 
 * Gestiona las acreditaciones finales de estudiantes
 * 
 * @package SIEP\Models
 * @version 3.0.0 - Actualizado para formulario completo
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
    public function getPendingSubmissions() {
        $sql = "SELECT 
                    ac.id, ac.boleta, ac.programa_academico, ac.empresa_nombre,
                    ac.tipo_acreditacion, ac.fecha_inicio, ac.fecha_fin,
                    ac.final_report_path, ac.validation_letter_path, 
                    ac.metadata, ac.submitted_at, ac.status,
                    u.first_name, u.last_name_p, u.last_name_m, u.email,
                    sp.boleta as student_boleta, sp.career
                FROM accreditation_submissions ac
                JOIN users u ON ac.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE ac.status = 'pending'
                ORDER BY ac.submitted_at ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decodificar metadata para cada resultado
        foreach ($results as &$result) {
            if (isset($result['metadata'])) {
                $result['metadata_decoded'] = json_decode($result['metadata'], true);
            }
        }
        
        return $results;
    }
    
    /**
     * Actualizar estado de acreditación
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

    // ========================================
    // MÉTODOS DE COMPATIBILIDAD (legacy)
    // ========================================
    
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
}