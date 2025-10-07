<?php
/**
 * Modelo para generación de reportes
 * Adaptado 100% a la estructura real de la base de datos SIEP
 */

require_once(__DIR__ . '/../Config/Database.php');

class Report {
    private $conn;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Dashboard: Estadísticas generales
     */
    public function getDashboardStats() {
        $stats = [];
        
        // Total de empresas registradas
        $query = "SELECT COUNT(*) as total FROM company_profiles";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_empresas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de estudiantes (rol 'student')
        $query = "SELECT COUNT(*) as total 
                  FROM users 
                  WHERE role = 'student' AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_estudiantes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Solicitudes activas (en proceso)
        $query = "SELECT COUNT(*) as total 
                  FROM document_applications 
                  WHERE status IN ('pending', 'approved')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['estancias_activas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Procesos completados
        $query = "SELECT COUNT(*) as total 
                  FROM document_applications 
                  WHERE status = 'processed'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['procesos_completados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Vacantes activas (aprobadas)
        $query = "SELECT COUNT(*) as total FROM vacancies WHERE status = 'approved'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['vacantes_activas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Distribución por carrera
        $query = "SELECT career, COUNT(*) as total 
                  FROM student_profiles
                  GROUP BY career
                  ORDER BY total DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['por_carrera'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }
    
    /**
     * Historial de solicitudes completadas (cartas de presentación procesadas)
     */
    public function getEstanciasCompletadas($filtros = []) {
    $query = "SELECT 
                sp.boleta,
                CONCAT(u.first_name, ' ', u.last_name_p, ' ', u.last_name_m) as estudiante,
                sp.career as carrera,
                da.target_company_name as empresa,
                da.application_type as tipo_documento,
                COALESCE(da.status, 'Sin estado') as estatus,
                da.created_at as fecha_inicio,
                da.updated_at as fecha_actualizacion,
                da.reviewed_at as fecha_revision
              FROM document_applications da
              INNER JOIN users u ON da.student_user_id = u.id
              INNER JOIN student_profiles sp ON u.id = sp.user_id
              WHERE 1=1";
    
    // Aplicar filtros
    if (!empty($filtros['carrera'])) {
        $query .= " AND sp.career = :carrera";
    }
    if (!empty($filtros['fecha_inicio'])) {
        $query .= " AND DATE(da.created_at) >= :fecha_inicio";
    }
    if (!empty($filtros['fecha_fin'])) {
        $query .= " AND DATE(da.created_at) <= :fecha_fin";
    }
    
    $query .= " ORDER BY da.updated_at DESC";
    
    $stmt = $this->conn->prepare($query);
    
    // Bind de parámetros
    if (!empty($filtros['carrera'])) {
        $stmt->bindParam(':carrera', $filtros['carrera']);
    }
    if (!empty($filtros['fecha_inicio'])) {
        $stmt->bindParam(':fecha_inicio', $filtros['fecha_inicio']);
    }
    if (!empty($filtros['fecha_fin'])) {
        $stmt->bindParam(':fecha_fin', $filtros['fecha_fin']);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
    /**
     * Reporte de vacantes activas (aprobadas)
     */
    public function getVacantesActivas() {
        $query = "SELECT 
                    v.id,
                    v.title as titulo,
                    cp.company_name as empresa,
                    v.description as descripcion,
                    CONCAT_WS(', ', 
                        NULLIF(v.required_knowledge, ''), 
                        NULLIF(v.required_competencies, ''),
                        NULLIF(v.required_languages, '')
                    ) as requisitos,
                    v.work_location_address as ubicacion,
                    v.start_date as fecha_inicio,
                    v.end_date as fecha_fin,
                    v.posted_at as fecha_publicacion,
                    (SELECT COUNT(*) 
                     FROM company_student_links csl 
                     WHERE csl.company_profile_id = v.company_profile_id
                     AND csl.status = 'active') as postulantes
                  FROM vacancies v
                  INNER JOIN company_profiles cp ON v.company_profile_id = cp.id
                  WHERE v.status = 'approved'
                  ORDER BY v.posted_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Reporte de empresas con estudiantes vinculados
     */
    public function getEmpresasConEstudiantes() {
        $query = "SELECT 
                    cp.id,
                    cp.company_name as empresa,
                    u.email as contacto_email,
                    u.phone_number as contacto_telefono,
                    COUNT(DISTINCT CASE 
                        WHEN csl.status = 'active' 
                        THEN csl.student_user_id 
                    END) as estudiantes_activos,
                    COUNT(DISTINCT CASE 
                        WHEN csl.status = 'completed' 
                        THEN csl.student_user_id 
                    END) as estudiantes_completados,
                    COUNT(DISTINCT csl.student_user_id) as total_estudiantes
                  FROM company_profiles cp
                  INNER JOIN users u ON cp.contact_person_user_id = u.id
                  LEFT JOIN company_student_links csl ON cp.id = csl.company_profile_id
                  GROUP BY cp.id
                  HAVING total_estudiantes > 0
                  ORDER BY estudiantes_activos DESC, estudiantes_completados DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estudiantes de una empresa específica
     */
    public function getEstudiantesPorEmpresa($company_id) {
        $query = "SELECT 
                    sp.boleta,
                    CONCAT(u.first_name, ' ', u.last_name_p, ' ', u.last_name_m) as estudiante,
                    sp.career as carrera,
                    csl.status as estatus,
                    csl.acceptance_date as fecha_inicio,
                    csl.completion_date as ultima_actualizacion
                  FROM company_student_links csl
                  INNER JOIN users u ON csl.student_user_id = u.id
                  INNER JOIN student_profiles sp ON u.id = sp.user_id
                  WHERE csl.company_profile_id = :company_id
                  ORDER BY csl.status, csl.acceptance_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener carreras para filtros
     */
    public function getCarreras() {
        $query = "SELECT DISTINCT career FROM student_profiles ORDER BY career";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}