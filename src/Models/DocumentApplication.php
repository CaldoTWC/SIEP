<?php
/**
 * Modelo de Solicitudes de Documentos
 * 
 * Gestiona las solicitudes de cartas de presentación de estudiantes
 * 
 * @package SIEP\Models
 * @version 3.0.0 - Sistema de 3 estados (pending, approved, completed)
 */

require_once(__DIR__ . '/../Config/Database.php');

class DocumentApplication {
    
    private $conn;
    
    // Propiedades
    public $id;
    public $student_user_id;
    public $application_type;
    public $status;
    public $credits_percentage;
    public $current_semester;
    public $transcript_path;
    public $target_company_name;
    public $target_recipient_name;
    public $target_recipient_position;
    public $show_required_hours;
    
    /**
     * Constructor
     */
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Crear una nueva solicitud de carta de presentación
     * 
     * @param array $data Datos de la solicitud
     * @return bool
     */
    public function create($data) {
        $sql = "INSERT INTO document_applications 
                (student_user_id, application_type, credits_percentage, current_semester,
                 transcript_path, target_company_name, target_recipient_name, 
                 target_recipient_position, show_required_hours, status) 
                VALUES 
                (:student_user_id, 'presentation_letter', :credits_percentage, :current_semester,
                 :transcript_path, :target_company_name, :target_recipient_name, 
                 :target_recipient_position, :show_required_hours, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':student_user_id', $data['student_user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':credits_percentage', $data['credits_percentage']);
        $stmt->bindParam(':current_semester', $data['current_semester'], PDO::PARAM_INT);
        $stmt->bindParam(':transcript_path', $data['transcript_path']);
        $stmt->bindParam(':target_company_name', $data['target_company_name']);
        $stmt->bindParam(':target_recipient_name', $data['target_recipient_name']);
        $stmt->bindParam(':target_recipient_position', $data['target_recipient_position']);
        $stmt->bindParam(':show_required_hours', $data['show_required_hours'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // ========================================================================
    // MÉTODOS PARA SISTEMA DE 3 ESTADOS (pending, approved, completed)
    // ========================================================================
    
    /**
     * Obtener solicitudes de cartas PENDIENTES de revisión
     * 
     * @return array
     */
    public function getPendingLetters() {
        $sql = "SELECT 
                    da.id, 
                    da.created_at, 
                    da.credits_percentage, 
                    da.current_semester,
                    da.target_company_name, 
                    da.transcript_path,
                    da.has_specific_recipient,
                    da.recipient_name,
                    da.recipient_position,
                    da.requires_hours,
                    da.letter_template_type,
                    u.first_name, u.last_name_p, u.last_name_m, u.email,
                    sp.boleta, sp.career
                FROM document_applications da
                JOIN users u ON da.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE da.status = 'pending' 
                  AND da.application_type = 'presentation_letter'
                ORDER BY da.created_at ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener solicitudes APROBADAS (listas para descargar)
     * 
     * @return array
     */
    public function getApprovedLetters() {
        $sql = "SELECT 
                    da.id, 
                    da.created_at, 
                    da.reviewed_at,
                    da.letter_number,
                    da.credits_percentage,
                    da.current_semester,  
                    da.target_company_name,
                    da.has_specific_recipient,
                    da.recipient_name,
                    da.recipient_position,
                    da.requires_hours,
                    da.letter_template_type,
                    u.first_name, u.last_name_p, u.last_name_m, u.email,
                    sp.boleta, sp.career
                FROM document_applications da
                JOIN users u ON da.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE da.status = 'approved' 
                  AND da.application_type = 'presentation_letter'
                ORDER BY da.reviewed_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener solicitudes COMPLETADAS (finalizadas y entregadas)
     * 
     * @return array
     */
    public function getCompletedLetters() {
        $sql = "SELECT 
                    da.id, 
                    da.created_at, 
                    da.reviewed_at,
                    da.completed_at,
                    da.letter_number,
                    da.credits_percentage,
                    da.target_company_name,
                    da.has_specific_recipient,
                    da.recipient_name,
                    da.recipient_position,
                    da.requires_hours,
                    da.letter_template_type,
                    u.first_name, u.last_name_p, u.last_name_m, u.email,
                    sp.boleta, sp.career
                FROM document_applications da
                JOIN users u ON da.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE da.status = 'completed' 
                  AND da.application_type = 'presentation_letter'
                ORDER BY da.completed_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Marcar una carta como completada
     * 
     * @param int $application_id
     * @return bool
     */
    public function markAsCompleted($application_id) {
        $sql = "UPDATE document_applications 
                SET status = 'completed',
                    completed_at = NOW()
                WHERE id = :application_id
                  AND status = 'approved'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener carta para re-descargar (approved o completed)
     * 
     * @param int $application_id
     * @return array|false
     */
    public function getLetterForDownload($application_id) {
        $sql = "SELECT 
                    da.id as application_id,
                    da.letter_number,
                    da.letter_template_type,
                    da.has_specific_recipient,
                    da.recipient_name,
                    da.recipient_position,
                    da.requires_hours,
                    da.credits_percentage as percentage_progress,
                    u.first_name,
                    u.last_name_p,
                    u.last_name_m,
                    sp.boleta,
                    sp.career
                FROM document_applications da
                JOIN users u ON da.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE da.id = :application_id
                  AND da.status IN ('approved', 'completed')
                  AND da.application_type = 'presentation_letter'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // ========================================================================
    // MÉTODOS LEGACY (Compatibilidad con código existente)
    // ========================================================================
    
    /**
     * Obtener solicitudes de cartas pendientes de revisión
     * 
     * @return array
     */
    public function getPendingPresentationLetters() {
        return $this->getPendingLetters();
    }
    
    /**
     * Obtener solicitudes aprobadas
     * 
     * @return array
     */
    public function getApprovedPresentationLetters() {
        return $this->getApprovedLetters();
    }
    
    /**
     * Obtener solicitudes de un estudiante específico
     * 
     * @param int $student_user_id
     * @return array
     */
    public function getByStudentId($student_user_id) {
        $sql = "SELECT * FROM document_applications 
                WHERE student_user_id = :student_user_id
                ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener solicitud por ID
     * 
     * @param int $application_id
     * @return array|false
     */
    public function findById($application_id) {
        $sql = "SELECT 
                    da.*,
                    u.first_name, u.last_name_p, u.last_name_m, u.email, u.phone_number,
                    sp.boleta, sp.career
                FROM document_applications da
                JOIN users u ON da.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE da.id = :application_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Aprobar una solicitud de carta
     * 
     * @param int $application_id
     * @param int $reviewer_id Usuario UPIS que aprueba
     * @return bool
     */
    public function approve($application_id, $reviewer_id = null) {
        $sql = "UPDATE document_applications 
                SET status = 'approved',
                    upis_reviewer_id = :reviewer_id,
                    reviewed_at = NOW()
                WHERE id = :application_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
        $stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Rechazar una solicitud de carta
     * 
     * @param int $application_id
     * @param string $feedback Motivo del rechazo
     * @param int $reviewer_id Usuario UPIS que rechaza
     * @return bool
     */
    public function reject($application_id, $feedback, $reviewer_id = null) {
        $sql = "UPDATE document_applications 
                SET status = 'rejected',
                    feedback = :feedback,
                    upis_reviewer_id = :reviewer_id,
                    reviewed_at = NOW()
                WHERE id = :application_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
        $stmt->bindParam(':feedback', $feedback);
        $stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Marcar como procesada (carta generada)
     * 
     * @param int $application_id
     * @return bool
     */
    public function markAsProcessed($application_id) {
        $sql = "UPDATE document_applications 
                SET status = 'processed'
                WHERE id = :application_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
 * Obtener todos los IDs de solicitudes aprobadas
 * 
 * @return array Array de IDs
 */
public function getAllApprovedLetterIds() {
    $sql = "SELECT id 
            FROM document_applications 
            WHERE status = 'approved' 
              AND application_type = 'presentation_letter'";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    return $results ? $results : [];
}

/**
 * Obtener datos de estudiantes para cartas aprobadas
 * Incluye información de plantillas
 * 
 * @param array $application_ids - IDs de solicitudes
 * @return array
 */
public function getApprovedStudentDataForLetters($application_ids) {
    if (empty($application_ids)) {
        return [];
    }
    
    $placeholders = implode(',', array_fill(0, count($application_ids), '?'));
    
    $sql = "SELECT 
                da.id as application_id,
                da.letter_number,
                da.letter_template_type,
                da.has_specific_recipient,
                da.recipient_name,
                da.recipient_position,
                da.requires_hours,
                da.credits_percentage as percentage_progress,
                u.first_name,
                u.last_name_p,
                u.last_name_m,
                sp.boleta,
                sp.career
            FROM document_applications da
            JOIN users u ON da.student_user_id = u.id
            JOIN student_profiles sp ON u.id = sp.user_id
            WHERE da.id IN ($placeholders)
              AND da.status = 'approved'
              AND da.application_type = 'presentation_letter'
            ORDER BY sp.boleta ASC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute($application_ids);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Eliminar múltiples solicitudes por IDs
 * 
 * @param array $ids Array de IDs a eliminar
 * @return bool
 */
public function deleteByIds($ids) {
    if (empty($ids)) {
        return false;
    }
    
    // Crear placeholders
    $placeholders = [];
    foreach ($ids as $index => $id) {
        $placeholders[] = ':id' . $index;
    }
    $placeholders_string = implode(',', $placeholders);
    
    $sql = "DELETE FROM document_applications WHERE id IN ($placeholders_string)";
    
    $stmt = $this->conn->prepare($sql);
    
    // Bind cada ID
    foreach ($ids as $index => $id) {
        $stmt->bindValue(':id' . $index, (int)$id, PDO::PARAM_INT);
    }
    
    return $stmt->execute();
}

    /**
 * Actualizar el estado de múltiples solicitudes (aprobación/rechazo en lote)
 * 
 * @param array $request_ids - Array de IDs de solicitudes
 * @param string $new_status - Nuevo estado ('approved' o 'rejected')
 * @param int $reviewer_id - ID del usuario UPIS que revisa
 * @return bool
 */
public function updateStatusForMultipleIds($request_ids, $new_status, $reviewer_id) {
    if (empty($request_ids)) {
        return false;
    }
    
    // Crear placeholders para la consulta (:id0, :id1, :id2, ...)
    $placeholders = [];
    foreach ($request_ids as $index => $id) {
        $placeholders[] = ':id' . $index;
    }
    $placeholders_string = implode(',', $placeholders);
    
    $sql = "UPDATE document_applications 
            SET status = :status, 
                upis_reviewer_id = :reviewer_id, 
                reviewed_at = NOW() 
            WHERE id IN ($placeholders_string)";
    
    $stmt = $this->conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    // Bind del status y reviewer_id
    $stmt->bindParam(':status', $new_status);
    $stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
    
    // Bind de cada ID
    foreach ($request_ids as $index => $id) {
        $stmt->bindValue(':id' . $index, (int)$id, PDO::PARAM_INT);
    }
    
    return $stmt->execute();
}

/**
 * Crear solicitud con información de plantilla
 * (Versión mejorada con soporte para variantes)
 * 
 * @param array $data - Datos de la solicitud
 * @return bool|int - ID de la solicitud creada o false
 */
public function createWithTemplate($data) {
    $sql = "INSERT INTO document_applications 
            (student_user_id, application_type, credits_percentage, current_semester,
             transcript_path, target_company_name, 
             has_specific_recipient, recipient_name, recipient_position,
             requires_hours, letter_template_type, status) 
            VALUES 
            (:student_user_id, 'presentation_letter', :credits_percentage, :current_semester,
             :transcript_path, :target_company_name,
             :has_specific_recipient, :recipient_name, :recipient_position,
             :requires_hours, :letter_template_type, 'pending')";
    
    $stmt = $this->conn->prepare($sql);
    
    $stmt->bindParam(':student_user_id', $data['student_user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':credits_percentage', $data['credits_percentage']);
    $stmt->bindParam(':current_semester', $data['current_semester'], PDO::PARAM_INT);
    $stmt->bindParam(':transcript_path', $data['transcript_path']);
    $stmt->bindParam(':target_company_name', $data['target_company_name']);
    $stmt->bindParam(':has_specific_recipient', $data['has_specific_recipient'], PDO::PARAM_INT);
    $stmt->bindParam(':recipient_name', $data['recipient_name']);
    $stmt->bindParam(':recipient_position', $data['recipient_position']);
    $stmt->bindParam(':requires_hours', $data['requires_hours'], PDO::PARAM_INT);
    $stmt->bindParam(':letter_template_type', $data['letter_template_type']);
    
    if ($stmt->execute()) {
        return $this->conn->lastInsertId();
    }
    
    return false;
}

/**
 * Asignar número de oficio a una solicitud aprobada
 * 
 * @param int $application_id - ID de la solicitud
 * @param string $letter_number - Número de oficio (ej: "No. 01-2025/2")
 * @return bool
 */
public function assignLetterNumber($application_id, $letter_number) {
    $sql = "UPDATE document_applications 
            SET letter_number = :letter_number
            WHERE id = :application_id";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':letter_number', $letter_number);
    $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
    
    return $stmt->execute();
}

/**
 * Obtener solicitud completa con información de plantilla
 * 
 * @param int $application_id
 * @return array|false
 */
public function findByIdWithTemplate($application_id) {
    $sql = "SELECT 
                da.*,
                u.first_name, u.last_name_p, u.last_name_m, u.email, u.phone_number,
                sp.boleta, sp.career,
                lt.template_name, lt.template_file_path, lt.academic_period
            FROM document_applications da
            JOIN users u ON da.student_user_id = u.id
            JOIN student_profiles sp ON u.id = sp.user_id
            LEFT JOIN letter_templates lt ON da.letter_template_type = lt.template_type
            WHERE da.id = :application_id";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Obtener solicitudes pendientes con información de plantilla
 * 
 * @return array
 */
public function getPendingPresentationLettersWithTemplate() {
    $sql = "SELECT 
                da.id, da.created_at, da.credits_percentage, da.current_semester,
                da.target_company_name, da.transcript_path,
                da.has_specific_recipient, da.recipient_name, da.recipient_position,
                da.requires_hours, da.letter_template_type,
                u.first_name, u.last_name_p, u.last_name_m, u.email,
                sp.boleta, sp.career,
                lt.template_name
            FROM document_applications da
            JOIN users u ON da.student_user_id = u.id
            JOIN student_profiles sp ON u.id = sp.user_id
            LEFT JOIN letter_templates lt ON da.letter_template_type = lt.template_type
            WHERE da.status = 'pending' 
              AND da.application_type = 'presentation_letter'
            ORDER BY da.created_at ASC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener solicitudes aprobadas listas para generar carta
 * (Con toda la información necesaria para el PDF)
 * 
 * @return array
 */
public function getApprovedLettersReadyForGeneration() {
    $sql = "SELECT 
                da.id as application_id,
                da.letter_number,
                da.letter_template_type,
                da.has_specific_recipient,
                da.recipient_name,
                da.recipient_position,
                da.requires_hours,
                da.target_company_name,
                u.id as student_user_id,
                u.first_name,
                u.last_name_p,
                u.last_name_m,
                CONCAT(u.first_name, ' ', u.last_name_p, ' ', u.last_name_m) as full_name,
                sp.boleta,
                sp.career,
                da.credits_percentage as percentage_progress,
                lt.template_file_path,
                lt.academic_period
            FROM document_applications da
            JOIN users u ON da.student_user_id = u.id
            JOIN student_profiles sp ON u.id = sp.user_id
            LEFT JOIN letter_templates lt ON da.letter_template_type = lt.template_type
            WHERE da.status = 'approved'
              AND da.application_type = 'presentation_letter'
              AND da.letter_number IS NOT NULL
            ORDER BY da.updated_at DESC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener estadísticas de uso de plantillas
 * 
 * @return array
 */
public function getTemplateUsageStats() {
    $sql = "SELECT 
                da.letter_template_type,
                lt.template_name,
                COUNT(*) as total_requests,
                SUM(CASE WHEN da.status = 'approved' THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN da.status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN da.status = 'rejected' THEN 1 ELSE 0 END) as rejected_count
            FROM document_applications da
            LEFT JOIN letter_templates lt ON da.letter_template_type = lt.template_type
            WHERE da.application_type = 'presentation_letter'
            GROUP BY da.letter_template_type, lt.template_name
            ORDER BY total_requests DESC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}