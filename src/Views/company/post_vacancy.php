<?php
// Archivo: src/Views/company/post_vacancy.php

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['company']); 

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Nueva Vacante</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Publicar Nueva Vacante</h1>
        <p>Complete los detalles de la vacante. Será revisada por la UPIS antes de ser visible para los estudiantes.</p>

        <form action="/SIEP/public/index.php?action=postVacancy" method="post">
            
            <div class="form-group">
                <label for="title">Título del Puesto:</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="description">Descripción del Perfil Buscado:</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="activities">Actividades a Realizar:</label>
                <textarea id="activities" name="activities" rows="5" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="modality">Modalidad:</label>
                <select id="modality" name="modality" required>
                    <option value="Presencial">Presencial</option>
                    <option value="Híbrido">Híbrido</option>
                    <option value="Remoto">Remoto</option>
                </select>
            </div>

            <button type="submit" class="btn">Enviar Vacante a Revisión</button>
        </form>
        <a href="/SIEP/public/index.php?action=companyDashboard" style="display: block; text-align: center; margin-top: 20px;">Volver al Panel</a>
    </div>
</body>
</html>