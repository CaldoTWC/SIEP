<?php
// Archivo: src/Views/auth/register_company.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Empresa</title>

    <!-- Mantengo EXACTAMENTE los vínculos -->

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
    <!-- CONTENEDOR INSTITUCIONAL CENTRADO -->
    <div class="auth-wrapper">

        <!-- TARJETA SIEP -->
        <div class="auth-card">

            <h1>Registro de Usuario Tipo Empresa</h1>
            <p>Complete el formulario. Su solicitud será revisada por la UPIS.</p>

            <form action="/SIEP/public/index.php?action=registerCompany" method="post" class="auth-form">

                <!-- =======================================================
                     SECCIÓN EMPRESA
                ======================================================= -->
                <h2 class="section-title">Información de la Empresa</h2>

                <div class="form-group">
                    <label for="company_name">Nombre de la empresa (Razón Social):</label>
                    <input type="text" id="company_name" name="company_name" required>
                </div>

                <div class="form-group">
                    <label for="commercial_name">Nombre comercial:</label>
                    <input type="text" id="commercial_name" name="commercial_name">
                </div>

                <div class="form-group">
                    <label for="rfc">RFC (13 caracteres):</label>
                    <input type="text" id="rfc" name="rfc" maxlength="13">
                </div>

                <div class="form-group">
                    <label for="company_description">Descripción de la empresa:</label>
                    <textarea id="company_description" name="company_description" rows="3" placeholder="Ej: Somos una fintech dedicada al desarrollo de soluciones de pago..."></textarea>
                </div>

                <div class="form-group">
                    <label for="business_area">Giro de la empresa:</label>
                    <input type="text" id="business_area" name="business_area" required>
                </div>

                <div class="form-group">
                    <label for="company_type">Tipo de empresa o dependencia:</label>
                    <input type="text" id="company_type" name="company_type" required>
                </div>

                <div class="form-group">
                    <label for="tax_id_url">Constancia de Situación Fiscal (URL pública):</label>
                    <input type="text" id="tax_id_url" name="tax_id_url" placeholder="https://...">
                </div>

                <div class="form-group">
                    <label for="website">Página web:</label>
                    <input type="text" id="website" name="website" placeholder="https://www.suempresa.com">
                </div>

                <div class="form-group">
                    <label for="employee_count">Cantidad de empleados:</label>
                    <select id="employee_count" name="employee_count">
                        <option value="1-50">1 - 50 empleados</option>
                        <option value="51-100">51 - 100 empleados</option>
                        <option value="100+">Más de 100 empleados</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Programas de desarrollo profesional:</label>

                    <div class="checkbox-group">
                        <label><input type="checkbox" name="student_programs[]" value="Becarios"> Becarios</label>
                        <label><input type="checkbox" name="student_programs[]" value="Medio Tiempo"> Medio Tiempo</label>
                        <label><input type="checkbox" name="student_programs[]" value="Trainee"> Trainee</label>
                        <label><input type="checkbox" name="student_programs[]" value="Residencias"> Residencias</label>
                        <label><input type="checkbox" name="student_programs[]" value="Ninguno"> Ninguno</label>
                        <label><input type="checkbox" name="student_programs[]" value="Otro"> Otro</label>
                    </div>
                </div>

                <!-- =======================================================
                     SECCIÓN CONTACTO
                ======================================================= -->
                <h2 class="section-title">Persona de Contacto</h2>

                <div class="form-group">
                    <label for="first_name">Nombre(s):</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="last_name_p">Apellido Paterno:</label>
                    <input type="text" id="last_name_p" name="last_name_p" required>
                </div>

                <div class="form-group">
                    <label for="last_name_m">Apellido Materno:</label>
                    <input type="text" id="last_name_m" name="last_name_m" required>
                </div>

                <div class="form-group">
                    <label for="contact_person_position">Puesto:</label>
                    <input type="text" id="contact_person_position" name="contact_person_position" placeholder="Ej: Reclutador de TI" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Teléfono de contacto:</label>
                    <input type="tel" id="phone_number" name="phone_number" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo electrónico (usuario):</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" minlength="8" required>
                    <small class="info-text">Debe tener mínimo 8 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmar Contraseña:</label>
                    <input type="password" id="password_confirm" name="password_confirm" minlength="8" required>
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
                        <input type="checkbox" id="accept_privacy" name="accept_privacy" required>
                        Acepto el aviso de privacidad.
                    </label>
                </div>

                <button type="submit" class="btn">Solicitar Registro</button>

            </form>

        </div>
    </div>

</body>
</html>
