<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Ejecutivo - SIEP UPIS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            border-bottom: 3px solid #2980b9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.blue {
            background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
        }
        
        .stat-card.green {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        }
        
        .stat-card.orange {
            background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%);
        }
        
        .stat-card.red {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
        }
        
        .stat-card h3 {
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
        }
        
        .section {
            margin-top: 40px;
        }
        
        .section h2 {
            color: #2c3e50;
            font-size: 22px;
            margin-bottom: 20px;
            border-left: 4px solid #2980b9;
            padding-left: 15px;
        }
        
        .career-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .career-table thead {
            background: #34495e;
            color: white;
        }
        
        .career-table th,
        .career-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .career-table tbody tr:hover {
            background: #ecf0f1;
        }
        
        .career-table .bar-container {
            background: #ecf0f1;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        
        .career-table .bar {
            background: linear-gradient(90deg, #2980b9, #3498db);
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s;
        }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }
        
        .quick-link {
            display: block;
            padding: 20px;
            background: #ecf0f1;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            color: #2c3e50;
            font-weight: 600;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .quick-link:hover {
            background: #2980b9;
            color: white;
            border-color: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .quick-link .icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .timestamp {
            text-align: right;
            color: #95a5a6;
            font-size: 12px;
            margin-top: 30px;
        }
        
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background: #34495e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: background 0.3s;
        }
        
        .btn-back:hover {
            background: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/SIEP/public/index.php?page=upis_dashboard" class="btn-back">← Volver al Dashboard</a>
        
        <div class="header">
            <h1>📊 Dashboard Ejecutivo UPIS</h1>
            <p>Resumen general del sistema de estancias profesionales</p>
        </div>
        
        <!-- Estadísticas principales -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <h3>Total Empresas Activas</h3>
                <div class="number"><?php echo $stats['total_empresas']; ?></div>
            </div>
            
            <div class="stat-card green">
                <h3>Total Estudiantes</h3>
                <div class="number"><?php echo $stats['total_estudiantes']; ?></div>
            </div>
            
            <div class="stat-card orange">
                <h3>Estancias en Proceso</h3>
                <div class="number"><?php echo $stats['estancias_activas']; ?></div>
            </div>
            
            <div class="stat-card red">
                <h3>Procesos Completados</h3>
                <div class="number"><?php echo $stats['procesos_completados']; ?></div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);">
                <h3>Vacantes Activas</h3>
                <div class="number"><?php echo $stats['vacantes_activas']; ?></div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%);">
                <h3>Tasa de Éxito</h3>
                <div class="number">
                    <?php 
                        $total = $stats['estancias_activas'] + $stats['procesos_completados'];
                        $tasa = $total > 0 ? round(($stats['procesos_completados'] / $total) * 100, 1) : 0;
                        echo $tasa . '%';
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Distribución por carrera -->
        <div class="section">
            <h2>Distribución de Estudiantes por Carrera</h2>
            
            <?php if (!empty($stats['por_carrera'])): ?>
                <table class="career-table">
                    <thead>
                        <tr>
                            <th>Carrera</th>
                            <th>Total Estudiantes</th>
                            <th>Distribución</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $max_total = max(array_column($stats['por_carrera'], 'total'));
                            foreach ($stats['por_carrera'] as $carrera): 
                                $porcentaje = ($carrera['total'] / $max_total) * 100;
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($carrera['career']); ?></strong></td>
                                <td><?php echo $carrera['total']; ?> estudiantes</td>
                                <td>
                                    <div class="bar-container">
                                        <div class="bar" style="width: <?php echo $porcentaje; ?>%;"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #7f8c8d; text-align: center; padding: 20px;">No hay datos de carreras disponibles.</p>
            <?php endif; ?>
        </div>
        
        <!-- Enlaces rápidos -->
        <div class="section">
            <h2>Accesos Rápidos a Reportes</h2>
            
            <div class="quick-links">
                <a href="/SIEP/src/Controllers/ReportController.php?action=estancias" class="quick-link">
                    <div class="icon">📋</div>
                    Historial de Estancias
                </a>
                
                <a href="/SIEP/src/Controllers/ReportController.php?action=vacantes" class="quick-link">
                    <div class="icon">💼</div>
                    Reporte de Vacantes
                </a>
                
                <a href="/SIEP/src/Controllers/ReportController.php?action=empresas" class="quick-link">
                    <div class="icon">🏢</div>
                    Empresas con Estudiantes
                </a>
            </div>
        </div>
        
        <div class="timestamp">
            Generado el: <?php echo date('d/m/Y H:i:s'); ?> | Usuario: <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'N/A'); ?>
        </div>
    </div>
</body>
</html>