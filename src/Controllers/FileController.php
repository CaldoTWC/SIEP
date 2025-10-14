<?php
/**
 * Controlador de Archivos
 * 
 * Gestiona la descarga segura de archivos
 * 
 * @package SIEP\Controllers
 * @version 1.0.0
 */

require_once(__DIR__ . '/../Models/StudentDocument.php');
require_once(__DIR__ . '/../Lib/Session.php');
require_once(__DIR__ . '/../Lib/FileHelper.php');

class FileController {
    
    private $session;
    
    public function __construct() {
        $this->session = new Session();
    }
    
    /**
     * Descargar un documento de estudiante
     * 
     * URL: /SIEP/public/index.php?action=downloadDocument&id=123
     */
    public function downloadDocument() {
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            die('No autorizado. Debe iniciar sesión.');
        }
        
        $document_id = $_GET['id'] ?? null;
        
        if (!$document_id) {
            http_response_code(400);
            die('ID de documento no proporcionado.');
        }
        
        // Obtener información del documento
        $docModel = new StudentDocument();
        $document = $docModel->findById((int)$document_id);
        
        if (!$document) {
            http_response_code(404);
            die('Documento no encontrado.');
        }
        
        // Verificar permisos
        $can_access = FileHelper::userCanAccessFile(
            $_SESSION['user_id'],
            $_SESSION['user_role'],
            $document['file_path'],
            $document['student_user_id']
        );
        
        if (!$can_access) {
            http_response_code(403);
            die('No tiene permisos para acceder a este archivo.');
        }
        
        // Obtener ruta absoluta del archivo
        $file_path = FileHelper::getAbsolutePath($document['file_path']);
        
        if (!file_exists($file_path)) {
            http_response_code(404);
            die('El archivo no existe en el servidor.');
        }
        
        // Servir el archivo
        $this->serveFile($file_path, $document['original_filename']);
    }
    
    /**
     * Servir archivo al navegador
     * 
     * @param string $file_path Ruta absoluta del archivo
     * @param string $filename Nombre para mostrar al usuario
     */
    private function serveFile($file_path, $filename) {
        // Limpiar buffer de salida
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Obtener tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        
        // Headers para descarga
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        
        // Enviar archivo
        readfile($file_path);
        exit;
    }
    
    /**
     * Ver documento en el navegador (sin descargar)
     * 
     * URL: /SIEP/public/index.php?action=viewDocument&id=123
     */
    public function viewDocument() {
        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            die('No autorizado. Debe iniciar sesión.');
        }
        
        $document_id = $_GET['id'] ?? null;
        
        if (!$document_id) {
            http_response_code(400);
            die('ID de documento no proporcionado.');
        }
        
        // Obtener información del documento
        $docModel = new StudentDocument();
        $document = $docModel->findById((int)$document_id);
        
        if (!$document) {
            http_response_code(404);
            die('Documento no encontrado.');
        }
        
        // Verificar permisos
        $can_access = FileHelper::userCanAccessFile(
            $_SESSION['user_id'],
            $_SESSION['user_role'],
            $document['file_path'],
            $document['student_user_id']
        );
        
        if (!$can_access) {
            http_response_code(403);
            die('No tiene permisos para acceder a este archivo.');
        }
        
        // Obtener ruta absoluta
        $file_path = FileHelper::getAbsolutePath($document['file_path']);
        
        if (!file_exists($file_path)) {
            http_response_code(404);
            die('El archivo no existe en el servidor.');
        }
        
        // Servir para visualización en navegador
        $this->displayFile($file_path);
    }
    
    /**
     * Mostrar archivo en navegador (inline)
     * 
     * @param string $file_path
     */
    private function displayFile($file_path) {
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: inline');
        header('Content-Length: ' . filesize($file_path));
        
        readfile($file_path);
        exit;
    }
}