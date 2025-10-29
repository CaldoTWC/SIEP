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
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        /* Estilos adicionales para el modal mejorado */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
            backdrop-filter: blur(3px);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 3% auto;
            padding: 0;
            border: none;
            border-radius: 12px;
            max-width: 850px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .close-button {
            color: #aaa;
            float: right;
            font-size: 32px;
            font-weight: bold;
            line-height: 1;
            padding: 10px 15px;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .close-button:hover,
        .close-button:focus {
            color: #d32f2f;
        }
        
        .modal-content h2 {
            color: #6f1d33;
            border-bottom: 3px solid #d4a017;
            padding: 20px 30px 15px;
            margin: 0;
            font-size: 24px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px 12px 0 0;
        }
        
        .modal-details {
            padding: 20px 30px 30px;
        }
        
        .modal-details h3 {
            background: linear-gradient(135deg, #6f1d33 0%, #9b2847 100%);
            color: white;
            padding: 12px 20px;
            margin: 25px -30px 20px -30px;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        
        .modal-details h3:first-of-type {
            margin-top: 0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-grid > div:nth-child(odd) {
            font-weight: 600;
            color: #333;
        }
        
        .info-grid > div:nth-child(even) {
            color: #555;
        }
        
        .info-block {
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #6f1d33;
        }
        
        .info-block strong {
            display: block;
            margin-bottom: 8px;
            color: #6f1d33;
            font-size: 15px;
        }
        
        .info-block-content {
            color: #555;
            line-height: 1.7;
        }
        
        .link-block {
            margin-bottom: 15px;
        }
        
        .link-block strong {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-size: 14px;
        }
        
        .link-block a {
            color: #005a9c;
            text-decoration: underline;
            word-break: break-all;
            transition: all 0.3s ease;
            display: inline-block;
            padding-left: 20px;
        }
        
        .link-block a:hover {
            color: #003d6b;
            text-decoration: none;
        }
        
        .programs-block {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #4caf50;
            margin-bottom: 20px;
        }
        
        .programs-block strong {
            display: block;
            margin-bottom: 8px;
            color: #2e7d32;
            font-size: 15px;
        }
        
        .date-info {
            margin-top: 25px;
            padding: 12px 15px;
            background: #fff3cd;
            border-radius: 8px;
            border: 1px solid #ffc107;
            font-size: 14px;
        }
        
        .date-info strong {
            color: #856404;
        }
        
        /* Scroll suave para el modal */
        .modal-content::-webkit-scrollbar {
            width: 10px;
        }
        
        .modal-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .modal-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .modal-content::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Estilos para la tabla */
        .table-container {
            overflow-x: auto;
            margin: 20px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        thead {
            background: linear-gradient(135deg, #6f1d33 0%, #9b2847 100%);
            color: white;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .btn-details {
            background: #2196f3;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .btn-details:hover {
            background: #1976d2;
        }
        
        .approve {
            background: #4caf50;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .approve:hover {
            background: #45a049;
        }
        
        .reject {
            background: #f44336;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .reject:hover {
            background: #da190b;
        }
        
        /* Estilos para modales de confirmación */
        .modal-content form {
            padding: 0;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="color: #6f1d33; margin-bottom: 10px;">🏢 Revisión de Empresas Pendientes</h1>
        
        <a href="/SIEP/public/index.php?action=upisDashboard" style="display: inline-block; margin-bottom: 20px; color: #6f1d33; text-decoration: none; font-weight: bold;">
            ← Volver al Panel Principal
        </a>

        <?php if (isset($_SESSION['success'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #28a745;">
        <strong>✅ Éxito:</strong> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #f44336;">
        <strong>❌ Error:</strong> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

        <?php if (empty($pendingCompanies)): ?>
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
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
                                    <strong style="color: #6f1d33;"><?php echo htmlspecialchars($company['company_name']); ?></strong><br>
                                    <small style="color: #666;"><?php echo htmlspecialchars($company['commercial_name'] ?? 'Sin nombre comercial'); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($company['first_name'] . ' ' . $company['last_name_p']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($company['created_at'])); ?></td>
                                <td class="actions">
                                    <button class="btn-details" onclick="openModal('modal-<?php echo $index; ?>')">
                                        📋 Ver Detalles Completos
                                    </button>
                                    <hr style="margin: 4px 0; border: none; border-top: 1px solid #e0e0e0;">
                                    <button class="approve" onclick="showApproveModal(<?php echo $company['id']; ?>, '<?php echo htmlspecialchars($company['company_name'], ENT_QUOTES); ?>')">
                                        ✅ Aprobar
                                    </button>
                                    <button class="reject" onclick="showRejectModal(<?php echo $company['id']; ?>, '<?php echo htmlspecialchars($company['company_name'], ENT_QUOTES); ?>')">
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
                                <a href="<?php echo htmlspecialchars($company['website']); ?>" 
                                   target="_blank" 
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
                                <a href="<?php echo htmlspecialchars($company['tax_id_url']); ?>" 
                                   target="_blank" 
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
                            <div><?php echo htmlspecialchars($company['first_name'] . ' ' . $company['last_name_p'] . ' ' . $company['last_name_m']); ?></div>
                            
                            <div>Puesto:</div>
                            <div><?php echo htmlspecialchars($company['contact_person_position'] ?? 'No especificado'); ?></div>
                            
                            <div>Correo:</div>
                            <div>
                                <a href="mailto:<?php echo htmlspecialchars($company['email']); ?>" style="color: #005a9c; text-decoration: underline;">
                                    <?php echo htmlspecialchars($company['email']); ?>
                                </a>
                            </div>
                            
                            <div>Teléfono:</div>
                            <div>
                                <a href="tel:<?php echo htmlspecialchars($company['phone_number']); ?>" style="color: #005a9c; text-decoration: underline;">
                                    <?php echo htmlspecialchars($company['phone_number']); ?>
                                </a>
                            </div>
                        </div>
                        
                        <!-- FECHA DE REGISTRO -->
                        <div class="date-info">
                            <strong>📅 Fecha de Registro:</strong>
                            <span style="color: #555;"><?php echo date('d/m/Y H:i', strtotime($company['created_at'])); ?></span>
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
                        ¿Está seguro de aprobar a la empresa <strong id="approve_company_name" style="color: #6f1d33;"></strong>?
                    </p>
                    
                    <div class="form-group">
                        <label>Comentarios opcionales:</label>
                        <textarea name="comments" rows="4" placeholder="Ejemplo: Bienvenido al sistema SIEP. Su empresa ha sido aprobada exitosamente." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;"></textarea>
                        <small style="color: #666; display: block; margin-top: 5px;">Este mensaje será enviado por correo a la empresa.</small>
                    </div>
                </div>
                
                <div style="text-align: right; border-top: 1px solid #e0e0e0; padding-top: 15px;">
                    <button type="button" onclick="closeApproveModal()" style="margin-right: 10px; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="submit" style="padding: 10px 20px; background: #4caf50; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
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
                        ¿Está seguro de rechazar a la empresa <strong id="reject_company_name" style="color: #6f1d33;"></strong>?
                    </p>
                    
                    <div class="form-group">
                        <label style="color: #f44336;">Razón del rechazo (obligatorio):</label>
                        <textarea name="comments" rows="5" required placeholder="Ejemplo: El RFC proporcionado no es válido. Por favor, verifica los datos e intenta de nuevo." style="width: 100%; padding: 10px; border: 2px solid #f44336; border-radius: 4px; font-family: inherit;"></textarea>
                        <small style="color: #666; display: block; margin-top: 5px;">Esta razón será enviada por correo a la empresa.</small>
                    </div>
                </div>
                
                <div style="text-align: right; border-top: 1px solid #e0e0e0; padding-top: 15px;">
                    <button type="button" onclick="closeRejectModal()" style="margin-right: 10px; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="submit" style="padding: 10px 20px; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
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
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
        
        // Cerrar modales con la tecla ESC
        document.addEventListener('keydown', function(event) {
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