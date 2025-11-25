<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Estudiante</title>

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

        <!-- TARJETA ESTILO SIEP -->
        <div class="auth-card">

            <h1>Crear Cuenta de Estudiante</h1>
            <p>Completa tus datos para acceder a la plataforma.</p>

            <!-- BLOQUE DE ERRORES -->
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="message error">
                    <strong>Por favor, corrige los siguientes errores:</strong>
                    <ul style="text-align: left; margin-top: 10px; padding-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO PRINCIPAL -->
            <form action="/SIEP/public/index.php?action=registerStudent" method="post" class="auth-form">

                <!-- =======================================================
                     DATOS PERSONALES
                ======================================================= -->
                <h2 class="section-title">Datos Personales</h2>

                <div class="form-group">
                    <label for="first_name">Nombre(s):</label>
                    <input type="text" id="first_name" name="first_name"
                           required value="<?php echo htmlspecialchars($input['first_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="last_name_p">Apellido Paterno:</label>
                    <input type="text" id="last_name_p" name="last_name_p"
                           required value="<?php echo htmlspecialchars($input['last_name_p'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="last_name_m">Apellido Materno:</label>
                    <input type="text" id="last_name_m" name="last_name_m"
                           required value="<?php echo htmlspecialchars($input['last_name_m'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="boleta">Número de Boleta (10 dígitos):</label>
                    <input type="text" id="boleta" name="boleta" maxlength="10"
                           required value="<?php echo htmlspecialchars($input['boleta'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="phone_number">Teléfono de contacto (10 dígitos):</label>
                    <input type="tel" id="phone_number" name="phone_number"
                           required value="<?php echo htmlspecialchars($input['phone_number'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="career">Programa Académico:</label>
                    <select id="career" name="career" required>
                        <option value="" disabled <?php echo empty($input['career']) ? 'selected' : ''; ?>>
                            -- Selecciona tu carrera --
                        </option>

                        <option value="Ingeniería en Sistemas Computacionales"
                            <?php echo (isset($input['career']) && $input['career'] == 'Ingeniería en Sistemas Computacionales') ? 'selected' : ''; ?>>
                            Ingeniería en Sistemas Computacionales
                        </option>

                        <option value="Licenciatura en Ciencia de Datos"
                            <?php echo (isset($input['career']) && $input['career'] == 'Licenciatura en Ciencia de Datos') ? 'selected' : ''; ?>>
                            Licenciatura en Ciencia de Datos
                        </option>

                        <option value="Ingeniería en Inteligencia Artificial"
                            <?php echo (isset($input['career']) && $input['career'] == 'Ingeniería en Inteligencia Artificial') ? 'selected' : ''; ?>>
                            Ingeniería en Inteligencia Artificial
                        </option>
                    </select>
                </div>

                <!-- =======================================================
                     DATOS DE LA CUENTA
                ======================================================= -->
                <h2 class="section-title">Datos de la Cuenta</h2>

                <div class="form-group">
                    <label for="email">Correo Institucional (@alumno.ipn.mx):</label>
                    <input type="email" id="email" name="email"
                           required value="<?php echo htmlspecialchars($input['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <!-- =======================================================
                     PRIVACIDAD
                ======================================================= -->
                <h2 class="section-title">Privacidad</h2>

                <div class="form-group">
                    <label>Aviso de Privacidad:</label>

                    <div class="privacy-box">
                        Los datos personales recabados serán protegidos en términos de la Ley Federal de Transparencia...
                    </div>

                    <label class="accept-privacy">
                        <input type="checkbox" id="accept_privacy" name="accept_privacy" value="accepted" required>
                        Acepto el aviso de privacidad.
                    </label>
                </div>

                <button type="submit" class="btn">Crear Cuenta</button>

            </form>
        </div>
    </div>

</body>
</html>
