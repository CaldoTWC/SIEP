<?php
/**
 * Modelo de Vacante
 * 
 * Gestiona todas las operaciones relacionadas con vacantes:
 * - Creación de vacantes por empresas
 * - Aprobación/rechazo por UPIS
 * - Consulta de vacantes aprobadas para estudiantes
 * - Gestión de ciclo de vida completo (completar, cancelar, restaurar)
 * 
 * @package SIEP\Models
 * @version 4.0.0 - Sistema de gestión de ciclo de vida de vacantes
 * @date 2025-10-29
 */

require_once(__DIR__ . '/../Config/Database.php');

class Vacancy {
    
    private $conn;
    
    // Propiedades básicas
    public $id;
    public $company_profile_id;
    public $title;
    public $description;
    public $activities; // Campo legacy
    public $modality;
    public $num_vacancies;
    public $status; // pending, approved, completed, rejected
    public $posted_at;
    public $approved_at;
    public $approved_by;
    
    // ATENCIÓN A ESTUDIANTES INTERESADOS
    public $attention_days;      // JSON array
    public $attention_schedule;  // Texto libre (ej: "9:00 - 17:00")
    
    // GENERALIDADES DE LA POSTULACIÓN
    public $vacancy_names;       // Descripción adicional de vacantes
    public $economic_support;    // DECIMAL(10,2)
    public $start_date;          // DATE
    public $end_date;            // DATE
    
    // PERFIL PARA OCUPAR LA VACANTE
    public $key_information;     // TEXT
    public $related_career;      // VARCHAR(255)
    public $required_knowledge;  // TEXT
    public $required_competencies; // TEXT
    public $required_languages;  // JSON array
    
    // ACTIVIDADES A REALIZAR (NUEVO SISTEMA SIMPLIFICADO)
    public $activities_list;     // TEXT: Lista de actividades en texto libre
    public $activity_details;    // TEXT: Descripción general adicional
    
    // MODALIDAD DE TRABAJO
    public $work_location_address; // TEXT (solo para Presencial/Híbrida)
    public $work_days;            // JSON array
    public $start_time;           // TIME
    public $end_time;             // TIME
    
    // PUBLICACIÓN DE LOGOTIPOS
    public $logo_auth;            // TINYINT (0 o 1)
    public $logo_url;             // VARCHAR(500)
    
    // AVISO DE PRIVACIDAD
    public $privacy_accepted;     // TINYINT (0 o 1)
    
    // Feedback de UPIS
    public $feedback;             // TEXT
    
    /**
     * Constructor - Inicializa conexión a BD
     */
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    // ========================================================================
    // MÉTODOS DE CREACIÓN
    // ========================================================================
    
