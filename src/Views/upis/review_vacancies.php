<?php
// Archivo: src/Views/upis/review_vacancies.php
// Versión: 3.0.0 - Vista completa de revisión de vacantes
// Fecha: 2025-10-29

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis']); 

// La variable $pendingVacancies viene del UpisController
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Vacantes - UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .review-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .vacancy-review-card {
            background: #fff;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .vacancy-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .vacancy-header h2 {
            color: #6f1d33;
            margin: 0;
        }
        
        .vacancy-status {
            background: #fff3cd;
            color: #856404;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .info-block {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #6f1d33;
        }
        
        .info-block h4 {
            color: #6f1d33;
            margin: 0 0 10px 0;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .info-block p {
            margin: 5px 0;
            color: #555;
            line-height: 1.5;
        }
        
        .info-block strong {
            color: #333;
        }
        
        .section-title {
            background: linear-gradient(135deg, #6f1d33 0%, #9b2847 100%);
            color: white;
            padding: 12px 20px;
            margin: 25px 0 15px 0;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
        }
        
        .activities-review {
            background: #e8f5e9;
            padding: 20px;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        .activities-review ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .activities-review li {
            margin: 10px 0;
            color: #2e7d32;
            line-height: 1.6;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-approve {
            background: #4caf50;
            color: white;
        }
        
        .btn-approve:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
        }
        
        .btn-reject {
            background: #f44336;
            color: white;
        }
        
        .btn-reject:hover {
            background: #da190b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(244, 67, 54, 0.3);
        }
        
        .btn-details {
            background: #2196f3;
            color: white;
        }
        
        .btn-details:hover {
            background: #0b7dda;
        }
        
        .no-vacancies {
            text-align: center;
            padding: 80px 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 8px;
        }
        
        .badge-modality {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-career {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        
        .company-sensitive-info {
            background: #ffe0e0;
            border: 2px dashed #f44336;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        .company-sensitive-info h4 {
            color: #d32f2f;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="review-container">
        <h1 style="text-align: center; color: #6f1d33; margin-bottom: 10px;">🔍 Revisar Vacantes Pendientes</h1>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">
            Revisa y aprueba las vacantes publicadas por las empresas
        </p>

        <?php if (empty($pendingVacancies)): ?>
            <div class="no-vacancies">
                <h2>✅ No hay vacantes pendientes de revisión</h2>
                <p>Todas las vacantes han sido revisadas.</p>
            </div>
        <?php else: ?>
            <?php foreach ($pendingVacancies as $vacancy): 
                // Decodificar JSON
                $attention_days = json_decode($vacancy['attention_days'] ?? '[]', true) ?: [];
                $languages = json_decode($vacancy['required_languages'] ?? '[]', true) ?: [];
                $work_days = json_decode($vacancy['work_days'] ?? '[]', true) ?: [];
            ?>
                <div class="vacancy-review-card">
                    <!-- Header -->
                    <div class="vacancy-header">
                        <div>
                            <h2><?php echo htmlspecialchars($vacancy['title']); ?></h2>
                            <p style="color: #666; margin: 5px 0;">
                                Publicada el: <?php echo date('d/m/Y H:i', strtotime($vacancy['posted_at'])); ?>
                            </p>
                        </div>
                        <span class="vacancy-status">⏳ PENDIENTE</span>
                    </div>
                    
                    <!-- SECCIÓN 1: INFORMACIÓN DE LA EMPRESA -->
                    <h3 class="section-title">🏢 INFORMACIÓN DE LA EMPRESA</h3>
                    
                    <div class="info-grid">
                        <div class="info-block">
                            <h4>Empresa</h4>
                            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($vacancy['company_name']); ?></p>
                            <?php if (!empty($vacancy['commercial_name'])): ?>
                                <p><strong>Nombre Comercial:</strong> <?php echo htmlspecialchars($vacancy['commercial_name']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="info-block">
                            <h4>Contacto</h4>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($vacancy['company_email']); ?></p>
                            <?php if (!empty($vacancy['company_phone'])): ?>
                                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($vacancy['company_phone']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($vacancy['website'])): ?>
                            <div class="info-block">
                                <h4>Sitio Web</h4>
                                <p><a href="<?php echo htmlspecialchars($vacancy['website']); ?>" target="_blank"><?php echo htmlspecialchars($vacancy['website']); ?></a></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- SECCIÓN 2: ATENCIÓN A ESTUDIANTES -->
                    <?php if (!empty($attention_days) || !empty($vacancy['attention_schedule'])): ?>
                        <h3 class="section-title">📞 ATENCIÓN A ESTUDIANTES INTERESADOS</h3>
                        <div class="info-grid">
                            <?php if (!empty($attention_days)): ?>
                                <div class="info-block">
                                    <h4>Días de Atención</h4>
                                    <p><?php echo implode(', ', $attention_days); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($vacancy['attention_schedule'])): ?>
                                <div class="info-block">
                                    <h4>Horario de Atención</h4>
                                    <p><?php echo htmlspecialchars($vacancy['attention_schedule']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- SECCIÓN 3: GENERALIDADES -->
                    <h3 class="section-title">📋 GENERALIDADES DE LA VACANTE</h3>
                    <div class="info-grid">
                        <div class="info-block">
                            <h4>Plazas y Periodo</h4>
                            <p><strong>Número de vacantes:</strong> <?php echo $vacancy['num_vacancies']; ?></p>
                            <?php if (!empty($vacancy['start_date']) && !empty($vacancy['end_date'])): ?>
                                <p><strong>Periodo:</strong> <?php echo date('d/m/Y', strtotime($vacancy['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($vacancy['end_date'])); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($vacancy['economic_support'])): ?>
                            <div class="info-block">
                                <h4>Apoyo Económico</h4>
                                <p><strong>$<?php echo number_format($vacancy['economic_support'], 2); ?> MXN/mes</strong></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="info-block">
                            <h4>Carrera</h4>
                            <p><span class="badge badge-career"><?php echo htmlspecialchars($vacancy['related_career']); ?></span></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($vacancy['vacancy_names'])): ?>
                        <div class="info-block" style="margin-top: 15px;">
                            <h4>Descripción Adicional de Vacantes</h4>
                            <p><?php echo nl2br(htmlspecialchars($vacancy['vacancy_names'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- SECCIÓN 4: PERFIL -->
                    <h3 class="section-title">👤 PERFIL PARA OCUPAR LA VACANTE</h3>
                    
                    <?php if (!empty($vacancy['key_information'])): ?>
                        <div class="warning-box">
                            <strong>✨ Información Clave:</strong>
                            <p style="margin: 10px 0 0 0;"><?php echo nl2br(htmlspecialchars($vacancy['key_information'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($vacancy['description'])): ?>
                        <div class="info-block" style="margin: 15px 0;">
                            <h4>Descripción del Perfil</h4>
                            <p><?php echo nl2br(htmlspecialchars($vacancy['description'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-grid">
                        <?php if (!empty($vacancy['required_knowledge'])): ?>
                            <div class="info-block">
                                <h4>🛠️ Conocimientos Requeridos</h4>
                                <p><?php echo nl2br(htmlspecialchars($vacancy['required_knowledge'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($vacancy['required_competencies'])): ?>
                            <div class="info-block">
                                <h4>⭐ Competencias Requeridas</h4>
                                <p><?php echo nl2br(htmlspecialchars($vacancy['required_competencies'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($languages) && !in_array('Ninguno', $languages)): ?>
                        <div class="info-block" style="margin-top: 15px;">
                            <h4>🌐 Idiomas Requeridos</h4>
                            <p><?php echo implode(', ', $languages); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- SECCIÓN 5: ACTIVIDADES -->
<h3 class="section-title">💼 ACTIVIDADES A REALIZAR</h3>
<div class="activities-review">
    <?php if (!empty($vacancy['activities_list'])): ?>
        <h4 style="margin-top: 0; color: #2e7d32;">Lista de Actividades:</h4>
        <pre style="background: white; padding: 15px; border-radius: 5px; white-space: pre-wrap; font-family: inherit; line-height: 1.8; margin: 0; border: 1px solid #ddd;"><?php echo htmlspecialchars($vacancy['activities_list']); ?></pre>
    <?php endif; ?>
    
    <?php if (!empty($vacancy['activity_details'])): ?>
        <h4 style="color: #2e7d32; margin-top: 15px;">Descripción General:</h4>
        <p style="margin: 0;"><?php echo nl2br(htmlspecialchars($vacancy['activity_details'])); ?></p>
    <?php endif; ?>
</div>
                    
                    <!-- SECCIÓN 6: MODALIDAD -->
                    <h3 class="section-title">🏠 MODALIDAD DE TRABAJO</h3>
                    <div class="info-grid">
                        <div class="info-block">
                            <h4>Modalidad</h4>
                            <p><span class="badge badge-modality"><?php echo htmlspecialchars($vacancy['modality']); ?></span></p>
                        </div>
                        
                        <?php if (!empty($work_days)): ?>
                            <div class="info-block">
                                <h4>Días de Trabajo</h4>
                                <p><?php echo implode(', ', $work_days); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($vacancy['start_time']) && !empty($vacancy['end_time'])): ?>
                            <div class="info-block">
                                <h4>Horario</h4>
                                <p><?php echo substr($vacancy['start_time'], 0, 5); ?> - <?php echo substr($vacancy['end_time'], 0, 5); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($vacancy['work_location_address']) && ($vacancy['modality'] === 'Presencial' || $vacancy['modality'] === 'Hibrida')): ?>
                        <div class="info-block" style="margin-top: 15px;">
                            <h4>📍 Dirección del Lugar de Trabajo</h4>
                            <p><?php echo nl2br(htmlspecialchars($vacancy['work_location_address'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- SECCIÓN 7: LOGOTIPO -->
                    <?php if ($vacancy['logo_auth'] == 1): ?>
                        <h3 class="section-title">🖼️ PUBLICACIÓN DE LOGOTIPO</h3>
                        <div class="info-block">
                            <p><strong>✅ La empresa autorizó publicar su logotipo</strong></p>
                            <?php if (!empty($vacancy['logo_url'])): ?>
                                <p><strong>URL:</strong> <a href="<?php echo htmlspecialchars($vacancy['logo_url']); ?>" target="_blank"><?php echo htmlspecialchars($vacancy['logo_url']); ?></a></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Botones de acción -->
                    <div class="action-buttons">
                        <form method="POST" action="/SIEP/public/index.php?action=approveVacancy" style="display: inline;">
                            <input type="hidden" name="vacancy_id" value="<?php echo $vacancy['id']; ?>">
                            <button type="submit" class="btn btn-approve" onclick="return confirm('¿Está seguro de aprobar esta vacante?');">
                                ✅ Aprobar Vacante
                            </button>
                        </form>
                        
                        <button class="btn btn-reject" onclick="showRejectModal(<?php echo $vacancy['id']; ?>);">
                            ❌ Rechazar Vacante
                        </button>
                        
                        <a href="/SIEP/public/index.php?action=showVacancyDetails&id=<?php echo $vacancy['id']; ?>" class="btn btn-details" target="_blank">
                            📄 Ver Vista de Estudiante
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="/SIEP/public/index.php?action=upisDashboard" style="color: #6f1d33; text-decoration: none; font-weight: bold;">← Volver al Panel UPIS</a>
        </div>
    </div>

        <!-- Modal para rechazar -->
    <div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%;">
            <h3 style="color: #6f1d33; margin-top: 0;">Rechazar Vacante</h3>
            <form method="POST" action="/SIEP/public/index.php?action=rejectVacancy">
                <input type="hidden" name="vacancy_id" id="reject_vacancy_id">
                
                <label style="display: block; margin: 15px 0 10px 0; font-weight: bold;">
                    Motivo: <span style="color: red;">*</span>
                </label>
                
                <label style="display: block; margin: 8px 0;">
                    <input type="radio" name="rejection_reason" value="Información incompleta" required> 
                    Información incompleta
                </label>
                <label style="display: block; margin: 8px 0;">
                    <input type="radio" name="rejection_reason" value="Actividades no relacionadas" required> 
                    Actividades no relacionadas con carrera
                </label>
                <label style="display: block; margin: 8px 0;">
                    <input type="radio" name="rejection_reason" value="Apoyo económico insuficiente" required> 
                    Apoyo económico insuficiente
                </label>
                <label style="display: block; margin: 8px 0;">
                    <input type="radio" name="rejection_reason" value="Horarios inadecuados" required> 
                    Horarios inadecuados
                </label>
                <label style="display: block; margin: 8px 0;">
                    <input type="radio" name="rejection_reason" value="Otro" required> 
                    Otro
                </label>
                
                <label style="display: block; margin: 20px 0 10px 0; font-weight: bold;">
                    Justificación detallada: <span style="color: red;">*</span>
                </label>
                <textarea name="rejection_notes" required 
                          placeholder="Explica el motivo del rechazo para que la empresa pueda corregir..."
                          style="width: 100%; min-height: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-reject">Confirmar Rechazo</button>
                    <button type="button" class="btn" style="background: #999;" onclick="closeRejectModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectModal(vacancyId) {
            document.getElementById('reject_vacancy_id').value = vacancyId;
            document.getElementById('rejectModal').style.display = 'flex';
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }
    </script>
</body>
</html>