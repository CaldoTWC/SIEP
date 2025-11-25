<?php
// Archivo: src/Views/upis/review_accreditations.php
// Vista de Revisión de Acreditaciones para UPIS

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Acreditaciones - UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/upis.css">

</head>

<body>
    <!-- Encabezado bonito -->
    <div class="upis-header">
        <h1>Panel de Administración de UPIS</h1>
    </div>

    <div class="container">
        <div class="page-header">
            <h1>✅ Revisión de Acreditaciones</h1>
        </div>

        <a href="/SIEP/public/index.php?action=upisDashboard" class="logout-btn">← Volver al Dashboard</a><br><br>



        <!-- Tabs para cambiar entre pendientes y aprobadas -->
        <div class="tabs">
            <a href="/SIEP/public/index.php?action=reviewAccreditations" class="tab-button active">
                📋 Pendientes
            </a>
            <a href="/SIEP/public/index.php?action=viewApprovedAccreditations" class="tab-button">
                ✅ Aprobadas
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (empty($pendingAccreditations)): ?>
            <div class="empty-state">
                <h2>📭 No hay acreditaciones pendientes</h2>
                <p>Todas las solicitudes han sido procesadas.</p>
            </div>
        <?php else: ?>

            <?php foreach ($pendingAccreditations as $acc):
                // ✅ DECODIFICAR METADATA (ya viene decodificado desde el controlador)
                $metadata = $acc['metadata_decoded'] ?? json_decode($acc['metadata'], true);
                $student_info = $metadata['student_info'] ?? [];
                $company_info = $metadata['company_info'] ?? [];
                $docs = $metadata['documents'] ?? [];
                $tipo = $acc['tipo_acreditacion'];
                ?>

                <div class="accreditation-card">

                    <!-- Encabezado -->
                    <div class="card-header">
                        <h2>
                            👤
                            <?php echo htmlspecialchars($acc['first_name'] . ' ' . $acc['last_name_p'] . ' ' . $acc['last_name_m']); ?>
                            <span class="status-badge status-pending">⏳ Pendiente</span>
                            <span class="status-badge <?php echo $tipo === 'A' ? 'status-tipo-a' : 'status-tipo-b'; ?>">
                                Tipo <?php echo $tipo; ?>
                            </span>
                        </h2>
                        <p style="margin: 5px 0 0 0; color: #666;">
                            📅 Enviado: <?php echo date('d/m/Y H:i', strtotime($acc['submitted_at'])); ?>
                        </p>
                    </div>

                    <!-- Información del Estudiante -->
                    <div class="info-section">
                        <h3>📚 Información del Estudiante</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Boleta:</strong> <?php echo htmlspecialchars($acc['boleta']); ?>
                            </div>
                            <div class="info-item">
                                <strong>Carrera:</strong> <?php echo htmlspecialchars($acc['programa_academico']); ?>
                            </div>
                            <div class="info-item">
                                <strong>Email:</strong>
                                <?php echo htmlspecialchars($student_info['email_institucional'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Teléfono:</strong> <?php echo htmlspecialchars($student_info['telefono'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Semestre:</strong> <?php echo htmlspecialchars($student_info['semestre'] ?? 'N/A'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la Empresa -->
                    <div class="info-section">
                        <h3>🏢 Información de la Empresa</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Nombre Comercial:</strong> <?php echo htmlspecialchars($acc['empresa_nombre']); ?>
                            </div>
                            <div class="info-item">
                                <strong>Razón Social:</strong>
                                <?php echo htmlspecialchars($company_info['razon_social'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Tipo:</strong> <?php echo htmlspecialchars($company_info['tipo_empresa'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Giro:</strong> <?php echo htmlspecialchars($company_info['giro'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Contacto:</strong>
                                <?php echo htmlspecialchars($company_info['nombre_contacto'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Email:</strong>
                                <?php echo htmlspecialchars($company_info['email_contacto'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Teléfono:</strong>
                                <?php echo htmlspecialchars($company_info['telefono_contacto'] ?? 'N/A'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Periodo de Estancia -->
                    <div class="info-section">
                        <h3>📅 Periodo de Estancia</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Fecha Inicio:</strong> <?php echo date('d/m/Y', strtotime($acc['fecha_inicio'])); ?>
                            </div>
                            <div class="info-item">
                                <strong>Fecha Fin:</strong> <?php echo date('d/m/Y', strtotime($acc['fecha_fin'])); ?>
                            </div>
                            <div class="info-item">
                                <strong>Días de Estancia:</strong>
                                <?php
                                $dias = $company_info['dias_estancia'] ?? [];
                                echo is_array($dias) ? implode(', ', $dias) : 'N/A';
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Documentos Subidos -->
                    <div class="info-section">
                        <h3>📎 Documentos Subidos</h3>

                        <ul class="document-list">
                            <!-- Boleta Global (común para ambos tipos) -->
                            <?php if (isset($docs['boleta_global'])): ?>
                                <li>
                                    <span>📄 Boleta Global</span>
                                    <a href="/SIEP/public/index.php?action=viewDocument&path=<?php echo urlencode($docs['boleta_global']); ?>"
                                        target="_blank" class="btn-download">
                                        👁️ Ver Documento
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($tipo === 'A'): ?>
                                <!-- Tipo A: Empresa NO Registrada -->

                                <?php if (isset($docs['recibos_nomina']) && is_array($docs['recibos_nomina'])): ?>
                                    <?php foreach ($docs['recibos_nomina'] as $idx => $recibo): ?>
                                        <li>
                                            <span>💰 Recibo de Nómina #<?php echo $idx + 1; ?></span>
                                            <a href="/SIEP/public/index.php?action=viewDocument&path=<?php echo urlencode($recibo); ?>"
                                                target="_blank" class="btn-download">
                                                👁️ Ver Documento
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php if (isset($docs['constancia_laboral'])): ?>
                                    <li>
                                        <span>📋 Constancia Laboral</span>
                                        <a href="/SIEP/public/index.php?action=viewDocument&path=<?php echo urlencode($docs['constancia_laboral']); ?>"
                                            target="_blank" class="btn-download">
                                            👁️ Ver Documento
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (isset($docs['reporte_final'])): ?>
                                    <li>
                                        <span>📝 Reporte Final</span>
                                        <a href="/SIEP/public/index.php?action=viewDocument&path=<?php echo urlencode($docs['reporte_final']); ?>"
                                            target="_blank" class="btn-download">
                                            👁️ Ver Documento
                                        </a>
                                    </li>
                                <?php endif; ?>

                            <?php else: ?>
                                <!-- Tipo B: Empresa Registrada -->

                                <?php if (isset($docs['carta_aceptacion'])): ?>
                                    <li>
                                        <span>✉️ Carta de Aceptación</span>
                                        <a href="/SIEP/public/index.php?action=viewDocument&path=<?php echo urlencode($docs['carta_aceptacion']); ?>"
                                            target="_blank" class="btn-download">
                                            👁️ Ver Documento
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (isset($docs['constancia_validacion'])): ?>
                                    <li>
                                        <span>✔️ Constancia de Validación</span>
                                        <a href="/SIEP/public/index.php?action=viewDocument&path=<?php echo urlencode($docs['constancia_validacion']); ?>"
                                            target="_blank" class="btn-download">
                                            👁️ Ver Documento
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (isset($docs['reporte_final'])): ?>
                                    <li>
                                        <span>📝 Reporte Final</span>
                                        <a href="/SIEP/public/index.php?action=viewDocument&path=<?php echo urlencode($docs['reporte_final']); ?>"
                                            target="_blank" class="btn-download">
                                            👁️ Ver Documento
                                        </a>
                                    </li>
                                <?php endif; ?>

                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Acciones -->
                    <div class="action-section">

                        <!-- Formulario de Aprobación -->
                        <form method="POST" action="/SIEP/public/index.php?action=approveAccreditation"
                            onsubmit="return confirm('¿Aprobar esta acreditación?');" style="flex: 1;">
                            <input type="hidden" name="submission_id" value="<?php echo $acc['id']; ?>">
                            <div class="form-group">
                                <label>Comentarios (Opcional):</label>
                                <textarea name="comments" placeholder="Puedes agregar comentarios..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success" style="width: 100%;">
                                ✅ Aprobar Acreditación
                            </button>
                        </form>

                        <!-- Formulario de Rechazo -->
                        <form method="POST" action="/SIEP/public/index.php?action=rejectAccreditation"
                            onsubmit="return confirm('¿Rechazar esta acreditación? Esta acción no se puede deshacer.');"
                            style="flex: 1;">
                            <input type="hidden" name="submission_id" value="<?php echo $acc['id']; ?>">
                            <div class="form-group">
                                <label>Motivo del Rechazo (Obligatorio):</label>
                                <textarea name="comments" required placeholder="Explica por qué se rechaza..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger" style="width: 100%;">
                                ❌ Rechazar Acreditación
                            </button>
                        </form>

                    </div>

                </div>

            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</body>

</html>