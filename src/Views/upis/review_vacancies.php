<?php
// Archivo: src/Views/upis/review_vacancies.php (Versión con Modal)
require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisar Vacantes</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Revisión de Vacantes Pendientes</h1>
        <a href="/SIEP/public/index.php?action=upisDashboard">&larr; Volver al Panel Principal</a>

        <?php if (empty($pendingVacancies)): ?>
            <p>No hay vacantes pendientes de revisión.</p>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Título de la Vacante</th>
                            <th>Empresa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingVacancies as $index => $vacancy): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vacancy['title']); ?></td>
                                <td><?php echo htmlspecialchars($vacancy['company_name']); ?></td>
                                <td class="actions">
                                    <button class="btn-details" onclick="openModal('modal-vac-<?php echo $index; ?>')">Ver Detalles</button>
                                    <a href="/SIEP/public/index.php?action=approveVacancy&id=<?php echo $vacancy['id']; ?>" class="approve">Aprobar</a>
                                    <a href="/SIEP/public/index.php?action=rejectVacancy&id=<?php echo $vacancy['id']; ?>" class="reject" onclick="return confirm('¿Estás seguro de que deseas eliminar esta vacante?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- ESTRUCTURA DE LOS MODALES PARA VACANTES -->
    <?php if (!empty($pendingVacancies)): ?>
        <?php foreach ($pendingVacancies as $index => $vacancy): ?>
            <div id="modal-vac-<?php echo $index; ?>" class="modal">
                <div class="modal-content">
                    <span class="close-button" onclick="closeModal('modal-vac-<?php echo $index; ?>')">&times;</span>
                    <h2>Detalles de la Vacante</h2>
                    <div class="modal-details">
                        <p><strong>Título:</strong> <?php echo htmlspecialchars($vacancy['title']); ?></p>
                        <p><strong>Empresa:</strong> <?php echo htmlspecialchars($vacancy['company_name']); ?></p>
                        <p><strong>Modalidad:</strong> <?php echo htmlspecialchars($vacancy['modality']); ?></p>
                        <hr>
                        <p><strong>Descripción del Perfil:</strong><br><?php echo nl2br(htmlspecialchars($vacancy['description'])); ?></p>
                        <p><strong>Actividades a Realizar:</strong><br><?php echo nl2br(htmlspecialchars($vacancy['activities'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- JAVASCRIPT PARA CONTROLAR LOS MODALES (reutilizado) -->
    <script>
        function openModal(modalId) { document.getElementById(modalId).style.display = 'block'; }
        function closeModal(modalId) { document.getElementById(modalId).style.display = 'none'; }
        window.onclick = function(event) { if (event.target.classList.contains('modal')) { event.target.style.display = 'none'; } }
    </script>
</body>
</html>