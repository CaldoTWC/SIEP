<?php
// Archivo: src/Views/upis/review_companies.php
// Versión: 3.0.0 - Modal mejorado con diseño profesional
// Fecha: 2025-10-29

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Empresas - SIEP UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/upis.css">

</head>

<body>
    <!-- Encabezado bonito -->
    <div class="upis-header">
        <h1>Panel de Administración de UPIS</h1>
    </div>
    <div class="container">

        <div class="page-header">
            <h1>🏢 Revisión de Empresas Pendientes</h1>
        </div>

        

        <a href="/SIEP/public/index.php?action=upisDashboard" class="logout-btn">← Volver al Panel Principal</a><br><br>

        <?php if (isset($_SESSION['success'])): ?>
            <div
                style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                <strong>✅ Éxito:</strong> <?php echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div
                style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #f44336;">
                <strong>❌ Error:</strong> <?php echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($pendingCompanies)): ?>
            <div
                style="text-align: center; padding: 60px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2 style="color: #6f1d33;">✅ No hay empresas pendientes de aprobación</h2>
                <p style="color: #666;">Todas las empresas han sido revisadas.</p>
            </div>
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
                                    <strong
                                        style="color: #6f1d33;"><?php echo htmlspecialchars($company['company_name']); ?></strong><br>
                                    <small
                                        style="color: #666;"><?php echo htmlspecialchars($company['commercial_name'] ?? 'Sin nombre comercial'); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($company['first_name'] . ' ' . $company['last_name_p']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($company['created_at'])); ?></td>
                                <td class="actions">
                                    <button class="btn-details" onclick="openModal('modal-<?php echo $index; ?>')">
                                        📋 Ver Detalles Completos
                                    </button>
                                    <hr style="margin: 4px 0; border: none; border-top: 1px solid #e0e0e0;">
                                    <button class="approve"
                                        onclick="showApproveModal(<?php echo $company['id']; ?>, '<?php echo htmlspecialchars($company['company_name'], ENT_QUOTES); ?>')">
                                        ✅ Aprobar
                                    </button>
                                    <button class="reject"
                                        onclick="showRejectModal(<?php echo $company['id']; ?>, '<?php echo htmlspecialchars($company['company_name'], ENT_QUOTES); ?>')">
                                        ❌ Rechazar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- ============================================ -->
    <!-- MODALES DE DETALLES DE EMPRESAS -->
    <!-- ============================================ -->
    <?php if (!empty($pendingCompanies)): ?>
        <?php foreach ($pendingCompanies as $index => $company): ?>
            <div id="modal-<?php echo $index; ?>" class="modal">
                <div class="modal-content">
                    <span class="close-button" onclick="closeModal('modal-<?php echo $index; ?>')">&times;</span>
                    <h2>Detalles de: <?php echo htmlspecialchars($company['company_name']); ?></h2>

                    <div class="modal-details">
                        <!-- SECCIÓN: INFORMACIÓN DE LA EMPRESA -->
                        <h3>🏢 Información de la Empresa</h3>

                        <div class="info-grid">
                            <div>Nombre Comercial:</div>
                            <div><?php echo htmlspecialchars($company['commercial_name'] ?? 'No disponible'); ?></div>

                            <div>RFC:</div>
                            <div><?php echo htmlspecialchars($company['rfc'] ?? 'No disponible'); ?></div>

                            <div>Giro:</div>
                            <div><?php echo htmlspecialchars($company['business_area'] ?? 'No disponible'); ?></div>

                            <div>Tipo:</div>
                            <div><?php echo htmlspecialchars($company['company_type'] ?? 'No disponible'); ?></div>

                            <div>No. Empleados:</div>
                            <div><?php echo htmlspecialchars($company['employee_count'] ?? 'No especificado'); ?></div>
                        </div>

                        <!-- DESCRIPCIÓN EN BLOQUE COMPLETO -->
                        <div class="info-block">
                            <strong>📝 Descripción de la Empresa:</strong>
                            <div class="info-block-content">
                                <?php
                                echo isset($company['company_description']) && trim($company['company_description']) !== ''
                                    ? nl2br(htmlspecialchars($company['company_description']))
                                    : '<em style="color: #999;">No hay descripción registrada.</em>';
                                ?>
                            </div>
                        </div>

                        <!-- ENLACES EN BLOQUES SEPARADOS -->
                        <div class="link-block">
                            <strong>🌐 Sitio Web:</strong>
                            <?php if (!empty($company['website'])): ?>
                                <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank"
                                    rel="noopener noreferrer">
                                    <?php echo htmlspecialchars($company['website']); ?>
                                </a>
                                <span style="color: #28a745; margin-left: 5px; font-size: 12px;">↗ Abrir en nueva pestaña</span>
                            <?php else: ?>
                                <div style="padding-left: 20px; color: #999;">No registrado</div>
                            <?php endif; ?>
                        </div>

                        <div class="link-block">
                            <strong>📄 Constancia de Situación Fiscal:</strong>
                            <?php if (!empty($company['tax_id_url'])): ?>
                                <a href="<?php echo htmlspecialchars($company['tax_id_url']); ?>" target="_blank"
                                    rel="noopener noreferrer">
                                    <?php echo htmlspecialchars($company['tax_id_url']); ?>
                                </a>
                                <span style="color: #28a745; margin-left: 5px; font-size: 12px;">↗ Abrir documento</span>
                            <?php else: ?>
                                <div style="padding-left: 20px; color: #999;">No registrado</div>
                            <?php endif; ?>
                        </div>

                        <!-- PROGRAMAS PARA ESTUDIANTES -->
                        <div class="programs-block">
                            <strong>🎓 Programas para Estudiantes:</strong>
                            <div style="color: #555;">
                                <?php echo htmlspecialchars($company['student_programs'] ?? 'No registrado'); ?>
                            </div>
                        </div>

                        <!-- SECCIÓN: INFORMACIÓN DE CONTACTO -->
                        <h3>👤 Información de Contacto</h3>

                        <div class="info-grid">
                            <div>Nombre Completo:</div>
                            <div>
                                <?php echo htmlspecialchars($company['first_name'] . ' ' . $company['last_name_p'] . ' ' . $company['last_name_m']); ?>
                            </div>

                            <div>Puesto:</div>
                            <div><?php echo htmlspecialchars($company['contact_person_position'] ?? 'No especificado'); ?></div>

                            <div>Correo:</div>
                            <div>
                                <a href="mailto:<?php echo htmlspecialchars($company['email']); ?>"
                                    style="color: #005a9c; text-decoration: underline;">
                                    <?php echo htmlspecialchars($company['email']); ?>
                                </a>
                            </div>

                            <div>Teléfono:</div>
                            <div>
                                <a href="tel:<?php echo htmlspecialchars($company['phone_number']); ?>"
                                    style="color: #005a9c; text-decoration: underline;">
                                    <?php echo htmlspecialchars($company['phone_number']); ?>
                                </a>
                            </div>
                        </div>

                        <!-- FECHA DE REGISTRO -->
                        <div class="date-info">
                            <strong>📅 Fecha de Registro:</strong>
                            <span
                                style="color: #555;"><?php echo date('d/m/Y H:i', strtotime($company['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ============================================ -->
    <!-- MODAL PARA APROBAR EMPRESA -->
    <!-- ============================================ -->
    <div id="approveModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <span class="close-button" onclick="closeApproveModal()">&times;</span>
            <h2 style="color: #4caf50;">✅ Aprobar Empresa</h2>

            <form method="POST" action="/SIEP/public/index.php?action=approveCompany" style="padding: 20px 30px 30px;">
                <input type="hidden" name="company_id" id="approve_company_id">

                <div style="margin-bottom: 20px;">
                    <p style="font-size: 16px; color: #333;">
                        ¿Está seguro de aprobar a la empresa <strong id="approve_company_name"
                            style="color: #6f1d33;"></strong>?
                    </p>

                    <div class="form-group">
                        <label>Comentarios opcionales:</label>
                        <textarea name="comments" rows="4"
                            placeholder="Ejemplo: Bienvenido al sistema SIEP. Su empresa ha sido aprobada exitosamente."
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;"></textarea>
                        <small style="color: #666; display: block; margin-top: 5px;">Este mensaje será enviado por
                            correo a la empresa.</small>
                    </div>
                </div>

                <div style="text-align: right; border-top: 1px solid #e0e0e0; padding-top: 15px;">
                    <button type="button" onclick="closeApproveModal()"
                        style="margin-right: 10px; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="submit"
                        style="padding: 10px 20px; background: #4caf50; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        ✅ Aprobar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL PARA RECHAZAR EMPRESA -->
    <!-- ============================================ -->
    <div id="rejectModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <span class="close-button" onclick="closeRejectModal()">&times;</span>
            <h2 style="color: #f44336;">❌ Rechazar Empresa</h2>

            <form method="POST" action="/SIEP/public/index.php?action=rejectCompany" style="padding: 20px 30px 30px;">
                <input type="hidden" name="company_id" id="reject_company_id">

                <div style="margin-bottom: 20px;">
                    <p style="font-size: 16px; color: #333;">
                        ¿Está seguro de rechazar a la empresa <strong id="reject_company_name"
                            style="color: #6f1d33;"></strong>?
                    </p>

                    <div class="form-group">
                        <label style="color: #f44336;">Razón del rechazo (obligatorio):</label>
                        <textarea name="comments" rows="5" required
                            placeholder="Ejemplo: El RFC proporcionado no es válido. Por favor, verifica los datos e intenta de nuevo."
                            style="width: 100%; padding: 10px; border: 2px solid #f44336; border-radius: 4px; font-family: inherit;"></textarea>
                        <small style="color: #666; display: block; margin-top: 5px;">Esta razón será enviada por correo
                            a la empresa.</small>
                    </div>
                </div>

                <div style="text-align: right; border-top: 1px solid #e0e0e0; padding-top: 15px;">
                    <button type="button" onclick="closeRejectModal()"
                        style="margin-right: 10px; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="submit"
                        style="padding: 10px 20px; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        ❌ Rechazar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- JAVASCRIPT PARA CONTROLAR LOS MODALES -->
    <!-- ============================================ -->
    <script>
        // Modales de detalles de empresas
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevenir scroll del body
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto'; // Restaurar scroll del body
        }

        // Modal de Aprobar
        function showApproveModal(companyId, companyName) {
            document.getElementById('approve_company_id').value = companyId;
            document.getElementById('approve_company_name').textContent = companyName;
            document.getElementById('approveModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Modal de Rechazar
        function showRejectModal(companyId, companyName) {
            document.getElementById('reject_company_id').value = companyId;
            document.getElementById('reject_company_name').textContent = companyName;
            document.getElementById('rejectModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Cerrar modales con la tecla ESC
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    modal.style.display = 'none';
                });
                document.body.style.overflow = 'auto';
            }
        });
    </script>
</body>

</html>