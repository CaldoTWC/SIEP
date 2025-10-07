<?php
// Archivo: src/Views/company/validation_letter_form.php

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['company']); 

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Constancia de Validación</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Generar Constancia de Validación de Estancia Profesional</h1>
        <p>Complete los datos para certificar que el estudiante ha concluido satisfactoriamente su estancia profesional en su empresa.</p>

        <form action="/SIEP/public/index.php?action=generateValidationLetter" method="post" target="_blank">
            
            <h2>Datos del Estudiante</h2>
            <div class="form-group">
                <label for="student_name">Nombre Completo del Estudiante:</label>
                <input type="text" id="student_name" name="student_name" required>
            </div>
            <div class="form-group">
                <label for="student_boleta">Número de Boleta:</label>
                <input type="text" id="student_boleta" name="student_boleta" maxlength="10" required>
            </div>
            <div class="form-group">
                <label for="student_career">Carrera del Estudiante:</label>
                <select id="student_career" name="student_career" required>
                     <option value="" disabled selected>-- Seleccione una carrera --</option>
                    <option value="Ingeniería en Sistemas Computacionales">Ingeniería en Sistemas Computacionales</option>
                    <option value="Licenciatura en Ciencia de Datos">Licenciatura en Ciencia de Datos</option>
                    <option value="Ingeniería en Inteligencia Artificial">Ingeniería en Inteligencia Artificial</option>
                </select>
            </div>
             <div class="form-group">
                <label for="gender">Género del Estudiante (para "el/la estudiante"):</label>
                <select id="gender" name="gender" required>
                    <option value="el">Masculino (el)</option>
                    <option value="la">Femenino (la)</option>
                </select>
            </div>

            <h2>Detalles de la Estancia Concluida</h2>
            <div class="form-group">
                <label for="start_date">Periodo - Fecha de Inicio:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>
            <div class="form-group">
                <label for="end_date">Periodo - Fecha de Término:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
             <div class="form-group">
                <label for="total_hours">Total de Horas Cubiertas (máximo 200):</label>
                <input type="number" id="total_hours" name="total_hours" max="200" required>
            </div>
            <div class="form-group">
                <label for="area">Área donde realizó actividades:</label>
                <input type="text" id="area" name="area" required>
            </div>
            <div class="form-group">
                <label for="project_name">Nombre del Proyecto en que trabajó:</label>
                <textarea id="project_name" name="project_name" rows="4" required></textarea>
            </div>

            <button type="submit" class="btn">Generar Constancia en PDF</button>
        </form>
        
        <a href="/SIEP/public/index.php?action=companyDashboard" style="display: block; text-align: center; margin-top: 20px;">Volver al Panel</a>
    </div>
</body>
</html>