<?php
/**
 * Helper para manejo seguro de archivos de estudiantes
 * 
 * @package SIEP\Lib
 * @version 1.0.0
 */

class FileHelper {
    
    /**
     * Ruta base de almacenamiento (fuera de public)
     */
    private static $base_storage_path = __DIR__ . '/../../storage/students/';
    
    /**
     * Obtener o crear la carpeta de un estudiante
     * 
     * @param string $boleta Número de boleta
     * @param string $first_name Primer nombre
     * @param string $last_name_p Apellido paterno
     * @return string Ruta absoluta de la carpeta del estudiante
     */
    public static function getStudentFolder($boleta, $first_name, $last_name_p) {
        // Limpiar nombres (sin espacios, sin acentos, sin caracteres especiales)
        $clean_name = self::cleanString($first_name);
        $clean_lastname = self::cleanString($last_name_p);
        
        // Formato: BOLETA_PrimerNombreApellido
        $folder_name = $boleta . '_' . $clean_name . $clean_lastname;
        
        // Ruta completa
        $full_path = self::$base_storage_path . $folder_name;
        
        // Crear carpeta si no existe
        if (!is_dir($full_path)) {
            mkdir($full_path, 0755, true);
        }
        
        return $full_path;
    }
    
    /**
     * Obtener subcarpeta específica del estudiante
     * 
     * @param string $boleta
     * @param string $first_name
     * @param string $last_name_p
     * @param string $subfolder 'transcripts', 'signed_documents', 'accreditation'
     * @return string Ruta absoluta de la subcarpeta
     */
    public static function getStudentSubfolder($boleta, $first_name, $last_name_p, $subfolder) {
        $student_folder = self::getStudentFolder($boleta, $first_name, $last_name_p);
        $full_path = $student_folder . '/' . $subfolder;
        
        // Crear subcarpeta si no existe
        if (!is_dir($full_path)) {
            mkdir($full_path, 0755, true);
        }
        
        return $full_path;
    }
    
    /**
     * Obtener ruta relativa para guardar en BD
     * 
     * @param string $full_path Ruta absoluta del archivo
     * @return string Ruta relativa desde storage/
     */
    public static function getRelativePath($full_path) {
        $base = realpath(self::$base_storage_path);
        $file = realpath($full_path);
        
        if ($file && strpos($file, $base) === 0) {
            return 'storage/students' . substr($file, strlen($base));
        }
        
        return $full_path;
    }
    
    /**
     * Obtener ruta absoluta desde ruta relativa de BD
     * 
     * @param string $relative_path Ruta relativa (storage/students/...)
     * @return string Ruta absoluta
     */
    public static function getAbsolutePath($relative_path) {
        // Si ya es absoluta, retornarla
        if (file_exists($relative_path)) {
            return $relative_path;
        }
        
        // Convertir relativa a absoluta
        $absolute = __DIR__ . '/../../' . $relative_path;
        return realpath($absolute) ?: $absolute;
    }
    
    /**
     * Limpiar string para nombres de carpeta
     * 
     * @param string $str
     * @return string
     */
    private static function cleanString($str) {
        // Remover acentos y caracteres especiales
        $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        // Solo letras
        $str = preg_replace('/[^a-zA-Z]/', '', $str);
        return ucfirst(strtolower($str));
    }
    
    /**
     * Verificar si un usuario tiene permiso para acceder a un archivo
     * 
     * @param int $user_id ID del usuario que solicita
     * @param string $user_role Rol del usuario
     * @param string $file_path Ruta del archivo
     * @param int $file_owner_id ID del propietario del archivo
     * @return bool
     */
    public static function userCanAccessFile($user_id, $user_role, $file_path, $file_owner_id) {
        // UPIS y Admin pueden ver todo
        if (in_array($user_role, ['upis', 'admin'])) {
            return true;
        }
        
        // Estudiantes solo pueden ver sus propios archivos
        if ($user_role === 'student') {
            return $user_id === $file_owner_id;
        }
        
        // Empresas: implementar lógica según necesidad
        if ($user_role === 'company') {
            // Por ahora, no tienen acceso a archivos de estudiantes
            return false;
        }
        
        return false;
    }
}