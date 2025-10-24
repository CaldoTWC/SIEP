<?php
/**
 * Servicio de envío de correos electrónicos
 * 
 * @package SIEP\Services
 * @version 1.0.0
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Config/email.php';
require_once __DIR__ . '/EmailTemplates.php';

use PHPMailer\PHPMailer\Exception;

class EmailService {
    
    private $templates;
    
    public function __construct() {
        $this->templates = new EmailTemplates();
    }
    
    /**
     * Envía notificación a UPIS sobre nueva solicitud de acreditación
     * 
     * @param array $student_data Datos del estudiante
     * @param array $submission_data Datos de la solicitud
     * @return bool
     */
    public function notifyUPISNewAccreditation($student_data, $submission_data) {
        try {
            $mail = getMailer();
            
            // Destinatario: UPIS
            $upis_email = getenv('UPIS_EMAIL');
            $upis_name = getenv('UPIS_NAME') ?: 'Equipo UPIS';
            
            if (!$upis_email) {
                error_log("UPIS_EMAIL no está configurado en .env");
                return false;
            }
            
            $mail->addAddress($upis_email, $upis_name);
            
            // Asunto
            $mail->Subject = "Nueva solicitud de acreditación - {$student_data['full_name']}";
            
            // Cuerpo del correo
            $mail->Body = $this->templates->newAccreditationUPIS($student_data, $submission_data);
            $mail->AltBody = $this->templates->newAccreditationUPISPlainText($student_data, $submission_data);
            
            $result = $mail->send();
            
            if ($result) {
                $this->logEmail($upis_email, 'new_accreditation_upis', $submission_data['id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al enviar notificación a UPIS: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envía confirmación al estudiante sobre su solicitud recibida
     * 
     * @param array $student_data Datos del estudiante
     * @param array $submission_data Datos de la solicitud
     * @return bool
     */
    public function notifyStudentAccreditationReceived($student_data, $submission_data) {
        try {
            $mail = getMailer();
            
            // Destinatario: Estudiante
            $mail->addAddress($student_data['email'], $student_data['full_name']);
            
            // Asunto
            $mail->Subject = "Solicitud de acreditación recibida - SIEP UPIICSA";
            
            // Cuerpo del correo
            $mail->Body = $this->templates->accreditationReceivedStudent($student_data, $submission_data);
            $mail->AltBody = $this->templates->accreditationReceivedStudentPlainText($student_data, $submission_data);
            
            $result = $mail->send();
            
            if ($result) {
                $this->logEmail($student_data['email'], 'accreditation_received_student', $submission_data['id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al enviar confirmación al estudiante: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envía notificación al estudiante sobre aprobación/rechazo
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
            
            $status_text = $status === 'approved' ? 'aprobada' : 'rechazada';
            $mail->Subject = "Solicitud de acreditación {$status_text} - SIEP UPIICSA";
            
            $mail->Body = $this->templates->accreditationStatusStudent($student_data, $status, $comments);
            $mail->AltBody = $this->templates->accreditationStatusStudentPlainText($student_data, $status, $comments);
            
            $result = $mail->send();
            
            if ($result) {
                $this->logEmail($student_data['email'], 'accreditation_status_' . $status, $student_data['user_id']);
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error al enviar notificación de estado: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Registra el envío de correo en logs
     * 
     * @param string $to Email destinatario
     * @param string $type Tipo de correo
     * @param int $reference_id ID de referencia
     */
    private function logEmail($to, $type, $reference_id) {
        $log_entry = sprintf(
            "[%s] Email sent: %s | To: %s | Ref ID: %d\n",
            date('Y-m-d H:i:s'),
            $type,
            $to,
            $reference_id
        );
        
        $log_file = __DIR__ . '/../../storage/logs/emails.log';
        $log_dir = dirname($log_file);
        
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
}