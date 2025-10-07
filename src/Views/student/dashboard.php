<?php
// Archivo: src/Views/student/dashboard.php (Versión Final con Enlace Corregido)

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['student']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard del Estudiante</title>
    <!-- Asegúrate de que la ruta a tu CSS sea correcta (/SIEP/ o /SIEP/) -->
    <link rel="stylesheet" href="/SIEP/public/css/styles.css"> 
</head>
<body>
    <div class="container">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        
        <!-- Bloque de Mensajes -->
        <?php if (isset($_GET['status'])): ?>
            <?php
                $message = '';
                $message_type = 'approved';
                switch ($_GET['status']) {
                    case 'request_sent':
                        $message = "¡Tu solicitud de carta ha sido enviada exitosamente para revisión!";
                        break;
                    case 'request_failed':
                        $message = "Error: No se pudo enviar la solicitud. Es posible que ya tengas una pendiente.";
                        $message_type = 'rejected';
                        break;
                    case 'accreditation_sent':
                        $message = "¡Tus documentos de acreditación han sido subidos exitosamente!";
                        break;
                    case 'upload_error':
                        $message = "Error: Hubo un problema al subir tus archivos.";
                        $message_type = 'error';
                        break;
                }
            ?>
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <p>Este es tu panel de control. Desde aquí puedes gestionar tus trámites de Estancia Profesional.</p>

        <div class="menu-buttons">
            <a href="/SIEP/public/index.php?action=listVacancies" class="btn">Ver Catálogo de Vacantes</a>
            <!-- ENLACE CORREGIDO -->
            <a href="/SIEP/public/index.php?action=showDetailedLetterForm" class="btn">Solicitar Carta de Presentación</a>
            <a href="/SIEP/public/index.php?action=showMyDocuments" class="btn">Mis Documentos Emitidos</a>
            <a href="/SIEP/public/index.php?action=showAccreditationForm" class="btn" style="background-color: var(--color-exito);">Subir Documentos de Acreditación Final</a>
            <a href="/SIEP/public/index.php?action=showChangePasswordForm" class="btn btn-sm btn-outline-primary">🔐 Cambiar Contraseña</a>
            <a href="/SIEP/public/index.php?action=logout" class="btn" style="background-color: #5a6a7e;">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>