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
    <link rel="stylesheet" href="/SIEP/public/css/upis.css">

</head>

<body>
    <!-- Encabezado bonito -->
    <div class="upis-header">
        <h1>Panel de Administración de UPIS</h1>
    </div>

    <div class="container">

        <!-- Header -->
        <div class="page-header">
            <h1>📊 Centro de Reportes y Estadísticas</h1>
        </div>
        <a href="/SIEP/public/index.php?action=upisDashboard" class="logout-btn">← Volver al Dashboard</a><br><br>

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
                        <a href="/SIEP/public/index.php?action=exportActivePDF" class="btn-report btn-pdf"
                            target="_blank">
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
                        <a href="/SIEP/public/index.php?action=exportCompletedPDF" class="btn-report btn-pdf"
                            target="_blank">
                            📄 Descargar PDF
                        </a>
                    </div>
                </div>

                <!-- Vacantes Canceladas -->
                <div class="report-card">
                    <h3>🗑️ Vacantes Canceladas</h3>
                    <p>Historial de vacantes rechazadas por UPIS o canceladas por empresas.</p>
                    <div class="report-actions">
                        <a href="/SIEP/public/index.php?action=exportCanceledPDF" class="btn-report btn-pdf"
                            target="_blank">
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
                            <p>Análisis del tiempo que tardan los estudiantes desde su registro hasta la acreditación
                                aprobada por UPIS.</p>
                            <div class="report-actions">
                                <a href="/SIEP/public/index.php?action=exportStudentProcessingPDF"
                                    class="btn-report btn-pdf" target="_blank">
                                    📄 Descargar PDF
                                </a>
                                <a href="/SIEP/public/index.php?action=exportStudentProcessingExcel"
                                    class="btn-report btn-excel">
                                    📗 Descargar Excel
                                </a>
                            </div>
                        </div>

                        <!-- Empresas y Estudiantes en Servicio -->
                        <div class="report-card">
                            <h3>🏢 Empresas y Estudiantes</h3>
                            <p>Listado de empresas con los estudiantes que están realizando o realizaron su estancia
                                profesional.</p>
                            <div class="report-actions">
                                <a href="/SIEP/public/index.php?action=exportCompanyStudentsPDF"
                                    class="btn-report btn-pdf" target="_blank">
                                    📄 Descargar PDF
                                </a>
                                <a href="/SIEP/public/index.php?action=exportCompanyStudentsExcel"
                                    class="btn-report btn-excel">
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
        </div>
        <a href="/SIEP/public/index.php?action=upisDashboard" class="logout-btn">← Volver al Dashboard</a><br><br>
    </div>



</body>

</html>