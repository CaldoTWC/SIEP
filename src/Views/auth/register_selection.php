<?php
// Archivo: src/Views/auth/register_selection.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Tipo de Registro</title>

    <!-- Mantengo exactamente tus vínculos -->
  
    <link rel="stylesheet" href="/SIEP/public/css/auth.css">
</head>

<body>
        <!-- BARRA DE NAVEGACIÓN -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="/SIEP/public/index.php" class="nav-logo">SIEP</a>
            <ul class="nav-menu">
                <li class="nav-item"><a href="#hero" class="nav-link">Inicio</a></li>
                <li class="nav-item"><a href="#user-section" class="nav-link">Usuarios</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showLogin" class="nav-link btn-nav">Iniciar Sesión</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showRegisterSelection" class="nav-link btn-nav">Registrarse</a></li>
            </ul>
        </div>
    </nav>
    <!-- CONTENEDOR CENTRADO -->
    <div class="auth-wrapper">

        <!-- TARJETA INSTITUCIONAL -->
        <div class="auth-card">

            <h1>Selecciona tu tipo de cuenta</h1>
            <p>Elige el perfil que mejor se adapte a tus necesidades en la plataforma.</p>

            <!-- BOTONES DE SELECCIÓN -->
            <div class="register-selection-btns">
                <a href="/SIEP/public/index.php?action=showStudentRegisterForm" class="btn">Soy Estudiante</a><br><br><br>
                <a href="/SIEP/public/index.php?action=showCompanyRegisterForm" class="btn">Soy Empresa</a>

            </div>

            <!-- LINK A LOGIN -->
            <div class="form-link" style="margin-top: 25px;">
                <p>¿Ya tienes una cuenta? <a href="/SIEP/public/index.php?action=showLogin">Inicia sesión aquí</a></p>
            </div>

        </div>

    </div>

</body>
</html>
