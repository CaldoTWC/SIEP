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
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .task-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .task-card {
            background-color: #fff;
            border: 1px solid var(--color-borde);
            border-radius: 8px;
            padding: 20px;
            text-decoration: none;
            color: var(--color-texto-principal);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 39, 87, 0.1);
        }
        .task-card .info h3 {
            margin: 0 0 10px 0;
            color: var(--color-ipn-azul);
        }
        .task-card .info p {
            margin: 0;
            text-align: left;
        }
        .task-card .counter {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--color-ipn-guinda);
            padding-left: 20px;
            border-left: 2px solid var(--color-borde);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Panel de Administración de UPIS</h1>
        <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>. Selecciona una tarea para comenzar.</p>

        <div class="task-grid">
            <a href="/SIEP/public/index.php?action=reviewCompanies" class="task-card">
                <div class="info">
                    <h3>Empresas</h3>
                    <p>Revisar nuevas solicitudes de registro.</p>
                </div>
                <div class="counter"><?php echo $pendingCompaniesCount; ?></div>
            </a>

            <a href="/SIEP/public/index.php?action=reviewVacancies" class="task-card">
                <div class="info">
                    <h3>Vacantes</h3>
                    <p>Validar nuevas vacantes publicadas.</p>
                </div>
                <div class="counter"><?php echo $pendingVacanciesCount; ?></div>
            </a>

            <a href="/SIEP/public/index.php?action=reviewLetters" class="task-card">
                <div class="info">
                    <h3>Cartas de Presentación</h3>
                    <p>Gestionar solicitudes de estudiantes.</p>
                </div>
                <div class="counter"><?php echo $pendingLettersCount; ?></div>
            </a>

            <!-- Gestión de Plantillas -->
<a href="/SIEP/public/index.php?action=manageTemplates" class="task-card">
    <div class="info">
        <h3>🎨 Plantillas</h3>
        <p>Gestionar plantillas de cartas y periodo académico.</p>
    </div>
    <div class="counter">⚙️</div>
</a>

            <a href="/SIEP/public/index.php?action=showUploadDocumentsForm" class="task-card">
                <div class="info">
                <h3>📤 Subir Cartas Firmadas</h3>
                <p>Devolver documentos firmados y sellados.</p>
                </div>
                <div class="counter" style="font-size: 2rem;">📄</div>
            </a>
            <!-- Aquí podríamos añadir más tarjetas en el futuro -->
            <div class="dashboard-card">
            <a href="/SIEP/src/Controllers/ReportController.php?action=dashboard">
                <div class="card-icon">📊</div>
                    <h3>Dashboard de Reportes</h3>
                    <p>Estadísticas, estancias, vacantes y empresas</p>
                    </a>
        </div>

        

        <a href="/SIEP/public/index.php?action=showChangePasswordForm" class="btn btn-outline-secondary">🔐 Cambiar Contraseña </a>

        <a href="/SIEP/public/index.php?action=logout" class="btn" style="background-color: #5a6a7e; margin-top: 40px;">Cerrar Sesión</a>
    </div>
</body>
</html>