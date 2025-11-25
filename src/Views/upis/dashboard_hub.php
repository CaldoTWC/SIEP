<?php
// Archivo: src/Views/upis/dashboard_hub.php
require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']);
// Las variables de conteo ($pendingCompaniesCount, etc.) vienen del controlador
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/upis.css">

</head>

<body>
    <!-- Encabezado bonito -->
    <div class="upis-header">
        <h1>Panel de Administración de UPIS</h1>
        <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>. Selecciona una tarea
            para comenzar.</p>
    </div>


    <div class="container">

        <!-- GRID DE TAREAS -->
        <div class="task-grid">
            <a href="/SIEP/public/index.php?action=presentationLettersHub" class="task-card">
                <div class="info">
                    <h3>📝 Cartas de Presentación</h3>
                    <p>Gestionar solicitudes de estudiantes.</p>
                </div>
                <div class="counter"><?php echo $pendingLettersCount; ?></div>
            </a>

            <!-- Revisar Acreditaciones -->
            <a href="/SIEP/public/index.php?action=reviewAccreditations" class="task-card">
                <div class="info">
                    <h3>✅ Acreditaciones</h3>
                    <p>Revisar solicitudes de acreditación de estudiantes.</p>
                </div>
                <div class="counter">📋</div>
            </a>

            <a href="/SIEP/public/index.php?action=companyManagementHub" class="task-card">
                <div class="info">
                    <h3>🏢 Empresas</h3>
                    <p>Revisar solicitudes de acreditación de estudiantes.</p>
                </div>
                <div class="counter">📋</div>
            </a>

            <a href="/SIEP/public/index.php?action=vacancyHub" class="task-card">
                <div class="info">
                    <h3>📊 Gestión de Vacantes</h3>
                    <p>Ciclo de vida completo: activas, completadas, papelera.</p>
                </div>
                <div class="counter">🔄</div>
            </a>

            <a href="/SIEP/public/index.php?action=showHistory" class="task-card">
                <div class="info">
                    <h3>📈 Centro de Reportes</h3>
                    <p>Reportes, estadísticas y análisis del sistema.</p>
                </div>
                <div class="counter">📊</div>
            </a>

            <a href="/SIEP/public/index.php?action=manageTemplates" class="task-card">
                <div class="info">
                    <h3>🎨 Plantillas</h3>
                    <p>Gestionar plantillas de cartas y periodo académico.</p>
                </div>
                <div class="counter">⚙️</div>
            </a>
            <!-- Botón de cambio de contraseña -->
            <a href="/SIEP/public/index.php?action=showChangePasswordForm" class="task-card">
                <div class="info">
                    <h3>🔐 Cambiar Contraseña</h3>
                    <p>Revisar solicitudes de acreditación de estudiantes.</p>
                </div>
                <div class="counter">⚙️</div>
            </a>
        </div>

        <!-- Botón de cerrar sesión -->
        <br>
        <a href="/SIEP/public/index.php?action=logout" class="logout-btn">Cerrar Sesión</a>
    </div>
</body>

</html>