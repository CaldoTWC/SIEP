<?php
// Archivo: src/Views/auth/register_success.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡Registro Exitoso!</title>

    <!-- Mantengo exactamente tus vínculos -->
    
    <link rel="stylesheet" href="/SIEP/public/css/auth.css">

    <style>
        .success-icon {
            font-size: 4.5rem;
            color: #28a745; /* Verde institucional para éxito */
            margin-bottom: 10px;
        }
        .success-message {
            color: #444;
            font-size: 1.1rem;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>

    <div class="auth-wrapper">

        <div class="auth-card" style="max-width: 480px;">

            <div class="success-icon">✔</div>

            <h1 style="color:#28a745;">¡Registro Completado!</h1>

            <p class="success-message">
                Tu cuenta ha sido creada exitosamente.<br>
                Ya puedes acceder a la plataforma.
            </p>

            <a href="/SIEP/public/index.php?action=showLogin" class="btn">
                Ir a Iniciar Sesión
            </a>

        </div>

    </div>

</body>
</html>
