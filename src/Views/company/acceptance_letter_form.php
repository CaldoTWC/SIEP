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
            <h1>Generar Carta de Aceptación y Vincular Estudiante</h1>
            <p>Este proceso creará la Carta de Aceptación y registrará formalmente al estudiante como activo en su
                empresa para fines de seguimiento de la UPIS.</p>
        </div>
        <a href="/SIEP/public/index.php?action=companyDashboard" class="logout-btn">←Volver al Panel</a><br><br>

        <!-- El action apunta al método del controlador y se abre en una nueva pestaña -->
        <form action="/SIEP/public/index.php?action=generateAcceptanceLetter" method="post" target="_blank">

            <h2 class="section-title">1. Buscar Estudiante</h2>
            <div class="form-group">
                <label for="student_boleta">Introduce el Número de Boleta del estudiante a aceptar:</label>
                <input type="text" id="student_boleta" name="student_boleta" required maxlength="10"
                    placeholder="Ej: 2022630554">
                <small>El sistema buscará al estudiante en la plataforma y usará sus datos registrados (nombre, carrera)
                    para generar la carta.</small>
            </div>

            <h2 class="section-title">2. Información Adicional para la Carta</h2>
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

            <button type="submit" class="btn-submit">Buscar Estudiante y Generar Carta</button>
        </form>

        <a href="/SIEP/public/index.php?action=companyDashboard" class="logout-btn">←Volver al Panel</a>
    </div>
</body>

</html>