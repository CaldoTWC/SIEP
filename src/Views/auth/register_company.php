<?php
// Archivo: src/Views/auth/register_company.php (Versión Detallada)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Empresa</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Registro de Usuario Tipo Empresa</h1>
        <p>Complete el formulario. Su cuenta será revisada por la UPIS antes de ser activada.</p>

        <form action="/SIEP/public/index.php?action=registerCompany" method="post">
            
            <h2>Información de la Empresa</h2>
            <div class="form-group">
                <label for="company_name">1. Nombre de la empresa (Razón Social):</label>
                <input type="text" id="company_name" name="company_name" required>
            </div>
            <div class="form-group">
                <label for="commercial_name">2. Nombre comercial:</label>
                <input type="text" id="commercial_name" name="commercial_name">
            </div>
            <div class="form-group">
                <label for="rfc">RFC de la Empresa (13 caracteres):</label>
                <input type="text" id="rfc" name="rfc" maxlength="13">
            </div>
            <div class="form-group">
                <label for="company_description">Breve Descripción de la Empresa:</label>
                <textarea id="company_description" name="company_description" rows="3" placeholder="Ej: Somos una fintech enfocada en el desarrollo de soluciones de pago..."></textarea>
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
                <label for="tax_id_url">Enlace a la Constancia de Situación Fiscal:</label>
                <input type="text" id="tax_id_url" name="tax_id_url" placeholder="URL pública del documento" required>
            </div>
            <div class="form-group">
                <label for="website">Página WEB de la empresa:</label>
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
                <label>¿Qué programas de desarrollo profesional tiene la empresa?</label>
                <!-- Usamos checkboxes para selección múltiple -->
                <div><input type="checkbox" name="student_programs[]" value="Becarios"> Programas de Becarios</div>
                <div><input type="checkbox" name="student_programs[]" value="Medio Tiempo"> Trabajo de Medio Tiempo</div>
                <div><input type="checkbox" name="student_programs[]" value="Trainee"> Programas de Trainee o Jóvenes Talentos</div>
                <div><input type="checkbox" name="student_programs[]" value="Residencias"> Programas de Residencias Profesionales</div>
                <div><input type="checkbox" name="student_programs[]" value="Ninguno"> Ninguno</div>
                <div><input type="checkbox" name="student_programs[]" value="Otro"> Otro</div>
            </div>

            <h2>Información de la Persona de Contacto</h2>
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
                <label for="contact_person_position">Puesto de la Persona de Contacto:</label>
                <input type="text" id="contact_person_position" name="contact_person_position" placeholder="Ej: Reclutador de TI, Gerente de Proyecto" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Teléfono de contacto:</label>
                <input type="tel" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="email">Correo electrónico de contacto (será su usuario):</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña para la Cuenta:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Aviso de Privacidad:</label>
            <div class="privacy-notice">
                    Los datos personales recabados serán protegidos en términos de los artículos 1, 9, 
                    11, fracción VI, 16 113, fracción I, 117, fracción V, 186, fracción IV de la Ley Federal de 
                    Transparencia y Acceso a la Información Pública... (y el resto del texto legal).
            </div>
            <div class="acceptance-group">
                <input type="checkbox" id="accept_privacy" name="accept_privacy" value="accepted" required>
                <label for="accept_privacy">Acepto el aviso de privacidad.</label>
            </div>
            </div>

            <button type="submit" class="btn">Solicitar Registro</button>
        </form>
    </div>
</body>
</html>