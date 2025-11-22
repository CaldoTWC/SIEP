<?php
// Archivo: src/Views/upis/view_approved_accreditations.php
// Vista de Acreditaciones Aprobadas con descarga de información

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acreditaciones Aprobadas - UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: white;
            color: #11998e;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: transform 0.2s;
        }

        .back-link:hover {
            transform: translateX(-5px);
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-button {
            padding: 12px 24px;
            background-color: white;
            color: #11998e;
            border: 2px solid white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
        }

        .tab-button.active {
            background-color: #11998e;
            color: white;
        }

        .tab-button:hover {
            transform: translateY(-2px);
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #11998e;
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: bold;
        }

        .status-approved {
            background-color: #28a745;
            color: white;
        }

        .status-tipo-a {
            background-color: #ff6b6b;
            color: white;
        }

        .status-tipo-b {
            background-color: #4ecdc4;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-small {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            font-size: 0.9em;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-view {
            background-color: #007bff;
            color: white;
        }

        .btn-view:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .btn-download {
            background-color: #28a745;
            color: white;
        }

        .btn-download:hover {
            background-color: #218838;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state h2 {
            color: #11998e;
            margin-bottom: 10px;
        }

        .search-box {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
        }

        .search-box input {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1em;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #11998e;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/SIEP/public/index.php?action=upisDashboard" class="back-link">← Volver al Dashboard</a>
        
        <h1>✅ Acreditaciones Aprobadas</h1>

        <!-- Tabs -->
        <div class="tabs">
            <a href="/SIEP/public/index.php?action=reviewAccreditations" 
               class="tab-button">
                📋 Pendientes
            </a>
            <a href="/SIEP/public/index.php?action=viewApprovedAccreditations" 
               class="tab-button active">
                ✅ Aprobadas
            </a>
        </div>

        <!-- Estadísticas -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($approvedAccreditations); ?></div>
                <div class="stat-label">Total Aprobadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo count(array_filter($approvedAccreditations, function($acc) { 
                        return $acc['tipo_acreditacion'] === 'A'; 
                    })); ?>
                </div>
                <div class="stat-label">Tipo A</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo count(array_filter($approvedAccreditations, function($acc) { 
                        return $acc['tipo_acreditacion'] === 'B'; 
                    })); ?>
                </div>
                <div class="stat-label">Tipo B</div>
            </div>
        </div>

        <!-- Búsqueda -->
        <div class="search-box">
            <input type="text" 
                   id="searchInput" 
                   placeholder="🔍 Buscar por nombre, boleta o empresa..."
                   onkeyup="filterTable()">
        </div>

        <?php if (empty($approvedAccreditations)): ?>
            <div class="table-container">
                <div class="empty-state">
                    <h2>📭 No hay acreditaciones aprobadas</h2>
                    <p>Las acreditaciones aprobadas aparecerán aquí.</p>
                </div>
            </div>
        <?php else: ?>
            
            <div class="table-container">
                <table id="accreditationsTable">
                    <thead>
                        <tr>
                            <th>Boleta</th>
                            <th>Estudiante</th>
                            <th>Carrera</th>
                            <th>Empresa</th>
                            <th>Tipo</th>
                            <th>Fecha Aprobación</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approvedAccreditations as $acc): 
                            $metadata = json_decode($acc['metadata'], true);
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($acc['boleta']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($acc['first_name'] . ' ' . $acc['last_name_p'] . ' ' . $acc['last_name_m']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($acc['career']); ?></td>
                                <td><?php echo htmlspecialchars($acc['empresa_nombre']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $acc['tipo_acreditacion'] === 'A' ? 'status-tipo-a' : 'status-tipo-b'; ?>">
                                        Tipo <?php echo $acc['tipo_acreditacion']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($acc['reviewed_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- ✅ BOTÓN ÚNICO: Abre el PDF directamente (se puede ver y descargar desde ahí) -->
                                        <a href="/SIEP/public/index.php?action=downloadAccreditationPDF&id=<?php echo $acc['id']; ?>" 
                                           class="btn-small btn-view"
                                           title="Ver/Descargar PDF con toda la información"
                                           target="_blank">
                                            📄 Ver Documento
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </div>

    <script>
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('accreditationsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const row = tr[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const textValue = cell.textContent || cell.innerText;
                        if (textValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                row.style.display = found ? '' : 'none';
            }
        }
    </script>
</body>
</html>