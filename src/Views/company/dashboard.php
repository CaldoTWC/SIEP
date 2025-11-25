<?php
require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['company']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Empresa</title>
    <link rel="stylesheet" href="/SIEP/public/css/company.css">

</head>

<body>
    <!-- BARRA DE NAVEGACIÓN -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="/SIEP/public/index.php" class="nav-logo">SIEP</a>
            <ul class="nav-menu">
                <li class="nav-item"><a href="#hero" class="nav-link">Inicio</a></li>
                <li class="nav-item"><a href="#user-section" class="nav-link">Usuarios</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showLogin" class="nav-link btn-nav">Iniciar
                        Sesión</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showRegisterSelection"
                        class="nav-link btn-nav">Registrarse</a></li>
            </ul>
        </div>
    </nav>


    <div class="container">
        <div class="page-header">
            <h1>Panel de Administración</h1>
            
        </div>

        <div class="task-grid">
            <a href="/SIEP/public/index.php?action=showPostVacancyForm" class="task-card">
                <div class="info">
                    <h3>Publicar Nueva Vacante</h3>
                </div>
                <div class="counter">💼</div>
            </a>

            <a href="/SIEP/public/index.php?action=showAcceptanceLetterForm" class="task-card">
                <div class="info">
                    <h3>Generar Carta de Aceptación</h3>
                </div>
                <div class="counter">💼</div>
            </a>

            <a href="/SIEP/public/index.php?action=showValidationLetterForm" class="task-card">
                <div class="info">
                    <h3>Generar Constancia de Validación</h3>
                </div>
                <div class="counter">💼</div>
            </a>

            <a href="/SIEP/public/index.php?action=showChangePasswordForm" class="task-card">
                <div class="info">
                    <h3>Cambiar Contraseña</h3>
                </div>
                <div class="counter">💼</div>
            </a>

            <a href="/SIEP/public/index.php?action=showAllNotifications" class="task-card">
                <div class="info">
                    <h3>🔔 Mis Notificaciones</h3>
                </div>
                <div class="counter">💼</div>
                <?php
                // Mostrar contador de no leídas
                require_once(__DIR__ . '/../../Models/Notification.php');
                require_once(__DIR__ . '/../../Config/Database.php');
                $database = Database::getInstance();
                $notificationModel = new Notification($database->getConnection());
                $unreadCount = $notificationModel->countUnread($_SESSION['user_id']);
                if ($unreadCount > 0) {
                    echo '<span style="background: #fff; color: #ff6b6b; padding: 2px 8px; border-radius: 10px; margin-left: 8px; font-weight: bold;">' . $unreadCount . '</span>';
                }
                ?>
            </a>


            <a href="/SIEP/public/index.php?action=logout" class="logout-btn">Cerrar Sesión</a>
        </div><br>

        <div class="page-header">
            <h2>Mis Vacantes Publicadas</h2>
        </div>


        <!-- Cuadro de ayuda -->
        <div class="help-box">
            <strong>ℹ️ Guía rápida de acciones:</strong><br><br>
            <strong>✔️ Completar:</strong> Usa cuando llenaste todos los cupos o la estancia concluyó
            exitosamente.<br>
            <strong>❌ Cancelar:</strong> Usa cuando hay cambios de presupuesto, proyecto cancelado o
            reestructuración.
        </div>

        <?php
        // Filtrar solo vacantes activas y pendientes (excluir completed y rejected)
        $active_vacancies = array_filter($vacancies, function ($v) {
            return in_array($v['status'], ['pending', 'approved']);
        });
        ?>
        <div class="companyHubTable">
            <?php if (empty($active_vacancies)): ?>
                <p>Aún no has publicado ninguna vacante activa. ¡Crea una nueva!</p>
            <?php else: ?>
                <table class="companyHub">
                    <thead>
                        <tr>
                            <th>Título del Puesto</th>
                            <th>Modalidad</th>
                            <th>Plazas</th>
                            <th>Apoyo Mensual</th>
                            <th>Publicada</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($active_vacancies as $vacancy): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($vacancy['title']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($vacancy['modality']); ?></td>
                                <td><?php echo $vacancy['num_vacancies']; ?></td>
                                <td>$<?php echo number_format($vacancy['economic_support'], 2); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($vacancy['posted_at'])); ?></td>
                                <td>
                                    <?php
                                    if ($vacancy['status'] === 'pending') {
                                        echo '<span class="status pending">⏳ Pendiente</span>';
                                    } else {
                                        echo '<span class="status approved">✅ Activa</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="/SIEP/public/index.php?action=showVacancyDetails&id=<?php echo $vacancy['id']; ?>"
                                        class="btn-small btn-view">👁️ Ver</a>

                                    <?php if ($vacancy['status'] === 'approved'): ?>
                                        <button
                                            onclick="showCompleteModal(<?php echo $vacancy['id']; ?>, '<?php echo htmlspecialchars($vacancy['title'], ENT_QUOTES); ?>')"
                                            class="btn-small btn-complete">✔️ Completar</button>
                                        <button
                                            onclick="showCancelModal(<?php echo $vacancy['id']; ?>, '<?php echo htmlspecialchars($vacancy['title'], ENT_QUOTES); ?>')"
                                            class="btn-small btn-cancel">❌ Cancelar</button>
                                    <?php endif; ?>

                                    <?php if ($vacancy['status'] === 'pending'): ?>
                                        <button
                                            onclick="showCancelModal(<?php echo $vacancy['id']; ?>, '<?php echo htmlspecialchars($vacancy['title'], ENT_QUOTES); ?>')"
                                            class="btn-small btn-cancel">❌ Cancelar</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>




    <script>
        // Modal para completar vacante
        function showCompleteModal(id, title) {
            const html = `
                <div id="modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
                    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%;">
                        <h3 style="margin-top: 0; color: #17a2b8;">✔️ Marcar como Completada</h3>
                        <p><strong>Vacante:</strong> ${title}</p>
                        
                        <form method="POST" action="/SIEP/public/index.php?action=completeVacancy">
                            <input type="hidden" name="vacancy_id" value="${id}">
                            
                            <label style="display: block; margin: 15px 0 10px 0; font-weight: bold;">
                                ¿Por qué motivo? <span style="color: red;">*</span>
                            </label>
                            
                            <label style="display: block; margin: 8px 0;">
                                <input type="radio" name="completion_reason" value="Cupos llenos" required> 
                                Todos los cupos fueron llenados
                            </label>
                            <label style="display: block; margin: 8px 0;">
                                <input type="radio" name="completion_reason" value="Estancia concluida" required> 
                                Estancia concluyó satisfactoriamente
                            </label>
                            <label style="display: block; margin: 8px 0;">
                                <input type="radio" name="completion_reason" value="No se requieren más" required> 
                                Ya no se requieren más candidatos
                            </label>
                            <label style="display: block; margin: 8px 0;">
                                <input type="radio" name="completion_reason" value="Otro" required> 
                                Otro
                            </label>
                            
                            <label style="display: block; margin: 20px 0 5px 0; font-weight: bold;">
                                Comentarios adicionales (opcional):
                            </label>
                            <textarea name="completion_notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                            
                            <div style="margin-top: 20px; text-align: right;">
                                <button type="button" onclick="document.getElementById('modal').remove()" 
                                        style="padding: 10px 20px; margin-right: 10px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                    ✔️ Confirmar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', html);
        }

        // Modal para cancelar vacante
        function showCancelModal(id, title) {
            const html = `
                <div id="modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
                    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%;">
                        <h3 style="margin-top: 0; color: #dc3545;">❌ Cancelar Vacante</h3>
                        <div style="background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; color: #856404;">
                            ⚠️ Esta acción no se puede deshacer
                        </div>
                        <p><strong>Vacante:</strong> ${title}</p>
                        
                        <form method="POST" action="/SIEP/public/index.php?action=deleteVacancy">
                            <input type="hidden" name="vacancy_id" value="${id}">
                            
                            <label style="display: block; margin: 15px 0 10px 0; font-weight: bold;">
                                Motivo: <span style="color: red;">*</span>
                            </label>
                            
                            <label style="display: block; margin: 8px 0;">
                                <input type="radio" name="rejection_reason" value="Cambio de presupuesto" required> 
                                Cambio de presupuesto
                            </label>
                            <label style="display: block; margin: 8px 0;">
                                <input type="radio" name="rejection_reason" value="Proyecto cancelado" required> 
                                Proyecto cancelado/pospuesto
                            </label>
                            <label style="display: block; margin: 8px 0;">
                                <input type="radio" name="rejection_reason" value="Reestructuración" required> 
                                Reestructuración interna
                            </label>
                            <label style="display: block; margin: 8px 0;">
                                <input type="radio" name="rejection_reason" value="No encontró candidatos" required> 
                                No se encontraron candidatos
                            </label>
                            <label style="display: block; margin: 8px 0;">
                                <input type="radio" name="rejection_reason" value="Otro" required> 
                                Otro
                            </label>
                            
                            <label style="display: block; margin: 20px 0 5px 0; font-weight: bold;">
                                Explica la situación: <span style="color: red;">*</span>
                            </label>
                            <textarea name="rejection_notes" rows="3" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                            
                            <div style="margin-top: 20px; text-align: right;">
                                <button type="button" onclick="document.getElementById('modal').remove()" 
                                        style="padding: 10px 20px; margin-right: 10px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                    Volver
                                </button>
                                <button type="submit" 
                                        style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                    ❌ Confirmar
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