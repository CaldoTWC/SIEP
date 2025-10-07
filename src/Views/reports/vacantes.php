<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Vacantes - SIEP UPIS</title>
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
            max-width: 1600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            border-bottom: 3px solid #9b59b6;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 28px;
        }
        
        .btn-back {
            padding: 10px 20px;
            background: #34495e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .btn-back:hover {
            background: #2c3e50;
        }
        
        .summary {
            background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        
        .summary-item h3 {
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .summary-item .number {
            font-size: 32px;
            font-weight: bold;
        }
        
        .vacancy-grid {
            display: grid;
            gap: 20px;
        }
        
        .vacancy-card {
            border: 1px solid #ecf0f1;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .vacancy-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-3px);
        }
        
        .vacancy-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .vacancy-title {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .vacancy-company {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .vacancy-stats {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .stat-badge {
            background: #ecf0f1;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 13px;
            color: #2c3e50;
        }
        
        .stat-badge.highlight {
            background: #3498db;
            color: white;
            font-weight: 600;
        }
        
        .vacancy-description {
            color: #34495e;
            line-height: 1.6;
            margin: 15px 0;
        }
        
        .vacancy-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-icon {
            font-size: 18px;
        }
        
        .detail-text {
            font-size: 14px;
            color: #34495e;
        }
        
        .no-results {
            text-align: center;
            padding: 60px;
            color: #7f8c8d;
        }
        
        .no-results-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💼 Reporte de Vacantes Activas</h1>
            <a href="/SIEP/src/Controllers/ReportController.php?action=dashboard" class="btn-back">← Volver</a>
        </div>
        
        <!-- Resumen -->
        <div class="summary">
            <div class="summary-item">
                <h3>Total Vacantes Activas</h3>
                <div class="number"><?php echo count($vacantes); ?></div>
            </div>
            <div class="summary-item">
                <h3>Total Postulantes</h3>
                <div class="number"><?php echo array_sum(array_column($vacantes, 'postulantes')); ?></div>
            </div>
            <div class="summary-item">
                <h3>Promedio Postulantes/Vacante</h3>
                <div class="number">
                    <?php 
                        $total_vacantes = count($vacantes);
                        $total_postulantes = array_sum(array_column($vacantes, 'postulantes'));
                        echo $total_vacantes > 0 ? round($total_postulantes / $total_vacantes, 1) : 0;
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Lista de vacantes -->
        <?php if (!empty($vacantes)): ?>
            <div class="vacancy-grid">
                <?php foreach ($vacantes as $vacante): ?>
                    <div class="vacancy-card">
                        <div class="vacancy-header">
                            <div>
                                <div class="vacancy-title"><?php echo htmlspecialchars($vacante['titulo']); ?></div>
                                <div class="vacancy-company">🏢 <?php echo htmlspecialchars($vacante['empresa']); ?></div>
                            </div>
                            <div class="vacancy-stats">
                                <span class="stat-badge highlight">
                                    👥 <?php echo $vacante['postulantes']; ?> postulante<?php echo $vacante['postulantes'] != 1 ? 's' : ''; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="vacancy-description">
                            <?php echo nl2br(htmlspecialchars(substr($vacante['descripcion'], 0, 200))); ?>
                            <?php if (strlen($vacante['descripcion']) > 200) echo '...'; ?>
                        </div>
                        
                        <div class="vacancy-details">
                            <div class="detail-item">
                                <span class="detail-icon">📍</span>
                                <span class="detail-text"><?php echo htmlspecialchars($vacante['ubicacion']); ?></span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-icon">📅</span>
                                <span class="detail-text">
                                    Inicio: <?php echo date('d/m/Y', strtotime($vacante['fecha_inicio'])); ?>
                                </span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-icon">📅</span>
                                <span class="detail-text">
                                    Fin: <?php echo date('d/m/Y', strtotime($vacante['fecha_fin'])); ?>
                                </span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-icon">📢</span>
                                <span class="detail-text">
                                    Publicada: <?php echo date('d/m/Y', strtotime($vacante['fecha_publicacion'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <div class="no-results-icon">💼</div>
                <h3>No hay vacantes activas</h3>
                <p>En este momento no existen vacantes publicadas en el sistema.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>