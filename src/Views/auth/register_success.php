<?php
// Archivo: src/Views/auth/register_success.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡Registro Exitoso!</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        /* Estilos adicionales para esta página */
        .success-container {
            text-align: center;
            padding: 40px;
        }
        .success-icon {
            font-size: 5rem;
            color: var(--color-exito); /* Verde */
        }
        .success-container h1 {
            color: var(--color-exito);
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .success-container p {
            font-size: 1.1rem;
            color: var(--color-texto-secundario);
        }
        .login-button {
            display: inline-block; /* Ajusta el botón para que no ocupe todo el ancho */
            width: auto;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container success-container">
        <!-- Puedes usar un ícono SVG o una fuente de íconos aquí para un look más profesional -->
        <div class="success-icon">&#10004;</div> 
        
        <h1>¡Registro Completado!</h1>
        
        <p>Tu cuenta ha sido creada exitosamente. <br>Ya puedes acceder a la plataforma.</p>
        
        <a href="/SIEP/public/index.php?action=showLogin" class="btn login-button">Ir a Iniciar Sesión</a>
    </div>
</body>
</html>