<?php
/**
 * NotificationController
 * Maneja la creación, lectura y gestión de notificaciones
 */

require_once __DIR__ . '/../models/Notification.php';

class NotificationController {
    private $db;
    private $notification;

    public function __construct($db) {
        $this->db = $db;
        $this->notification = new Notification($db);
    }

    /**
     * Obtener notificaciones para el dropdown (últimas 5)
     */
    public function getNotificationsDropdown() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $notifications = $this->notification->getByUserId($userId, 5);
        $unreadCount = $this->notification->countUnread($userId);

        echo json_encode([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Obtener contador de no leídas (para el badge)
     */
    public function getUnreadCount() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            return;
        }

        $userId = $_SESSION['user_id'];
        $count = $this->notification->countUnread($userId);

        echo json_encode(['count' => $count]);
    }

    /**
     * Marcar como leída
     */
    public function markAsRead() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        $notificationId = $_POST['notification_id'] ?? null;
        $userId = $_SESSION['user_id'];

        if (!$notificationId) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            return;
        }

        $result = $this->notification->markAsRead($notificationId, $userId);

        echo json_encode(['success' => $result]);
    }

    /**
     * Marcar todas como leídas
     */
    public function markAllAsRead() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $result = $this->notification->markAllAsRead($userId);

        echo json_encode(['success' => $result]);
    }

    /**
     * Ver todas las notificaciones (página completa)
     */
    /**
 * Ver todas las notificaciones (página completa)
 */
public function showAllNotifications() {
    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Verificar autenticación
    if (!isset($_SESSION['user_id'])) {
        header('Location: /SIEP/public/index.php?action=showLogin');
        exit;
    }

    $userId = $_SESSION['user_id'];
    $notifications = $this->notification->getAllByUserId($userId);

    // Cargar vista
    require_once __DIR__ . '/../Views/notifications/all_notifications.php';
}

    /**
     * Eliminar una notificación
     */
    public function deleteNotification() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        $notificationId = $_POST['notification_id'] ?? null;
        $userId = $_SESSION['user_id'];

        if (!$notificationId) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            return;
        }

        $result = $this->notification->delete($notificationId, $userId);

        echo json_encode(['success' => $result]);
    }

    // ============================================
    // MÉTODOS PARA CREAR NOTIFICACIONES
    // ============================================

    /**
     * Crear notificación genérica
     */
    public function createNotification($userId, $type, $title, $message, $relatedId = null, $relatedType = null, $link = null) {
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'link' => $link
        ];

        $notificationId = $this->notification->create($data);

        if ($notificationId) {
            // Enviar email
            $this->sendNotificationEmail($userId, $title);
            $this->notification->markEmailSent($notificationId);
        }

        return $notificationId;
    }

    // ============================================
    // TEMPLATES DE NOTIFICACIONES - ESTUDIANTES
    // ============================================

    public function notifyCartaAprobada($studentId, $letterId) {
        return $this->createNotification(
            $studentId,
            'carta_aprobada',
            'Carta de Presentación Aprobada',
            'Tu solicitud de carta de presentación ha sido aprobada por UPIS. En breve estará disponible para descargar.',
            $letterId,
            'presentation_letter',
            'index.php?action=studentDashboard'
        );
    }

    public function notifyCartaRechazada($studentId, $letterId, $motivo) {
        return $this->createNotification(
            $studentId,
            'carta_rechazada',
            'Carta de Presentación Rechazada',
            'Tu solicitud de carta de presentación ha sido rechazada. Motivo: ' . $motivo,
            $letterId,
            'presentation_letter',
            'index.php?action=studentDashboard'
        );
    }

    public function notifyCartaFirmadaDisponible($studentId, $letterId) {
        return $this->createNotification(
            $studentId,
            'carta_firmada_disponible',
            'Carta Firmada Disponible',
            'Tu carta de presentación firmada está lista para descargar.',
            $letterId,
            'presentation_letter',
            'index.php?action=studentDashboard'
        );
    }

    public function notifyAcreditacionAprobada($studentId, $accreditationId) {
        return $this->createNotification(
            $studentId,
            'acreditacion_aprobada',
            'Acreditación Aprobada',
            '¡Felicidades! Tu solicitud de acreditación ha sido aprobada por UPIS.',
            $accreditationId,
            'accreditation',
            'index.php?action=studentDashboard'
        );
    }

    public function notifyAcreditacionRechazada($studentId, $accreditationId, $motivo) {
        return $this->createNotification(
            $studentId,
            'acreditacion_rechazada',
            'Acreditación Rechazada',
            'Tu solicitud de acreditación ha sido rechazada. Motivo: ' . $motivo,
            $accreditationId,
            'accreditation',
            'index.php?action=studentDashboard'
        );
    }

    // ============================================
    // TEMPLATES DE NOTIFICACIONES - EMPRESAS
    // ============================================

    public function notifyEmpresaAprobada($companyUserId, $companyId) {
        return $this->createNotification(
            $companyUserId,
            'empresa_aprobada',
            'Registro Aprobado',
            '¡Bienvenido! Tu registro como empresa ha sido aprobado. Ya puedes publicar vacantes.',
            $companyId,
            'company',
            'index.php?action=companyDashboard'
        );
    }

    public function notifyEmpresaRechazada($companyUserId, $companyId, $motivo) {
        return $this->createNotification(
            $companyUserId,
            'empresa_rechazada',
            'Registro Rechazado',
            'Tu registro como empresa ha sido rechazado. Motivo: ' . $motivo,
            $companyId,
            'company',
            'index.php?action=login'
        );
    }

    public function notifyVacanteAprobada($companyUserId, $vacancyId, $tituloVacante) {
        return $this->createNotification(
            $companyUserId,
            'vacante_aprobada',
            'Vacante Aprobada',
            'Tu vacante "' . $tituloVacante . '" ha sido aprobada y ya está publicada.',
            $vacancyId,
            'vacancy',
            'index.php?action=companyDashboard'
        );
    }

    public function notifyVacanteRechazada($companyUserId, $vacancyId, $tituloVacante, $motivo) {
        return $this->createNotification(
            $companyUserId,
            'vacante_rechazada',
            'Vacante Rechazada',
            'Tu vacante "' . $tituloVacante . '" ha sido rechazada. Motivo: ' . $motivo,
            $vacancyId,
            'vacancy',
            'index.php?action=companyDashboard'
        );
    }

    public function notifyVacanteRemovida($companyUserId, $vacancyId, $tituloVacante, $motivo) {
        return $this->createNotification(
            $companyUserId,
            'vacante_removida',
            'Vacante Removida',
            'Tu vacante "' . $tituloVacante . '" ha sido removida por UPIS. Motivo: ' . $motivo,
            $vacancyId,
            'vacancy',
            'index.php?action=companyDashboard'
        );
    }

    // ============================================
    // ENVÍO DE EMAIL
    // ============================================

    private function sendNotificationEmail($userId, $notificationTitle) {
    // Obtener datos del usuario
    $stmt = $this->db->prepare("SELECT email, first_name, last_name_p FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        return false;
    }
    
    // ✅ USAR EL EmailService EXISTENTE
    try {
        require_once(__DIR__ . '/../Services/EmailService.php');
        $emailService = new EmailService();
        
        $nombre = $user['first_name'] . ' ' . $user['last_name_p'];
        $email = $user['email'];
        
        // Usar el método genérico que ya existe
        return $emailService->sendGenericNotification($email, $nombre, 'general');
        
    } catch (Exception $e) {
        error_log("❌ Error al enviar email de notificación: " . $e->getMessage());
        return false;
    }
}
}