<?php
// Archivo: src/Views/student/accreditation_form.php
require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['student']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acreditación Final</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Subir Documentos de Acreditación Final</h1>
        <p>Sube los siguientes documentos para completar tu trámite de Estancia Profesional. Serán revisados por la UPIS.</p>

        <!-- El 'enctype' es OBLIGATORIO para la subida de archivos -->
        <form action="/SIEP/public/index.php?action=submitAccreditation" method="post" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="final_report">1. Reporte Final de Actividades (PDF):</label>
                <input type="file" id="final_report" name="final_report" required accept=".pdf">
            </div>
            
            <div class="form-group">
                <label for="signed_validation_letter">2. Constancia de Validación Firmada (PDF):</label>
                <input type="file" id="signed_validation_letter" name="signed_validation_letter" required accept=".pdf">
            </div>

            <button type="submit" class="btn">Enviar Documentos para Revisión Final</button>
        </form>
        
        <a href="/SIEP/public/index.php?action=studentDashboard" style="display: block; text-align: center; margin-top: 20px;">← Volver al Panel</a>
    </div>
</body>
</html>