    /**
     * Crear una nueva vacante con todos los campos
     * 
     * @return bool
     */
    public function create() {
        $sql = "INSERT INTO vacancies 
                (company_profile_id, title, description, activities, modality, 
                 attention_days, attention_schedule, 
                 num_vacancies, vacancy_names, economic_support, start_date, end_date,
                 key_information, related_career, required_knowledge, required_competencies, required_languages,
                 activities_list, activity_details,
                 work_location_address, work_days, start_time, end_time,
                 logo_auth, logo_url, privacy_accepted, status) 
                VALUES 
                (:company_profile_id, :title, :description, :activities, :modality,
                 :attention_days, :attention_schedule,
                 :num_vacancies, :vacancy_names, :economic_support, :start_date, :end_date,
                 :key_information, :related_career, :required_knowledge, :required_competencies, :required_languages,
                 :activities_list, :activity_details,
                 :work_location_address, :work_days, :start_time, :end_time,
                 :logo_auth, :logo_url, :privacy_accepted, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        
        // Bind de parámetros básicos
        $stmt->bindParam(':company_profile_id', $this->company_profile_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':activities', $this->activities);
        $stmt->bindParam(':modality', $this->modality);
        
        // ATENCIÓN A ESTUDIANTES
        $stmt->bindParam(':attention_days', $this->attention_days);
        $stmt->bindParam(':attention_schedule', $this->attention_schedule);
        
        // GENERALIDADES
        $stmt->bindParam(':num_vacancies', $this->num_vacancies, PDO::PARAM_INT);
        $stmt->bindParam(':vacancy_names', $this->vacancy_names);
        $stmt->bindParam(':economic_support', $this->economic_support);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        
        // PERFIL
        $stmt->bindParam(':key_information', $this->key_information);
        $stmt->bindParam(':related_career', $this->related_career);
        $stmt->bindParam(':required_knowledge', $this->required_knowledge);
        $stmt->bindParam(':required_competencies', $this->required_competencies);
        $stmt->bindParam(':required_languages', $this->required_languages);
        
        // ACTIVIDADES (NUEVO SISTEMA SIMPLIFICADO)
        $stmt->bindParam(':activities_list', $this->activities_list);
        $stmt->bindParam(':activity_details', $this->activity_details);
        
        // MODALIDAD
        $stmt->bindParam(':work_location_address', $this->work_location_address);
        $stmt->bindParam(':work_days', $this->work_days);
        $stmt->bindParam(':start_time', $this->start_time);
        $stmt->bindParam(':end_time', $this->end_time);
        
        // PUBLICACIÓN
        $stmt->bindParam(':logo_auth', $this->logo_auth, PDO::PARAM_INT);
        $stmt->bindParam(':logo_url', $this->logo_url);
        $stmt->bindParam(':privacy_accepted', $this->privacy_accepted, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // ========================================================================
    // MÉTODOS DE CONSULTA
    // ========================================================================
    
    /**
     * Obtener vacantes pendientes de aprobación (para UPIS)
     * 
     * @return array
     */
    public function getPendingVacancies() {
        $sql = "SELECT 
                    v.*,
                    cp.company_name, 
                    cp.commercial_name,
                    u.email as company_email,
                    u.phone_number as company_phone
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                JOIN users u ON cp.contact_person_user_id = u.id
                WHERE v.status = 'pending'
                ORDER BY v.posted_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener vacantes aprobadas (para estudiantes)
     * 
     * @return array
     */
    public function getApprovedVacancies() {
        $sql = "SELECT 
                    v.*,
                    cp.company_name, 
                    cp.commercial_name,
                    cp.website,
                    cp.business_area,
                    cp.company_type
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                WHERE v.status = 'approved'
                ORDER BY v.approved_at DESC, v.posted_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener vacantes de una empresa específica
     * 
     * @param int $company_profile_id
     * @return array
     */
    public function getVacanciesByCompany($company_profile_id) {
        $sql = "SELECT 
                    v.*,
                    cp.company_name
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                WHERE v.company_profile_id = :company_profile_id
                ORDER BY v.posted_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':company_profile_id', $company_profile_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener una vacante por ID (con datos de empresa)
     * 
     * @param int $vacancy_id
     * @return array|false
     */
    public function getVacancyById($vacancy_id) {
        $sql = "SELECT 
                    v.*,
                    cp.company_name, 
                    cp.commercial_name,
                    cp.website,
                    cp.business_area,
                    cp.company_type,
                    cp.employee_count,
                    u.email as company_email,
                    u.phone_number as company_phone
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                JOIN users u ON cp.contact_person_user_id = u.id
                WHERE v.id = :vacancy_id
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // ========================================================================
    // MÉTODOS DE GESTIÓN (UPIS)
    // ========================================================================
    
    /**
     * Aprobar una vacante
     * 
     * @param int $vacancy_id ID de la vacante
     * @param int $reviewer_id ID del usuario UPIS que aprueba
     * @return bool
     */
    public function approve($vacancy_id, $reviewer_id) {
        $sql = "UPDATE vacancies 
                SET status = 'approved',
                    approved_at = NOW(),
                    approved_by = :reviewer_id
                WHERE id = :vacancy_id 
                  AND status = 'pending'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Rechazar una vacante durante revisión inicial
     * Guarda el origen del rechazo, motivo y justificación
     * 
     * @param int $vacancy_id ID de la vacante
     * @param int $reviewer_id ID del usuario UPIS que rechaza
     * @param string $rejection_reason Motivo predefinido
     * @param string $rejection_notes Justificación obligatoria
     * @return bool
     */
    public function reject($vacancy_id, $reviewer_id, $rejection_reason, $rejection_notes) {
        $sql = "UPDATE vacancies 
                SET status = 'rejected',
                    rejection_source = 'upis_review',
                    rejection_reason = :rejection_reason,
                    rejection_notes = :rejection_notes,
                    approved_at = NOW(),
                    approved_by = :reviewer_id
                WHERE id = :vacancy_id 
                  AND status = 'pending'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
        $stmt->bindParam(':rejection_reason', $rejection_reason);
        $stmt->bindParam(':rejection_notes', $rejection_notes);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar una vacante (soft delete - cambiar a rejected)
     * Sin feedback en BD, se notifica por email
     * 
     * @param int $vacancy_id
     * @return bool
     */
    public function delete($vacancy_id) {
        $sql = "UPDATE vacancies 
                SET status = 'rejected',
                    approved_at = NOW()
                WHERE id = :vacancy_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar permanentemente una vacante (hard delete)
     * Solo usar en casos excepcionales
     * 
     * @param int $vacancy_id
     * @return bool
     */
    public function hardDelete($vacancy_id) {
        $sql = "DELETE FROM vacancies WHERE id = :vacancy_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // ========================================================================
    // MÉTODOS DE CICLO DE VIDA (NUEVO)
    // ========================================================================
    
    /**
     * Completar vacante (solo empresa)
     * Marca la vacante como completada exitosamente
     * 
     * @param int $vacancy_id ID de la vacante
     * @param string $completion_reason Motivo predefinido (Cupos llenos, Estancia concluida, etc.)
     * @param string $completion_notes Comentarios adicionales opcionales
     * @return bool
     */
    public function complete($vacancy_id, $completion_reason, $completion_notes = '') {
        $sql = "UPDATE vacancies 
                SET status = 'completed',
                    completion_reason = :reason,
                    completion_notes = :notes,
                    completed_at = NOW()
                WHERE id = :vacancy_id 
                  AND status = 'approved'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->bindParam(':reason', $completion_reason);
        $stmt->bindParam(':notes', $completion_notes);
        
        return $stmt->execute();
    }
    
    /**
     * Cancelar vacante por empresa (con justificación obligatoria)
     * La empresa puede cancelar vacantes pending o approved
     * 
     * @param int $vacancy_id ID de la vacante
     * @param string $rejection_reason Motivo predefinido
     * @param string $rejection_notes Justificación obligatoria
     * @return bool
     */
    public function cancelByCompany($vacancy_id, $rejection_reason, $rejection_notes) {
        $sql = "UPDATE vacancies 
                SET status = 'rejected',
                    rejection_source = 'company_cancel',
                    rejection_reason = :reason,
                    rejection_notes = :notes,
                    approved_at = NOW()
                WHERE id = :vacancy_id 
                  AND status IN ('pending', 'approved')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->bindParam(':reason', $rejection_reason);
        $stmt->bindParam(':notes', $rejection_notes);
        
        return $stmt->execute();
    }
    
    /**
     * Tumbar vacante activa (solo UPIS)
     * UPIS puede desactivar una vacante ya aprobada
     * 
     * @param int $vacancy_id ID de la vacante
     * @param int $reviewer_id ID del usuario UPIS
     * @param string $rejection_notes Justificación obligatoria
     * @return bool
     */
    public function takedown($vacancy_id, $reviewer_id, $rejection_notes) {
        $sql = "UPDATE vacancies 
                SET status = 'rejected',
                    rejection_source = 'upis_takedown',
                    rejection_notes = :notes,
                    approved_at = NOW(),
                    approved_by = :reviewer_id
                WHERE id = :vacancy_id 
                  AND status = 'approved'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        $stmt->bindParam(':reviewer_id', $reviewer_id, PDO::PARAM_INT);
        $stmt->bindParam(':notes', $rejection_notes);
        
        return $stmt->execute();
    }
    
    /**
     * Restaurar vacante rechazada (volver a pending)
     * Permite re-revisar una vacante que fue rechazada
     * 
     * @param int $vacancy_id ID de la vacante
     * @return bool
     */
    public function restore($vacancy_id) {
        $sql = "UPDATE vacancies 
                SET status = 'pending',
                    rejection_source = NULL,
                    rejection_reason = NULL,
                    rejection_notes = NULL,
                    approved_at = NULL,
                    approved_by = NULL
                WHERE id = :vacancy_id 
                  AND status = 'rejected'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':vacancy_id', $vacancy_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // ========================================================================
    // MÉTODOS DE ESTADÍSTICAS Y REPORTES
    // ========================================================================
    
    /**
     * Obtener estadísticas de la papelera
     * Cuenta vacantes rechazadas por origen
     * 
     * @return array
     */
    public function getTrashStatistics() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN rejection_source = 'upis_review' THEN 1 ELSE 0 END) as upis_review,
                    SUM(CASE WHEN rejection_source = 'company_cancel' THEN 1 ELSE 0 END) as company_cancel,
                    SUM(CASE WHEN rejection_source = 'upis_takedown' THEN 1 ELSE 0 END) as upis_takedown
                FROM vacancies
                WHERE status = 'rejected'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estadísticas globales del sistema
     * Incluye el nuevo estado 'completed'
     * 
     * @return array
     */
    public function getGlobalStatistics() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                FROM vacancies";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener vacantes por estado
     * Con datos de empresa y revisor
     * 
     * @param string $status Estado de la vacante
     * @return array
     */
    public function getVacanciesByStatus($status) {
        $sql = "SELECT 
                    v.*,
                    cp.company_name,
                    cp.rfc,
                    u.email as company_email,
                    reviewer.name as reviewer_name
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                JOIN users u ON cp.contact_person_user_id = u.id
                LEFT JOIN users reviewer ON v.approved_by = reviewer.id
                WHERE v.status = :status
                ORDER BY v.posted_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todas las vacantes para reportes
     * Sin filtros, con datos de empresa
     * 
     * @return array
     */
    public function getAllVacanciesForReports() {
        $sql = "SELECT 
                    v.*,
                    cp.company_name,
                    cp.rfc,
                    u.email as company_email
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                JOIN users u ON cp.contact_person_user_id = u.id
                ORDER BY v.posted_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener vacantes agrupadas por empresa
     * Con estadísticas calculadas por empresa
     * 
     * @return array
     */
    public function getVacanciesGroupedByCompany() {
        // Primero obtener todas las empresas con vacantes
        $sql = "SELECT DISTINCT 
                    cp.id as company_profile_id,
                    cp.company_name,
                    cp.rfc,
                    u.email as company_email
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                JOIN users u ON cp.contact_person_user_id = u.id
                ORDER BY cp.company_name ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Para cada empresa, obtener sus vacantes y estadísticas
        foreach ($companies as &$company) {
            // Obtener vacantes de la empresa
            $sql_vacancies = "SELECT * FROM vacancies 
                              WHERE company_profile_id = :company_id 
                              ORDER BY posted_at DESC";
            $stmt_v = $this->conn->prepare($sql_vacancies);
            $stmt_v->bindParam(':company_id', $company['company_profile_id']);
            $stmt_v->execute();
            $company['vacancies'] = $stmt_v->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular estadísticas
            $total = count($company['vacancies']);
            $completed = 0;
            $cancelled = 0;
            $active = 0;
            $pending = 0;
            
            foreach ($company['vacancies'] as $vacancy) {
                switch ($vacancy['status']) {
                    case 'completed':
                        $completed++;
                        break;
                    case 'rejected':
                        $cancelled++;
                        break;
                    case 'approved':
                        $active++;
                        break;
                    case 'pending':
                        $pending++;
                        break;
                }
            }
            
            $finalized = $completed + $cancelled;
            $success_rate = $finalized > 0 ? round(($completed / $finalized) * 100, 1) : 0;
            
            $company['stats'] = [
                'total' => $total,
                'pending' => $pending,
                'active' => $active,
                'completed' => $completed,
                'cancelled' => $cancelled,
                'success_rate' => $success_rate
            ];
        }
        
        return $companies;
    }
    
    // ========================================================================
    // MÉTODOS AUXILIARES EXISTENTES
    // ========================================================================
    
    /**
     * Contar vacantes por status
     * 
     * @param string $status
     * @return int
     */
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total 
                FROM vacancies 
                WHERE status = :status";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }
    
    /**
     * Obtener estadísticas de vacantes
     * 
     * @return array
     */
    public function getStatistics() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(num_vacancies) as total_positions
                FROM vacancies";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Buscar vacantes por palabra clave
     * 
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $sql = "SELECT 
                    v.*,
                    cp.company_name, 
                    cp.commercial_name
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                WHERE v.status = 'approved'
                  AND (v.title LIKE :keyword 
                       OR v.description LIKE :keyword 
                       OR v.required_knowledge LIKE :keyword
                       OR v.activities_list LIKE :keyword
                       OR cp.company_name LIKE :keyword)
                ORDER BY v.approved_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $search_term = '%' . $keyword . '%';
        $stmt->bindParam(':keyword', $search_term);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Filtrar vacantes por carrera
     * 
     * @param string $career
     * @return array
     */
    public function filterByCareer($career) {
        $sql = "SELECT 
                    v.*,
                    cp.company_name, 
                    cp.commercial_name
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                WHERE v.status = 'approved'
                  AND (v.related_career = :career 
                       OR v.related_career = 'Todas las anteriores')
                ORDER BY v.approved_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':career', $career);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Filtrar vacantes por modalidad
     * 
     * @param string $modality (Presencial, Híbrida, Virtual)
     * @return array
     */
    public function filterByModality($modality) {
        $sql = "SELECT 
                    v.*,
                    cp.company_name, 
                    cp.commercial_name
                FROM vacancies v
                JOIN company_profiles cp ON v.company_profile_id = cp.id
                WHERE v.status = 'approved'
                  AND v.modality = :modality
                ORDER BY v.approved_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':modality', $modality);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}