<?php
/**
 * Hub de Gestión de Empresas
 * 
 * Vista integrada con 3 secciones en tabs:
 * 1. Pendientes - usando módulo existente review_companies.php
 * 2. Empresas Activas - lista con datos básicos
 * 3. Historial de Rechazos - registros de rechazos
 * 
 * @package SIEP\Views\Upis
 * @version 1.0.0
 * @date 2025-11-08
 */

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empresas - SIEP UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/upis.css">

</head>

<body>
    <!-- Encabezado bonito -->
    <div class="upis-header">
        <h1>Panel de Administración de UPIS</h1>
    </div>

    <div class="container">
        <div class="page-header">
            <h1>🏢 Gestión de Empresas</h1>
            <p>Revisa, aprueba y rechaza a las empresas</p>
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

        <!-- Tabs Container -->
        <div class="tabs-container">
            <div class="tabs-header">
                <button class="tab-button active" onclick="switchTab('pending')">
                    📋 Pendientes (<?php echo count($pendingCompanies); ?>)
                </button>
                <button class="tab-button" onclick="switchTab('active')">
                    ✅ Empresas Activas (<?php echo count($activeCompanies); ?>)
                </button>
                <button class="tab-button" onclick="switchTab('history')">
                    🗂️ Historial de Rechazos (<?php echo count($rejectionHistory); ?>)
                </button>
            </div>

            <!-- TAB 1: PENDIENTES -->
            <div id="tab-pending" class="tab-content active">
                <h2 style="color: #6f1d33;">Empresas Pendientes de Aprobación</h2>
                <p style="color: #666; margin-bottom: 20px;">Empresas que esperan revisión y aprobación de UPIS.</p>

                <?php if (empty($pendingCompanies)): ?>
                    <div class="empty-state">
                        <h3>✅ No hay empresas pendientes</h3>
                        <p>Todas las solicitudes han sido procesadas.</p>
                    </div>
                <?php else: ?>
                    <div style="margin-top: 20px;">
                        <a href="/SIEP/public/index.php?action=reviewCompanies" class="btn"
                            style="background: var(--color-ipn-guinda); color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; display: inline-block;">
                            📋 Ir a Revisión Completa de Empresas Pendientes
                        </a>
                        <p style="margin-top: 15px; color: #666;">
                            Hay <strong
                                style="color: var(--color-ipn-guinda);"><?php echo count($pendingCompanies); ?></strong>
                            empresa(s) pendiente(s) de aprobación.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TAB 2: EMPRESAS ACTIVAS -->
            <div id="tab-active" class="tab-content">
                <h2 style="color: #6f1d33;">Empresas Activas</h2>
                <p style="color: #666; margin-bottom: 20px;">Empresas aprobadas que pueden publicar vacantes en el
                    sistema.</p>

                <?php if (empty($activeCompanies)): ?>
                    <div class="empty-state">
                        <h3>📭 No hay empresas activas</h3>
                        <p>Aún no hay empresas aprobadas en el sistema.</p>
                    </div>
                <?php else: ?>
                    <table class="companies-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Razón Social</th>
                                <th>Nombre Comercial</th>
                                <th>Contacto</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Fecha de Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activeCompanies as $company): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($company['id']); ?></strong></td>
                                    <td>
                                        <strong
                                            style="color: #6f1d33;"><?php echo htmlspecialchars($company['company_name']); ?></strong>
                                        <?php if (!empty($company['rfc'])): ?>
                                            <br><small style="color: #999;">RFC:
                                                <?php echo htmlspecialchars($company['rfc']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($company['commercial_name'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($company['first_name'] . ' ' . $company['last_name_p']); ?>
                                        <?php if (!empty($company['contact_person_position'])): ?>
                                            <br><small
                                                style="color: #666;"><?php echo htmlspecialchars($company['contact_person_position']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($company['email']); ?>"
                                            style="color: #005a9c;">
                                            <?php echo htmlspecialchars($company['email']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="tel:<?php echo htmlspecialchars($company['phone_number']); ?>"
                                            style="color: #005a9c;">
                                            <?php echo htmlspecialchars($company['phone_number']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($company['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- TAB 3: HISTORIAL DE RECHAZOS -->
            <div id="tab-history" class="tab-content">
                <h2 style="color: #6f1d33;">Historial de Rechazos</h2>
                <p style="color: #666; margin-bottom: 20px;">Registro de todas las empresas rechazadas. Las empresas
                    pueden volver a registrarse.</p>

                <?php if (empty($rejectionHistory)): ?>
                    <div class="empty-state">
                        <h3>📂 No hay rechazos registrados</h3>
                        <p>Aún no se han rechazado empresas en el sistema.</p>
                    </div>
                <?php else: ?>
                    <table class="companies-table">
                        <thead>
                            <tr>
                                <th>Nombre de Empresa</th>
                                <th>Email de Contacto</th>
                                <th>Fecha del Intento</th>
                                <th>Motivo del Rechazo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rejectionHistory as $rejection): ?>
                                <tr>
                                    <td>
                                        <strong
                                            style="color: #6f1d33;"><?php echo htmlspecialchars($rejection['company_name']); ?></strong>
                                        <?php if (!empty($rejection['rfc'])): ?>
                                            <br><small style="color: #999;">RFC:
                                                <?php echo htmlspecialchars($rejection['rfc']); ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($rejection['commercial_name'])): ?>
                                            <br><small style="color: #666;">Nombre comercial:
                                                <?php echo htmlspecialchars($rejection['commercial_name']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($rejection['contact_email']); ?>"
                                            style="color: #005a9c;">
                                            <?php echo htmlspecialchars($rejection['contact_email']); ?>
                                        </a>
                                        <br><small
                                            style="color: #666;"><?php echo htmlspecialchars($rejection['contact_name']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y H:i', strtotime($rejection['rejection_date'])); ?>
                                    </td>
                                    <td>
                                        <div class="rejection-reason"
                                            title="<?php echo htmlspecialchars($rejection['rejection_reason']); ?>">
                                            <?php echo htmlspecialchars($rejection['rejection_reason']); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Ocultar todos los tabs
            const allTabs = document.querySelectorAll('.tab-content');
            allTabs.forEach(tab => tab.classList.remove('active'));

            // Desactivar todos los botones
            const allButtons = document.querySelectorAll('.tab-button');
            allButtons.forEach(button => button.classList.remove('active'));

            // Activar el tab seleccionado
            document.getElementById('tab-' + tabName).classList.add('active');

            // Activar el botón correspondiente
            event.target.classList.add('active');
        }
    </script>
</body>

</html>