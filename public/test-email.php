<?php
require_once __DIR__ . '/../src/Config/env.php';
load_dotenv(__DIR__ . '/../.env');
require_once __DIR__ . '/../vendor/init_libraries.php';
require_once __DIR__ . '/../src/Config/email.php';

echo "<h2>🧪 Prueba de Conexión SMTP con Outlook</h2>";

echo "<h3>📋 Configuración actual:</h3>";
echo "<pre>";
echo "SMTP_HOST: " . getenv('SMTP_HOST') . "\n";
echo "SMTP_PORT: " . getenv('SMTP_PORT') . "\n";
echo "SMTP_USER: " . getenv('SMTP_USER') . "\n";
echo "SMTP_PASS: " . (getenv('SMTP_PASS') ? '[SET - ' . strlen(getenv('SMTP_PASS')) . ' caracteres]' : '[NO SET]') . "\n";
echo "SMTP_FROM: " . getenv('SMTP_FROM_EMAIL') . "\n";
echo "</pre>";

// Cambia esto por un email tuyo real para probar
$test_email = "asalazarg54@gmail.com";

echo "<h3>📧 Enviando correo de prueba a: <strong>{$test_email}</strong></h3>";

try {
    if (sendTestEmail($test_email)) {
        echo "<p style='color:green; font-size: 18px;'>✅ ¡Correo enviado exitosamente!</p>";
        echo "<p>Revisa tu bandeja de entrada (y spam) en: <strong>{$test_email}</strong></p>";
    } else {
        echo "<p style='color:red; font-size: 18px;'>❌ Error al enviar correo.</p>";
        echo "<p>Revisa la configuración en .env</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red; font-size: 18px;'>❌ Excepción: " . $e->getMessage() . "</p>";
}
?>