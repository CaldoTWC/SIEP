<?php
// Archivo: src/Views/upis/history_report.php
require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']); 
// La variable $completedProcesses viene del UpisController
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Trámites Completados</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <!-- Reutilizamos estilos de tabla -->
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #6c757d; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Histórico de Trámites Completados</h1>
        <p>Este es un registro de todos los estudiantes que han finalizado su proceso de Estancia Profesional a través de la plataforma.</p>
        
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
        
        <a href="/SIEP/public/index.php?action=upisDashboard" style="display: block; text-align: center; margin-top: 30px;">← Volver al Panel Principal</a>
    </div>
</body>
</html>