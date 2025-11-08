<?php
/**
 * Modelo de Usuario
 * 
 * Gestiona todas las operaciones relacionadas con usuarios:
 * - Registro de estudiantes y empresas
 * - Autenticación (login)
 * - Aprobación/rechazo de empresas
 * - Consultas de perfiles
 * 
 * @package SIEP\Models
 * @version 3.0.0 - Agregados métodos createUser, emailExists y createCompanyProfile
 * @date 2025-10-29
 */

require_once(__DIR__ . '/../Config/Database.php');

class User {
    
    private $conn;
    
    // Propiedades del usuario
    public $id;
    public $email;
    public $password;
    public $first_name;
    public $last_name_p;
    public $last_name_m;
    public $phone_number;
    public $role;  // student, company, upis, admin
    public $status; // active, inactive, pending
    
    // Propiedades específicas de estudiante
    public $boleta;
    public $career;
    
    // Propiedades específicas de empresa
    public $company_name;
    public $commercial_name;
    public $rfc;
    public $company_description;
    public $business_area;
    public $company_type;
    public $website;
    public $tax_id_url;
    public $employee_count;
    public $student_programs;
    public $contact_person_position;
    
    /**
     * Constructor - Inicializa conexión a BD
     */
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    // ========================================================================
    // MÉTODOS DE AUTENTICACIÓN
    // ========================================================================
    
    /**
     * Autentica un usuario en el sistema
     * 
     * CAMBIO PRINCIPAL: Ahora obtiene 'role' directamente de la columna users.role
     * en lugar de hacer JOIN con user_roles y roles
     * 
     * @return array|false Datos del usuario si es exitoso, false si falla
     */
    public function login() {
        // SQL actualizado: Ya no necesita JOIN con roles/user_roles
        $sql = "SELECT 
                    u.id, 
                    u.email, 
                    u.password, 
                    u.first_name, 
                    u.last_name_p, 
                    u.last_name_m,
                    u.role AS role_name,  -- ⬅️ CAMBIO: Directo de users.role
                    u.status
                FROM users u
                WHERE u.email = :email";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar contraseña
            if (password_verify($this->password, $user['password'])) {
                
                // Actualizar último login
                $this->updateLastLogin($user['id']);
                
                // Retornar datos del usuario (incluye role_name para Session.php)
                return $user;
            }
        }
        
