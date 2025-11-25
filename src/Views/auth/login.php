<?php
// Archivo: src/Views/auth/login.php
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Trámites ESCOM</title>

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
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showLogin" class="nav-link btn-nav">Iniciar
                        Sesión</a></li>
                <li class="nav-item"><a href="/SIEP/public/index.php?action=showRegisterSelection"
                        class="nav-link btn-nav">Registrarse</a></li>
            </ul>
        </div>
    </nav>


    <div class="container">
        <div class="auth-card">
            <h1>Iniciar Sesión</h1>
            <p>Ingresa tus credenciales para acceder al sistema.</p>

            <!-- 
                El formulario apunta a nuestro enrutador con la acción 'login'.
            -->
            <form action="/SIEP/public/index.php?action=login" method="post">

                <div class="form-group">
                    <label for="email">Correo:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn">Acceder</button>
            </form>
            <div class="form-link" style="text-align: center; margin-top: 20px;">
                <!-- ASEGÚRATE DE QUE ESTE ENLACE APUNTE A 'showRegisterSelection' -->
                <p>¿No tienes una cuenta? <a href="/SIEP/public/index.php?action=showRegisterSelection">Regístrate
                        aquí</a></p>
            </div>
        </div>
    </div>
</body>

</html>