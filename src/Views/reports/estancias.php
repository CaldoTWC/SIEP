<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Estancias - SIEP UPIS</title>
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
            border-bottom: 3px solid #27ae60;
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
        
        .filters {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .filters h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            color: #34495e;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .filter-group select,
        .filter-group input {
            padding: 10px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #27ae60;
            color: white;
        }
        
        .btn-primary:hover {
            background: #229954;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .btn-export {
            background: #e74c3c;
            color: white;
        }
        
        .btn-export:hover {
            background: #c0392b;
        }
        
        .btn-excel {
            background: #27ae60;
            color: white;
        }
        
        .btn-excel:hover {
            background: #229954;
        }
        
        .export-section {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .results-info {
            background: #3498db;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }
        
        thead {
            background: #27ae60;
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
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
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
            <h1>📋 Historial de Estancias Completadas</h1>
            <a href="/SIEP/src/Controllers/ReportController.php?action=dashboard" class="btn-back">← Volver</a>
        </div>
        
        <!-- Filtros -->
        <div class="filters">
            <h3>🔍 Filtros de Búsqueda</h3>
            <form method="GET" action="">
                <input type="hidden" name="action" value="estancias">
                
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="carrera">Carrera:</label>
                        <select name="carrera" id="carrera">
                            <option value="">Todas las carreras</option>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?php echo htmlspecialchars($carrera); ?>" 
                                    <?php echo (isset($_GET['carrera']) && $_GET['carrera'] == $carrera) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($carrera); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="fecha_inicio">Fecha Inicio:</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" 
                               value="<?php echo htmlspecialchars($_GET['fecha_inicio'] ?? ''); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="fecha_fin">Fecha Fin:</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" 
                               value="<?php echo htmlspecialchars($_GET['fecha_fin'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary">🔍 Buscar</button>
                    <a href="?action=estancias" class="btn btn-secondary">🔄 Limpiar Filtros</a>
                </div>
            </form>
        </div>
        
        <!-- Botones de exportación -->
        <?php if (!empty($estancias)): ?>
            <div class="export-section">
                <a href="?action=estancias&export=pdf<?php echo isset($_GET['carrera']) ? '&carrera=' . urlencode($_GET['carrera']) : ''; ?><?php echo isset($_GET['fecha_inicio']) ? '&fecha_inicio=' . $_GET['fecha_inicio'] : ''; ?><?php echo isset($_GET['fecha_fin']) ? '&fecha_fin=' . $_GET['fecha_fin'] : ''; ?>" 
                   class="btn btn-export">📄 Exportar a PDF</a>
                
                <a href="?action=estancias&export=excel<?php echo isset($_GET['carrera']) ? '&carrera=' . urlencode($_GET['carrera']) : ''; ?><?php echo isset($_GET['fecha_inicio']) ? '&fecha_inicio=' . $_GET['fecha_inicio'] : ''; ?><?php echo isset($_GET['fecha_fin']) ? '&fecha_fin=' . $_GET['fecha_fin'] : ''; ?>" 
                   class="btn btn-excel">📊 Exportar a Excel</a>
            </div>
        <?php endif; ?>
        
        <!-- Información de resultados -->
        <div class="results-info">
            <div>
                <strong>Total de registros encontrados:</strong> <?php echo count($estancias); ?>
            </div>
            <div>
                Generado: <?php echo date('d/m/Y H:i:s'); ?>
            </div>
        </div>
        
        <!-- Tabla de resultados -->
        <?php if (!empty($estancias)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Boleta</th>
                            <th>Estudiante</th>
                            <th>Carrera</th>
                            <th>Empresa</th>
                            <th>Tipo Documento</th>
                            <th>Estatus</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Finalización</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estancias as $estancia): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($estancia['boleta']); ?></strong></td>
                                <td><?php echo htmlspecialchars($estancia['estudiante']); ?></td>
                                <td><?php echo htmlspecialchars($estancia['carrera']); ?></td>
                                <td><?php echo htmlspecialchars($estancia['empresa']); ?></td>
                                <td><?php echo htmlspecialchars($estancia['tipo_documento']); ?></td>
                                <td><span class="badge badge-completed">✓ Completado</span></td>
                                <td><?php echo date('d/m/Y', strtotime($estancia['fecha_inicio'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($estancia['fecha_actualizacion'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-results">
                <div class="no-results-icon">📭</div>
                <h3>No se encontraron resultados</h3>
                <p>Intenta ajustar los filtros de búsqueda.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>