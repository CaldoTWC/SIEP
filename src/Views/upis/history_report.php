<?php
// Archivo: src/Views/upis/history_report.php
require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']); 

// Obtener estadísticas de vacantes
require_once(__DIR__ . '/../../Models/Vacancy.php');
$vacancyModel = new Vacancy();
$vacancyStats = $vacancyModel->getGlobalStatistics();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Histórico y Reportes - UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #6c757d; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        
        .reports-section {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            margin: 30px 0;
        }
        
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .report-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s;
        }
        
        .report-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .report-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #005a9c;
        }
        
        .report-description {
            font-size: 13px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .stats-mini {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px 0;
            padding: 15px;
            background: white;
            border-radius: 8px;
        }
        
        .stat-mini {
            text-align: center;
            padding: 10px;
        }
        
        .stat-mini-number {
            font-size: 24px;
            font-weight: bold;
            color: #005a9c;
        }
        
        .stat-mini-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Histórico y Reportes</h1>
        <p>Consulta el histórico de trámites y genera reportes del sistema de vacantes</p>
        
        <a href="/SIEP/public/index.php?action=upisDashboard" class="btn">← Volver al Dashboard</a>
        
        <!-- ========================================== -->
        <!-- SECCIÓN: REPORTES DE VACANTES -->
        <!-- ========================================== -->
        <div class="reports-section">
            <h2 style="margin-top: 0; color: #005a9c;">📈 Reportes de Vacantes</h2>
            <p>Genera reportes en PDF y Excel del sistema de gestión de vacantes</p>
            
            <!-- Mini Estadísticas -->
            <div class="stats-mini">
                <div class="stat-mini">
                    <div class="stat-mini-number"><?php echo $vacancyStats['pending'] ?? 0; ?></div>
                    <div class="stat-mini-label">Pendientes</div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-number"><?php echo $vacancyStats['approved'] ?? 0; ?></div>
                    <div class="stat-mini-label">Activas</div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-number"><?php echo $vacancyStats['completed'] ?? 0; ?></div>
                    <div class="stat-mini-label">Completadas</div>
                </div>
                <div class="stat-mini">
                    <div class="stat-mini-number"><?php echo $vacancyStats['rejected'] ?? 0; ?></div>
                    <div class="stat-mini-label">Canceladas</div>
                </div>
            </div>
            
            <!-- Grid de Reportes -->
            <div class="reports-grid">
                
                <!-- Reporte PDF: Vacantes Activas -->
                <div class="report-card">
                    <div class="report-icon">📄</div>
                    <div class="report-title">Vacantes Activas</div>
                    <div class="report-description">
                        Listado de todas las vacantes publicadas actualmente
                    </div>
                    <a href="/SIEP/public/index.php?action=exportActivePDF" 
                       class="btn" style="font-size: 13px; padding: 8px 16px;" target="_blank">
                        Descargar PDF
                    </a>
                </div>
                
                <!-- Reporte PDF: Vacantes Completadas -->
                <div class="report-card">
                    <div class="report-icon">✅</div>
                    <div class="report-title">Vacantes Completadas</div>
                    <div class="report-description">
                        Vacantes finalizadas exitosamente con sus motivos
                    </div>
                    <a href="/SIEP/public/index.php?action=exportCompletedPDF" 
                       class="btn" style="font-size: 13px; padding: 8px 16px; background: #28a745;" target="_blank">
                        Descargar PDF
                    </a>
                </div>
                
                <!-- Reporte PDF: Vacantes Canceladas -->
                <div class="report-card">
                    <div class="report-icon">❌</div>
                    <div class="report-title">Vacantes Canceladas</div>
                    <div class="report-description">
                        Vacantes rechazadas con justificaciones detalladas
                    </div>
                    <a href="/SIEP/public/index.php?action=exportCanceledPDF" 
                       class="btn" style="font-size: 13px; padding: 8px 16px; background: #dc3545;" target="_blank">
                        Descargar PDF
                    </a>
                </div>
                
                <!-- Reporte Excel: Todas las Vacantes -->
                <div class="report-card">
                    <div class="report-icon">📊</div>
                    <div class="report-title">Excel - Todas las Vacantes</div>
                    <div class="report-description">
                        Base de datos completa en formato Excel
                    </div>
                    <a href="/SIEP/public/index.php?action=exportAllExcel" 
                       class="btn" style="font-size: 13px; padding: 8px 16px; background: #17a2b8;">
                        Descargar Excel
                    </a>
                </div>
                
                <!-- Reporte Excel: Análisis de Empresas -->
                <div class="report-card">
                    <div class="report-icon">🏢</div>
                    <div class="report-title">Excel - Análisis Empresas</div>
                    <div class="report-description">
                        Vacantes agrupadas por empresa con estadísticas
                    </div>
                    <a href="/SIEP/public/index.php?action=exportCompanyAnalysisExcel" 
                       class="btn" style="font-size: 13px; padding: 8px 16px; background: #ffc107; color: #333;">
                        Descargar Excel
                    </a>
                </div>
                
            </div>
        </div>
        
        <hr style="margin: 40px 0;">
        
        <!-- ========================================== -->
        <!-- SECCIÓN: HISTÓRICO DE TRÁMITES -->
        <!-- ========================================== -->
        <h2>📋 Histórico de Trámites Completados</h2>
        <p>Registro de estudiantes que han finalizado su proceso de Estancia Profesional</p>
        
        <div style="text-align: right; margin-bottom: 20px;">
             <a href="/SIEP/public/index.php?action=downloadHistoryReport" class="btn" target="_blank">Exportar a PDF</a>
        </div>

        <?php if (empty($completedProcesses)): ?>
            <p>Aún no hay trámites completados en el histórico.</p>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Boleta</th>
                            <th>Fecha Solicitud Carta</th>
                            <th>Fecha Finalización</th>
                            <th>Duración (Días)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completedProcesses as $process): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($process['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($process['student_boleta']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($process['presentation_letter_date'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($process['accreditation_completed_date'])); ?></td>
                                <td><?php echo htmlspecialchars($process['total_duration_days']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
    </div>
</body>
</html>