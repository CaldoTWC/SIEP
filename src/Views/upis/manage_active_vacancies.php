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
    <title>Gestionar Vacantes Activas - UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/upis.css">

</head>
<body>
    <!-- Encabezado bonito -->
    <div class="upis-header">
        <h1>Panel de Administración de UPIS</h1>
    </div>
    <div class="container">
        <div class="page-header">
            <h1>⚙️ Gestionar Vacantes Activas</h1>
            <p>Supervisa y desactiva vacantes publicadas en caso de incumplimiento</p>
        </div>


        <a href="/SIEP/public/index.php?action=vacancyHub" class="logout-btn">← Volver al Hub</a>

        <div class="alert-box">
            <strong>⚠️ Atención:</strong> Solo tumba una vacante si hay reportes de incumplimiento por parte de la
            empresa (horarios no acordados, actividades no relacionadas, etc.).
        </div>

        <?php if (empty($activeVacancies)): ?>
            <p style="margin-top: 30px;">No hay vacantes activas en este momento.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Empresa</th>
                        <th>Vacante</th>
                        <th>Plazas</th>
                        <th>Apoyo</th>
                        <th>Modalidad</th>
                        <th>Aprobada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activeVacancies as $vacancy): ?>
                        <tr>
                            <td><?php echo $vacancy['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($vacancy['company_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($vacancy['company_email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($vacancy['title']); ?></td>
                            <td><?php echo $vacancy['num_vacancies']; ?></td>
                            <td>$<?php echo number_format($vacancy['economic_support'], 2); ?></td>
                            <td><?php echo htmlspecialchars($vacancy['modality']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($vacancy['approved_at'])); ?></td>
                            <td>
                                <a href="/SIEP/public/index.php?action=showVacancyDetails&id=<?php echo $vacancy['id']; ?>"
                                    class="btn" style="font-size: 13px; padding: 6px 12px;">👁️ Ver</a>
                                <button
                                    onclick="showTakedownModal(<?php echo $vacancy['id']; ?>, '<?php echo htmlspecialchars($vacancy['title'], ENT_QUOTES); ?>')"
                                    class="btn-takedown">⚠️ Tumbar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="/SIEP/public/index.php?action=vacancyHub" class="logout-btn">← Volver al Hub</a>

    </div>
    
    <script>
        function showTakedownModal(id, title) {
            const html = `
                <div id="modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
                    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%;">
                        <h3 style="margin-top: 0; color: #dc3545;">⚠️ Tumbar Vacante Activa</h3>
                        <div style="background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; color: #721c24;">
                            <strong>⚠️ Acción crítica:</strong> Esta vacante será desactivada inmediatamente
                        </div>
                        <p><strong>Vacante:</strong> ${title}</p>
                        
                        <form method="POST" action="/SIEP/public/index.php?action=takedownVacancy">
                            <input type="hidden" name="vacancy_id" value="${id}">
                            
                            <label style="display: block; margin: 20px 0 10px 0; font-weight: bold;">
                                Justificación obligatoria: <span style="color: red;">*</span>
                            </label>
                            <textarea name="rejection_notes" rows="4" required 
                                      placeholder="Explica el motivo de la desactivación (ej: Estudiantes reportaron que la empresa no cumple con los horarios acordados...)"
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                            
                            <div style="margin-top: 20px; text-align: right;">
                                <button type="button" onclick="document.getElementById('modal').remove()" 
                                        style="padding: 10px 20px; margin-right: 10px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                    ⚠️ Confirmar Desactivación
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