<?php
/**
 * Notification Model
 * Maneja todas las operaciones CRUD de notificaciones
 */

class Notification {
    private $conn;
    private $table = 'notifications';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Crear una nueva notificación
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, type, title, message, related_id, related_type, link) 
                  VALUES 
                  (:user_id, :type, :title, :message, :related_id, :related_type, :link)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':message', $data['message']);
        $stmt->bindParam(':related_id', $data['related_id']);
        $stmt->bindParam(':related_type', $data['related_type']);
        $stmt->bindParam(':link', $data['link']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Obtener notificaciones de un usuario
     */
    public function getByUserId($userId, $limit = 10, $onlyUnread = false) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_id = :user_id";
        
        if ($onlyUnread) {
            $query .= " AND is_read = 0";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todas las notificaciones de un usuario (para página completa)
     */
    public function getAllByUserId($userId) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar notificaciones no leídas
     */
    public function countUnread($userId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    /**
     * Marcar una notificación como leída
     */
    public function markAsRead($notificationId, $userId) {
        $query = "UPDATE " . $this->table . " 
                  SET is_read = 1 
                  WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllAsRead($userId) {
        $query = "UPDATE " . $this->table . " 
                  SET is_read = 1 
                  WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Marcar que el email fue enviado
     */
    public function markEmailSent($notificationId) {
        $query = "UPDATE " . $this->table . " 
                  SET email_sent = 1 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Eliminar una notificación
     */
    public function delete($notificationId, $userId) {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Eliminar todas las notificaciones leídas de un usuario
     */
    public function deleteAllRead($userId) {
        $query = "DELETE FROM " . $this->table . " 
                  WHERE user_id = :user_id AND is_read = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Obtener una notificación por ID
     */
    public function getById($notificationId) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}