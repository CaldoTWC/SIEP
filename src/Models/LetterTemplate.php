<?php
/**
 * Modelo de Plantillas de Cartas de Presentación
 * 
 * Gestiona las plantillas PDF y la numeración automática de oficios
 * 
 * @package SIEP\Models
 * @version 1.0.0
 * @date 2025-10-27
 */

require_once(__DIR__ . '/../Config/Database.php');

class LetterTemplate {
    
    private $conn;
    
    // Propiedades
    public $id;
    public $template_type;
    public $template_name;
    public $template_file_path;
    public $is_active;
    public $academic_period;
    public $academic_year;
    public $current_letter_number;
    public $updated_by_user_id;
    
    /**
     * Constructor
     */
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Obtener todas las plantillas activas
     * 
     * @return array
     */
    public function getAllActiveTemplates() {
        $sql = "SELECT * FROM letter_templates 
                WHERE is_active = 1 
                ORDER BY template_type ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener plantilla por tipo
     * 
     * @param string $template_type - Tipo de plantilla
     * @return array|false
     */
    public function getTemplateByType($template_type) {
        $sql = "SELECT * FROM letter_templates 
                WHERE template_type = :type AND is_active = 1 
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':type', $template_type);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Determinar tipo de plantilla según opciones del estudiante
     * 
     * @param bool $has_recipient - ¿Tiene destinatario específico?
     * @param bool $requires_hours - ¿Requiere mostrar horas?
     * @return string - Tipo de plantilla
     */
    public function determineTemplateType($has_recipient, $requires_hours) {
        if ($has_recipient && $requires_hours) {
            return 'destinatario_horas';
        } elseif ($has_recipient && !$requires_hours) {
            return 'destinatario';
        } elseif (!$has_recipient && $requires_hours) {
            return 'normal_horas';
        } else {
            return 'normal';
        }
    }
    
    /**
     * Obtener plantilla activa según necesidades
     * 
     * @param bool $has_recipient
     * @param bool $requires_hours
     * @return array|false
     */
    public function getActiveTemplateByNeeds($has_recipient, $requires_hours) {
        $type = $this->determineTemplateType($has_recipient, $requires_hours);
        return $this->getTemplateByType($type);
    }
    
    /**
 * Generar y obtener siguiente número de oficio GLOBAL
 * Formato: "No. 01-2025/2"
 * TODAS las cartas comparten el mismo contador
 * 
 * @return string|false - Número de oficio formateado
 */
public function generateNextLetterNumber() {
    try {
        // Iniciar transacción para evitar duplicados
        $this->conn->beginTransaction();
        
        // Incrementar el contador GLOBAL en la primera plantilla
        $sql = "UPDATE letter_templates 
                SET global_letter_counter = global_letter_counter + 1 
                WHERE id = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        // Obtener el nuevo número y periodo
        $sql2 = "SELECT global_letter_counter, academic_period 
                 FROM letter_templates 
                 WHERE id = 1";
        
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute();
        
        $result = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            $this->conn->rollBack();
            return false;
        }
        
        // Confirmar transacción
        $this->conn->commit();
        
        // Formatear como "No. 01-2025/2"
        $formatted = sprintf(
            "No. %02d-%s", 
            $result['global_letter_counter'], 
            $result['academic_period']
        );
        
        return $formatted;
        
    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("Error generando número de oficio: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtener el contador global actual (sin incrementar)
 * 
 * @return int
 */
public function getCurrentGlobalCounter() {
    $sql = "SELECT global_letter_counter FROM letter_templates WHERE id = 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? (int)$result['global_letter_counter'] : 0;
}
    
    /**
     * Actualizar plantilla (subir nuevo PDF)
     * 
     * @param string $template_type - Tipo de plantilla a actualizar
     * @param string $file_path - Nueva ruta del archivo
     * @param string $academic_period - Nuevo periodo (ej: 2025/2)
     * @param int $upis_user_id - ID del usuario UPIS que actualiza
     * @return bool
     */
    public function updateTemplate($template_type, $file_path, $academic_period, $upis_user_id) {
        $sql = "UPDATE letter_templates 
                SET template_file_path = :file_path,
                    academic_period = :period,
                    updated_by_user_id = :user_id,
                    updated_at = NOW()
                WHERE template_type = :type";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':file_path', $file_path);
        $stmt->bindParam(':period', $academic_period);
        $stmt->bindParam(':user_id', $upis_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':type', $template_type);
        
        return $stmt->execute();
    }
    
    /**
 * Reiniciar CONTADOR GLOBAL (inicio de año escolar)
 * 
 * @return bool
 */
public function resetAllCounters() {
    $sql = "UPDATE letter_templates 
            SET global_letter_counter = 0 
            WHERE id = 1";
    
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute();
}
    
    /**
     * Reiniciar TODOS los contadores (inicio de año escolar)
     * 
     * @return bool
     */
    public function resetAllCounters() {
        $sql = "UPDATE letter_templates 
                SET current_letter_number = 0";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute();
    }
    
    /**
     * Actualizar periodo académico de todas las plantillas
     * 
     * @param string $new_period - Nuevo periodo (ej: 2026/1)
     * @param int $upis_user_id - ID del usuario UPIS
     * @return bool
     */
    public function updateAcademicPeriodForAll($new_period, $upis_user_id) {
        // Extraer año del periodo (ej: "2026/1" -> "2026")
        $year = substr($new_period, 0, 4);
        
        $sql = "UPDATE letter_templates 
                SET academic_period = :period,
                    academic_year = :year,
                    updated_by_user_id = :user_id,
                    updated_at = NOW()
                WHERE is_active = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':period', $new_period);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':user_id', $upis_user_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Verificar si existe una plantilla
     * 
     * @param string $template_type
     * @return bool
     */
    public function templateExists($template_type) {
        $sql = "SELECT COUNT(*) as total FROM letter_templates 
                WHERE template_type = :type";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':type', $template_type);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
    
    /**
     * Obtener estadísticas de uso de plantillas
     * (Cuántas cartas se han generado de cada tipo)
     * 
     * @return array
     */
    public function getTemplateUsageStats() {
        $sql = "SELECT 
                    lt.template_type,
                    lt.template_name,
                    lt.current_letter_number as total_generated,
                    lt.academic_period
                FROM letter_templates lt
                WHERE lt.is_active = 1
                ORDER BY lt.current_letter_number DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Validar que el archivo de plantilla existe físicamente
     * 
     * @param string $template_type
     * @return bool
     */
    public function validateTemplateFileExists($template_type) {
        $template = $this->getTemplateByType($template_type);
        
        if (!$template) {
            return false;
        }
        
        $file_path = __DIR__ . '/../../' . $template['template_file_path'];
        return file_exists($file_path);
    }
    
    /**
     * Obtener historial de actualizaciones de una plantilla
     * (Requiere tabla audit_logs - futuro)
     * 
     * @param string $template_type
     * @return array
     */
    public function getTemplateHistory($template_type) {
        // Por ahora solo retorna la info actual
        // TODO: Implementar cuando se tenga tabla audit_logs
        return $this->getTemplateByType($template_type);
    }
}