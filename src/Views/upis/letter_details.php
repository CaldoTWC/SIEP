<?php
/**
 * Vista de Detalles de Solicitud de Carta
 * 
 * Muestra toda la información de una solicitud individual
 * con botones para aprobar o rechazar
 * 
 * @package SIEP\Views\Upis
 * @version 1.0.0
 */

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']); 

// $letter viene del controlador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Solicitud - SIEP</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
        }

        .details-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #6f1d33 0%, #9b2847 100%);
            color: white;
            padding: 25px 30px;
        }

        .card-header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }

        .card-header .badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
            background: rgba(255,255,255,0.2);
        }

        .card-body {
            padding: 30px;
        }

        .section-title {
            background: #f8f9fa;
            padding: 12px 15px;
            margin: 25px -30px 20px -30px;
            font-weight: 600;
            color: #6f1d33;
            border-left: 4px solid #8b1538;
        }

        .section-title:first-of-type {
            margin-top: 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .info-value {
            color: #333;
        }

        .actions-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #dee2e6;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .form-group {
            margin-top: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #dee2e6;
            border-radius: 5px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            min-height: 120px;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: #8b1538;
        }

        #rejectSection {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/SIEP/public/index.php?action=presentationLettersHub&tab=pending" class="btn btn-secondary" style="margin-bottom: 20px;">
            ← Volver a Solicitudes Pendientes
        </a>

        <div class="details-card">
            <div class="card-header">
                <h1>📄 Detalles de la Solicitud</h1>
                <span class="badge">Pendiente de Revisión</span>
            </div>

            <div class="card-body">
                <!-- Información del Estudiante -->
                <div class="section-title">👤 Información del Estudiante</div>
                
                <div class="info-grid">
                    <div class="info-label">Boleta:</div>
                    <div class="info-value"><strong><?php echo htmlspecialchars($letter['boleta']); ?></strong></div>
                </div>

                <div class="info-grid">
                    <div class="info-label">Nombre Completo:</div>
                    <div class="info-value">
                        <?php echo htmlspecialchars($letter['first_name'] . ' ' . $letter['last_name_p'] . ' ' . $letter['last_name_m']); ?>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-label">Carrera:</div>
                    <div class="info-value"><?php echo htmlspecialchars($letter['career']); ?></div>
                </div>

                <div class="info-grid">
                    <div class="info-label">Avance Curricular:</div>
                    <div class="info-value"><?php echo htmlspecialchars($letter['percentage_progress']); ?>%</div>
                </div>

                <div class="info-grid">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><?php echo htmlspecialchars($letter['student_email']); ?></div>
                </div>

                <!-- Información de la Empresa -->
                <?php if (!empty($letter['company_name'])): ?>
                <div class="section-title">🏢 Información de la Empresa</div>
                
                <div class="info-grid">
                    <div class="info-label">Razón Social:</div>
                    <div class="info-value"><?php echo htmlspecialchars($letter['company_name']); ?></div>
                </div>

                <?php if (!empty($letter['commercial_name'])): ?>
                <div class="info-grid">
                    <div class="info-label">Nombre Comercial:</div>
                    <div class="info-value"><?php echo htmlspecialchars($letter['commercial_name']); ?></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($letter['company_email'])): ?>
                <div class="info-grid">
                    <div class="info-label">Email Empresa:</div>
                    <div class="info-value"><?php echo htmlspecialchars($letter['company_email']); ?></div>
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Detalles de la Carta -->
                <div class="section-title">📝 Detalles de la Carta</div>
                
                <div class="info-grid">
                    <div class="info-label">Fecha de Solicitud:</div>
                    <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($letter['created_at'])); ?></div>
                </div>

                <div class="info-grid">
                    <div class="info-label">Destinatario Específico:</div>
                    <div class="info-value">
                        <?php if ($letter['has_specific_recipient']): ?>
                            ✅ Sí - <?php echo htmlspecialchars($letter['recipient_name']); ?><br>
                            <small style="color: #666;">Cargo: <?php echo htmlspecialchars($letter['recipient_position']); ?></small>
                        <?php else: ?>
                            ❌ No (A quien corresponda)
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-label">Requiere Horas:</div>
                    <div class="info-value">
                        <?php echo $letter['requires_hours'] ? '✅ Sí' : '❌ No'; ?>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-label">Tipo de Plantilla:</div>
                    <div class="info-value">
                        <?php 
                        $types = [
                            'normal' => 'Normal',
                            'with_hours' => 'Con Horas',
                            'specific_recipient' => 'Destinatario Específico'
                        ];
                        echo $types[$letter['letter_template_type']] ?? 'Normal';
                        ?>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="actions-container">
                    <!-- Formulario de Aprobación -->
                    <form action="/SIEP/public/index.php?action=approveSingleLetter" 
                          method="post" 
                          style="flex: 1;"
                          onsubmit="return confirm('¿Confirmas que deseas APROBAR esta solicitud?');">
                        <input type="hidden" name="application_id" value="<?php echo $letter['id']; ?>">
                        
                        <div class="form-group">
                            <label for="approve_comments">Comentarios (Opcional):</label>
                            <textarea id="approve_comments" name="comments" 
                                      placeholder="Agrega comentarios si lo consideras necesario..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success" style="width: 100%;">
                            ✅ Aprobar Solicitud
                        </button>
                    </form>

                    <!-- Botón para mostrar formulario de rechazo -->
                    <div style="flex: 1;">
                        <button type="button" 
                                class="btn btn-danger" 
                                style="width: 100%;"
                                onclick="toggleRejectSection()">
                            ❌ Rechazar Solicitud
                        </button>
                    </div>
                </div>

                <!-- Sección de Rechazo (oculta por defecto) -->
                <div id="rejectSection">
                    <h3 style="color: #856404; margin-top: 0;">⚠️ Rechazar Solicitud</h3>
                    <form action="/SIEP/public/index.php?action=rejectSingleLetter" 
                          method="post"
                          onsubmit="return confirm('¿Estás seguro de RECHAZAR esta solicitud? El estudiante será notificado.');">
                        <input type="hidden" name="application_id" value="<?php echo $letter['id']; ?>">
                        
                        <div class="form-group">
                            <label for="rejection_reason">Razón del Rechazo (Obligatorio): *</label>
                            <textarea id="rejection_reason" 
                                      name="rejection_reason" 
                                      required
                                      placeholder="Explica claramente por qué se rechaza la solicitud. Esta información será enviada al estudiante."></textarea>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-danger">
                                ❌ Confirmar Rechazo
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="toggleRejectSection()">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleRejectSection() {
            const section = document.getElementById('rejectSection');
            section.style.display = section.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>