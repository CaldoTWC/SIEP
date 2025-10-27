<?php
/**
 * Servicio de Envío de Correos Electrónicos
 * 
 * Gestiona el envío de notificaciones por email a todos los usuarios del sistema
 * 
 * @package SIEP\Services
 * @version 1.0.0
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Config/email.php';
require_once __DIR__ . '/EmailTemplates.php';

class EmailService {
    
    private $templates;
    
    public function __construct() {
        $this->templates = new EmailTemplates();
    }
    
    // ========================================================================
    // NOTIFICACIONES PARA EMPRESAS
    // ========================================================================
    
    /**
     * Envía confirmación a empresa cuando se registra
     * 
     * @param array $company_data Datos de la empresa
     * @return bool
     */
    public function notifyCompanyRegistered($company_data) {
        try {
            $mail = getMailer();
            
            // Destinatario: Empresa
            $mail->addAddress($company_data['email'], $company_data['contact_name']);
            
            // Asunto
            $mail->Subject = "Registro recibido - SIEP UPIICSA";
            
            // Cuerpo del correo
            $mail->Body = $this->templates->companyRegistrationReceived($company_data);
            $mail->AltBody = $this->templates->companyRegistrationReceivedPlainText($company_data);
            
            $result = $mail->send();
            
            if ($result) {
                $this->logEmail($company_data['email'], 'company_registration_received', $company_data['user_id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al enviar confirmación a empresa: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envía notificación a empresa sobre aprobación/rechazo de su cuenta
     * 
     * @param array $company_data Datos de la empresa
     * @param string $status 'approved' o 'rejected'
     * @param string $comments Comentarios opcionales
     * @return bool
     */
    public function notifyCompanyStatus($company_data, $status, $comments = '') {
        try {
            $mail = getMailer();
            
            $mail->addAddress($company_data['email'], $company_data['contact_name']);
            
            $status_text = $status === 'approved' ? 'aprobada' : 'rechazada';
            $mail->Subject = "Solicitud de registro {$status_text} - SIEP UPIICSA";
            
            $mail->Body = $this->templates->companyStatusNotification($company_data, $status, $comments);
            $mail->AltBody = $this->templates->companyStatusNotificationPlainText($company_data, $status, $comments);
            
            $result = $mail->send();
            
            if ($result) {
                $this->logEmail($company_data['email'], 'company_status_' . $status, $company_data['user_id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al enviar notificación de estado a empresa: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================================================
    // NOTIFICACIONES PARA VACANTES
    // ========================================================================
    
    /**
     * Envía confirmación a empresa cuando publica una vacante
     * 
     * @param array $company_data Datos de la empresa
     * @param array $vacancy_data Datos de la vacante
     * @return bool
     */
    public function notifyVacancyPublished($company_data, $vacancy_data) {
        try {
            $mail = getMailer();
            
            $mail->addAddress($company_data['email'], $company_data['contact_name']);
            $mail->Subject = "Vacante publicada - SIEP UPIICSA";
            
            $mail->Body = $this->templates->vacancyPublished($company_data, $vacancy_data);
            $mail->AltBody = $this->templates->vacancyPublishedPlainText($company_data, $vacancy_data);
            
            $result = $mail->send();
            
            if ($result) {
                $this->logEmail($company_data['email'], 'vacancy_published', $vacancy_data['id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al enviar confirmación de vacante: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envía notificación sobre estado de vacante (aprobada/rechazada)
     * 
     * @param array $company_data Datos de la empresa
     * @param array $vacancy_data Datos de la vacante
     * @param string $status 'approved' o 'rejected'
     * @param string $comments Comentarios opcionales
     * @return bool
     */
    public function notifyVacancyStatus($company_data, $vacancy_data, $status, $comments = '') {
        try {
            $mail = getMailer();
            
            $mail->addAddress($company_data['email'], $company_data['contact_name']);
            
            $status_text = $status === 'approved' ? 'aprobada' : 'rechazada';
            $mail->Subject = "Vacante {$status_text} - SIEP UPIICSA";
            
            $mail->Body = $this->templates->vacancyStatus($company_data, $vacancy_data, $status, $comments);
            $mail->AltBody = $this->templates->vacancyStatusPlainText($company_data, $vacancy_data, $status, $comments);
            
            $result = $mail->send();
            
            if ($result) {
                $this->logEmail($company_data['email'], 'vacancy_status_' . $status, $vacancy_data['id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al enviar estado de vacante: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================================================
    // NOTIFICACIONES PARA ESTUDIANTES - CARTAS DE PRESENTACIÓN
    // ========================================================================
    
    /**
     * Confirmación al estudiante cuando solicita carta de presentación
     * 
     * @param array $student_data Datos del estudiante
     * @param array $application_data Datos de la solicitud
     * @return bool
     */
    public function notifyStudentLetterReceived($student_data, $application_data) {
        try {
            $mail = getMailer();
            
            $mail->addAddress($student_data['email'], $student_data['full_name']);
            $mail->Subject = "Solicitud de carta recibida - SIEP UPIICSA";
            
            $mail->Body = $this->templates->letterRequestReceived($student_data, $application_data);
            $mail->AltBody = $this->templates->letterRequestReceivedPlainText($student_data, $application_data);
            
            $result = $mail->send();
            
            if ($result) {
                $this->logEmail($student_data['email'], 'letter_received', $student_data['user_id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al enviar confirmación de carta: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Notifica al estudiante el estado de su carta de presentación
     * 
     * @param array $student_data Datos del estudiante
     * @param string $status 'approved' o 'rejected'
     * @param string $comments Comentarios opcionales
     * @return bool
     */
    public function notifyStudentLetterStatus($student_data, $status, $comments = '') {
        try {
            $mail = getMailer();
            
            $mail->addAddress($student_data['email'], $student_data['full_name']);
            
            $status_text = $status === 'approved' ? 'aprobada' : 'requiere revisión';
            $mail->Subject = "Carta de presentación {$status_text} - SIEP UPIICSA";
            
            $mail->Body = $this->templates->letterStatus($student_data, $status, $comments);
            $mail->AltBody = $this->templates->letterStatusPlainText($student_data, $status, $comments);
            
            $result = $mail->send();
            
            if ($result) {
                $this->logEmail($student_data['email'], 'letter_status_' . $status, $student_data['user_id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al enviar estado de carta: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================================================
    // NOTIFICACIONES PARA ESTUDIANTES - ACREDITACIÓN
    // ========================================================================
    
    /**
     * Confirmación al estudiante cuando sube documentos de acreditación
     * 
     * @param array $student_data Datos del estudiante
     * @param array $submission_data Datos de la acreditación
     * @return bool
     */
    public function notifyStudentAccreditationReceived($student_data, $submission_data) {
        try {
            $mail = getMailer();
            
            $mail->addAddress($student_data['email'], $student_data['full_name']);
            $mail->Subject = "Documentos de acreditación recibidos - SIEP UPIICSA";
            
            $mail->Body = $this->templates->accreditationReceived($student_data, $submission_data);
            $mail->AltBody = $this->templates->accreditationReceivedPlainText($student_data, $submission_data);
            
            $result = $mail->send();
            
            if ($result) {
                $this->logEmail($student_data['email'], 'accreditation_received', $student_data['user_id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al enviar confirmación de acreditación: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Notifica al estudiante el estado de su acreditación
     * 
     * @param array $student_data Datos del estudiante
     * @param string $status 'approved' o 'rejected'
     * @param string $comments Comentarios opcionales
     * @return bool
     */
    public function notifyStudentAccreditationStatus($student_data, $status, $comments = '') {
        try {
            $mail = getMailer();
            
            $mail->addAddress($student_data['email'], $student_data['full_name']);
            
            $status_text = $status === 'approved' ? 'aprobada' : 'requiere revisión';
            $mail->Subject = "Acreditación {$status_text} - SIEP UPIICSA";
            
            $mail->Body = $this->templates->accreditationStatusStudent($student_data, $status, $comments);
            $mail->AltBody = $this->templates->accreditationStatusStudentPlainText($student_data, $status, $comments);
            
            $result = $mail->send();
            
            if ($result) {
                $this->logEmail($student_data['email'], 'accreditation_status_' . $status, $student_data['user_id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al enviar estado de acreditación: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================================================
    // UTILIDADES
    // ========================================================================
    
    /**
     * Registra el envío de un email en la base de datos
     * 
     * @param string $recipient_email Email del destinatario
     * @param string $email_type Tipo de email
     * @param int $related_id ID relacionado (user_id, vacancy_id, etc)
     * @return void
     */
    private function logEmail($recipient_email, $email_type, $related_id = null) {
        $log_dir = __DIR__ . '/../../storage/logs/';
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_file = $log_dir . 'emails.log';
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[{$timestamp}] SUCCESS - To: {$recipient_email} | Type: {$email_type} | Related ID: {$related_id}\n";
        
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
}