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
    <title>Papelera de Vacantes - UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; font-size: 13px; }
        th { background-color: #dc3545; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }
        
        .badge.upis-review { background: #6c757d; }
        .badge.company-cancel { background: #ffc107; color: #333; }
        .badge.upis-takedown { background: #dc3545; }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-restore { background: #28a745; color: white; }
        .btn-delete { background: #6c757d; color: white; }
        
        .stats-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #dc3545;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗑️ Papelera de Vacantes</h1>
        <p>Restaura vacantes rechazadas o elimínalas permanentemente</p>
        
        <a href="/SIEP/public/index.php?action=vacancyHub" class="btn">← Volver al Hub</a>
        
        <!-- Estadísticas de la Papelera -->
        <div class="stats-box">
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats['total'] ?? 0; ?></div>
                <div class="stat-label">Total en Papelera</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats['upis_review'] ?? 0; ?></div>
                <div class="stat-label">Rechazadas en Revisión</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats['company_cancel'] ?? 0; ?></div>
                <div class="stat-label">Canceladas por Empresa</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats['upis_takedown'] ?? 0; ?></div>
                <div class="stat-label">Tumbadas por UPIS</div>
            </div>
        </div>
        
        <?php if (empty($rejectedVacancies)): ?>
            <p style="margin-top: 30px; text-align: center; color: #666;">
                ✅ La papelera está vacía
            </p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Empresa</th>
                        <th>Vacante</th>
                        <th>Origen</th>
                        <th>Motivo</th>
                        <th>Justificación</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rejectedVacancies as $vacancy): ?>
                        <tr>
                            <td><?php echo $vacancy['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($vacancy['company_name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($vacancy['title']); ?></td>
                            <td>
                                <?php
                                $source = $vacancy['rejection_source'] ?? 'unknown';
                                if ($source === 'upis_review') {
                                    echo '<span class="badge upis-review">UPIS - Revisión</span>';
                                } elseif ($source === 'company_cancel') {
                                    echo '<span class="badge company-cancel">Empresa Canceló</span>';
                                } elseif ($source === 'upis_takedown') {
                                    echo '<span class="badge upis-takedown">UPIS - Tumbada</span>';
                                } else {
                                    echo '<span class="badge upis-review">Desconocido</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                echo htmlspecialchars($vacancy['rejection_reason'] ?? 'N/A');
                                ?>
                            </td>
                            <td>
                                <small>
                                    <?php 
                                    $notes = $vacancy['rejection_notes'] ?? 'Sin justificación';
                                    echo strlen($notes) > 100 
                                        ? htmlspecialchars(substr($notes, 0, 100)) . '...' 
                                        : htmlspecialchars($notes);
                                    ?>
                                </small>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($vacancy['approved_at'])); ?></td>
                            <td>
                                <form method="POST" action="/SIEP/public/index.php?action=restoreVacancy" style="display: inline;">
                                    <input type="hidden" name="vacancy_id" value="<?php echo $vacancy['id']; ?>">
                                    <button type="submit" class="btn-small btn-restore" 
                                            onclick="return confirm('¿Restaurar esta vacante a estado pendiente?')">
                                        ♻️ Restaurar
                                    </button>
                                </form>
                                
                                <button onclick="showDeleteModal(<?php echo $vacancy['id']; ?>, '<?php echo htmlspecialchars($vacancy['title'], ENT_QUOTES); ?>')" 
                                        class="btn-small btn-delete">
                                    💀 Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
    </div>
    
    <script>
        function showDeleteModal(id, title) {
            const html = `
                <div id="modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
                    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%;">
                        <h3 style="margin-top: 0; color: #dc3545;">💀 Eliminar Permanentemente</h3>
                        <div style="background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0; color: #721c24;">
                            <strong>⚠️ ADVERTENCIA:</strong> Esta acción NO SE PUEDE DESHACER.<br>
                            La vacante será eliminada completamente de la base de datos.
                        </div>
                        <p><strong>Vacante:</strong> ${title}</p>
                        
                        <form method="POST" action="/SIEP/public/index.php?action=hardDeleteVacancy">
                            <input type="hidden" name="vacancy_id" value="${id}">
                            
                            <div style="margin-top: 20px; text-align: right;">
                                <button type="button" onclick="document.getElementById('modal').remove()" 
                                        style="padding: 10px 20px; margin-right: 10px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                    💀 Eliminar Permanentemente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', html);
        }
    </script>
</body>
</html>