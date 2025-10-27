<?php
/**
 * Servicio de Email
 * 
 * Gestiona el envío de correos electrónicos del sistema
 * 
 * @package SIEP\Services
 * @version 2.0.0
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
    // NOTIFICACIONES PARA ESTUDIANTES
    // ========================================
    
    /**
     * Notificar al estudiante que su acreditación fue recibida
     * 
     * @param array $student_data
     * @param array $submission_data
     * @return bool
     */
    public function notifyStudentAccreditationReceived($student_data, $submission_data) {
        try {
            $mail = $this->getMailer();
            
            // Destinatario
            $mail->addAddress($student_data['email'], $student_data['full_name']);
            
            // Asunto
            $mail->Subject = '✅ Documentación de Acreditación Recibida - SIEP UPIICSA';
            
            // Cuerpo HTML
            $mail->Body = $this->templates->accreditationReceivedStudent($student_data, $submission_data);
            
            // Cuerpo texto plano
            $mail->AltBody = $this->templates->accreditationReceivedStudentPlainText($student_data, $submission_data);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Email de acreditación enviado a: {$student_data['email']}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar email de acreditación: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Notificar al estudiante sobre el estado de su acreditación
     * 
     * @param array $student_data
     * @param string $status ('approved', 'rejected', 'pending')
     * @param string $comments
     * @return bool
     */
    public function notifyStudentAccreditationStatus($student_data, $status, $comments = '') {
        try {
            $mail = $this->getMailer();
            
            $mail->addAddress($student_data['email'], $student_data['full_name']);
            
            $status_text = $status === 'approved' ? 'Aprobada' : 'Requiere Revisión';
            $mail->Subject = "🔔 Acreditación {$status_text} - SIEP UPIICSA";
            
            $mail->Body = $this->templates->accreditationStatusStudent($student_data, $status, $comments);
            $mail->AltBody = $this->templates->accreditationStatusStudentPlainText($student_data, $status, $comments);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Email de estado de acreditación enviado a: {$student_data['email']}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar email de estado: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Notificar al estudiante que su carta de presentación fue generada
     * 
     * @param array $student_data
     * @param array $letter_data
     * @return bool
     */
    public function notifyStudentLetterGenerated($student_data, $letter_data) {
        try {
            $mail = $this->getMailer();
            
            $mail->addAddress($student_data['email'], $student_data['full_name']);
            $mail->Subject = '📄 Carta de Presentación Generada - SIEP UPIICSA';
            
            $download_url = $letter_data['download_url'] ?? '#';
            
            $content = <<<HTML
<h2 style="color: #6f1d33;">✅ Carta de Presentación Lista</h2>

<p>Estimado/a <strong>{$student_data['full_name']}</strong>,</p>

<p>Tu carta de presentación ha sido generada exitosamente.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{$download_url}" style="display: inline-block; padding: 15px 30px; background: #6f1d33; color: white; text-decoration: none; border-radius: 5px;">
        📥 Descargar Carta de Presentación
    </a>
</div>

<p><strong>Próximos pasos:</strong></p>
<ol>
    <li>Descarga tu carta</li>
    <li>Preséntala a la empresa donde realizarás tu estancia</li>
    <li>Una vez completada tu estancia, sube tu documentación de acreditación</li>
</ol>
HTML;
            
            $mail->Body = $content;
            $mail->AltBody = "Tu carta de presentación está lista. Descárgala en: {$download_url}";
            
            return $mail->send();
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar notificación de carta: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================
    // NOTIFICACIONES PARA EMPRESAS
    // ========================================
    
    /**
     * Notificar a la empresa que su registro fue recibido
     * 
     * @param array $company_data
     * @return bool
     */
    public function notifyCompanyRegistrationReceived($company_data) {
        try {
            $mail = $this->getMailer();
            
            $mail->addAddress($company_data['email'], $company_data['contact_name']);
            $mail->Subject = '✅ Registro Recibido - SIEP UPIICSA';
            
            $mail->Body = $this->templates->companyRegistrationReceived($company_data);
            $mail->AltBody = $this->templates->companyRegistrationReceivedPlainText($company_data);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Email de registro enviado a empresa: {$company_data['email']}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar email a empresa: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Notificar a la empresa sobre el estado de su registro
     * 
     * @param array $company_data
     * @param string $status ('approved', 'rejected')
     * @param string $comments
     * @return bool
     */
    public function notifyCompanyStatus($company_data, $status, $comments = '') {
        try {
            $mail = $this->getMailer();
            
            $mail->addAddress($company_data['email'], $company_data['contact_name']);
            
            $status_text = $status === 'approved' ? 'Aprobado' : 'Rechazado';
            $mail->Subject = "🔔 Registro {$status_text} - SIEP UPIICSA";
            
            $mail->Body = $this->templates->companyStatusNotification($company_data, $status, $comments);
            $mail->AltBody = $this->templates->companyStatusNotificationPlainText($company_data, $status, $comments);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Email de estado enviado a empresa: {$company_data['email']}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar email de estado a empresa: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================
    // NOTIFICACIONES PARA UPIS
    // ========================================
    
    /**
     * Alertar a UPIS sobre nueva solicitud de acreditación
     * 
     * @param array $student_data
     * @param array $submission_data
     * @return bool
     */
    public function notifyUPISNewAccreditation($student_data, $submission_data) {
        try {
            $mail = $this->getMailer();
            
            // Email de UPIS desde .env
            $upis_email = getenv('UPIS_EMAIL') ?: 'upis@upiicsa.ipn.mx';
            
            $mail->addAddress($upis_email, 'UPIS - UPIICSA');
            
            $tipo_text = $submission_data['tipo'] === 'A' ? 'TIPO A' : 'TIPO B';
            $mail->Subject = "🔔 Nueva Acreditación [{$tipo_text}] - {$student_data['boleta']}";
            
            $mail->Body = $this->templates->newAccreditationAlertUPIS($student_data, $submission_data);
            $mail->AltBody = $this->templates->newAccreditationAlertUPISPlainText($student_data, $submission_data);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Alerta enviada a UPIS sobre nueva acreditación");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar alerta a UPIS: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Alertar a UPIS sobre nueva solicitud de carta de presentación
     * 
     * @param array $student_data
     * @param array $application_data
     * @return bool
     */
    public function notifyUPISNewLetterRequest($student_data, $application_data) {
        try {
            $mail = $this->getMailer();
            
            $upis_email = getenv('UPIS_EMAIL') ?: 'upis@upiicsa.ipn.mx';
            $mail->addAddress($upis_email, 'UPIS - UPIICSA');
            
            $mail->Subject = "📄 Nueva Solicitud de Carta - {$student_data['boleta']}";
            
            $review_url = $application_data['review_url'] ?? '#';
            
            $content = <<<HTML
<h2 style="color: #6f1d33;">📄 Nueva Solicitud de Carta de Presentación</h2>

<p><strong>Estudiante:</strong> {$student_data['full_name']}</p>
<p><strong>Boleta:</strong> {$student_data['boleta']}</p>
<p><strong>Carrera:</strong> {$student_data['career']}</p>
<p><strong>Email:</strong> {$student_data['email']}</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{$review_url}" style="display: inline-block; padding: 15px 30px; background: #6f1d33; color: white; text-decoration: none; border-radius: 5px;">
        📋 Revisar Solicitud
    </a>
</div>
HTML;
            
            $mail->Body = $content;
            $mail->AltBody = "Nueva solicitud de carta de {$student_data['full_name']} ({$student_data['boleta']}). Revisar en: {$review_url}";
            
            return $mail->send();
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar alerta de carta a UPIS: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================
    // MÉTODO GENÉRICO DE ENVÍO
    // ========================================
    
    /**
     * Enviar email genérico
     * 
     * @param string $to_email
     * @param string $to_name
     * @param string $subject
     * @param string $body_html
     * @param string $body_text
     * @return bool
     */
    public function sendEmail($to_email, $to_name, $subject, $body_html, $body_text = '') {
        try {
            $mail = $this->getMailer();
            
            $mail->addAddress($to_email, $to_name);
            $mail->Subject = $subject;
            $mail->Body = $body_html;
            $mail->AltBody = $body_text ?: strip_tags($body_html);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Email genérico enviado a: {$to_email}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar email genérico: " . $e->getMessage());
            return false;
        }
    }
}