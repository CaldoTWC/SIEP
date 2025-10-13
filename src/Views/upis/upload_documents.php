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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Cartas Firmadas</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .instructions {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .format-example {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📤 Subir Cartas de Presentación Firmadas</h1>
        
        <div class="instructions">
            <h3>📋 Instrucciones:</h3>
            <ol>
                <li>Descarga el ZIP con las cartas desde "Gestión de Cartas"</li>
                <li>Imprime, firma y sella cada carta</li>
                <li>Escanea cada carta firmada como PDF</li>
                <li>Renombra con formato: <strong>BOLETA_CP.pdf</strong></li>
                <li>Selecciona todos los archivos y súbelos aquí</li>
            </ol>
        </div>
        
        <div class="format-example">
            <strong>⚠️ Formato de Nombres:</strong><br>
            ✅ Correcto: <span style="color: green;">2022630554_CP.pdf</span><br>
            ❌ Incorrecto: <span style="color: red;">carta_juan.pdf</span>
        </div>

        <form action="/SIEP/public/index.php?action=uploadSignedLetters" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="signed_letters">Seleccionar Archivos PDF:</label>
                <input type="file" id="signed_letters" name="signed_letters[]" multiple required accept=".pdf">
            </div>

            <button type="submit" class="btn" style="background-color: #28a745;">
                ✅ Subir y Procesar Archivos
            </button>
        </form>
        
        <a href="/SIEP/public/index.php?action=reviewLetters" style="display: block; text-align: center; margin-top: 20px;">← Volver a Gestión de Cartas</a>
        <a href="/SIEP/public/index.php?action=upisDashboard" style="display: block; text-align: center; margin-top: 10px;">← Volver al Panel</a>
    </div>
</body>
</html>