        return false;
    }
    
    /**
     * Actualiza la fecha del último login
     * 
     * @param int $user_id ID del usuario
     */
    private function updateLastLogin($user_id) {
        $sql = "UPDATE users SET last_login_at = NOW() WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    // ========================================================================
    // MÉTODOS DE REGISTRO
    // ========================================================================
    
    /**
     * Registra un nuevo estudiante en el sistema
     * 
     * CAMBIO: Ahora incluye el campo 'role' = 'student'
     * Los estudiantes se crean con status 'active' (no requieren aprobación)
     * 
     * @return bool True si el registro fue exitoso
     */
    public function createStudent() {
        $this->conn->beginTransaction();
        
        try {
            // 1. Insertar usuario con role 'student' y status 'active'
            $sql_user = "INSERT INTO users 
                        (email, password, first_name, last_name_p, last_name_m, 
                         phone_number, role, status) 
                        VALUES 
                        (:email, :password, :first_name, :last_name_p, :last_name_m, 
                         :phone_number, 'student', 'active')";
            
            $stmt_user = $this->conn->prepare($sql_user);
            
            // Hash de la contraseña
            $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
            
            $stmt_user->bindParam(':email', $this->email);
            $stmt_user->bindParam(':password', $hashed_password);
            $stmt_user->bindParam(':first_name', $this->first_name);
            $stmt_user->bindParam(':last_name_p', $this->last_name_p);
            $stmt_user->bindParam(':last_name_m', $this->last_name_m);
            $stmt_user->bindParam(':phone_number', $this->phone_number);
            
            if (!$stmt_user->execute()) {
                throw new Exception("Error al crear usuario");
            }
            
            $user_id = $this->conn->lastInsertId();
            
            // 2. Insertar perfil de estudiante
            $sql_profile = "INSERT INTO student_profiles 
                           (user_id, boleta, career) 
                           VALUES 
                           (:user_id, :boleta, :career)";
            
            $stmt_profile = $this->conn->prepare($sql_profile);
            $stmt_profile->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_profile->bindParam(':boleta', $this->boleta);
            $stmt_profile->bindParam(':career', $this->career);
            
            if (!$stmt_profile->execute()) {
                throw new Exception("Error al crear perfil de estudiante");
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error en createStudent(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Registra una nueva empresa en el sistema
     * 
     * CAMBIO: Ahora incluye el campo 'role' = 'company'
     * Las empresas se crean con status 'pending' (requieren aprobación UPIS)
     * 
     * @return bool True si el registro fue exitoso
     */
    public function createCompany() {
        $this->conn->beginTransaction();
        
        try {
            // 1. Insertar usuario con role 'company' y status 'pending'
            $sql_user = "INSERT INTO users 
                        (email, password, first_name, last_name_p, last_name_m, 
                         phone_number, role, status) 
                        VALUES 
                        (:email, :password, :first_name, :last_name_p, :last_name_m, 
                         :phone_number, 'company', 'pending')";
            
            $stmt_user = $this->conn->prepare($sql_user);
            
            // Hash de la contraseña
            $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
            
            $stmt_user->bindParam(':email', $this->email);
            $stmt_user->bindParam(':password', $hashed_password);
            $stmt_user->bindParam(':first_name', $this->first_name);
            $stmt_user->bindParam(':last_name_p', $this->last_name_p);
            $stmt_user->bindParam(':last_name_m', $this->last_name_m);
            $stmt_user->bindParam(':phone_number', $this->phone_number);
            
            if (!$stmt_user->execute()) {
                throw new Exception("Error al crear usuario empresa");
            }
            
            $user_id = $this->conn->lastInsertId();
            
            // 2. Insertar perfil de empresa
            $sql_profile = "INSERT INTO company_profiles 
                           (company_name, commercial_name, rfc, company_description,
                            business_area, company_type, website, tax_id_url, 
                            employee_count, student_programs, contact_person_user_id, 
                            contact_person_position) 
                           VALUES 
                           (:company_name, :commercial_name, :rfc, :company_description,
                            :business_area, :company_type, :website, :tax_id_url, 
                            :employee_count, :student_programs, :contact_person_user_id, 
                            :contact_person_position)";
            
            $stmt_profile = $this->conn->prepare($sql_profile);
            
            $stmt_profile->bindParam(':company_name', $this->company_name);
            $stmt_profile->bindParam(':commercial_name', $this->commercial_name);
            $stmt_profile->bindParam(':rfc', $this->rfc);
            $stmt_profile->bindParam(':company_description', $this->company_description);
            $stmt_profile->bindParam(':business_area', $this->business_area);
            $stmt_profile->bindParam(':company_type', $this->company_type);
            $stmt_profile->bindParam(':website', $this->website);
            $stmt_profile->bindParam(':tax_id_url', $this->tax_id_url);
            $stmt_profile->bindParam(':employee_count', $this->employee_count);
            $stmt_profile->bindParam(':student_programs', $this->student_programs);
            $stmt_profile->bindParam(':contact_person_user_id', $user_id, PDO::PARAM_INT);
            $stmt_profile->bindParam(':contact_person_position', $this->contact_person_position);
            
            if (!$stmt_profile->execute()) {
                throw new Exception("Error al crear perfil de empresa");
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error en createCompany(): " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================================================
    // NUEVOS MÉTODOS AGREGADOS (2025-10-29)
    // ========================================================================
    
    /**
     * Crear un nuevo usuario (genérico)
     * Utilizado por AuthController para registros más flexibles
     * 
     * @param array $data Datos del usuario
     * @return int|false ID del usuario creado o false en caso de error
     */
    public function createUser($data) {
        $sql = "INSERT INTO users 
                (email, password, first_name, last_name_p, last_name_m, 
                 phone_number, role, status) 
                VALUES 
                (:email, :password, :first_name, :last_name_p, :last_name_m, 
                 :phone_number, :role, :status)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name_p', $data['last_name_p']);
        $stmt->bindParam(':last_name_m', $data['last_name_m']);
        $stmt->bindParam(':phone_number', $data['phone_number']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':status', $data['status']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Verificar si un email ya existe en el sistema
     * 
     * @param string $email Email a verificar
     * @return bool True si existe, false si no
     */
    public function emailExists($email) {
        $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
    
    /**
     * Crear perfil de empresa (método público separado)
     * Utilizado por AuthController para separar la creación de usuario y perfil
     * 
     * @param array $data Datos del perfil de empresa
     * @return bool True si fue exitoso, false si falla
     */
    public function createCompanyProfile($data) {
        $sql = "INSERT INTO company_profiles 
                (company_name, commercial_name, business_area, company_type, 
                 rfc, company_description, website, tax_id_url, employee_count, 
                 student_programs, contact_person_user_id, contact_person_position) 
                VALUES 
                (:company_name, :commercial_name, :business_area, :company_type, 
                 :rfc, :company_description, :website, :tax_id_url, :employee_count, 
                 :student_programs, :contact_person_user_id, :contact_person_position)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':company_name', $data['company_name']);
        $stmt->bindParam(':commercial_name', $data['commercial_name']);
        $stmt->bindParam(':business_area', $data['business_area']);
        $stmt->bindParam(':company_type', $data['company_type']);
        $stmt->bindParam(':rfc', $data['rfc']);
        $stmt->bindParam(':company_description', $data['company_description']);
        $stmt->bindParam(':website', $data['website']);
        $stmt->bindParam(':tax_id_url', $data['tax_id_url']);
        $stmt->bindParam(':employee_count', $data['employee_count']);
        $stmt->bindParam(':student_programs', $data['student_programs']);
        $stmt->bindParam(':contact_person_user_id', $data['contact_person_user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':contact_person_position', $data['contact_person_position']);
        
        return $stmt->execute();
    }
    
    /**
 * Obtiene todas las empresas con status 'pending'
 * 
 * @return array Lista de empresas pendientes de aprobación con toda la información
 */
public function getPendingCompanies() {
    $sql = "SELECT 
                u.id, 
                u.id as user_id,
                u.email, 
                u.first_name, 
                u.last_name_p, 
                u.last_name_m,
                u.phone_number, 
                u.status, 
                u.created_at,
                cp.id as company_profile_id,
                cp.company_name, 
                cp.commercial_name, 
                cp.rfc, 
                cp.company_description,
                cp.business_area, 
                cp.company_type,
                cp.website,
                cp.tax_id_url,
                cp.employee_count,
                cp.student_programs,
                cp.contact_person_position
            FROM users u
            JOIN company_profiles cp ON u.id = cp.contact_person_user_id
            WHERE u.role = 'company' 
              AND u.status = 'pending'
            ORDER BY u.created_at ASC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /**
     * Obtiene todas las empresas con status 'active'
     * 
     * @return array Lista de empresas activas con información básica
     */
    public function getActiveCompanies() {
        $sql = "SELECT 
                    u.id, 
                    u.id as user_id,
                    u.email, 
                    u.first_name, 
                    u.last_name_p, 
                    u.last_name_m,
                    u.phone_number, 
                    u.created_at,
                    cp.company_name, 
                    cp.commercial_name, 
                    cp.rfc,
                    cp.contact_person_position
                FROM users u
                JOIN company_profiles cp ON u.id = cp.contact_person_user_id
                WHERE u.role = 'company' 
                  AND u.status = 'active'
                ORDER BY u.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Aprueba una empresa (cambia status a 'active')
     * 
     * @param int $user_id ID del usuario empresa
     * @return bool True si fue exitoso
     */
    public function approveUser($user_id) {
        $sql = "UPDATE users 
                SET status = 'active' 
                WHERE id = :user_id 
                  AND role = 'company'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Rechaza una empresa (cambia status a 'inactive')
     * 
     * Nota: Si implementas el sistema de eliminación + blacklist,
     * este método cambiaría para eliminar en lugar de marcar inactive
     * 
     * @param int $user_id ID del usuario empresa
     * @return bool True si fue exitoso
     */
    public function rejectUser($user_id) {
        $sql = "UPDATE users 
                SET status = 'inactive' 
                WHERE id = :user_id 
                  AND role = 'company'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // ========================================================================
    // MÉTODOS AUXILIARES
    // ========================================================================
    
    /**
     * Obtiene el perfil completo de un estudiante para formularios
     * 
     * @param int $user_id ID del usuario estudiante
     * @return array|false Datos del perfil o false
     */
    public function getStudentProfileForForm($user_id) {
        $sql = "SELECT 
                    u.id, u.email, u.first_name, u.last_name_p, u.last_name_m,
                    u.phone_number,
                    CONCAT(u.first_name, ' ', u.last_name_p, ' ', u.last_name_m) as full_name,
                    sp.boleta, sp.career
                FROM users u
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE u.id = :user_id 
                  AND u.role = 'student'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el perfil completo de una empresa por ID de usuario
     * 
     * @param int $user_id ID del usuario empresa
     * @return array|false Datos del perfil de empresa o false
     */
    public function getCompanyProfileByUserId($user_id) {
        $sql = "SELECT 
                    u.id, 
                    u.email, 
                    u.first_name, 
                    u.last_name_p, 
                    u.last_name_m,
                    u.phone_number,
                    cp.id as company_profile_id,
                    cp.company_name, 
                    cp.commercial_name, 
                    cp.rfc,
                    cp.company_description,
                    cp.business_area, 
                    cp.company_type, 
                    cp.website,
                    cp.tax_id_url,
                    cp.employee_count,
                    cp.student_programs,
                    cp.contact_person_position
                FROM users u
                JOIN company_profiles cp ON u.id = cp.contact_person_user_id
                WHERE u.id = :user_id 
                  AND u.role = 'company'
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener datos completos de un estudiante por boleta
     * 
     * @param string $boleta
     * @return array|false
     */
    public function getStudentDataByBoleta($boleta) {
        $sql = "SELECT 
                    u.id,
                    u.first_name,
                    u.last_name_p,
                    u.last_name_m,
                    sp.boleta
                FROM users u
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE sp.boleta = :boleta
                  AND u.role = 'student'
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':boleta', $boleta);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene datos de empresa para proceso de rechazo
     * (Usado cuando se implementa eliminación + email)
     * 
     * @param int $company_user_id ID del usuario empresa
     * @return array|false Datos de la empresa
     */
    public function getCompanyDataForRejection($company_user_id) {
        $sql = "SELECT 
                    u.id, u.email, u.first_name, u.last_name_p,
                    cp.company_name, cp.commercial_name
                FROM users u
                JOIN company_profiles cp ON u.id = cp.contact_person_user_id
                WHERE u.id = :user_id 
                  AND u.role = 'company'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $company_user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Elimina una empresa y su perfil completamente
     * (Para implementación futura con blacklist)
     * 
     * @param int $company_user_id ID del usuario empresa
     * @return bool True si fue exitoso
     */
    public function deleteCompany($company_user_id) {
        // El CASCADE en FK se encarga de eliminar company_profile automáticamente
        $sql = "DELETE FROM users 
                WHERE id = :user_id 
                  AND role = 'company'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $company_user_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // ========================================================================
    // MÉTODOS DE GESTIÓN DE CONTRASEÑA
    // ========================================================================
    
    /**
     * Cambia la contraseña de un usuario
     * 
     * @param int $user_id ID del usuario
     * @param string $current_password Contraseña actual (para validar)
     * @param string $new_password Nueva contraseña
     * @return array ['success' => bool, 'message' => string]
     */
    public function changePassword($user_id, $current_password, $new_password) {
        // 1. Obtener contraseña actual de la BD
        $sql = "SELECT password FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ];
        }
        
        // 2. Verificar contraseña actual
        if (!password_verify($current_password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'La contraseña actual es incorrecta.'
            ];
        }
        
        // 3. Validar nueva contraseña
        if (strlen($new_password) < 8) {
            return [
                'success' => false,
                'message' => 'La nueva contraseña debe tener al menos 8 caracteres.'
            ];
        }
        
        // 4. Actualizar contraseña
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql_update = "UPDATE users SET password = :password WHERE id = :user_id";
        $stmt_update = $this->conn->prepare($sql_update);
        $stmt_update->bindParam(':password', $hashed_password);
        $stmt_update->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        if ($stmt_update->execute()) {
            return [
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente.'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Error al actualizar la contraseña.'
        ];
    }
    
    /**
     * Obtiene datos básicos del usuario para el perfil
     * 
     * @param int $user_id
     * @return array|false
     */
    public function getUserProfile($user_id) {
        $sql = "SELECT 
                    id, email, first_name, last_name_p, last_name_m,
                    phone_number, role, status, created_at
                FROM users 
                WHERE id = :user_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar el ID de un estudiante por su número de boleta
     * 
     * @param string $boleta Número de boleta (10 dígitos)
     * @return int|false ID del usuario o false si no se encuentra
     */
    public function findStudentIdByBoleta($boleta) {
        $sql = "SELECT u.id 
                FROM users u
                JOIN student_profiles sp ON u.id = sp.user_id
                WHERE sp.boleta = :boleta
                  AND u.role = 'student'
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':boleta', $boleta);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['id'] : false;
    }

    /**
     * Buscar un usuario por su ID
     * 
     * @param int $user_id ID del usuario
     * @return array|false Datos del usuario o false si no se encuentra
     */
    public function findById($user_id) {
        $sql = "SELECT 
                    id, email, first_name, last_name_p, last_name_m,
                    phone_number, role, status, created_at
                FROM users 
                WHERE id = :user_id
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // ========================================================================
    // MÉTODOS PARA BLACKLIST (Implementación futura - PINEADOS)
    // ========================================================================
    
    /**
     * Verifica si un email está en blacklist
     * (Implementar cuando actives el sistema de blacklist)
     * 
     * @param string $email Email a verificar
     * @return bool True si está bloqueado
     */
    public function isBlacklisted($email) {
        // TODO: Implementar cuando se active blacklist
        return false;
    }
    
    /**
     * Agrega o actualiza blacklist
     * (Implementar cuando actives el sistema de blacklist)
     */
    public function addOrUpdateBlacklist($email, $company_name, $reason, $rejected_by) {
        // TODO: Implementar cuando se active blacklist
        return ['rejection_count' => 1, 'is_banned' => false, 'status' => 'warning'];
    }
}