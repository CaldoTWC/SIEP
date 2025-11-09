<?php
/**
 * Servicio de Email Simplificado
 * 
 * Gestiona únicamente los envíos de correo esenciales del sistema:
 * 1. Rechazo de empresa (con motivo obligatorio)
 * 2. Notificación genérica (para avisar que hay algo nuevo en la plataforma)
 * 
 * @package SIEP\Services
 * @version 4.0.0 - Simplificado para Issue #4
 * @date 2025-11-08
 */

require_once(__DIR__ . '/../Config/email.php');
require_once(__DIR__ . '/EmailTemplates.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    
    private $templates;
    
    public function __construct() {
        $this->templates = new EmailTemplates();
    }
    
    /**
     * Obtener instancia configurada de PHPMailer
     * 
     * @return PHPMailer
     */
    private function getMailer() {
        return getMailer(); // Función definida en Config/email.php
    }
    
    // ========================================
    // NOTIFICACIÓN DE RECHAZO DE EMPRESA (FORZOSO)
    // ========================================
    
    /**
     * Notificar a la empresa que su registro fue rechazado
     * 
     * Este es el ÚNICO correo detallado que se envía.
     * Motivo: Issue #4 - La empresa necesita saber por qué fue rechazada
     * y debe poder re-registrarse.
     * 
     * @param array $company_data Datos de la empresa rechazada
     * @param string $rejection_reason Motivo del rechazo (OBLIGATORIO)
     * @return bool
     */
    public function notifyCompanyRejection($company_data, $rejection_reason) {
        try {
            $mail = $this->getMailer();
            
            $mail->addAddress($company_data['email'], $company_data['contact_name']);
            
            $mail->Subject = "❌ Registro de Empresa Rechazado - SIEP UPIICSA";
            
            $mail->Body = $this->templates->companyStatusNotification($company_data, 'rejected', $rejection_reason);
            $mail->AltBody = $this->templates->companyStatusNotificationPlainText($company_data, 'rejected', $rejection_reason);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Email de rechazo enviado a empresa: {$company_data['email']}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar email de rechazo a empresa: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================
    // NOTIFICACIÓN GENÉRICA
    // ========================================
    
    /**
     * Enviar notificación genérica a usuario
     * 
     * Este correo solo avisa que hay una nueva notificación en la plataforma.
     * NO incluye detalles, solo invita al usuario a ingresar al sistema.
     * 
     * @param string $email Email del destinatario
     * @param string $name Nombre del destinatario
     * @param string $notification_type Tipo de notificación ('student', 'company')
     * @return bool
     */
    public function sendGenericNotification($email, $name, $notification_type = 'general') {
        try {
            $mail = $this->getMailer();
            
            $mail->addAddress($email, $name);
            
            $mail->Subject = "🔔 Nueva Notificación - SIEP UPIICSA";
            
            $mail->Body = $this->templates->genericNotification($name, $notification_type);
            $mail->AltBody = $this->templates->genericNotificationPlainText($name, $notification_type);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Notificación genérica enviada a: {$email}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar notificación genérica: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================
    
    /**
     * Enviar email de prueba
     * 
     * @param string $to_email
     * @return bool
     */
    public function sendTestEmail($to_email) {
        return sendTestEmail($to_email); // Función de Config/email.php
    }
}