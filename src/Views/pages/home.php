<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIEP - Sistema de Integración de Estancia Profesional</title>
    <link rel="stylesheet" href="/SIEP/public/css/home.css"> 
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

    <!-- HERO PRINCIPAL -->
    <section class="hero" id="hero">
        <h1>SIEP</h1>
        <p>Sistema de Integración de Estancia Profesional — Plataforma oficial para gestionar, vincular y supervisar las estancias profesionales de los estudiantes.</p>

        <div class="hero-buttons">
            <a href="/SIEP/public/index.php?action=showLogin"><button class="btn-main">Iniciar Sesión</button></a>
            <a href="/SIEP/public/index.php?action=showRegisterSelection"><button class="btn-main">Registrarse</button></a>
        </div>
    </section>

    <!-- SECCIÓN TIPOS DE USUARIO -->
    <section class="user-section" id="user-section">
        <h2>¿Quién puede usar SIEP?</h2>

        <div class="user-cards">
            <div class="user-card">
                <h3>UPIS</h3>
                <p>Gestiona estudiantes, empresas, aprobaciones, revisión de documentos y seguimiento oficial del proceso de estancias profesionales.</p>
            </div>

            <div class="user-card">
                <h3>Estudiantes</h3>
                <p>Consulta vacantes, aplica a estancias, sube documentos, da seguimiento al proceso y recibe notificaciones sobre tu avance académico.</p>
            </div>

            <div class="user-card">
                <h3>Empresas</h3>
                <p>Registra ofertas de estancia, selecciona candidatos, administra postulaciones y participa en el programa profesional del instituto.</p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        &copy; <?php echo date("Y"); ?> SIEP — Sistema de Integración de Estancia Profesional. Todos los derechos reservados.
    </footer>

</body>
</html>
