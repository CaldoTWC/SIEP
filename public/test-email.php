<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../src/Config/env.php';
load_dotenv(__DIR__ . '/../.env');

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

echo "<h2>🧪 Prueba de Conexión SMTP</h2>";

echo "<h3>📋 Configuración actual:</h3>";
echo "<pre>";
echo "SMTP_HOST: " . getenv('SMTP_HOST') . "\n";
echo "SMTP_PORT: " . getenv('SMTP_PORT') . "\n";
echo "SMTP_SECURE: " . getenv('SMTP_SECURE') . "\n";
echo "SMTP_AUTH: " . getenv('SMTP_AUTH') . "\n";
echo "SMTP_USER: " . getenv('SMTP_USER') . "\n";
echo "SMTP_PASS: " . (getenv('SMTP_PASS') ? '[SET - ' . strlen(getenv('SMTP_PASS')) . ' caracteres]' : '[NO SET]') . "\n";
echo "SMTP_FROM_EMAIL: " . getenv('SMTP_FROM_EMAIL') . "\n";
echo "SMTP_FROM_NAME: " . getenv('SMTP_FROM_NAME') . "\n";
echo "</pre>";

$mail = new PHPMailer(true);

try {
    // Debug completo
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = 'html';
    
    // ✅ LEER TODO DEL .ENV - NO HARDCODEAR
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST');  // ← Cambio importante
    $mail->SMTPAuth   = (getenv('SMTP_AUTH') === 'true');
    $mail->Username   = getenv('SMTP_USER');
    $mail->Password   = getenv('SMTP_PASS');
    $mail->SMTPSecure = getenv('SMTP_SECURE') === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)getenv('SMTP_PORT');
    
    // Timeout más largo
    $mail->Timeout = 30;
    
    // Remitente
    $mail->setFrom(getenv('SMTP_FROM_EMAIL'), getenv('SMTP_FROM_NAME'));
    
    // Destinatario
    $mail->addAddress(getenv('SMTP_USER'));
    
    // Contenido
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = '✅ Test SIEP - ' . date('Y-m-d H:i:s');
    $mail->Body    = '
    <!DOCTYPE html>
    <html>
    <body style="font-family: Arial, sans-serif;">
        <div style="max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5;">
            <h1 style="color: #6f1d33;">✅ ¡Prueba Exitosa!</h1>
            <p>El sistema SIEP está correctamente configurado para enviar correos.</p>
            <hr>
            <p><strong>Servidor:</strong> ' . getenv('SMTP_HOST') . '</p>
            <p><strong>Puerto:</strong> ' . getenv('SMTP_PORT') . '</p>
            <p><strong>Fecha:</strong> ' . date('d/m/Y H:i:s') . '</p>
        </div>
    </body>
    </html>';
    
    $mail->AltBody = 'Prueba exitosa del sistema SIEP. Servidor: ' . getenv('SMTP_HOST') . ', Puerto: ' . getenv('SMTP_PORT') . ', Fecha: ' . date('d/m/Y H:i:s');
    
    echo "<h3>📧 Enviando correo...</h3>";
    
    $mail->send();
    
    echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #155724; margin: 0;'>✅ ¡Correo enviado exitosamente!</h3>";
    echo "<p>Revisa tu bandeja de entrada en: <strong>" . getenv('SMTP_USER') . "</strong></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #721c24;'>❌ Error al enviar correo</h3>";
    echo "<p><strong>Mensaje de error:</strong> {$mail->ErrorInfo}</p>";
    echo "<p><strong>Excepción:</strong> {$e->getMessage()}</p>";
    echo "</div>";
    
    echo "<h3>🔍 Verificaciones:</h3>";
    echo "<ul>";
    echo "<li>Usuario: <code>" . getenv('SMTP_USER') . "</code></li>";
    echo "<li>Servidor: <code>" . getenv('SMTP_HOST') . "</code></li>";
    echo "<li>Puerto: <code>" . getenv('SMTP_PORT') . "</code></li>";
    echo "<li>Longitud de contraseña: " . strlen(getenv('SMTP_PASS')) . " caracteres</li>";
    echo "</ul>";
}
?>