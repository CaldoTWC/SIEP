<?php
/**
 * Modelo de Historial de Rechazos de Empresas
 * 
 * Gestiona el historial de rechazos de empresas sin relación con la tabla users,
 * permitiendo que las empresas rechazadas puedan volver a registrarse.
 * 
 * @package SIEP\Models
 * @version 1.0.0
 * @date 2025-11-08
 */

require_once(__DIR__ . '/../Config/Database.php');

class CompanyRejection {
    
    private $conn;
    
    /**
     * Constructor - Inicializa conexión a BD
     */
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Crear un nuevo registro de rechazo (Método simplificado con parámetros directos)
     * 
     * @param string $company_name Nombre de la empresa
     * @param string $contact_email Email de contacto
     * @param string $contact_name Nombre del contacto
     * @param string $rejection_reason Razón del rechazo
     * @param string $rfc RFC de la empresa
     * @param string $commercial_name Nombre comercial
     * @return bool True si fue exitoso
     */
    public function createRejection($company_name, $contact_email, $contact_name, $rejection_reason, $rfc = null, $commercial_name = null) {
        $sql = "INSERT INTO company_rejections_history 
                (company_name, contact_email, contact_name, rejection_reason, rfc, commercial_name) 
                VALUES 
                (:company_name, :contact_email, :contact_name, :rejection_reason, :rfc, :commercial_name)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':company_name', $company_name);
        $stmt->bindParam(':contact_email', $contact_email);
        $stmt->bindParam(':contact_name', $contact_name);
        $stmt->bindParam(':rejection_reason', $rejection_reason);
        $stmt->bindParam(':rfc', $rfc);
        $stmt->bindParam(':commercial_name', $commercial_name);
        
        return $stmt->execute();
    }
    
    /**
     * Crear un nuevo registro de rechazo en el historial (Método con array)
     * 
     * @param array $data Datos del rechazo [company_name, contact_email, contact_name, rejection_reason, rfc, commercial_name]
     * @return bool True si fue exitoso
     */
    public function create($data) {
        $sql = "INSERT INTO company_rejections_history 
                (company_name, contact_email, contact_name, rejection_reason, rfc, commercial_name) 
                VALUES 
                (:company_name, :contact_email, :contact_name, :rejection_reason, :rfc, :commercial_name)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':company_name', $data['company_name']);
        $stmt->bindParam(':contact_email', $data['contact_email']);
        $stmt->bindParam(':contact_name', $data['contact_name']);
        $stmt->bindParam(':rejection_reason', $data['rejection_reason']);
        $stmt->bindParam(':rfc', $data['rfc']);
        $stmt->bindParam(':commercial_name', $data['commercial_name']);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener todos los rechazos ordenados por fecha descendente
     * 
     * @return array Lista de rechazos
     */
    public function getAll() {
        $sql = "SELECT 
                    id,
                    company_name,
                    contact_email,
                    contact_name,
                    rejection_date,
                    rejection_reason,
                    rfc,
                    commercial_name
                FROM company_rejections_history
                ORDER BY rejection_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener rechazos con paginación
     * 
     * @param int $limit Número de registros por página
     * @param int $offset Offset para la paginación
     * @return array Lista de rechazos
     */
    public function getPaginated($limit = 20, $offset = 0) {
        $sql = "SELECT 
                    id,
                    company_name,
                    contact_email,
                    contact_name,
                    rejection_date,
                    rejection_reason,
                    rfc,
                    commercial_name
                FROM company_rejections_history
                ORDER BY rejection_date DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener el número total de rechazos
     * 
     * @return int Total de rechazos
     */
    public function getTotal() {
        $sql = "SELECT COUNT(*) as total FROM company_rejections_history";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }
    
    /**
     * Obtener rechazos por email de contacto
     * 
     * @param string $email Email a buscar
     * @return array Lista de rechazos para ese email
     */
    public function getByEmail($email) {
        $sql = "SELECT 
                    id,
                    company_name,
                    contact_email,
                    contact_name,
                    rejection_date,
                    rejection_reason,
                    rfc,
                    commercial_name
                FROM company_rejections_history
                WHERE contact_email = :email
                ORDER BY rejection_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener un rechazo por ID
     * 
     * @param int $id ID del rechazo
     * @return array|false Datos del rechazo o false
     */
    public function getById($id) {
        $sql = "SELECT 
                    id,
                    company_name,
                    contact_email,
                    contact_name,
                    rejection_date,
                    rejection_reason,
                    rfc,
                    commercial_name
                FROM company_rejections_history
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}