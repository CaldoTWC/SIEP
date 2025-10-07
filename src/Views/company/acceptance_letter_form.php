<?php
// Archivo: src/Views/company/acceptance_letter_form.php (Versión Final con Búsqueda)

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['company']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Carta de Aceptación</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Generar Carta de Aceptación y Vincular Estudiante</h1>
        <p>Este proceso creará la Carta de Aceptación y registrará formalmente al estudiante como activo en su empresa para fines de seguimiento de la UPIS.</p>

        <!-- El action apunta al método del controlador y se abre en una nueva pestaña -->
        <form action="/SIEP/public/index.php?action=generateAcceptanceLetter" method="post" target="_blank">
            
            <h2>1. Buscar Estudiante</h2>
            <div class="form-group">
                <label for="student_boleta">Introduce el Número de Boleta del estudiante a aceptar:</label>
                <input type="text" id="student_boleta" name="student_boleta" required maxlength="10" placeholder="Ej: 2022630554">
                <small>El sistema buscará al estudiante en la plataforma y usará sus datos registrados (nombre, carrera) para generar la carta.</small>
            </div>

            <h2>2. Información Adicional para la Carta</h2>
            <div class="form-group">
                <label for="area">Área donde el estudiante realizará la estancia:</label>
                <input type="text" id="area" name="area" required>
            </div>
            <div class="form-group">
                <label for="project_name">Nombre del Proyecto en el que participará:</label>
                <textarea id="project_name" name="project_name" rows="4" required></textarea>
            </div>
             <div class="form-group">
                <label for="gender">Género del Estudiante (para la redacción "el/la alumno/a"):</label>
                <select id="gender" name="gender" required>
                    <option value="el">Masculino (el)</option>
                    <option value="la">Femenino (la)</option>
                </select>
            </div>

            <button type="submit" class="btn">Buscar Estudiante y Generar Carta</button>
        </form>
        
        <a href="/SIEP/public/index.php?action=companyDashboard" style="display: block; text-align: center; margin-top: 20px;">&larr; Volver al Panel</a>
    </div>
</body>
</html>