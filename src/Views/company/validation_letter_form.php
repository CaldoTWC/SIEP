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
    <link rel="stylesheet" href="/SIEP/public/css/company.css">
</head>

<body>
    <!-- BARRA DE NAVEGACIÓN -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="/SIEP/public/index.php" class="nav-logo">SIEP</a>
            <ul class="nav-menu">
                <li class="nav-item"><a href="#hero" class="nav-link">Inicio</a></li>
                <li class="nav-item"><a href="#user-section" class="nav-link">Usuarios</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showLogin" class="nav-link btn-nav">Iniciar
                        Sesión</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showRegisterSelection"
                        class="nav-link btn-nav">Registrarse</a></li>
            </ul>
        </div>
    </nav>

    <div class="form-container">
        <div class="page-header">
            <h1>Generar Constancia de Validación de Estancia Profesional</h1>
            <p>Complete los datos para certificar que el estudiante ha concluido satisfactoriamente su estancia profesional en su empresa.</p>
        </div>
        <div style=" margin-top: 20px;">
            <a href="/SIEP/public/index.php?action=companyDashboard" class="logout-btn">←Volver al Panel</a><br><br>
        </div>

        <form action="/SIEP/public/index.php?action=generateValidationLetter" method="post" target="_blank">
            <h2 class="section-title">Datos del Estudiante</h2>
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
                    <option value="Ingeniería en Sistemas Computacionales">Ingeniería en Sistemas Computacionales
                    </option>
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

            <h2 class="section-title">Detalles de la Estancia Concluida</h2>
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

            <button type="submit" class="btn-submit">Generar Constancia en PDF</button>
        </form>

        <div style=" margin-top: 20px;">
            <a href="/SIEP/public/index.php?action=companyDashboard" class="logout-btn">←Volver al Panel</a>
        </div>
    </div>
</body>

</html>