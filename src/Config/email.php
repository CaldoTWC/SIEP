<?php
/**
 * Configuración de Email con PHPMailer
 * 
 * Funciones auxiliares para el envío de correos electrónicos
 * usando PHPMailer y configuración desde variables de entorno
 * 
 * @package SIEP\Config
 * @version 1.0.0
 */

if (!file_exists(__DIR__ . '/../../vendor/phpmailer/src/PHPMailer.php')) {
    die('ERROR: No se encuentra PHPMailer en la ruta esperada');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Cargar librerías externas
require_once __DIR__ . '/../../vendor/init_libraries.php';

// Cargar variables de entorno
require_once __DIR__ . '/env.php';

/**
 * Obtiene una instancia configurada de PHPMailer
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
        $mail->Port       = (int)(getenv('SMTP_PORT') ?: 587);
        
        // Configuración del remitente
        $mail->setFrom(
            getenv('SMTP_FROM_EMAIL') ?: getenv('SMTP_USER'),
            getenv('SMTP_FROM_NAME') ?: 'SIEP - UPIICSA'
        );
        
        // Configuración de codificación
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        
        // Configuración HTML
        $mail->isHTML(true);
        
        // Debug (comentar en producción)
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        
    } catch (Exception $e) {
        error_log("Error al configurar PHPMailer: " . $e->getMessage());
        throw $e;
    }
    
    return $mail;
}

/**
 * Envía un correo de prueba
 * 
 * @param string $to_email Email de destino
 * @return bool
 */
function sendTestEmail($to_email) {
    try {
        $mail = getMailer();
        
        // Destinatario
        $mail->addAddress($to_email);
        
        // Contenido del correo
        $mail->Subject = 'Prueba de Configuración - SIEP UPIICSA';
        
        $mail->Body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #6f1d33; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; color: #155724; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 ¡Configuración Exitosa!</h1>
        </div>
        <div class="content">
            <div class="success">
                <h2>✅ El sistema de correos está funcionando correctamente</h2>
                <p>Este es un correo de prueba del Sistema Integral de Estancias Profesionales (SIEP).</p>
            </div>
            <h3>Detalles de la Prueba:</h3>
            <ul>
                <li><strong>Servidor SMTP:</strong> {$_ENV['SMTP_HOST']}</li>
                <li><strong>Puerto:</strong> {$_ENV['SMTP_PORT']}</li>
                <li><strong>Fecha:</strong> {date('d/m/Y H:i:s')}</li>
            </ul>
            <p>Si recibes este correo, significa que el sistema está listo para enviar notificaciones.</p>
        </div>
        <div class="footer">
            <p>Sistema Integral de Estancias Profesionales<br>
            UPIICSA - Instituto Politécnico Nacional</p>
        </div>
    </div>
</body>
</html>
HTML;
        
        $mail->AltBody = "¡Configuración Exitosa!\n\n" .
                         "El sistema de correos está funcionando correctamente.\n" .
                         "Este es un correo de prueba del SIEP.\n\n" .
                         "Servidor SMTP: {$_ENV['SMTP_HOST']}\n" .
                         "Puerto: {$_ENV['SMTP_PORT']}\n" .
                         "Fecha: " . date('d/m/Y H:i:s') . "\n\n" .
                         "---\n" .
                         "Sistema Integral de Estancias Profesionales\n" .
                         "UPIICSA - IPN";
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("Error al enviar correo de prueba: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Registra el envío de un email en logs
 * 
 * @param string $to Email destinatario
 * @param string $subject Asunto del correo
 * @param bool $success Si se envió exitosamente
 * @return void
 */
function logEmailSent($to, $subject, $success = true) {
    $status = $success ? 'SUCCESS' : 'FAILED';
    $log_dir = __DIR__ . '/../../storage/logs/';
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . 'emails.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] {$status} - To: {$to} | Subject: {$subject}\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}