<?php
// Archivo: src/Views/auth/register_company_success.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Registro Enviada</title>
    <link rel="stylesheet" href="/SIEP/public/css/auth.css">
 
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <!-- Un ícono de 'reloj' o 'enviado' sería ideal aquí -->
            <div class="success-icon">&#9203;</div> <!-- Símbolo de reloj de arena -->
        
            <h1>Solicitud de Registro Enviada</h1>
        
            <p>
                ¡Gracias por registrarse! Su solicitud ha sido enviada exitosamente y será revisada por el personal de la UPIS.
            </p>
            <p>
                Recibirá una notificación en su correo electrónico una vez que su cuenta sea activada.
            </p>
        
            <a href="/SIEP/public/index.php?action=home" class="btn">Volver a la Página Principal</a>
        </div>
    </div>

</body>
</html>