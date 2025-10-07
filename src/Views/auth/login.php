<?php
// Archivo: src/Views/auth/login.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Trámites ESCOM</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>
        <p>Ingresa tus credenciales para acceder al sistema.</p>

        <!-- 
            El formulario apunta a nuestro enrutador con la acción 'login'.
        -->
        <form action="/SIEP/public/index.php?action=login" method="post">
            
            <div class="form-group">
                <label for="email">Correo Institucional:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">Acceder</button>
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <!-- ASEGÚRATE DE QUE ESTE ENLACE APUNTE A 'showRegisterSelection' -->
            <p>¿No tienes una cuenta? <a href="/SIEP/public/index.php?action=showRegisterSelection">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>