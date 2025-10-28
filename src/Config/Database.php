<?php
/**
 * Clase Singleton para conexión a base de datos
 * 
 * @package SIEP\Config
 * @version 2.2.0 - Con configuración de zona horaria
 */

// Cargar variables de entorno
require_once __DIR__ . '/env.php';
load_dotenv(__DIR__ . '/../../.env');

class Database {
    
    private static $instance = null;
    private $conn;
    
    // Configuración de conexión desde variables de entorno
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;
    
    /**
     * Constructor privado (patrón Singleton)
     */
    private function __construct() {
        // Leer configuración desde variables de entorno
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'siep';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
        $this->charset = getenv('DB_CHARSET') ?: 'utf8mb4';
        
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            // ✅ CONFIGURAR ZONA HORARIA DE MYSQL A MÉXICO (UTC-6)
            $this->conn->exec("SET time_zone = '-06:00'");
            
        } catch(PDOException $e) {
            // Versión de producción (segura)
            error_log("Error de conexión: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }
    
    /**
     * Obtiene la instancia única de Database
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    /**
     * Obtiene la conexión PDO
     * 
     * @return PDO
     */
    public function getConnection() {  // ✅ DEBE SER PUBLIC
        return $this->conn;
    }
    
    /**
     * Prevenir clonación
     */
    private function __clone() {}
    
    /**
     * Prevenir unserialize
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton");
    }
}