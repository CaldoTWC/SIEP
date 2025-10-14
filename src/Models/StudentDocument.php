<?php
/**
 * Modelo de Documentos de Estudiante
 * 
 * Gestiona los documentos almacenados (cartas generadas, reportes, etc)
 * 
 * @package SIEP\Models
 * @version 2.0.0 - Migrado de MySQLi a PDO
 */

require_once(__DIR__ . '/../Config/Database.php');

class StudentDocument {
    
    private $conn;
    
    // Propiedades
    public $id;
    public $student_user_id;
    public $document_type; // presentation_letter, acceptance_letter, validation_letter
    public $file_path;
    public $original_filename;
    
    /**
     * Constructor
     */
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
 * Guardar un nuevo documento
 * 
 * @param int $student_user_id
 * @param string $document_type
 * @param string $file_path
 * @param string $original_filename
 * @return bool
 */
public function create($student_user_id, $document_type, $file_path, $original_filename) {
    $sql = "INSERT INTO student_documents 
            (student_user_id, document_type, file_path, original_filename) 
            VALUES 
            (:student_user_id, :document_type, :file_path, :original_filename)";
    
    $stmt = $this->conn->prepare($sql);
    
    $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
    $stmt->bindParam(':document_type', $document_type);
    $stmt->bindParam(':file_path', $file_path);
    $stmt->bindParam(':original_filename', $original_filename);
    
    return $stmt->execute();
}
    
    /**
     * Obtener todos los documentos de un estudiante
     * 
     * @param int $student_user_id
     * @return array
     */
    public function getByStudentId($student_user_id) {
        $sql = "SELECT * FROM student_documents 
                WHERE student_user_id = :student_user_id
                ORDER BY uploaded_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Obtener documentos por tipo
     * 
     * @param int $student_user_id
     * @param string $document_type
     * @return array
     */
    public function getByStudentAndType($student_user_id, $document_type) {
        $sql = "SELECT * FROM student_documents 
                WHERE student_user_id = :student_user_id
                  AND document_type = :document_type
                ORDER BY uploaded_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':document_type', $document_type);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Obtener documento por ID
     * 
     * @param int $document_id
     * @return array|false
     */
    public function findById($document_id) {
        $sql = "SELECT 
                    sd.*,
                    u.first_name, u.last_name_p, u.last_name_m,
                    sp.boleta, sp.career
                FROM student_documents sd
                JOIN users u ON sd.student_user_id = u.id
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE sd.id = :document_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
    
    /**
     * Verificar si existe un documento de cierto tipo para un estudiante
     * 
     * @param int $student_user_id
     * @param string $document_type
     * @return bool
     */
    public function exists($student_user_id, $document_type) {
        $sql = "SELECT COUNT(*) as count 
                FROM student_documents 
                WHERE student_user_id = :student_user_id
                  AND document_type = :document_type";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':document_type', $document_type);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Eliminar un documento
     * 
     * @param int $document_id
     * @return bool
     */
    public function delete($document_id) {
        // Primero obtener la ruta del archivo para eliminarlo del servidor
        $document = $this->findById($document_id);
        
        if ($document && file_exists($document['file_path'])) {
            unlink($document['file_path']);
        }
        
        // Luego eliminar de BD
        $sql = "DELETE FROM student_documents WHERE id = :document_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener el último documento de un tipo
     * 
     * @param int $student_user_id
     * @param string $document_type
     * @return array|false
     */
    public function getLatestByType($student_user_id, $document_type) {
        $sql = "SELECT * FROM student_documents 
                WHERE student_user_id = :student_user_id
                  AND document_type = :document_type
                ORDER BY uploaded_at DESC
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':student_user_id', $student_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':document_type', $document_type);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // ✅ CORRECTO: PDO
    }
}