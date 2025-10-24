<?php
/**
 * Configuración de PHPMailer para Outlook/Office365
 * 
 * @package SIEP\Config
 * @version 1.0.0
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Cargar variables de entorno si no están cargadas
if (!getenv('SMTP_HOST')) {
    require_once __DIR__ . '/env.php';
    load_dotenv(__DIR__ . '/../../.env');
}

/**
 * Crea y retorna una instancia configurada de PHPMailer
 * 
 * @return PHPMailer
 * @throws Exception
 */
function getMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST') ?: 'smtp-mail.outlook.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USER');
        $mail->Password   = getenv('SMTP_PASS');
        $mail->SMTPSecure = getenv('SMTP_ENCRYPTION') ?: PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = getenv('SMTP_PORT') ?: 587;
        
        // Configuración del remitente
        $mail->setFrom(
            getenv('SMTP_FROM_EMAIL') ?: getenv('SMTP_USER'),
            getenv('SMTP_FROM_NAME') ?: 'SIEP - UPIICSA'
        );
        
        // Configuración de caracteres
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        
        // Formato HTML
        $mail->isHTML(true);
        
        // Debug (solo en desarrollo)
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        
        return $mail;
        
    } catch (Exception $e) {
        error_log("Error al configurar PHPMailer: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Envía un correo de prueba
 * 
 * @param string $to Email de destino
 * @return bool
 */
function sendTestEmail($to) {
    try {
        $mail = getMailer();
        
        $mail->addAddress($to);
        $mail->Subject = 'Prueba de configuración - SIEP';
        $mail->Body    = '<h1>¡Configuración exitosa!</h1><p>El sistema de correo está funcionando correctamente.</p>';
        $mail->AltBody = 'Configuración exitosa! El sistema de correo está funcionando correctamente.';
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("Error al enviar correo de prueba: " . $mail->ErrorInfo);
        return false;
    }
}