<?php
// Archivo: src/Views/upis/review_companies.php (Versión con notificaciones)

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']); 
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
                                    
                                    <!-- Botón de Aprobar con modal -->
                                    <button class="approve" onclick="showApproveModal(<?php echo $company['user_id']; ?>, '<?php echo htmlspecialchars($company['company_name']); ?>')">Aprobar</button>
                                    
                                    <!-- Botón de Rechazar con modal -->
                                    <button class="reject" onclick="showRejectModal(<?php echo $company['user_id']; ?>, '<?php echo htmlspecialchars($company['company_name']); ?>')">Rechazar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- MODALES DE DETALLES -->
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

    <!-- ============================================ -->
    <!-- NUEVOS MODALES PARA APROBAR/RECHAZAR -->
    <!-- ============================================ -->
    
    <!-- Modal para Aprobar Empresa -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeApproveModal()">&times;</span>
            <h2>Aprobar Empresa</h2>
            
            <form method="POST" action="/SIEP/public/index.php?action=approveCompany">
                <input type="hidden" name="company_id" id="approve_company_id">
                
                <div class="modal-details">
                    <p>¿Está seguro de aprobar a la empresa <strong id="approve_company_name"></strong>?</p>
                    
                    <div class="form-group">
                        <label>Comentarios opcionales:</label>
                        <textarea name="comments" rows="3" placeholder="Ejemplo: Bienvenido al sistema SIEP. Su empresa ha sido aprobada exitosamente." style="width: 100%; padding: 8px;"></textarea>
                        <small style="color: #666;">Este mensaje será enviado por correo a la empresa.</small>
                    </div>
                </div>
                
                <div style="margin-top: 20px; text-align: right;">
                    <button type="button" onclick="closeApproveModal()" style="margin-right: 10px; padding: 8px 16px; background: #6c757d; color: white; border: none; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="padding: 8px 16px; background: #28a745; color: white; border: none; cursor: pointer;">✅ Aprobar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Rechazar Empresa -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeRejectModal()">&times;</span>
            <h2 style="color: #dc3545;">Rechazar Empresa</h2>
            
            <form method="POST" action="/SIEP/public/index.php?action=rejectCompany">
                <input type="hidden" name="company_id" id="reject_company_id">
                
                <div class="modal-details">
                    <p>¿Está seguro de rechazar a la empresa <strong id="reject_company_name"></strong>?</p>
                    
                    <div class="form-group">
                        <label style="color: #dc3545;">Razón del rechazo (obligatorio):</label>
                        <textarea name="comments" rows="4" required placeholder="Ejemplo: El RFC proporcionado no es válido. Por favor, verifica los datos e intenta de nuevo." style="width: 100%; padding: 8px; border: 2px solid #dc3545;"></textarea>
                        <small style="color: #666;">Esta razón será enviada por correo a la empresa.</small>
                    </div>
                </div>
                
                <div style="margin-top: 20px; text-align: right;">
                    <button type="button" onclick="closeRejectModal()" style="margin-right: 10px; padding: 8px 16px; background: #6c757d; color: white; border: none; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="padding: 8px 16px; background: #dc3545; color: white; border: none; cursor: pointer;">❌ Rechazar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT PARA CONTROLAR LOS MODALES -->
    <script>
        // Modales de detalles (original)
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // ============================================
        // NUEVAS FUNCIONES PARA APROBAR/RECHAZAR
        // ============================================
        
        // Modal de Aprobar
        function showApproveModal(companyId, companyName) {
            document.getElementById('approve_company_id').value = companyId;
            document.getElementById('approve_company_name').textContent = companyName;
            document.getElementById('approveModal').style.display = 'block';
        }
        
        function closeApproveModal() {
            document.getElementById('approveModal').style.display = 'none';
        }
        
        // Modal de Rechazar
        function showRejectModal(companyId, companyName) {
            document.getElementById('reject_company_id').value = companyId;
            document.getElementById('reject_company_name').textContent = companyName;
            document.getElementById('rejectModal').style.display = 'block';
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>