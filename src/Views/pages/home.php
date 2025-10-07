<?php
// Archivo: src/Views/pages/home.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Sistema de Trámites ESCOM</title>
    <!-- Enlazamos a nuestra hoja de estilos principal -->
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        /* Estilos adicionales solo para esta página para hacerla más atractiva */
        .hero {
            text-align: center;
            padding: 40px 20px;
        }
        .hero h1 {
            font-size: 2.5rem;
        }
        .hero p {
            font-size: 1.2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .home-menu {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
        }
        .home-menu .btn {
            padding: 15px 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>Bienvenido al Sistema de Trámites ESCOM</h1>
            <p>Tu plataforma centralizada para la gestión eficiente de Estancias Profesionales. Conecta con empresas y gestiona tu documentación en un solo lugar.</p>
        </div>
        
        <div class="home-menu">
            <!-- Usamos la clase 'btn' para que los enlaces se vean como botones -->
            <a href="/SIEP/public/index.php?action=showLogin" class="btn">Iniciar Sesión</a>
            <a href="/SIEP/public/index.php?action=showRegisterSelection" class="btn" style="background-color: #005a9c;">Registrarse</a>
        </div>
    </div>
</body>
</html>