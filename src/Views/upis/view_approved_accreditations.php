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
    <link rel="stylesheet" href="/SIEP/public/css/upis.css">

</head>

<body>
    <!-- Encabezado bonito -->
    <div class="upis-header">
        <h1>Panel de Administración de UPIS</h1>
    </div>

    <div class="container">
        <div class="page-header">
            <h1>✅ Acreditaciones Aprobadas</h1>
        </div>

        <a href="/SIEP/public/index.php?action=upisDashboard" class="logout-btn">← Volver al Dashboard</a><br><br>

        

        <!-- Tabs -->
        <div class="tabs">
            <a href="/SIEP/public/index.php?action=reviewAccreditations" class="tab-button">
                📋 Pendientes
            </a>
            <a href="/SIEP/public/index.php?action=viewApprovedAccreditations" class="tab-button active">
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
                    <?php echo count(array_filter($approvedAccreditations, function ($acc) {
                        return $acc['tipo_acreditacion'] === 'A';
                    })); ?>
                </div>
                <div class="stat-label">Tipo A</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo count(array_filter($approvedAccreditations, function ($acc) {
                        return $acc['tipo_acreditacion'] === 'B';
                    })); ?>
                </div>
                <div class="stat-label">Tipo B</div>
            </div>
        </div>

        <!-- Búsqueda -->
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="🔍 Buscar por nombre, boleta o empresa..."
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
                                    <span
                                        class="status-badge <?php echo $acc['tipo_acreditacion'] === 'A' ? 'status-tipo-a' : 'status-tipo-b'; ?>">
                                        Tipo <?php echo $acc['tipo_acreditacion']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($acc['reviewed_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- ✅ BOTÓN ÚNICO: Abre el PDF directamente (se puede ver y descargar desde ahí) -->
                                        <a href="/SIEP/public/index.php?action=downloadAccreditationPDF&id=<?php echo $acc['id']; ?>"
                                            class="btn-small btn-view" title="Ver/Descargar PDF con toda la información"
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