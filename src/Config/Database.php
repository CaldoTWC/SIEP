<?php
/**
 * Clase Singleton para conexión a base de datos
 * 
 * @package SIEP\Config
 * @version 2.2.1 - Corrección de zona horaria con fallback
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
            
            // ✅ PASO 1: Crear la conexión
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            // ✅ PASO 2: Intentar configurar zona horaria (si falla, continuar)
            try {
                $this->conn->exec("SET time_zone = '-06:00'");
            } catch (PDOException $tz_error) {
                // Si falla, solo registrar en log pero NO romper la conexión
                error_log("Advertencia: No se pudo configurar zona horaria MySQL: " . $tz_error->getMessage());
            }
            
        } catch(PDOException $e) {
            // Registrar error completo en el log
            error_log("Error de conexión DB: " . $e->getMessage());
            
            // ✅ En desarrollo mostrar error, en producción mensaje genérico
            if (getenv('APP_ENV') === 'production') {
                die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
            } else {
                // ✅ MODO DEBUG: Mostrar error completo
                die("<div style='background:#f8d7da; padding:20px; border:2px solid #dc3545; border-radius:5px; font-family:monospace;'>" .
                    "<h3 style='color:#721c24;'>❌ Error de Conexión a la Base de Datos</h3>" .
                    "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>" .
                    "<p><strong>Host:</strong> {$this->host}</p>" .
                    "<p><strong>Base de datos:</strong> {$this->db_name}</p>" .
                    "<p><strong>Usuario:</strong> {$this->username}</p>" .
                    "<hr><p><small>Verifica que XAMPP esté corriendo y que la base de datos 'siep' exista.</small></p>" .
                    "</div>");
            }
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
    public function getConnection() {
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