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
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: transform 0.2s;
        }

        .back-link:hover {
            transform: translateX(-5px);
        }

        .accreditation-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .card-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .card-header h2 {
            color: #667eea;
            margin: 0;
            font-size: 1.8em;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            margin-left: 10px;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-tipo-a {
            background-color: #ff6b6b;
            color: white;
        }

        .status-tipo-b {
            background-color: #4ecdc4;
            color: white;
        }

        .info-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .info-section h3 {
            color: #667eea;
            margin-top: 0;
            font-size: 1.3em;
        }

        .info-section p {
            margin: 8px 0;
            color: #333;
        }

        .info-section strong {
            color: #555;
        }

        .document-list {
            list-style: none;
            padding: 0;
        }

        .document-list li {
            margin: 10px 0;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-download {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
            transition: background-color 0.3s;
            font-weight: 500;
        }

        .btn-download:hover {
            background-color: #0056b3;
        }

        .action-section {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
        }

        .empty-state h2 {
            color: #667eea;
            margin-bottom: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .info-item {
            padding: 10px;
            background-color: white;
            border-radius: 5px;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-button {
            padding: 12px 24px;
            background-color: white;
            color: #667eea;
            border: 2px solid white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .tab-button.active {
            background-color: #667eea;
            color: white;
        }

        .tab-button:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/SIEP/public/index.php?action=upisDashboard" class="back-link">← Volver al Dashboard</a>
        
        <h1>✅ Revisión de Acreditaciones</h1>

        <!-- Tabs para cambiar entre pendientes y aprobadas -->
        <div class="tabs">
            <a href="/SIEP/public/index.php?action=reviewAccreditations" 
               class="tab-button active">
                📋 Pendientes
            </a>
            <a href="/SIEP/public/index.php?action=viewApprovedAccreditations" 
               class="tab-button">
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
                $metadata = json_decode($acc['metadata'], true);
                $student_info = $metadata['student_info'] ?? [];
                $company_info = $metadata['company_info'] ?? [];
                $docs = $metadata['documents'] ?? [];
                $tipo = $acc['tipo_acreditacion'];
            ?>
                
                <div class="accreditation-card">
                    
                    <!-- Encabezado -->
                    <div class="card-header">
                        <h2>
                            👤 <?php echo htmlspecialchars($acc['first_name'] . ' ' . $acc['last_name_p'] . ' ' . $acc['last_name_m']); ?>
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
                                <strong>Email:</strong> <?php echo htmlspecialchars($student_info['email_institucional'] ?? 'N/A'); ?>
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
                                <strong>Razón Social:</strong> <?php echo htmlspecialchars($company_info['razon_social'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Tipo:</strong> <?php echo htmlspecialchars($company_info['tipo_empresa'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Giro:</strong> <?php echo htmlspecialchars($company_info['giro'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Contacto:</strong> <?php echo htmlspecialchars($company_info['nombre_contacto'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Email:</strong> <?php echo htmlspecialchars($company_info['email_contacto'] ?? 'N/A'); ?>
                            </div>
                            <div class="info-item">
                                <strong>Teléfono:</strong> <?php echo htmlspecialchars($company_info['telefono_contacto'] ?? 'N/A'); ?>
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
                                    <a href="/SIEP/public/<?php echo str_replace('\\', '/', $docs['boleta_global']); ?>" 
                                       target="_blank" 
                                       class="btn-download">
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
                                            <a href="/SIEP/public/<?php echo str_replace('\\', '/', $recibo); ?>" 
                                               target="_blank" 
                                               class="btn-download">
                                                👁️ Ver Documento
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php if (isset($docs['constancia_laboral'])): ?>
                                    <li>
                                        <span>📋 Constancia Laboral</span>
                                        <a href="/SIEP/public/<?php echo str_replace('\\', '/', $docs['constancia_laboral']); ?>" 
                                           target="_blank" 
                                           class="btn-download">
                                            👁️ Ver Documento
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (isset($docs['reporte_final'])): ?>
                                    <li>
                                        <span>📝 Reporte Final</span>
                                        <a href="/SIEP/public/<?php echo str_replace('\\', '/', $docs['reporte_final']); ?>" 
                                           target="_blank" 
                                           class="btn-download">
                                            👁️ Ver Documento
                                        </a>
                                    </li>
                                <?php endif; ?>

                            <?php else: ?>
                                <!-- Tipo B: Empresa Registrada -->
                                
                                <?php if (isset($docs['carta_aceptacion'])): ?>
                                    <li>
                                        <span>✉️ Carta de Aceptación</span>
                                        <a href="/SIEP/public/<?php echo str_replace('\\', '/', $docs['carta_aceptacion']); ?>" 
                                           target="_blank" 
                                           class="btn-download">
                                            👁️ Ver Documento
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (isset($docs['constancia_validacion'])): ?>
                                    <li>
                                        <span>✔️ Constancia de Validación</span>
                                        <a href="/SIEP/public/<?php echo str_replace('\\', '/', $docs['constancia_validacion']); ?>" 
                                           target="_blank" 
                                           class="btn-download">
                                            👁️ Ver Documento
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (isset($docs['reporte_final'])): ?>
                                    <li>
                                        <span>📝 Reporte Final</span>
                                        <a href="/SIEP/public/<?php echo str_replace('\\', '/', $docs['reporte_final']); ?>" 
                                           target="_blank" 
                                           class="btn-download">
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
                        <form method="POST" 
                              action="/SIEP/public/index.php?action=approveAccreditation" 
                              onsubmit="return confirm('¿Aprobar esta acreditación?');"
                              style="flex: 1;">
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
                        <form method="POST" 
                              action="/SIEP/public/index.php?action=rejectAccreditation" 
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