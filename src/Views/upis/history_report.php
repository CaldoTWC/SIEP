<?php
require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Reportes - SIEP UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .reports-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .page-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }

        .page-header p {
            margin: 0;
            opacity: 0.9;
        }

        .section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }

        .section-title h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }

        .section-title .icon {
            font-size: 32px;
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .report-card {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            border-color: #667eea;
        }

        .report-card h3 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .report-card p {
            color: #666;
            font-size: 14px;
            margin: 0 0 15px 0;
            line-height: 1.5;
        }

        .report-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-report {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-pdf {
            background: #e74c3c;
            color: white;
        }

        .btn-pdf:hover {
            background: #c0392b;
        }

        .btn-excel {
            background: #27ae60;
            color: white;
        }

        .btn-excel:hover {
            background: #229954;
        }

        .btn-view {
            background: #3498db;
            color: white;
        }

        .btn-view:hover {
            background: #2980b9;
        }

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-box.blue { background: linear-gradient(135deg, #2980b9 0%, #3498db 100%); }
        .stat-box.green { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); }
        .stat-box.orange { background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%); }
        .stat-box.red { background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%); }

        .stat-box h4 {
            font-size: 14px;
            font-weight: 400;
            margin: 0 0 10px 0;
            opacity: 0.9;
        }

        .stat-box .number {
            font-size: 32px;
            font-weight: bold;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-info {
            background: #d6eaf8;
            color: #2471a3;
            border-left: 4px solid #2980b9;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: #34495e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="reports-container">
        
        <a href="/SIEP/public/index.php?action=upisDashboard" class="back-button">← Volver al Dashboard</a>

        <!-- Header -->
        <div class="page-header">
            <h1>📊 Centro de Reportes y Estadísticas</h1>
            <p>Sistema Integral de Estancias Profesionales - UPIICSA IPN</p>
        </div>

        <!-- Sección: Reportes de Vacantes (Nuevos) -->
        <div class="section">
            <div class="section-title">
                <span class="icon">📈</span>
                <h2>Reportes de Vacantes</h2>
            </div>

            <p class="alert alert-info">
                <strong>📌 Nota:</strong> Estos reportes se generan en tiempo real con los datos actuales del sistema.
            </p>

            <div class="reports-grid">
                
                <!-- Vacantes Activas -->
                <div class="report-card">
                    <h3>🟢 Vacantes Activas</h3>
                    <p>Listado de todas las vacantes aprobadas y disponibles actualmente para estudiantes.</p>
                    <div class="report-actions">
                        <a href="/SIEP/public/index.php?action=exportActivePDF" class="btn-report btn-pdf" target="_blank">
                            📄 Descargar PDF
                        </a>
                        <a href="/SIEP/public/index.php?action=manageActiveVacancies" class="btn-report btn-view">
                            👁️ Ver Listado
                        </a>
                    </div>
                </div>

                <!-- Vacantes Completadas -->
                <div class="report-card">
                    <h3>✅ Vacantes Completadas</h3>
                    <p>Vacantes finalizadas exitosamente (cupos llenos, estancias concluidas, etc.).</p>
                    <div class="report-actions">
                        <a href="/SIEP/public/index.php?action=exportCompletedPDF" class="btn-report btn-pdf" target="_blank">
                            📄 Descargar PDF
                        </a>
                    </div>
                </div>

                <!-- Vacantes Canceladas -->
                <div class="report-card">
                    <h3>🗑️ Vacantes Canceladas</h3>
                    <p>Historial de vacantes rechazadas por UPIS o canceladas por empresas.</p>
                    <div class="report-actions">
                        <a href="/SIEP/public/index.php?action=exportCanceledPDF" class="btn-report btn-pdf" target="_blank">
                            📄 Descargar PDF
                        </a>
                        <a href="/SIEP/public/index.php?action=vacancyTrash" class="btn-report btn-view">
                            👁️ Ver Papelera
                        </a>
                    </div>
                </div>

                <!-- Reporte Completo (Excel) -->
                <div class="report-card">
                    <h3>📊 Base de Datos Completa</h3>
                    <p>Exportación de TODAS las vacantes (activas, completadas, canceladas) en formato Excel.</p>
                    <div class="report-actions">
                        <a href="/SIEP/public/index.php?action=exportAllExcel" class="btn-report btn-excel">
                            📗 Descargar Excel
                        </a>
                    </div>
                </div>

                <!-- Análisis de Empresas (Excel) -->
                <div class="report-card">
                    <h3>🏢 Análisis de Empresas</h3>
                    <p>Estadísticas por empresa: vacantes totales, tasa de éxito, completadas vs canceladas.</p>
                    <div class="report-actions">
                        <a href="/SIEP/public/index.php?action=exportCompanyAnalysisExcel" class="btn-report btn-excel">
                            📗 Descargar Excel
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sección: Reportes de Estancias (Legacy) -->
        <div class="section">
            <div class="section-title">
                <span class="icon">📚</span>
                <h2>Reportes de Estancias y Trámites</h2>
            </div>

            <div class="reports-grid">
                
                        <!-- Sección: Reportes de Estudiantes -->
        <div class="section">
            <div class="section-title">
                <span class="icon">👥</span>
                <h2>Reportes de Estudiantes y Empresas</h2>
            </div>

            <div class="reports-grid">
                
                <!-- Tiempo de Procesamiento de Estudiantes -->
                <div class="report-card">
                    <h3>⏱️ Tiempo de Procesamiento</h3>
                    <p>Análisis del tiempo que tardan los estudiantes desde su registro hasta la acreditación aprobada por UPIS.</p>
                    <div class="report-actions">
                        <a href="/SIEP/public/index.php?action=exportStudentProcessingPDF" class="btn-report btn-pdf" target="_blank">
                            📄 Descargar PDF
                        </a>
                        <a href="/SIEP/public/index.php?action=exportStudentProcessingExcel" class="btn-report btn-excel">
                            📗 Descargar Excel
                        </a>
                    </div>
                </div>

                <!-- Empresas y Estudiantes en Servicio -->
                <div class="report-card">
                    <h3>🏢 Empresas y Estudiantes</h3>
                    <p>Listado de empresas con los estudiantes que están realizando o realizaron su estancia profesional.</p>
                    <div class="report-actions">
                        <a href="/SIEP/public/index.php?action=exportCompanyStudentsPDF" class="btn-report btn-pdf" target="_blank">
                            📄 Descargar PDF
                        </a>
                        <a href="/SIEP/public/index.php?action=exportCompanyStudentsExcel" class="btn-report btn-excel">
                            📗 Descargar Excel
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sección: Accesos Rápidos -->
        <div class="section">
            <div class="section-title">
                <span class="icon">⚡</span>
                <h2>Accesos Rápidos</h2>
            </div>

            <div class="reports-grid">
                
                <div class="report-card">
                    <h3>🔄 Hub de Vacantes</h3>
                    <p>Centro de gestión del ciclo de vida completo de vacantes.</p>
                    <div class="report-actions">
                        <a href="/SIEP/public/index.php?action=vacancyHub" class="btn-report btn-view">
                            🚀 Ir al Hub
                        </a>
                    </div>
                </div>

                <div class="report-card">
                    <h3>📝 Revisar Vacantes Pendientes</h3>
                    <p>Aprobar o rechazar vacantes nuevas publicadas por empresas.</p>
                    <div class="report-actions">
                        <a href="/SIEP/public/index.php?action=reviewVacancies" class="btn-report btn-view">
                            ✅ Revisar
                        </a>
                    </div>
                </div>

                <div class="report-card">
                    <h3>🏢 Revisar Empresas</h3>
                    <p>Aprobar o rechazar solicitudes de registro de empresas.</p>
                    <div class="report-actions">
                        <a href="/SIEP/public/index.php?action=reviewCompanies" class="btn-report btn-view">
                            ✅ Revisar
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</body>
</html>