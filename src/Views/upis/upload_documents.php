<?php
// Archivo: src/Views/upis/upload_documents.php

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']); 

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <title>Cargar Documentos Firmados</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Cargar Cartas de Presentación Firmadas</h1>
        <p>
            Seleccione los archivos PDF de las cartas ya firmadas y selladas. <br>
            <strong>Importante:</strong> Los archivos deben tener el formato <strong>BOLETA_CP.pdf</strong> (Ej: 2022630554_CP.pdf).
        </p>

        <!-- El 'enctype' es OBLIGATORIO para la subida de archivos -->
        <form action="/SIEP/public/index.php?action=uploadSignedLetters" method="post" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="signed_letters">Seleccionar Archivos:</label>
                <!-- El 'multiple' permite seleccionar varios archivos a la vez -->
                <!-- El '[]' en el name indica a PHP que recibirá un array de archivos -->
                <input type="file" id="signed_letters" name="signed_letters[]" multiple required accept=".pdf">
            </div>

            <button type="submit" class="btn">Subir y Procesar Archivos</button>
        </form>
        
        <a href="/SIEP/public/index.php?action=upisDashboard" style="display: block; text-align: center; margin-top: 20px;">Volver al Panel</a>
    </div>
</body>
</html>