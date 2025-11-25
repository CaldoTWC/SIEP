<?php
// Archivo: src/Views/student/request_presentation_letter.php
// VERSIÓN ACTUALIZADA: Soporte para 4 variantes de plantillas
require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['student']);

// La variable $profile_data viene del controlador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Carta de Presentación - SIEP</title>
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
            <h1>📝 Solicitud de Carta de Presentación</h1>
            <p>Completa el siguiente formulario para solicitar tu carta de presentación.</p>
        </div>
        <a href="/SIEP/public/index.php?action=studentDashboard" class="logout-btn">← Volver al Dashboard</a><br><br>
        <div class="info-notice">
            <strong>ℹ️ Información importante:</strong><br>
            Ahora puedes elegir entre 4 tipos de cartas según tus necesidades:
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Con o sin destinatario específico</li>
                <li>Con o sin mención de las 200 horas requeridas</li>
            </ul>
            La carta será revisada y aprobada por UPIS antes de su emisión.
        </div>

        <form method="POST" action="/SIEP/public/index.php?action=submitDetailedLetterRequest" enctype="multipart/form-data">
            
            <!-- SECCIÓN 1: DATOS DEL ESTUDIANTE -->
            <h2 class="section-title">👤 Datos del Estudiante</h2>
            
            <div class="readonly-notice">
                📋 Los siguientes datos se obtienen automáticamente de tu perfil y no pueden ser modificados.
            </div>

            <div class="form-group">
                <label for="full_name">Nombre Completo</label>
                <input 
                    type="text" 
                    id="full_name" 
                    value="<?= htmlspecialchars($profile_data['first_name'] . ' ' . $profile_data['last_name_p'] . ' ' . $profile_data['last_name_m']) ?>" 
                    readonly
                >
            </div>

            <div class="form-group">
                <label for="boleta">Número de Boleta</label>
                <input 
                    type="text" 
                    id="boleta" 
                    value="<?= htmlspecialchars($profile_data['boleta']) ?>" 
                    readonly
                >
            </div>

            <div class="form-group">
                <label for="career">Carrera</label>
                <input 
                    type="text" 
                    id="career" 
                    value="<?= htmlspecialchars($profile_data['career']) ?>" 
                    readonly
                >
            </div>

            <!-- SECCIÓN 2: INFORMACIÓN ACADÉMICA -->
            <h2 class="section-title">📚 Información Académica</h2>

            <div class="editable-notice">
                ✏️ Completa la siguiente información sobre tu avance académico.
            </div>

            <div class="form-group">
                <label for="credits_percentage">
                    Porcentaje de Créditos Aprobados <span class="required-indicator">*</span>
                </label>
                <input 
                    type="number" 
                    id="credits_percentage" 
                    name="credits_percentage"
                    min="0" 
                    max="100" 
                    step="0.01"
                    required
                    placeholder="Ej: 75.50"
                >
                <small>Ingresa tu porcentaje de avance académico (0-100)</small>
            </div>

            <div class="form-group">
                <label for="semester">
                    Semestre Actual <span class="required-indicator">*</span>
                </label>
                <select id="semester" name="semester" required>
                    <option value="">-- Selecciona tu semestre --</option>
                    <option value="6">6° Semestre</option>
                    <option value="7">7° Semestre</option>
                    <option value="8">8° Semestre</option>
                    <option value="9">9° Semestre</option>
                </select>
            </div>

            <!-- SECCIÓN 3: TIPO DE CARTA -->
            <h2 class="section-title">📋 Configuración de la Carta</h2>

            <div class="template-info-box">
                <h3>💡 ¿Qué tipo de carta necesitas?</h3>
                <p style="margin: 10px 0; color: #495057;">
                    Selecciona las opciones según los requisitos de la empresa o institución:
                </p>
                <ul>
                    <li><strong>Destinatario:</strong> Si la empresa solicita que la carta vaya dirigida a una persona específica</li>
                    <li><strong>Horas:</strong> Si requieren que se mencione explícitamente las 200 horas de estancia</li>
                </ul>
            </div>

            <!-- ¿Tiene destinatario específico? -->
            <div class="form-group">
                <label for="has_specific_recipient">
                    ¿La carta va dirigida a un destinatario específico? <span class="required-indicator">*</span>
                </label>
                <select id="has_specific_recipient" name="has_specific_recipient" required>
                    <option value="">-- Selecciona una opción --</option>
                    <option value="0">No - "A QUIEN CORRESPONDA"</option>
                    <option value="1">Sí - Destinatario específico (empresa/institución)</option>
                </select>
                <small>
                    Si la empresa te proporcionó el nombre y cargo de la persona a quien dirigir la carta, selecciona "Sí"
                </small>
            </div>

            <!-- Campos del destinatario (se muestran condicionalmente) -->
            <div id="recipient_fields" style="display: none;">
                <div class="form-group">
                    <label for="recipient_name">
                        Nombre completo del destinatario <span class="required-indicator">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="recipient_name" 
                        name="recipient_name"
                        placeholder="Ej: Ing. Juan Pérez López"
                        maxlength="200"
                    >
                    <small>Nombre completo de la persona a quien va dirigida la carta (con título si aplica)</small>
                </div>
                
                <div class="form-group">
                    <label for="recipient_position">
                        Cargo del destinatario <span class="required-indicator">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="recipient_position" 
                        name="recipient_position"
                        placeholder="Ej: Director de Recursos Humanos"
                        maxlength="200"
                    >
                    <small>Puesto o cargo que ocupa en la empresa/institución</small>
                </div>
            </div>

            <!-- ¿Requiere mostrar horas? -->
            <div class="form-group">
                <label for="requires_hours">
                    ¿La carta debe especificar las 200 horas requeridas? <span class="required-indicator">*</span>
                </label>
                <select id="requires_hours" name="requires_hours" required>
                    <option value="">-- Selecciona una opción --</option>
                    <option value="0">No - Sin mención de horas</option>
                    <option value="1">Sí - Incluir mención de 200 horas</option>
                </select>
                <small>
                    Algunas empresas requieren que se mencione explícitamente el número de horas de la estancia
                </small>
            </div>

            <!-- Empresa destino (opcional - para referencia) -->
            <div class="form-group">
                <label for="target_company_name">
                    Nombre de la empresa/institución (opcional)
                </label>
                <input 
                    type="text" 
                    id="target_company_name" 
                    name="target_company_name"
                    placeholder="Ej: Google México, CINVESTAV, etc."
                    maxlength="200"
                >
                <small>Solo para tu referencia interna. No aparecerá en la carta.</small>
            </div>

            <!-- Separador visual -->
            <hr class="separator">

            <!-- SECCIÓN 4: DOCUMENTOS REQUERIDOS -->
            <h2 class="section-title">📄 Documentos Requeridos</h2>

            <div class="editable-notice">
                📎 Sube tu kárdex o boleta global actualizada (solo archivos PDF, máximo 5 MB)
            </div>

            <div class="form-group">
                <label for="transcript">
                    Kárdex / Boleta Global <span class="required-indicator">*</span>
                </label>
                <input 
                    type="file" 
                    id="transcript" 
                    name="transcript"
                    accept=".pdf"
                    required
                >
                <small>
                    📌 Formato: PDF únicamente | Tamaño máximo: 5 MB<br>
                    Debe ser tu kárdex o boleta global actualizada
                </small>
            </div>

            <!-- Botón de envío -->
            <button type="submit" class="btn-submit" id="submitBtn">
                📤 Enviar Solicitud
            </button>

            <a href="/SIEP/public/index.php?action=studentDashboard" class="logout-btn">← Volver al Dashboard</a>
        </form>
    </div>

    <script>
        // Mostrar/ocultar campos de destinatario según selección
        document.getElementById('has_specific_recipient').addEventListener('change', function() {
            const recipientFields = document.getElementById('recipient_fields');
            const recipientNameInput = document.getElementById('recipient_name');
            const recipientPositionInput = document.getElementById('recipient_position');
            
            if (this.value === '1') {
                recipientFields.style.display = 'block';
                recipientNameInput.required = true;
                recipientPositionInput.required = true;
            } else {
                recipientFields.style.display = 'none';
                recipientNameInput.required = false;
                recipientPositionInput.required = false;
                recipientNameInput.value = '';
                recipientPositionInput.value = '';
            }
        });

        // Validar tamaño de archivo
        document.getElementById('transcript').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const maxSize = 5 * 1024 * 1024; // 5 MB
                if (file.size > maxSize) {
                    alert('⚠️ El archivo es demasiado grande. Tamaño máximo: 5 MB');
                    this.value = '';
                    return;
                }
                
                if (file.type !== 'application/pdf') {
                    alert('⚠️ Solo se permiten archivos PDF');
                    this.value = '';
                    return;
                }
            }
        });

        // Validación antes de enviar
        document.querySelector('form').addEventListener('submit', function(e) {
            const hasRecipient = document.getElementById('has_specific_recipient').value;
            const requiresHours = document.getElementById('requires_hours').value;
            const transcript = document.getElementById('transcript').files[0];
            
            // Validar que se seleccionaron ambas opciones de configuración
            if (hasRecipient === '' || requiresHours === '') {
                e.preventDefault();
                alert('⚠️ Por favor, selecciona el tipo de carta que necesitas (destinatario y horas).');
                return false;
            }
            
            // Si tiene destinatario, validar campos
            if (hasRecipient === '1') {
                const recipientName = document.getElementById('recipient_name').value.trim();
                const recipientPosition = document.getElementById('recipient_position').value.trim();
                
                if (recipientName === '' || recipientPosition === '') {
                    e.preventDefault();
                    alert('⚠️ Por favor, completa los datos del destinatario.');
                    return false;
                }
            }

            // Validar kárdex
            if (!transcript) {
                e.preventDefault();
                alert('⚠️ Por favor, sube tu kárdex o boleta global.');
                return false;
            }

            // Validar porcentaje
            const percentage = parseFloat(document.getElementById('credits_percentage').value);
            if (isNaN(percentage) || percentage < 0 || percentage > 100) {
                e.preventDefault();
                alert('⚠️ El porcentaje de créditos debe estar entre 0 y 100.');
                return false;
            }
            
            // Deshabilitar botón para evitar doble envío
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Enviando...';
            
            return true;
        });

        // Prevenir que el usuario salga sin guardar si ha llenado campos
        let formModified = false;
        document.querySelectorAll('input, select, textarea').forEach(function(element) {
            element.addEventListener('change', function() {
                formModified = true;
            });
        });

        window.addEventListener('beforeunload', function(e) {
            if (formModified) {
                e.preventDefault();
                e.returnValue = '¿Estás seguro de salir? Los cambios no guardados se perderán.';
            }
        });

        // Limpiar flag cuando se envía el form
        document.querySelector('form').addEventListener('submit', function() {
            formModified = false;
        });
    </script>
</body>
</html>