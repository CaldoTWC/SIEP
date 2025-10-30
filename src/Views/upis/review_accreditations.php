<?php
// Archivo: src/Views/upis/review_accreditations.php
require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Acreditaciones - SIEP UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-left: 4px solid #27ae60;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #27ae60;
        }

        .accreditation-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .accreditation-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
        }

        .card-header h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 20px;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }

        .badge-type-a {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-type-b {
            background: #d4edda;
            color: #155724;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .info-section h4 {
            margin: 0 0 12px 0;
            color: #27ae60;
            font-size: 14px;
            text-transform: uppercase;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            min-width: 120px;
        }

        .info-value {
            color: #333;
        }

        .documents-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .document-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: white;
            border-radius: 5px;
            margin-bottom: 8px;
        }

        .action-section {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            resize: vertical;
            min-height: 80px;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-success {
            background: #27ae60;
            color: white;
        }

        .btn-success:hover {
            background: #229954;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            background: white;
            border-radius: 10px;
        }

        .empty-state .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <a href="/SIEP/public/index.php?action=upisDashboard" class="btn btn-secondary" style="margin-bottom: 20px;">
            ← Volver al Dashboard
        </a>

        <div class="page-header">
            <h1>📋 Revisar Solicitudes de Acreditación</h1>
            <p>Gestiona las solicitudes de acreditación de estancia profesional enviadas por los estudiantes</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Pendientes</h3>
                <div class="number"><?php echo count($pendingAccreditations); ?></div>
            </div>
            <div class="stat-card">
                <h3>Tipo A (No Registrada)</h3>
                <div class="number">
                    <?php 
                    echo count(array_filter($pendingAccreditations, function($acc) {
                        return $acc['tipo_acreditacion'] === 'A';
                    }));
                    ?>
                </div>
            </div>
            <div class="stat-card">
                <h3>Tipo B (Registrada)</h3>
                <div class="number">
                    <?php 
                    echo count(array_filter($pendingAccreditations, function($acc) {
                        return $acc['tipo_acreditacion'] === 'B';
                    }));
                    ?>
                </div>
            </div>
        </div>

        <!-- Lista de Acreditaciones -->
        <?php if (empty($pendingAccreditations)): ?>
            <div class="empty-state">
                <div class="icon">✅</div>
                <h3>No hay solicitudes pendientes</h3>
                <p>Todas las acreditaciones han sido revisadas.</p>
            </div>
        <?php else: ?>
            <?php foreach ($pendingAccreditations as $acc): ?>
                <?php 
                // Decodificar metadata
                $metadata = [];
                if (!empty($acc['metadata'])) {
                    $metadata = json_decode($acc['metadata'], true) ?? [];
                }
                ?>
                
                <div class="accreditation-card">
                    
                    <!-- Header -->
                    <div class="card-header">
                        <div>
                            <h3>Solicitud #<?php echo $acc['id']; ?> - <?php echo htmlspecialchars($acc['boleta']); ?></h3>
                            <p style="margin: 5px 0 0 0; color: #666;">
                                Enviada el <?php echo date('d/m/Y H:i', strtotime($acc['submitted_at'])); ?>
                            </p>
                        </div>
                        <div>
                            <span class="badge <?php echo $acc['tipo_acreditacion'] === 'A' ? 'badge-type-a' : 'badge-type-b'; ?>">
                                Tipo <?php echo $acc['tipo_acreditacion']; ?> - 
                                <?php echo $acc['tipo_acreditacion'] === 'A' ? 'Empresa NO registrada' : 'Empresa registrada'; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Información -->
                    <div class="info-grid">
                        
                        <!-- Estudiante -->
                        <div class="info-section">
                            <h4>👤 Estudiante</h4>
                            <div class="info-row">
                                <div class="info-label">Nombre:</div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($acc['first_name'] . ' ' . 
                                                               $acc['last_name_p'] . ' ' . 
                                                               $acc['last_name_m']); ?>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Email:</div>
                                <div class="info-value"><?php echo htmlspecialchars($acc['email']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Carrera:</div>
                                <div class="info-value"><?php echo htmlspecialchars($acc['career']); ?></div>
                            </div>
                        </div>

                        <!-- Empresa -->
                        <div class="info-section">
                            <h4>🏢 Empresa</h4>
                            <div class="info-row">
                                <div class="info-label">Nombre:</div>
                                <div class="info-value"><?php echo htmlspecialchars($acc['empresa_nombre'] ?? 'N/A'); ?></div>
                            </div>
                            <?php if (isset($metadata['company_info'])): ?>
                                <div class="info-row">
                                    <div class="info-label">Contacto:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($metadata['company_info']['nombre_contacto'] ?? 'N/A'); ?></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Email:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($metadata['company_info']['email_contacto'] ?? 'N/A'); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Periodo -->
                        <div class="info-section">
                            <h4>📅 Periodo de Estancia</h4>
                            <div class="info-row">
                                <div class="info-label">Inicio:</div>
                                <div class="info-value">
                                    <?php echo $acc['fecha_inicio'] ? date('d/m/Y', strtotime($acc['fecha_inicio'])) : 'N/A'; ?>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Fin:</div>
                                <div class="info-value">
                                    <?php echo $acc['fecha_fin'] ? date('d/m/Y', strtotime($acc['fecha_fin'])) : 'N/A'; ?>
                                </div>
                            </div>
                            <?php if ($acc['fecha_inicio'] && $acc['fecha_fin']): ?>
                                <div class="info-row">
                                    <div class="info-label">Duración:</div>
                                    <div class="info-value">
                                        <?php 
                                        $inicio = new DateTime($acc['fecha_inicio']);
                                        $fin = new DateTime($acc['fecha_fin']);
                                        echo $inicio->diff($fin)->days . ' días';
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- Documentos -->
                    <?php if (isset($metadata['documents']) && !empty(array_filter($metadata['documents']))): ?>
                        <div class="documents-list">
                            <h4 style="margin: 0 0 12px 0; color: #27ae60;">📎 Documentos Adjuntos</h4>
                            
                            <?php if (!empty($metadata['documents']['boleta_global'])): ?>
                                <div class="document-item">
                                    <span>📄 Boleta Global de Calificaciones</span>
                                    <a href="/SIEP/public/index.php?action=viewDocument&path=<?php echo urlencode($metadata['documents']['boleta_global']); ?>" 
                                       class="btn btn-secondary btn-sm" target="_blank">
                                        Ver
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($metadata['documents']['constancia_laboral'])): ?>
                                <div class="document-item">
                                    <span>📄 Constancia Laboral / Carta de Validación</span>
                                    <a href="/SIEP/public/index.php?action=viewDocument&path=<?php echo urlencode($metadata['documents']['constancia_laboral']); ?>" 
                                       class="btn btn-secondary btn-sm" target="_blank">
                                        Ver
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($metadata['documents']['recibos_nomina'])): ?>
                                <?php foreach ($metadata['documents']['recibos_nomina'] as $index => $recibo): ?>
                                    <div class="document-item">
                                        <span>💰 Recibo de Nómina #<?php echo $index + 1; ?></span>
                                        <a href="/SIEP/public/index.php?action=viewDocument&path=<?php echo urlencode($recibo); ?>" 
                                           class="btn btn-secondary btn-sm" target="_blank">
                                            Ver
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Acciones -->
                    <div class="action-section">
                        
                        <!-- Formulario de Aprobación -->
                        <form method="POST" action="/SIEP/public/index.php?action=approveAccreditation" 
                              onsubmit="return confirm('¿Aprobar esta acreditación?');"
                              style="flex: 1;">
                            <input type="hidden" name="submission_id" value="<?php echo $acc['id']; ?>">
                            <div class="form-group">
                                <label>Comentarios (Opcional):</label>
                                <textarea name="comments" placeholder="Puedes agregar comentarios..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success" style="width: 100%;">
                                ✅ Aprobar
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
                                ❌ Rechazar
                            </button>
                        </form>

                    </div>

                </div>

            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</body>
</html>