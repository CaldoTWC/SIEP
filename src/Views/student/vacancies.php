<?php
// Archivo: src/Views/student/vacancies.php
// Versión: 3.0.0 - Vista completa de vacantes con sistema simplificado de actividades
// Fecha: 2025-10-29

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['student']); 

// La variable $approvedVacancies viene del StudentController.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacantes Disponibles - SIEP</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .vacancies-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .page-header h1 {
            color: #6f1d33;
            margin-bottom: 10px;
            font-size: 32px;
        }
        
        .page-header p {
            color: #666;
            font-size: 16px;
        }
        
        .vacancy-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-left: 5px solid #005a9c;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .vacancy-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .vacancy-card h3 {
            margin-top: 0;
            color: #005a9c;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .company-name {
            color: #6f1d33;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .vacancy-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .meta-item strong {
            color: #333;
            font-size: 14px;
        }
        
        .meta-item span {
            color: #555;
            font-size: 14px;
        }
        
        .section-divider {
            border-top: 2px solid #e0e0e0;
            margin: 20px 0;
        }
        
        .info-section {
            margin: 20px 0;
        }
        
        .info-section h4 {
            color: #6f1d33;
            margin-bottom: 12px;
            font-size: 17px;
            border-left: 4px solid #d4a017;
            padding-left: 12px;
            font-weight: 600;
        }
        
        .info-section p {
            color: #555;
            line-height: 1.7;
            margin-bottom: 10px;
        }
        
        .info-section ul {
            list-style: none;
            padding-left: 0;
        }
        
        .info-section ul li {
            padding: 5px 0;
            padding-left: 20px;
            position: relative;
            color: #555;
            line-height: 1.6;
        }
        
        .info-section ul li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4caf50;
            font-weight: bold;
        }
        
        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        
        .badge-modality {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-support {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .badge-career {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #005a9c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            margin-right: 10px;
            transition: background 0.3s;
            font-weight: 600;
        }
        
        .btn:hover {
            background: #003d6b;
        }
        
        .btn-secondary {
            background: #6f1d33;
        }
        
        .btn-secondary:hover {
            background: #4a1322;
        }
        
        .no-vacancies {
            text-align: center;
            padding: 80px 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .no-vacancies h2 {
            color: #6f1d33;
            margin-bottom: 15px;
        }
        
        .no-vacancies p {
            color: #666;
            font-size: 16px;
        }
        
        .activities-box {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #005a9c;
        }
        
        .activities-box pre {
            background: white;
            padding: 15px;
            border-radius: 5px;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: inherit;
            line-height: 1.8;
            margin: 0;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #6f1d33;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .vacancy-meta {
                grid-template-columns: 1fr;
            }
            
            .vacancy-card h3 {
                font-size: 20px;
            }
            
            .company-name {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="vacancies-container">
        <div class="page-header">
            <h1>💼 Vacantes Disponibles</h1>
            <p>Explora las oportunidades de estancia profesional disponibles</p>
        </div>

        <a href="/SIEP/public/index.php?action=studentDashboard" class="back-link">← Volver al Panel</a>

        <?php if (empty($approvedVacancies)): ?>
            <div class="no-vacancies">
                <h2>📭 No hay vacantes disponibles en este momento</h2>
                <p>Vuelve pronto para ver nuevas oportunidades.</p>
            </div>
        <?php else: ?>
            <?php foreach ($approvedVacancies as $vacancy): 
                // Decodificar JSON
                $languages = json_decode($vacancy['required_languages'] ?? '[]', true) ?: [];
                $work_days = json_decode($vacancy['work_days'] ?? '[]', true) ?: [];
                $attention_days = json_decode($vacancy['attention_days'] ?? '[]', true) ?: [];
            ?>
                <div class="vacancy-card">
                    <!-- Título y Empresa -->
                    <h3><?php echo htmlspecialchars($vacancy['title']); ?></h3>
                    <div class="company-name">
                        🏢 <?php echo htmlspecialchars($vacancy['company_name']); ?>
                        <?php if (!empty($vacancy['commercial_name']) && $vacancy['commercial_name'] !== $vacancy['company_name']): ?>
                            (<?php echo htmlspecialchars($vacancy['commercial_name']); ?>)
                        <?php endif; ?>
                    </div>
                    
                    <!-- Badges informativos -->
                    <div style="margin-bottom: 15px;">
                        <span class="badge badge-modality">📍 <?php echo htmlspecialchars($vacancy['modality']); ?></span>
                        <?php if (!empty($vacancy['economic_support'])): ?>
                            <span class="badge badge-support">💰 $<?php echo number_format($vacancy['economic_support'], 2); ?> MXN/mes</span>
                        <?php endif; ?>
                        <span class="badge badge-career">🎓 <?php echo htmlspecialchars($vacancy['related_career']); ?></span>
                    </div>
                    
                    <!-- Información meta -->
                    <div class="vacancy-meta">
                        <?php if (!empty($vacancy['start_date']) && !empty($vacancy['end_date'])): ?>
                            <div class="meta-item">
                                <strong>📅 Periodo:</strong>
                                <span><?php echo date('d/m/Y', strtotime($vacancy['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($vacancy['end_date'])); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($vacancy['num_vacancies'])): ?>
                            <div class="meta-item">
                                <strong>👥 Plazas:</strong>
                                <span><?php echo $vacancy['num_vacancies']; ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($work_days)): ?>
                            <div class="meta-item">
                                <strong>📆 Días:</strong>
                                <span><?php echo implode(', ', $work_days); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($vacancy['start_time']) && !empty($vacancy['end_time'])): ?>
                            <div class="meta-item">
                                <strong>🕐 Horario:</strong>
                                <span><?php echo substr($vacancy['start_time'], 0, 5); ?> - <?php echo substr($vacancy['end_time'], 0, 5); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="section-divider"></div>
                    
                    <!-- Información clave -->
                    <?php if (!empty($vacancy['key_information'])): ?>
                        <div class="info-section">
                            <h4>✨ Información Destacada</h4>
                            <p><?php echo nl2br(htmlspecialchars($vacancy['key_information'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Descripción del perfil -->
                    <?php if (!empty($vacancy['description']) || !empty($vacancy['activity_details'])): ?>
                        <div class="info-section">
                            <h4>📋 Descripción del Perfil</h4>
                            <?php if (!empty($vacancy['description'])): ?>
                                <p><?php echo nl2br(htmlspecialchars($vacancy['description'])); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($vacancy['activity_details'])): ?>
                                <p><?php echo nl2br(htmlspecialchars($vacancy['activity_details'])); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- ACTIVIDADES A REALIZAR (NUEVO SISTEMA SIMPLIFICADO) -->
                    <?php if (!empty($vacancy['activities_list'])): ?>
                        <div class="info-section">
                            <h4>💼 Actividades a Realizar</h4>
                            <div class="activities-box">
                                <pre><?php echo htmlspecialchars($vacancy['activities_list']); ?></pre>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Conocimientos requeridos -->
                    <?php if (!empty($vacancy['required_knowledge'])): ?>
                        <div class="info-section">
                            <h4>🛠️ Conocimientos Requeridos</h4>
                            <p><?php echo nl2br(htmlspecialchars($vacancy['required_knowledge'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Competencias requeridas -->
                    <?php if (!empty($vacancy['required_competencies'])): ?>
                        <div class="info-section">
                            <h4>⭐ Competencias Requeridas</h4>
                            <p><?php echo nl2br(htmlspecialchars($vacancy['required_competencies'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Idiomas requeridos -->
                    <?php if (!empty($languages) && !in_array('Ninguno', $languages)): ?>
                        <div class="info-section">
                            <h4>🌐 Idiomas Requeridos</h4>
                            <ul>
                                <?php foreach ($languages as $lang): ?>
                                    <li><?php echo htmlspecialchars($lang); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Información de contacto -->
                    <?php if (!empty($attention_days) || !empty($vacancy['attention_schedule'])): ?>
                        <div class="info-section">
                            <h4>📞 Información de Contacto</h4>
                            <?php if (!empty($attention_days)): ?>
                                <p><strong>Días de atención:</strong> <?php echo implode(', ', $attention_days); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($vacancy['attention_schedule'])): ?>
                                <p><strong>Horario de atención:</strong> <?php echo htmlspecialchars($vacancy['attention_schedule']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Botones de acción -->
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                        <?php if (!empty($vacancy['website'])): ?>
                            <a href="<?php echo htmlspecialchars($vacancy['website']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                                🌐 Visitar Sitio Web
                            </a>
                        <?php endif; ?>
                        <a href="/SIEP/public/index.php?action=showVacancyDetails&id=<?php echo $vacancy['id']; ?>" class="btn">
                            📄 Ver Detalles Completos
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="/SIEP/public/index.php?action=studentDashboard" class="back-link">← Volver al Panel</a>
        </div>
    </div>
</body>
</html>