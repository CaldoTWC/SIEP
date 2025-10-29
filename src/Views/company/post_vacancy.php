<?php
// Archivo: src/Views/company/post_vacancy.php
// Versión: 3.0.0 - Formulario completo actualizado
// Fecha: 2025-10-29

require_once(__DIR__ . '/../../Lib/Session.php');
require_once(__DIR__ . '/../../Models/User.php');

$session = new Session();
$session->guard(['company']); 

// Obtener datos de la empresa para prellenar el formulario
$userModel = new User();
$company_data = $userModel->getCompanyProfileByUserId($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro de Vacantes - SIEP</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .form-container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section-title {
            background: linear-gradient(135deg, #6f1d33 0%, #9b2847 100%);
            color: white;
            padding: 15px 20px;
            margin: 30px -30px 20px -30px;
            font-size: 18px;
            font-weight: bold;
            border-left: 5px solid #d4a017;
        }
        
        .section-title:first-of-type {
            margin-top: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group input[type="time"],
        .form-group input[type="url"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-group input:disabled,
        .form-group textarea:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }
        
        .checkbox-group,
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .checkbox-group label,
        .radio-group label {
            display: flex;
            align-items: center;
            font-weight: normal;
            cursor: pointer;
        }
        
        .checkbox-group input[type="checkbox"],
        .radio-group input[type="radio"] {
            margin-right: 8px;
            width: auto;
        }
        
        .required {
            color: #d32f2f;
            font-weight: bold;
        }
        
        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .readonly-notice {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .conditional-section {
            display: none;
            border-left: 3px solid #2196f3;
            padding-left: 20px;
            margin-left: 10px;
        }
        
        .conditional-section.active {
            display: block;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #6f1d33 0%, #9b2847 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 30px;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, #5a1729 0%, #7d1f38 100%);
        }
        
        .privacy-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .privacy-box h3 {
            margin-top: 0;
            color: #856404;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .section-title {
                margin-left: -15px;
                margin-right: -15px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1 style="text-align: center; color: #6f1d33; margin-bottom: 10px;">📋 Formulario de Registro de Vacantes</h1>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">Complete todos los campos requeridos para publicar una nueva vacante</p>

        <form action="/SIEP/public/index.php?action=postVacancy" method="POST" id="vacancyForm">
            
            <!-- ========================================
                 SECCIÓN 1: INFORMACIÓN DE LA EMPRESA
                 ======================================== -->
            <h2 class="section-title">🏢 INFORMACIÓN DE LA EMPRESA</h2>
            
            <div class="readonly-notice">
                ℹ️ Los siguientes campos están prellenados con la información de su empresa registrada y no pueden ser modificados.
            </div>
            
            <div class="form-group">
                <label>1. Nombre de la empresa</label>
                <input type="text" value="<?php echo htmlspecialchars($company_data['company_name'] ?? ''); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>2. Nombre comercial</label>
                <input type="text" value="<?php echo htmlspecialchars($company_data['commercial_name'] ?? ''); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>3. Giro de la empresa</label>
                <input type="text" value="<?php echo htmlspecialchars($company_data['business_area'] ?? ''); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>4. Tipo de empresa o dependencia</label>
                <input type="text" value="<?php echo htmlspecialchars($company_data['company_type'] ?? ''); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>5. Enlace de la Constancia de Situación Fiscal</label>
                <input type="text" value="<?php echo htmlspecialchars($company_data['tax_id_url'] ?? ''); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>6. Página WEB de la empresa</label>
                <input type="text" value="<?php echo htmlspecialchars($company_data['website'] ?? 'No especificado'); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>7. Cantidad de empleados</label>
                <input type="text" value="<?php echo htmlspecialchars($company_data['employee_count'] ?? ''); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>8. Nombre de la persona contacto en la empresa</label>
                <input type="text" value="<?php echo htmlspecialchars($company_data['first_name'] . ' ' . $company_data['last_name_p'] . ' ' . $company_data['last_name_m']); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>9. Teléfono de la persona contacto en la empresa</label>
                <input type="text" value="<?php echo htmlspecialchars($company_data['phone_number'] ?? ''); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>10. Correo electrónico de la persona contacto en la empresa</label>
                <input type="text" value="<?php echo htmlspecialchars($company_data['email'] ?? ''); ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>11. ¿Qué programas de desarrollo profesional tiene la empresa para estudiantes?</label>
                <input type="text" value="<?php echo htmlspecialchars($company_data['student_programs'] ?? 'No especificado'); ?>" disabled>
            </div>

            <!-- ========================================
                 SECCIÓN 2: ATENCIÓN A ESTUDIANTES INTERESADOS
                 ======================================== -->
            <h2 class="section-title">📞 ATENCIÓN A ESTUDIANTES INTERESADOS</h2>
            
            <div class="form-group">
                <label>1. ¿Qué días puede atender a las/los postulantes para las vacantes?</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="attention_days[]" value="Lunes"> Lunes</label>
                    <label><input type="checkbox" name="attention_days[]" value="Martes"> Martes</label>
                    <label><input type="checkbox" name="attention_days[]" value="Miércoles"> Miércoles</label>
                    <label><input type="checkbox" name="attention_days[]" value="Jueves"> Jueves</label>
                    <label><input type="checkbox" name="attention_days[]" value="Viernes"> Viernes</label>
                </div>
            </div>
            
            <div class="form-group">
                <label>2. ¿En qué horario se brinda atención a las/los postulantes para las vacantes?</label>
                <input type="text" name="attention_schedule" placeholder="Ej: 9:00 - 17:00">
                <p class="help-text">Especifique el horario de atención</p>
            </div>

            <!-- ========================================
                 SECCIÓN 3: GENERALIDADES DE LA POSTULACIÓN DE VACANTES
                 ======================================== -->
            <h2 class="section-title">📋 GENERALIDADES DE LA POSTULACIÓN DE VACANTES</h2>
            
            <div class="form-group">
                <label>1. Número de vacante/s <span class="required">*</span></label>
                <input type="number" name="num_vacancies" min="1" required>
                <p class="help-text">¿Cuántas plazas disponibles hay?</p>
            </div>
            
            <div class="form-group">
                <label>2. Nombre de la/s vacante/s <span class="required">*</span></label>
                <input type="text" name="title" required placeholder="Ej: Desarrollador Full Stack Junior">
                <p class="help-text">Nombre del puesto o vacante</p>
            </div>
            
            <div class="form-group">
                <label>Descripción adicional de las vacantes (opcional)</label>
                <textarea name="vacancy_names" rows="3" placeholder="Si tiene múltiples vacantes, describa cada una"></textarea>
            </div>
            
            <div class="form-group">
                <label>3. Monto del apoyo económico mensual <span class="required">*</span></label>
                <input type="number" name="economic_support" step="0.01" min="0" required placeholder="0.00">
                <p class="help-text">Monto en pesos mexicanos (MXN)</p>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>4. Fecha de inicio <span class="required">*</span></label>
                    <input type="date" name="start_date" required>
                </div>
                
                <div class="form-group">
                    <label>5. Fecha de conclusión <span class="required">*</span></label>
                    <input type="date" name="end_date" required>
                </div>
            </div>

            <!-- ========================================
                 SECCIÓN 4: PERFIL PARA OCUPAR LA VACANTE
                 ======================================== -->
            <h2 class="section-title">👤 PERFIL PARA OCUPAR LA VACANTE</h2>
            
            <div class="form-group">
                <label>1. Información clave para la publicación en el catálogo de vacantes</label>
                <textarea name="key_information" rows="4" placeholder="Describa los aspectos más importantes de la vacante que quiera destacar"></textarea>
            </div>
            
            <div class="form-group">
                <label>2. Carrera profesional relacionada con la vacante <span class="required">*</span></label>
                <select name="related_career" required>
                    <option value="">-- Seleccione una carrera --</option>
                    <option value="Ingeniería en Sistemas Computacionales">Ingeniería en Sistemas Computacionales</option>
                    <option value="Licenciatura en Ciencia de Datos">Licenciatura en Ciencia de Datos</option>
                    <option value="Ingeniería en Inteligencia Artificial">Ingeniería en Inteligencia Artificial</option>
                    <option value="Todas las anteriores">Todas las anteriores</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>3. Conocimientos requeridos <span class="required">*</span></label>
                <textarea name="required_knowledge" rows="5" required placeholder="Ej: JavaScript, React, Node.js, MySQL, Git, etc."></textarea>
                <p class="help-text">Liste las tecnologías, herramientas y conocimientos necesarios</p>
            </div>
            
            <div class="form-group">
                <label>4. Competencias requeridas</label>
                <textarea name="required_competencies" rows="4" placeholder="Ej: Trabajo en equipo, comunicación efectiva, resolución de problemas, etc."></textarea>
            </div>
            
            <div class="form-group">
                <label>5. Idioma requerido</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="required_languages[]" value="Ingles"> Inglés</label>
                    <label><input type="checkbox" name="required_languages[]" value="Frances"> Francés</label>
                    <label><input type="checkbox" name="required_languages[]" value="Aleman"> Alemán</label>
                    <label><input type="checkbox" name="required_languages[]" value="Chino"> Chino</label>
                    <label><input type="checkbox" name="required_languages[]" value="Portugues"> Portugués</label>
                    <label><input type="checkbox" name="required_languages[]" value="Otro"> Otro</label>
                    <label><input type="checkbox" name="required_languages[]" value="Ninguno"> Ninguno</label>
                </div>
            </div>

            <!-- ========================================
     SECCIÓN 5: ACTIVIDADES A REALIZAR
     ======================================== -->
<h2 class="section-title">💼 ACTIVIDADES A REALIZAR POR EL O LA OCUPANTE DE LA VACANTE</h2>

<p style="margin-bottom: 20px; color: #666;">
    Liste las actividades de TI que el estudiante realizará durante su estancia profesional. 
    Escriba cada actividad en una línea nueva.
</p>

<div class="form-group">
    <label>Actividades a Realizar <span class="required">*</span></label>
    <textarea 
        name="activities_list" 
        rows="10" 
        required 
        placeholder="Ejemplo:
- Desarrollar componentes de interfaz de usuario utilizando React.js
- Implementar APIs RESTful en Node.js y Express
- Realizar consultas y optimización de bases de datos MySQL
- Participar en reuniones diarias de Scrum y estimaciones
- Documentar código y realizar pruebas unitarias"
        style="font-family: monospace; line-height: 1.8;"
    ></textarea>
    <p class="help-text">Escriba cada actividad en una línea nueva. Puede usar guiones (-) o números para listarlas.</p>
</div>

<div class="form-group">
    <label>Descripción general de actividades (opcional)</label>
    <textarea 
        name="activity_details" 
        rows="4" 
        placeholder="Contexto adicional sobre las actividades, ambiente de trabajo, etc."
    ></textarea>
    <p class="help-text">Información complementaria sobre el rol y responsabilidades generales</p>
</div>

            <!-- ========================================
                 SECCIÓN 6: MODALIDAD DE TRABAJO
                 ======================================== -->
            <h2 class="section-title">🏠 MODALIDAD EN QUE SE DESARROLLARÁN LAS ACTIVIDADES</h2>
            
            <div class="form-group">
                <label>6. Modalidad en que se desarrollarán las actividades <span class="required">*</span></label>
                <div class="radio-group">
                    <label><input type="radio" name="modality" value="Presencial" required> Presencial</label>
                    <label><input type="radio" name="modality" value="Hibrida" required> Híbrida</label>
                    <label><input type="radio" name="modality" value="Virtual" required> Virtual</label>
                </div>
            </div>
            
            <!-- Sección condicional para Presencial o Híbrida -->
            <div id="presencial-section" class="conditional-section">
                <h3 style="color: #6f1d33; margin-top: 20px;">📍 Modalidad Presencial o Híbrida</h3>
                
                <div class="form-group">
                    <label>Domicilio donde el o la estudiante desarrollará las actividades <span class="required">*</span></label>
                    <textarea name="work_location_address" rows="3" id="work_address" placeholder="Calle, número, colonia, ciudad, estado, CP"></textarea>
                </div>
            </div>
            
            <!-- Sección común para todas las modalidades -->
            <div style="margin-top: 20px;">
                <div class="form-group">
                    <label>Días en que se realizarán las actividades</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="work_days[]" value="Lunes"> Lunes</label>
                        <label><input type="checkbox" name="work_days[]" value="Martes"> Martes</label>
                        <label><input type="checkbox" name="work_days[]" value="Miércoles"> Miércoles</label>
                        <label><input type="checkbox" name="work_days[]" value="Jueves"> Jueves</label>
                        <label><input type="checkbox" name="work_days[]" value="Viernes"> Viernes</label>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Horario de Entrada</label>
                        <input type="time" name="start_time">
                    </div>
                    
                    <div class="form-group">
                        <label>Horario de Salida</label>
                        <input type="time" name="end_time">
                    </div>
                </div>
            </div>

            <!-- ========================================
                 SECCIÓN 7: PUBLICACIÓN DE LOGOTIPOS
                 ======================================== -->
            <h2 class="section-title">🖼️ PUBLICACIÓN DE LOGOTIPOS</h2>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="logo_auth" style="width: auto; margin-right: 10px;">
                    1. Autorizo la publicación del logotipo de la empresa en el catálogo de vacantes
                </label>
            </div>
            
            <div class="form-group">
                <label>2. Subir enlace de la imagen del logotipo de la empresa en formato .png o .jpg</label>
                <input type="url" name="logo_url" placeholder="https://ejemplo.com/logo.png">
                <p class="help-text">Proporcione una URL pública de su logotipo</p>
            </div>

            <!-- ========================================
                 SECCIÓN 8: AVISO DE PRIVACIDAD
                 ======================================== -->
            <h2 class="section-title">🔒 AVISO DE PRIVACIDAD</h2>
            
            <div class="privacy-box">
                <h3>📜 Aviso de Privacidad</h3>
                <p style="font-size: 13px; line-height: 1.6; margin-bottom: 15px;">
                    Los datos personales recabados serán protegidos en términos de los artículos 1, 9, 11, fracción VI, 16 113, fracción I, 117, fracción V, 186, fracción IV de la Ley Federal de Transparencia y Acceso a la Información Pública, 68, fracciones II y VI, 116 y 206, fracción IV de la Ley General de Transparencia y Acceso a la Información Pública; 1, 16, 17, 18, 19, 21, 22, 23, 24, 25 y 66 fracción I, 67, 69, 70, fracción I y 163 fracciones III, IV y X de la Ley General de Protección de Datos Personales en Posesión de Sujetos Obligados.
                </p>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: flex; align-items: center; font-weight: bold; color: #856404;">
                        <input type="checkbox" name="privacy_accepted" required style="width: auto; margin-right: 10px;">
                        Acepto el aviso de privacidad <span class="required">*</span>
                    </label>
                </div>
            </div>

            <!-- Botón de Envío -->
            <button type="submit" class="btn-submit">📤 Enviar Vacante a Revisión</button>
        </form>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="/SIEP/public/index.php?action=companyDashboard" style="color: #6f1d33; text-decoration: none;">← Volver al Panel</a>
        </div>
    </div>

    <script>
        // Script para mostrar/ocultar sección de dirección según modalidad
        document.addEventListener('DOMContentLoaded', function() {
            const modalityRadios = document.querySelectorAll('input[name="modality"]');
            const presencialSection = document.getElementById('presencial-section');
            const workAddress = document.getElementById('work_address');
            
            modalityRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'Presencial' || this.value === 'Hibrida') {
                        presencialSection.classList.add('active');
                        workAddress.required = true;
                    } else {
                        presencialSection.classList.remove('active');
                        workAddress.required = false;
                        workAddress.value = '';
                    }
                });
            });
            
            // Validación del formulario antes de enviar
            document.getElementById('vacancyForm').addEventListener('submit', function(e) {
                const privacyCheck = document.querySelector('input[name="privacy_accepted"]');
                if (!privacyCheck.checked) {
                    e.preventDefault();
                    alert('⚠️ Debe aceptar el aviso de privacidad para continuar');
                    privacyCheck.focus();
                    return false;
                }
            });
        });
    </script>
</body>
</html>