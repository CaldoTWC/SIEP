<?php
// Archivo: src/Views/student/accreditation_form.php
// Formulario Completo de Acreditación de Estancia Profesional
// Versión: 2.0 - Detección automática Tipo A/B

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['student']);

// Obtener datos del estudiante para prellenar
require_once(__DIR__ . '/../../Models/User.php');
$userModel = new User();
$profile_data = $userModel->getStudentProfileForForm($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Acreditación de Estancia Profesional</title>
    <link rel="stylesheet" href="/SIEP/public/css/student.css">

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
        <div class="page-header">
            <h1>📋 Formulario de Recepción de Documentación</h1>
            <p>Acreditación de la Estancia Profesional</p>
        </div>
        <div class="form-container">

        <a href="/SIEP/public/index.php?action=studentDashboard" class="logout-btn">← Volver al Panel</a>
            <p class="intro-text">
                <strong>⚠️ IMPORTANTE:</strong> Llena todos los campos con información verídica y completa.
                Asegúrate de tener todos los documentos requeridos en formato PDF antes de comenzar.
            </p>

            <form action="/SIEP/public/index.php?action=submitAccreditation" method="POST" enctype="multipart/form-data"
                id="accreditationForm">

                <!-- ========================================
                     SECCIÓN 1: INFORMACIÓN DEL ESTUDIANTE
                     ======================================== -->
                <h2 class="section-title">👤 Información del Estudiante</h2>

                <div class="readonly-notice">
                    ℹ️ <strong>Nota:</strong> Estos datos provienen de tu perfil. Si algo es incorrecto, contacta a la
                    UPIS.
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Nombre(s):</label>
                        <input type="text" name="nombres"
                            value="<?php echo htmlspecialchars($profile_data['first_name'] ?? ''); ?>" readonly
                            required>
                    </div>

                    <div class="form-group">
                        <label>Apellido Paterno:</label>
                        <input type="text" name="apellido_paterno"
                            value="<?php echo htmlspecialchars($profile_data['last_name_p'] ?? ''); ?>" readonly
                            required>
                    </div>

                    <div class="form-group">
                        <label>Apellido Materno:</label>
                        <input type="text" name="apellido_materno"
                            value="<?php echo htmlspecialchars($profile_data['last_name_m'] ?? ''); ?>" readonly
                            required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Número de Boleta:</label>
                        <input type="text" name="boleta"
                            value="<?php echo htmlspecialchars($profile_data['boleta'] ?? ''); ?>" readonly required>
                    </div>

                    <div class="form-group">
                        <label>Correo Electrónico Institucional:</label>
                        <input type="email" name="email_institucional"
                            value="<?php echo htmlspecialchars($profile_data['email'] ?? ''); ?>" readonly required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefono">Número Telefónico de Contacto <span class="required">*</span></label>
                        <input type="tel" id="telefono" name="telefono" placeholder="5512345678" pattern="[0-9]{10}"
                            required>
                        <small style="color: #666;">10 dígitos sin espacios ni guiones</small>
                    </div>

                    <div class="form-group">
                        <label for="programa_academico">Programa Académico <span class="required">*</span></label>
                        <select id="programa_academico" name="programa_academico" required>
                            <option value="" disabled selected>-- Selecciona --</option>
                            <option value="Ingeniería en Sistemas Computacionales" <?php echo (isset($profile_data['career']) && $profile_data['career'] == 'Ingeniería en Sistemas Computacionales') ? 'selected' : ''; ?>>Ingeniería en Sistemas Computacionales</option>
                            <option value="Ingeniería en Inteligencia Artificial">Ingeniería en Inteligencia Artificial
                            </option>
                            <option value="Licenciatura en Ciencia de Datos">Licenciatura en Ciencia de Datos</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="semestre">Semestre <span class="required">*</span></label>
                        <select id="semestre" name="semestre" required>
                            <option value="" disabled selected>-- Selecciona --</option>
                            <option value="6">6to Semestre</option>
                            <option value="7">7mo Semestre</option>
                            <option value="8">8vo Semestre</option>
                            <option value="9">9no Semestre</option>
                            <option value="10">10mo Semestre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="boleta_global">📄 Boleta Global Informativa Digital (PDF) <span
                                class="required">*</span></label>
                        <input type="file" id="boleta_global" name="boleta_global" accept=".pdf" required>
                        <small style="color: #666;">Descárgala de tu SAES. Máx. 5MB</small>
                    </div>
                </div>

                <!-- ========================================
                     SECCIÓN 2: AGENCIA DE COLOCACIÓN
                     ======================================== -->
                <h2 class="section-title">🏢 Información General de la Empresa</h2>

                <div class="form-group">
                    <label>¿Realizaste tu Estancia Profesional a través de una agencia de colocación? <span
                            class="required">*</span></label>
                    <small style="color: #666; display: block; margin-bottom: 10px;">Ejemplos: PROBECARIOS, PROMERITUM,
                        CANTRIA, etc.</small>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="agencia_colocacion" value="si" required> Sí
                        </label>
                        <label>
                            <input type="radio" name="agencia_colocacion" value="no" required> No
                        </label>
                    </div>
                </div>

                <!-- ========================================
                     SECCIÓN 3: GENERALIDADES DE LA EMPRESA
                     ======================================== -->
                <h2 class="section-title">🏭 Generalidades de la Empresa</h2>

                <div class="form-group">
                    <label for="nombre_comercial">Nombre Comercial de la Empresa <span class="required">*</span></label>
                    <input type="text" id="nombre_comercial" name="nombre_comercial" placeholder="Ej: Google México"
                        required>
                    <small style="color: #666;">Independientemente de la agencia de colocación intermediaria</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo_empresa">Tipo de Empresa o Dependencia <span class="required">*</span></label>
                        <select id="tipo_empresa" name="tipo_empresa" required>
                            <option value="" disabled selected>-- Selecciona --</option>
                            <option value="publica">Pública</option>
                            <option value="privada">Privada</option>
                            <option value="descentralizada">Descentralizada</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="giro">Giro <span class="required">*</span></label>
                        <select id="giro" name="giro" required>
                            <option value="" disabled selected>-- Selecciona --</option>
                            <option value="comercial">Comercial</option>
                            <option value="industrial">Industrial</option>
                            <option value="servicios">Servicios</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="razon_social">Razón Social de la Empresa <span class="required">*</span></label>
                    <input type="text" id="razon_social" name="razon_social"
                        placeholder="Ej: Google México S. de R.L. de C.V." required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha de Inicio de Estancia <span class="required">*</span></label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_fin">Fecha de Fin de Estancia <span class="required">*</span></label>
                        <input type="date" id="fecha_fin" name="fecha_fin" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Días en que realizaste tu estancia <span class="required">*</span></label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="dias_estancia[]" value="lunes"> Lunes</label>
                        <label><input type="checkbox" name="dias_estancia[]" value="martes"> Martes</label>
                        <label><input type="checkbox" name="dias_estancia[]" value="miercoles"> Miércoles</label>
                        <label><input type="checkbox" name="dias_estancia[]" value="jueves"> Jueves</label>
                        <label><input type="checkbox" name="dias_estancia[]" value="viernes"> Viernes</label>
                        <label><input type="checkbox" name="dias_estancia[]" value="sabado"> Sábado</label>
                        <label><input type="checkbox" name="dias_estancia[]" value="domingo"> Domingo</label>
                    </div>
                </div>

                <!-- ========================================
                     SECCIÓN 4: CONTACTO EMPRESARIAL
                     ======================================== -->
                <h2 class="section-title">📞 Contacto Empresarial</h2>

                <div class="form-group">
                    <label for="nombre_contacto">Nombre del Contacto (persona) dentro de la Empresa <span
                            class="required">*</span></label>
                    <input type="text" id="nombre_contacto" name="nombre_contacto" placeholder="Ej: Juan Pérez López"
                        required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email_contacto">Correo Corporativo del Contacto <span
                                class="required">*</span></label>
                        <input type="email" id="email_contacto" name="email_contacto"
                            placeholder="juan.perez@empresa.com" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono_contacto">Teléfono del Contacto <span class="required">*</span></label>
                        <input type="tel" id="telefono_contacto" name="telefono_contacto" placeholder="5512345678"
                            pattern="[0-9]{10}" required>
                    </div>
                </div>

                <!-- ========================================
                     PREGUNTA CLAVE: ¿EMPRESA REGISTRADA?
                     ======================================== -->
                <h2 class="section-title">🔑 Pregunta Clave</h2>

                <div class="alert-warning">
                    <strong>⚠️ IMPORTANTE:</strong> Tu respuesta a esta pregunta determinará qué documentos debes subir.
                </div>

                <div class="form-group">
                    <label>¿La empresa donde realizaste la Estancia Profesional se encuentra registrada en el Catálogo
                        de Vacantes de la UPIS-ESCOM? <span class="required">*</span></label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="empresa_registrada" value="si" id="empresa_si" required> Sí
                        </label>
                        <label>
                            <input type="radio" name="empresa_registrada" value="no" id="empresa_no" required> No
                        </label>
                    </div>
                </div>

                <!-- ========================================
                     SECCIÓN CONDICIONAL A: EMPRESA NO REGISTRADA
                     ======================================== -->
                <div id="seccion_tipo_a" class="conditional-section">
                    <h2 class="section-title"
                        style="background: #ff9800; color: white; padding: 15px; border-radius: 8px; margin-top: 30px;">
                        📁 Documentos Requeridos para Empresa NO Registrada
                    </h2>

                    <div class="alert-info">
                        <strong>📋 Debes subir:</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <li>Recibos de nómina correspondientes a 200 horas de estancia</li>
                            <li>Constancia laboral o de becaria</li>
                            <li>Reporte Final de Estancia Profesional</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label for="recibos_nomina">📄 Recibos de Nómina (puedes subir varios archivos) <span
                                class="required">*</span></label>
                        <input type="file" id="recibos_nomina" name="recibos_nomina[]" accept=".pdf" multiple>
                        <small style="color: #666;">Sube todos los recibos que sumen 200 horas. Formato: PDF. Máx. 5MB
                            cada uno</small>
                    </div>

                    <div class="form-group">
                        <label for="constancia_laboral">📄 Constancia Laboral o de Becaria <span
                                class="required">*</span></label>
                        <input type="file" id="constancia_laboral" name="constancia_laboral" accept=".pdf">
                        <small style="color: #666;">Formato: PDF. Máx. 5MB</small>
                    </div>

                    <div class="form-group">
                        <label for="reporte_final_a">📄 Reporte Final de Estancia Profesional <span
                                class="required">*</span></label>
                        <input type="file" id="reporte_final_a" name="reporte_final" accept=".pdf">
                        <small style="color: #666;">Descarga el formato de la página de la UPIS. Formato: PDF. Máx.
                            5MB</small>
                    </div>
                </div>

                <!-- ========================================
                     SECCIÓN CONDICIONAL B: EMPRESA REGISTRADA
                     ======================================== -->
                <div id="seccion_tipo_b" class="conditional-section">
                    <h2 class="section-title"
                        style="background: #4caf50; color: white; padding: 15px; border-radius: 8px; margin-top: 30px;">
                        📁 Documentos Requeridos para Empresa Registrada
                    </h2>

                    <div class="alert-info">
                        <strong>📋 Debes subir:</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <li>Carta de Aceptación de la Estancia Profesional</li>
                            <li>Constancia de Validación de Horas</li>
                            <li>Reporte Final de Estancia Profesional</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label for="carta_aceptacion">📄 Carta de Aceptación de la Estancia Profesional <span
                                class="required">*</span></label>
                        <input type="file" id="carta_aceptacion" name="carta_aceptacion" accept=".pdf">
                        <small style="color: #666;">Formato: PDF. Máx. 5MB</small>
                    </div>

                    <div class="form-group">
                        <label for="constancia_validacion">📄 Constancia de Validación de Horas <span
                                class="required">*</span></label>
                        <input type="file" id="constancia_validacion" name="constancia_validacion" accept=".pdf">
                        <small style="color: #666;">Obligatoria. Formato: PDF. Máx. 5MB</small>
                    </div>

                    <div class="form-group">
                        <label for="reporte_final_b">📄 Reporte Final de Estancia Profesional <span
                                class="required">*</span></label>
                        <input type="file" id="reporte_final_b" name="reporte_final" accept=".pdf">
                        <small style="color: #666;">Descarga el formato de la página de la UPIS. Formato: PDF. Máx.
                            5MB</small>
                    </div>
                </div>

                <!-- ========================================
                     SECCIÓN 5: PROTECCIÓN DE DATOS
                     ======================================== -->
                <div class="privacy-box">
                    <h3>🔒 PROTECCIÓN DE DATOS PERSONALES</h3>
                    <h4>AVISO DE PRIVACIDAD</h4>

                    <div class="privacy-text">
                        Los datos personales recabados serán protegidos en términos de los artículos 1, 9, 11, fracción
                        VI, 16 113,
                        fracción I, 117, fracción V, 186, fracción IV de la Ley Federal de Transparencia y Acceso a la
                        Información Pública,
                        68, fracciones II y VI, 116 y 206, fracción IV de la Ley General de Transparencia y Acceso a la
                        Información Pública;
                        1, 16, 17, 18, 19, 21, 22, 23, 24, 25 y 66 fracción I, 67, 69, 70, fracción I y 163 fracciones
                        III, IV y X de la
                        Ley General de Protección de Datos Personales en Posesión de Sujetos Obligados.

                        <br><br>

                        Los datos personales proporcionados serán utilizados exclusivamente para fines académicos y
                        administrativos
                        relacionados con el proceso de acreditación de la Estancia Profesional en la UPIS-ESCOM del
                        Instituto Politécnico Nacional.

                        <br><br>

                        Al aceptar este aviso, usted otorga su consentimiento para el tratamiento de sus datos
                        personales conforme a lo establecido
                        en la normatividad aplicable.
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="display: flex; align-items: center;">
                            <input type="checkbox" name="privacy_accept" id="privacy_accept" required
                                style="width: auto; margin-right: 10px;">
                            <strong>Acepto el Aviso de Privacidad <span class="required">*</span></strong>
                        </label>
                    </div>
                </div>

                <!-- ========================================
                     BOTÓN DE ENVÍO
                     ======================================== -->
                <button type="submit" class="btn-submit" id="submitBtn" disabled>
                    ✅ Enviar Documentación para Acreditación
                </button>

                <small style="display: block; text-align: center; margin-top: 15px; color: #666;">
                    * Campos obligatorios. Solo se aceptan archivos PDF de máximo 5MB cada uno.
                </small>
            </form>

            <a href="/SIEP/public/index.php?action=studentDashboard" class="logout-btn">← Volver al Panel</a>
        </div>
    </div>

    <script>
        // ========================================
        // DETECCIÓN AUTOMÁTICA DE TIPO A o B
        // ========================================
        const radioSi = document.getElementById('empresa_si');
        const radioNo = document.getElementById('empresa_no');
        const seccionA = document.getElementById('seccion_tipo_a');
        const seccionB = document.getElementById('seccion_tipo_b');

        function actualizarTipo() {
            // Limpiar campos no requeridos
            const camposA = seccionA.querySelectorAll('input[type="file"]');
            const camposB = seccionB.querySelectorAll('input[type="file"]');

            if (radioNo.checked) {
                // TIPO A: Empresa NO registrada
                seccionA.classList.add('active');
                seccionB.classList.remove('active');

                // Hacer obligatorios los campos de A, opcionales los de B
                camposA.forEach(input => input.required = true);
                camposB.forEach(input => input.required = false);

                // Scroll suave hacia la sección
                setTimeout(() => {
                    seccionA.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);

            } else if (radioSi.checked) {
                // TIPO B: Empresa registrada
                seccionB.classList.add('active');
                seccionA.classList.remove('active');

                // Hacer obligatorios los campos de B, opcionales los de A
                camposB.forEach(input => input.required = true);
                camposA.forEach(input => input.required = false);

                // Scroll suave hacia la sección
                setTimeout(() => {
                    seccionB.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        }

        radioSi.addEventListener('change', actualizarTipo);
        radioNo.addEventListener('change', actualizarTipo);

        // ========================================
        // VALIDACIÓN DE FECHAS
        // ========================================
        const fechaInicio = document.getElementById('fecha_inicio');
        const fechaFin = document.getElementById('fecha_fin');

        fechaFin.addEventListener('change', function () {
            if (fechaInicio.value && fechaFin.value) {
                if (new Date(fechaFin.value) < new Date(fechaInicio.value)) {
                    alert('❌ La fecha de fin no puede ser anterior a la fecha de inicio');
                    fechaFin.value = '';
                }
            }
        });

        // ========================================
        // VALIDACIÓN DE CHECKBOX DE PRIVACIDAD
        // ========================================
        const privacyCheck = document.getElementById('privacy_accept');
        const submitBtn = document.getElementById('submitBtn');

        privacyCheck.addEventListener('change', function () {
            submitBtn.disabled = !this.checked;
        });

        // ========================================
        // VALIDACIÓN DE ARCHIVOS
        // ========================================
        const fileInputs = document.querySelectorAll('input[type="file"]');

        fileInputs.forEach(input => {
            input.addEventListener('change', function (e) {
                const files = e.target.files;

                for (let file of files) {
                    const fileSize = file.size / 1024 / 1024; // MB

                    if (fileSize > 5) {
                        alert('❌ El archivo "' + file.name + '" es demasiado grande. Tamaño máximo: 5MB');
                        e.target.value = '';
                        return;
                    }

                    if (file.type !== 'application/pdf') {
                        alert('❌ Solo se permiten archivos PDF. El archivo "' + file.name + '" no es válido.');
                        e.target.value = '';
                        return;
                    }
                }
            });
        });

        // ========================================
        // VALIDACIÓN DE DÍAS DE ESTANCIA
        // ========================================
        const diasCheckboxes = document.querySelectorAll('input[name="dias_estancia[]"]');

        function validarDias() {
            const algunoMarcado = Array.from(diasCheckboxes).some(cb => cb.checked);

            if (!algunoMarcado) {
                diasCheckboxes.forEach(cb => cb.required = true);
            } else {
                diasCheckboxes.forEach(cb => cb.required = false);
            }
        }

        diasCheckboxes.forEach(cb => {
            cb.addEventListener('change', validarDias);
        });

        // ========================================
        // VALIDACIÓN ANTES DE ENVIAR
        // ========================================
        document.getElementById('accreditationForm').addEventListener('submit', function (e) {
            const empresaRegistrada = document.querySelector('input[name="empresa_registrada"]:checked');

            if (!empresaRegistrada) {
                e.preventDefault();
                alert('❌ Por favor responde si la empresa está registrada en el Catálogo de Vacantes');
                return false;
            }

            // Validar que al menos un día esté seleccionado
            const algunDiaMarcado = Array.from(diasCheckboxes).some(cb => cb.checked);
            if (!algunDiaMarcado) {
                e.preventDefault();
                alert('❌ Debes seleccionar al menos un día de estancia');
                return false;
            }

            // Validar archivos según el tipo
            if (empresaRegistrada.value === 'no') {
                // Tipo A: validar archivos de empresa NO registrada
                const recibos = document.getElementById('recibos_nomina').files;
                const constancia = document.getElementById('constancia_laboral').files;
                const reporte = document.getElementById('reporte_final_a').files;

                if (recibos.length === 0 || constancia.length === 0 || reporte.length === 0) {
                    e.preventDefault();
                    alert('❌ Debes subir todos los documentos requeridos:\n- Recibos de nómina\n- Constancia laboral\n- Reporte Final');
                    return false;
                }
            } else {
                // Tipo B: validar archivos de empresa registrada
                const carta = document.getElementById('carta_aceptacion').files;
                const validacion = document.getElementById('constancia_validacion').files;
                const reporte = document.getElementById('reporte_final_b').files;

                if (carta.length === 0 || validacion.length === 0 || reporte.length === 0) {
                    e.preventDefault();
                    alert('❌ Debes subir todos los documentos requeridos:\n- Carta de Aceptación\n- Constancia de Validación\n- Reporte Final');
                    return false;
                }
            }

            // Validar aceptación de privacidad
            if (!document.getElementById('privacy_accept').checked) {
                e.preventDefault();
                alert('❌ Debes aceptar el Aviso de Privacidad para continuar');
                return false;
            }

            // Confirmación final
            const confirmacion = confirm(
                '¿Estás seguro de que deseas enviar tu documentación?\n\n' +
                'Verifica que todos los archivos sean correctos antes de continuar.\n\n' +
                'Una vez enviado, el proceso de revisión será realizado por la UPIS.'
            );

            if (!confirmacion) {
                e.preventDefault();
                return false;
            }

            // Mostrar mensaje de carga
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Enviando documentación... Por favor espera';
            submitBtn.style.opacity = '0.7';
        });

        // ========================================
        // AUTOGUARDADO EN LOCALSTORAGE
        // ========================================
        const formFields = document.querySelectorAll('input:not([type="file"]):not([type="radio"]):not([type="checkbox"]), select, textarea');

        // Cargar datos guardados al cargar la página
        window.addEventListener('load', function () {
            formFields.forEach(field => {
                const savedValue = localStorage.getItem('acreditacion_' + field.name);
                if (savedValue && !field.readOnly) {
                    field.value = savedValue;
                }
            });
        });

        // Guardar datos mientras el usuario escribe
        formFields.forEach(field => {
            field.addEventListener('input', function () {
                if (!this.readOnly) {
                    localStorage.setItem('acreditacion_' + this.name, this.value);
                }
            });
        });

        // Limpiar localStorage al enviar exitosamente
        document.getElementById('accreditationForm').addEventListener('submit', function () {
            setTimeout(() => {
                formFields.forEach(field => {
                    localStorage.removeItem('acreditacion_' + field.name);
                });
            }, 1000);
        });
    </script>
</body>

</html>