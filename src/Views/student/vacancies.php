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
    <link rel="stylesheet" href="/SIEP/public/css/student.css">

</head>
<body>
<!-- BARRA DE NAVEGACIÓN -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="/SIEP/public/index.php" class="nav-logo">SIEP</a>
            <ul class="nav-menu">
                <li class="nav-item"><a href="#hero" class="nav-link">Inicio</a></li>
                <li class="nav-item"><a href="#user-section" class="nav-link">Usuarios</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showLogin" class="nav-link btn-nav">Iniciar
                        Sesión</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showRegisterSelection"
                        class="nav-link btn-nav">Registrarse</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <div class="page-header">
            <h1>💼 Vacantes Disponibles</h1>
            <p>Explora las oportunidades de estancia profesional disponibles</p>
        </div>

        <a href="/SIEP/public/index.php?action=studentDashboard" class="logout-btn">← Volver al Panel</a><br><br>

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
            <a href="/SIEP/public/index.php?action=studentDashboard" class="logout-btn">← Volver al Panel</a>
        </div>
    </div>
</body>
</html>