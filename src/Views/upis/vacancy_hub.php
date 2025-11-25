<?php
require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hub de Gestión de Vacantes - UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/upis.css">

</head>

<body>
    <!-- Encabezado bonito -->
    <div class="upis-header">
        <h1>Panel de Administración de UPIS</h1>
    </div>
    <div class="container">
        <div class="page-header">
            <h1>📊 Hub de Gestión de Vacantes</h1>
            <p>Sistema de gestión del ciclo de vida completo de vacantes</p>
        </div>



        <a href="/SIEP/public/index.php?action=upisDashboard" class="logout-btn" style="margin-bottom: 20px;">← Volver
            al Dashboard</a>

        <!-- Estadísticas Globales -->
        <div class="stats-summary">
            <div class="stat-card pending">
                <div class="stat-label">⏳ Pendientes de Revisión</div>
                <div class="stat-number"><?php echo $stats['pending'] ?? 0; ?></div>
            </div>

            <div class="stat-card active">
                <div class="stat-label">✅ Activas</div>
                <div class="stat-number"><?php echo $stats['approved'] ?? 0; ?></div>
            </div>

            <div class="stat-card completed">
                <div class="stat-label">✔️ Completadas</div>
                <div class="stat-number"><?php echo $stats['completed'] ?? 0; ?></div>
            </div>

            <div class="stat-card rejected">
                <div class="stat-label">🗑️ Canceladas</div>
                <div class="stat-number"><?php echo $stats['rejected'] ?? 0; ?></div>
            </div>
        </div>

        <hr style="margin: 40px 0;">

        <h2>Módulos de Gestión</h2>

        <!-- Módulos de Gestión -->
        <div class="modules-grid">

            <!-- Módulo 1: Revisar Nuevas -->
            <a href="/SIEP/public/index.php?action=reviewVacancies" class="module-card review">
                <div class="module-icon">📥</div>
                <div class="module-title">Revisar Nuevas Vacantes</div>
                <div class="module-description">
                    Aprobar o rechazar vacantes pendientes de revisión
                </div>
                <div class="module-stat">
                    <?php echo $stats['pending'] ?? 0; ?> pendientes
                </div>
            </a>

            <!-- Módulo 2: Gestionar Activas -->
            <a href="/SIEP/public/index.php?action=manageActiveVacancies" class="module-card manage">
                <div class="module-icon">⚙️</div>
                <div class="module-title">Gestionar Vacantes Activas</div>
                <div class="module-description">
                    Supervisar y desactivar vacantes publicadas
                </div>
                <div class="module-stat">
                    <?php echo $stats['approved'] ?? 0; ?> activas
                </div>
            </a>

            <!-- Módulo 3: Papelera -->
            <a href="/SIEP/public/index.php?action=vacancyTrash" class="module-card trash">
                <div class="module-icon">🗑️</div>
                <div class="module-title">Papelera de Vacantes</div>
                <div class="module-description">
                    Restaurar o eliminar definitivamente vacantes canceladas
                </div>
                <div class="module-stat">
                    <?php echo $stats['rejected'] ?? 0; ?> en papelera
                </div>
            </a>

        </div>
        <a href="/SIEP/public/index.php?action=upisDashboard" class="logout-btn" style="margin-bottom: 20px;">← Volver
            al Dashboard</a>
    </div>
</body>

</html>