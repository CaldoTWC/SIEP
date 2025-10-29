<?php
/**
 * Servicio de Email
 * 
 * Gestiona el envío de correos electrónicos del sistema
 * 
 * @package SIEP\Services
 * @version 3.0.0 - Agregadas notificaciones de vacantes
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
            error_log("❌ Error al enviar email de estado acreditación: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================
    // NOTIFICACIONES PARA EMPRESAS
    // ========================================
    
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
            
            $mail->addAddress($company_data['email'], $company_data['company_name']);
            
            $status_text = $status === 'approved' ? 'Aprobado' : 'Requiere Revisión';
            $mail->Subject = "🏢 Registro de Empresa {$status_text} - SIEP UPIICSA";
            
            $mail->Body = $this->templates->companyRegistrationStatus($company_data, $status, $comments);
            $mail->AltBody = $this->templates->companyRegistrationStatusPlainText($company_data, $status, $comments);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Email de estado empresa enviado a: {$company_data['email']}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar email de estado empresa: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Notificar a la empresa que su vacante fue aprobada
     * 
     * @param array $vacancy_data Datos de la vacante
     * @param array $company_data Datos de la empresa
     * @param string $comments Comentarios opcionales de UPIS
     * @return bool
     */
    public function notifyVacancyApproved($vacancy_data, $company_data, $comments = '') {
        try {
            $mail = $this->getMailer();
            
            $mail->addAddress($company_data['email'], $company_data['company_name']);
            
            $mail->Subject = "✅ Vacante Aprobada - {$vacancy_data['title']} - SIEP UPIICSA";
            
            $mail->Body = $this->templates->vacancyApproved($vacancy_data, $company_data, $comments);
            $mail->AltBody = $this->templates->vacancyApprovedPlainText($vacancy_data, $company_data, $comments);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Email de vacante aprobada enviado a: {$company_data['email']}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar email de vacante aprobada: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Notificar a la empresa que su vacante fue rechazada
     * 
     * @param array $vacancy_data Datos de la vacante
     * @param array $company_data Datos de la empresa
     * @param string $rejection_reason Razón del rechazo (OBLIGATORIO)
     * @return bool
     */
    public function notifyVacancyRejected($vacancy_data, $company_data, $rejection_reason) {
        try {
            $mail = $this->getMailer();
            
            $mail->addAddress($company_data['email'], $company_data['company_name']);
            
            $mail->Subject = "⚠️ Vacante Requiere Correcciones - {$vacancy_data['title']} - SIEP UPIICSA";
            
            $mail->Body = $this->templates->vacancyRejected($vacancy_data, $company_data, $rejection_reason);
            $mail->AltBody = $this->templates->vacancyRejectedPlainText($vacancy_data, $company_data, $rejection_reason);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Email de vacante rechazada enviado a: {$company_data['email']}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar email de vacante rechazada: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================
    // NOTIFICACIONES PARA UPIS
    // ========================================
    
    /**
     * Notificar a UPIS cuando una empresa publica una nueva vacante
     * 
     * @param array $vacancy_data Datos de la vacante
     * @param array $company_data Datos de la empresa
     * @return bool
     */
    public function notifyUPISNewVacancy($vacancy_data, $company_data) {
        try {
            $mail = $this->getMailer();
            
            // Email de UPIS desde variables de entorno o default
            $upis_email = getenv('UPIS_EMAIL') ?: 'upis@upiicsa.ipn.mx';
            $mail->addAddress($upis_email, 'UPIS - Revisiones');
            
            $mail->Subject = "🆕 Nueva Vacante Pendiente - {$company_data['company_name']} - SIEP";
            
            $mail->Body = $this->templates->newVacancyUPIS($vacancy_data, $company_data);
            $mail->AltBody = $this->templates->newVacancyUPISPlainText($vacancy_data, $company_data);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Email de nueva vacante enviado a UPIS: {$upis_email}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar email a UPIS: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Notificar a UPIS cuando una empresa se registra
     * 
     * @param array $company_data Datos de la empresa
     * @return bool
     */
    public function notifyUPISNewCompany($company_data) {
        try {
            $mail = $this->getMailer();
            
            $upis_email = getenv('UPIS_EMAIL') ?: 'upis@upiicsa.ipn.mx';
            $mail->addAddress($upis_email, 'UPIS - Revisiones');
            
            $mail->Subject = "🏢 Nueva Empresa Registrada - {$company_data['company_name']} - SIEP";
            
            $mail->Body = $this->templates->newCompanyUPIS($company_data);
            $mail->AltBody = $this->templates->newCompanyUPISPlainText($company_data);
            
            $success = $mail->send();
            
            if ($success) {
                error_log("✅ Email de nueva empresa enviado a UPIS: {$upis_email}");
            }
            
            return $success;
            
        } catch (Exception $e) {
            error_log("❌ Error al enviar email de nueva empresa a UPIS: " . $e->getMessage());
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