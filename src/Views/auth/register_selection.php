<?php
// Archivo: src/Views/auth/register_selection.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Tipo de Registro</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Selecciona tu tipo de cuenta</h1>
        <p>Elige el perfil que mejor se adapte a tus necesidades en la plataforma.</p>
        
        <div class="menu-buttons" style="margin-top: 30px;">
            <a href="/SIEP/public/index.php?action=showStudentRegisterForm" class="btn">Soy Estudiante</a>
            <a href="/SIEP/public/index.php?action=showCompanyRegisterForm" class="btn" style="background-color: #005a9c;">Soy Empresa</a>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <p>¿Ya tienes una cuenta? <a href="/SIEP/public/index.php?action=showLogin">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>