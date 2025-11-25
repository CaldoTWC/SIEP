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
    <link rel="stylesheet" href="/SIEP/public/css/student.css">
</head>

<body>
    <!-- BARRA DE NAVEGACIÓN -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="/SIEP/public/index.php" class="nav-logo">SIEP</a>
            <ul class="nav-menu">
                <li class="nav-item"><a href="#hero" class="nav-link">Inicio</a></li>
                <li class="nav-item"><a href="#user-section" class="nav-link">Usuarios</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showLogin" class="nav-link btn-nav">Iniciar
                        Sesión</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showRegisterSelection"
                        class="nav-link btn-nav">Registrarse</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">

        <div class="page-header">
            <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p>Este es tu panel de control. Desde aquí puedes gestionar tus trámites de Estancia Profesional.</p>
        </div>


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



        <div class="task-grid">
            <a href="/SIEP/public/index.php?action=showVacancies" class="task-card">
                <div class="info">
                    <h3>Ver Vacantes Disponibles</h3>
                </div>
                <div class="counter">💼</div>
            </a>
            <!-- ENLACE CORREGIDO -->
            <a href="/SIEP/public/index.php?action=showDetailedLetterForm" class="task-card">
                <div class="info">
                    <h3>Solicitar Carta de Presentación</h3>
                </div>
                <div class="counter">💼</div>
            </a>
            <a href="/SIEP/public/index.php?action=showMyDocuments" class="task-card">
                <div class="info">
                    <h3>Mis Documentos Emitidos</h3>
                </div>
                <div class="counter">💼</div>
            </a>
            <a href="/SIEP/public/index.php?action=showAccreditationForm" class="task-card">
                <div class="info">
                    <h3>Subir Documentos de Acreditación Final</h3>
                </div>
                <div class="counter">💼</div>
            </a>
            <a href="/SIEP/public/index.php?action=showAllNotifications" class="task-card">
                <div class="info">
                    <h3>Mis Notificaciones</h3>
                </div>
                <div class="counter">🔔</div>
                        <?php
            // Mostrar contador de no leídas
            require_once(__DIR__ . '/../../Models/Notification.php');
            require_once(__DIR__ . '/../../Config/Database.php');
            $database = Database::getInstance();
            $notificationModel = new Notification($database->getConnection());
            $unreadCount = $notificationModel->countUnread($_SESSION['user_id']);
            if ($unreadCount > 0) {
                echo '<span style="background: #fff; color: #ff6b6b; padding: 2px 8px; border-radius: 10px; margin-left: 8px; font-weight: bold;">' . $unreadCount . '</span>';
            }
            ?>
            </a>
            <a href="/SIEP/public/index.php?action=showChangePasswordForm" class="task-card">
                <div class="info">
                    <h3>Cambiar Contraseña</h3>
                </div>
                <div class="counter">🔐</div>
            </a>
        </div>
        <a href="/SIEP/public/index.php?action=logout" class="logout-btn">Cerrar Sesión</a>
    </div>

</body>

</html>