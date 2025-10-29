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
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .hub-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #005a9c;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-card.pending { border-left-color: #ffc107; }
        .stat-card.active { border-left-color: #28a745; }
        .stat-card.completed { border-left-color: #17a2b8; }
        .stat-card.rejected { border-left-color: #dc3545; }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .module-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.2);
        }
        
        .module-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .module-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #005a9c;
        }
        
        .module-description {
            color: #666;
            font-size: 14px;
        }
        
        .module-card.review { border-top: 4px solid #ffc107; }
        .module-card.manage { border-top: 4px solid #28a745; }
        .module-card.trash { border-top: 4px solid #dc3545; }
        .module-card.reports { border-top: 4px solid #17a2b8; }
    </style>
</head>
<body>
    <div class="hub-container">
        <h1>📊 Hub de Gestión de Vacantes</h1>
        <p>Sistema de gestión del ciclo de vida completo de vacantes</p>
        
        <a href="/SIEP/public/index.php?action=upisDashboard" class="btn" style="margin-bottom: 20px;">← Volver al Dashboard</a>
        
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
            
                   <h2>Módulos de Gestión</h2>
        
        <!-- Módulos de Gestión -->
        <div class="modules-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            
            <!-- Módulo 1: Revisar Nuevas -->
            <a href="/SIEP/public/index.php?action=reviewVacancies" class="module-card review">
                <div class="module-icon">📥</div>
                <div class="module-title">Revisar Nuevas Vacantes</div>
                <div class="module-description">
                    Aprobar o rechazar vacantes pendientes de revisión
                </div>
                <div style="margin-top: 15px; font-size: 24px; font-weight: bold; color: #ffc107;">
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
                <div style="margin-top: 15px; font-size: 24px; font-weight: bold; color: #28a745;">
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
                <div style="margin-top: 15px; font-size: 24px; font-weight: bold; color: #dc3545;">
                    <?php echo $stats['rejected'] ?? 0; ?> en papelera
                </div>
            </a>
            
        </div>
                <div style="margin-top: 15px; font-size: 24px; font-weight: bold; color: #17a2b8;">
                    <?php echo $stats['total'] ?? 0; ?> total
                </div>
            </a>
            
        </div>
        
    </div>
</body>
</html>