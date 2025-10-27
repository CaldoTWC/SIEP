<?php
/**
 * Configuración de Email con PHPMailer
 * 
 * Funciones auxiliares para el envío de correos electrónicos
 * usando PHPMailer y configuración desde variables de entorno (.env)
 * 
 * @package SIEP\Config
 * @version 2.0.0
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Cargar librerías externas (Composer)
require_once __DIR__ . '/../../vendor/autoload.php';

// Cargar variables de entorno
require_once __DIR__ . '/env.php';

/**
 * Devuelve una instancia configurada de PHPMailer
 *
 * @return PHPMailer
 * @throws Exception
 */
function getMailer()
{
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.office365.com';
        $mail->Port       = (int)(getenv('SMTP_PORT') ?: 587);
        $mail->SMTPAuth   = getenv('SMTP_AUTH') === 'true';
        $mail->Username   = getenv('SMTP_USER');
        $mail->Password   = getenv('SMTP_PASS');
        $mail->SMTPSecure = getenv('SMTP_SECURE') ?: PHPMailer::ENCRYPTION_STARTTLS;

        // Configuración del remitente
        $mail->setFrom(
            getenv('SMTP_FROM_EMAIL') ?: getenv('SMTP_USER'),
            getenv('SMTP_FROM_NAME') ?: 'SIEP - UPIICSA'
        );

        // Configuración general
        $mail->CharSet  = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isHTML(true);

        // Debug (solo en pruebas)
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        // $mail->Debugoutput = 'html';

    } catch (Exception $e) {
        error_log("❌ Error al configurar PHPMailer: " . $e->getMessage());
        throw $e;
    }

    return $mail;
}

/**
 * Envía un correo de prueba
 *
 * @param string $to_email Email destino
 * @return bool
 */
function sendTestEmail($to_email)
{
    try {
        $mail = getMailer();

        $mail->addAddress($to_email);

        $mail->Subject = '🧪 Prueba de Configuración - SIEP UPIICSA';

        $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px; border-radius: 8px; }
        .header { background: #6f1d33; color: white; padding: 15px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; color: #155724; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ ¡Correo de Prueba Exitoso!</h1>
        </div>
        <div class="content">
            <p>Este es un correo de prueba enviado desde el <strong>Sistema Integral de Estancias Profesionales (SIEP)</strong>.</p>
            <div class="success">
                <p>La configuración SMTP parece estar funcionando correctamente 🎉</p>
            </div>
            <h3>Detalles Técnicos:</h3>
            <ul>
                <li><strong>Servidor SMTP:</strong> {$_ENV['SMTP_HOST']}</li>
                <li><strong>Puerto:</strong> {$_ENV['SMTP_PORT']}</li>
                <li><strong>Seguridad:</strong> {$_ENV['SMTP_SECURE']}</li>
                <li><strong>Fecha:</strong> {date('d/m/Y H:i:s')}</li>
            </ul>
        </div>
        <div class="footer">
            <p>Sistema Integral de Estancias Profesionales<br>UPIICSA - Instituto Politécnico Nacional</p>
        </div>
    </div>
</body>
</html>
HTML;

        $mail->AltBody = "Configuración SMTP correcta.\n\nServidor: {$_ENV['SMTP_HOST']}\nPuerto: {$_ENV['SMTP_PORT']}\nFecha: " . date('d/m/Y H:i:s');

        $success = $mail->send();

        logEmailSent($to_email, $mail->Subject, $success);
        return $success;
    } catch (Exception $e) {
        error_log("❌ Error al enviar correo de prueba a {$to_email}: " . $e->getMessage());
        return false;
    }
}

/**
 * Registra el envío de un email en logs
 */
function logEmailSent($to, $subject, $success = true)
{
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
