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
 * Ver documento en el navegador
 * Soporta tanto ID de documento como ruta directa
 */
public function viewDocument() {
    // Verificar autenticación
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        die('No autorizado. Debe iniciar sesión.');
    }
    
    // OPCIÓN 1: Ver documento por ID (desde base de datos)
    if (isset($_GET['id'])) {
        $document_id = (int)$_GET['id'];
        
        $docModel = new StudentDocument();
        $document = $docModel->findById($document_id);
        
        if (!$document) {
            http_response_code(404);
            die('Documento no encontrado.');
        }
        
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
        
        $file_path = FileHelper::getAbsolutePath($document['file_path']);
        
        if (!file_exists($file_path)) {
            http_response_code(404);
            die('El archivo no existe en el servidor.');
        }
        
        $this->displayFile($file_path);
    }
    
    // OPCIÓN 2: Ver documento por ruta directa (para acreditaciones)
    elseif (isset($_GET['path'])) {
        
        // Solo UPIS/Admin pueden ver archivos por ruta directa
        if (!in_array($_SESSION['user_role'], ['upis', 'admin'])) {
            http_response_code(403);
            die('No tiene permisos para acceder a este archivo.');
        }
        
        $file_path = $_GET['path'];
        
        // ✅ NORMALIZAR BARRAS (Windows vs Linux)
        $file_path = str_replace('\\', '/', $file_path);
        
        // Construir ruta absoluta
        $base_path = __DIR__ . '/../../';
        
        // ✅ Intentar con realpath primero
        $absolute_path = realpath($base_path . $file_path);
        
        // ✅ Si realpath falla, construir manualmente
        if (!$absolute_path) {
            $absolute_path = $base_path . $file_path;
            $absolute_path = str_replace('\\', '/', $absolute_path);
        }
        
        // Validar que el archivo existe
        if (!file_exists($absolute_path)) {
            // DEBUG: Mostrar rutas para ver qué está pasando
            http_response_code(404);
            echo '<h3>Archivo no encontrado</h3>';
            echo '<p><strong>Ruta recibida:</strong> ' . htmlspecialchars($_GET['path']) . '</p>';
            echo '<p><strong>Ruta normalizada:</strong> ' . htmlspecialchars($file_path) . '</p>';
            echo '<p><strong>Ruta absoluta construida:</strong> ' . htmlspecialchars($absolute_path) . '</p>';
            echo '<p><strong>Base path:</strong> ' . htmlspecialchars($base_path) . '</p>';
            echo '<p><strong>¿Existe?:</strong> ' . (file_exists($absolute_path) ? 'SÍ' : 'NO') . '</p>';
            
            // Listar archivos en el directorio
            $dir = dirname($absolute_path);
            if (is_dir($dir)) {
                echo '<p><strong>Archivos en el directorio:</strong></p><ul>';
                foreach (scandir($dir) as $file) {
                    if ($file !== '.' && $file !== '..') {
                        echo '<li>' . htmlspecialchars($file) . '</li>';
                    }
                }
                echo '</ul>';
            }
            die();
        }
        
        // Servir archivo
        $this->displayFile($absolute_path);
    }
    
    else {
        http_response_code(400);
        die('Debe proporcionar un ID de documento o una ruta de archivo.');
    }
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