<?php
// Archivo: src/Views/upis/review_companies.php (Versión Final con Detalles Completos)

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']); 
// La variable $pendingCompanies viene del controlador y ahora contiene todos los campos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisar Empresas</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Revisión de Empresas Pendientes</h1>
        <a href="/SIEP/public/index.php?action=upisDashboard" style="display: inline-block; margin-bottom: 20px;">&larr; Volver al Panel Principal</a>

        <?php if (empty($pendingCompanies)): ?>
            <p>No hay empresas pendientes de aprobación en este momento.</p>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Empresa (Razón Social)</th>
                            <th>Contacto Principal</th>
                            <th>Fecha de Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingCompanies as $index => $company): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($company['company_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($company['commercial_name']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($company['first_name'] . ' ' . $company['last_name_p']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($company['created_at'])); ?></td>
                                <td class="actions">
                                    <!-- Botón que abre el modal -->
                                    <button class="btn-details" onclick="openModal('modal-<?php echo $index; ?>')">Ver Detalles Completos</button>
                                    <hr style="margin: 8px 0;">
                                    <a href="/SIEP/public/index.php?action=approveCompany&id=<?php echo $company['user_id']; ?>" class="approve">Aprobar</a>
                                    <a href="/SIEP/public/index.php?action=rejectCompany&id=<?php echo $company['user_id']; ?>" class="reject">Rechazar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- ESTRUCTURA DE LOS MODALES ACTUALIZADA CON TODOS LOS CAMPOS -->
    <?php if (!empty($pendingCompanies)): ?>
        <?php foreach ($pendingCompanies as $index => $company): ?>
            <div id="modal-<?php echo $index; ?>" class="modal">
                <div class="modal-content">
                    <span class="close-button" onclick="closeModal('modal-<?php echo $index; ?>')">&times;</span>
                    <h2>Detalles de: <?php echo htmlspecialchars($company['company_name']); ?></h2>
                    
                    <div class="modal-details">
    <h3>Información de la Empresa</h3>
    <p><strong>Nombre Comercial:</strong> <?php echo htmlspecialchars($company['commercial_name'] ?? 'No disponible'); ?></p>
    <p><strong>RFC:</strong> <?php echo htmlspecialchars($company['rfc'] ?? 'No disponible'); ?></p>
    <p><strong>Giro:</strong> <?php echo htmlspecialchars($company['business_area'] ?? 'No disponible'); ?></p>
    <p><strong>Tipo:</strong> <?php echo htmlspecialchars($company['company_type'] ?? 'No disponible'); ?></p>
    <p><strong>Descripción:</strong> <?php 
        echo isset($company['company_description']) && $company['company_description'] !== ''
            ? nl2br(htmlspecialchars($company['company_description']))
            : 'No hay descripción registrada.';
    ?></p>
    <p><strong>No. Empleados:</strong> <?php echo htmlspecialchars($company['employee_count'] ?? 'No especificado'); ?></p>
    <p><strong>Programas para Estudiantes:</strong> <?php echo htmlspecialchars($company['student_programs'] ?? 'No registrado'); ?></p>

    <h3>Información de Contacto</h3>
    <p><strong>Nombre Completo:</strong> <?php echo htmlspecialchars(($company['first_name'] ?? '') . ' ' . ($company['last_name_p'] ?? '') . ' ' . ($company['last_name_m'] ?? '')); ?></p>
    <p><strong>Puesto:</strong> <?php echo htmlspecialchars($company['contact_person_position'] ?? 'No especificado'); ?></p>
    <p><strong>Correo:</strong> <?php echo htmlspecialchars($company['email'] ?? 'No disponible'); ?></p>
    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($company['phone_number'] ?? 'No disponible'); ?></p>
</div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- JAVASCRIPT PARA CONTROLAR LOS MODALES (sin cambios) -->
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>