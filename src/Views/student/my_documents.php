<?php
// Archivo: src/Views/student/my_documents.php

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['student']); 

// La variable $myDocuments viene del StudentController.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Documentos Finales</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <!-- Reutilizamos los estilos de tabla del dashboard de UPIS -->
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #004a99; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mis Documentos Finales</h1>
        <p>Aquí encontrarás los documentos firmados y sellados por la UPIS, listos para su descarga.</p>

        <a href="/SIEP/public/index.php?action=studentDashboard" style="display: inline-block; margin-bottom: 20px;">← Volver al Panel</a>

        <?php if (empty($myDocuments)): ?>
            <p>Aún no tienes documentos finales disponibles.</p>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre del Documento</th>
                            <th>Tipo de Documento</th>
                            <th>Fecha de Carga</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($myDocuments as $doc): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($doc['original_filename']); ?></td>
                                <td><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($doc['document_type']))); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($doc['uploaded_at'])); ?></td>
                                <td>
                                    <!-- Este enlace apunta a la acción de descarga -->
                                    <a href="/SIEP/public/uploads/signed_documents/<?php echo htmlspecialchars($doc['original_filename']); ?>" class="btn" download>Descargar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>