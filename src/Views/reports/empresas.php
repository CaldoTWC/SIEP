<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Empresas - SIEP UPIS</title>
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
            border-bottom: 3px solid #e67e22;
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
            background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%);
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
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }
        
        thead {
            background: #e67e22;
            color: white;
        }
        
        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        .stat-col {
            text-align: center;
            font-weight: 600;
        }
        
        .stat-active {
            color: #27ae60;
            background: #d5f4e6;
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        .stat-completed {
            color: #2980b9;
            background: #d6eaf8;
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        .stat-total {
            color: #8e44ad;
            background: #ebdef0;
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        .contact-info {
            font-size: 13px;
            color: #7f8c8d;
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
            <h1>🏢 Reporte de Empresas con Estudiantes</h1>
            <a href="/SIEP/src/Controllers/ReportController.php?action=dashboard" class="btn-back">← Volver</a>
        </div>
        
        <!-- Resumen -->
        <div class="summary">
            <div class="summary-item">
                <h3>Empresas Activas</h3>
                <div class="number"><?php echo count($empresas); ?></div>
            </div>
            <div class="summary-item">
                <h3>Total Estudiantes Activos</h3>
                <div class="number"><?php echo array_sum(array_column($empresas, 'estudiantes_activos')); ?></div>
            </div>
            <div class="summary-item">
                <h3>Total Completados</h3>
                <div class="number"><?php echo array_sum(array_column($empresas, 'estudiantes_completados')); ?></div>
            </div>
        </div>
        
        <!-- Tabla -->
        <?php if (!empty($empresas)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Contacto</th>
                            <th>Estudiantes Activos</th>
                            <th>Estudiantes Completados</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empresas as $empresa): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($empresa['empresa']); ?></strong>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        📧 <?php echo htmlspecialchars($empresa['contacto_email']); ?><br>
                                        📞 <?php echo htmlspecialchars($empresa['contacto_telefono']); ?>
                                    </div>
                                </td>
                                <td class="stat-col">
                                    <span class="stat-active">
                                        <?php echo $empresa['estudiantes_activos']; ?>
                                    </span>
                                </td>
                                <td class="stat-col">
                                    <span class="stat-completed">
                                        <?php echo $empresa['estudiantes_completados']; ?>
                                    </span>
                                </td>
                                <td class="stat-col">
                                    <span class="stat-total">
                                        <?php echo $empresa['total_estudiantes']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-results">
                <div class="no-results-icon">🏢</div>
                <h3>No hay empresas con estudiantes</h3>
                <p>Actualmente no existen empresas con estudiantes asignados.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>