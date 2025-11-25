<?php
// Archivo: src/Views/upis/manage_templates.php
// Vista para gestionar plantillas de cartas de presentación
// VERSIÓN 2.0: Contador global unificado - Sin backup automático

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']);

// $templates viene del controlador
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Plantillas - SIEP</title>
    <link rel="stylesheet" href="/SIEP/public/css/upis.css">

</head>

<body>
    <!-- Encabezado bonito -->
    <div class="upis-header">
        <h1>Panel de Administración de UPIS</h1>

    </div>
    <div class="container">


        <div class="page-header">
            <h1>🎨 Gestión de Plantillas de Cartas de Presentación</h1>
            <p>Administra la plantilla PDF y configura el periodo académico actual</p>
        </div>
        <a href="/SIEP/public/index.php?action=upisDashboard" class="logout-btn">← Volver al Dashboard</a><br><br>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'success'): ?>
                <div class="alert alert-success">
                    ✅ <strong>¡Éxito!</strong> La plantilla se actualizó correctamente.
                </div>
            <?php elseif ($_GET['status'] === 'error'): ?>
                <div class="alert alert-error">
                    ❌ <strong>Error:</strong> Hubo un problema al actualizar la plantilla. Inténtalo de nuevo.
                </div>
            <?php elseif ($_GET['status'] === 'period_updated'): ?>
                <div class="alert alert-success">
                    ✅ <strong>¡Periodo actualizado!</strong> Se actualizó el periodo académico para todas las plantillas.
                </div>
            <?php elseif ($_GET['status'] === 'counters_reset'): ?>
                <div class="alert alert-success">
                    ✅ <strong>¡Contador reiniciado!</strong> El contador global de numeración ha sido reiniciado a 0.
                </div>
            <?php elseif ($_GET['status'] === 'invalid_file'): ?>
                <div class="alert alert-error">
                    ❌ <strong>Archivo inválido:</strong> Solo se permiten archivos PDF de máximo 10 MB.
                </div>
            <?php elseif ($_GET['status'] === 'invalid_period'): ?>
                <div class="alert alert-error">
                    ❌ <strong>Periodo inválido:</strong> El formato del periodo debe ser YYYY/X (ej: 2025/2).
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="info-box">
            <strong>ℹ️ Información importante:</strong><br>
            El sistema maneja 4 tipos de cartas de presentación que se generan dinámicamente sobre la misma plantilla:
            <ul style="margin: 10px 0;">
                <li><strong>Normal:</strong> Sin destinatario específico, sin mención de horas</li>
                <li><strong>Normal con Horas:</strong> Sin destinatario, con mención de 200 horas</li>
                <li><strong>Con Destinatario:</strong> Con destinatario específico, sin horas</li>
                <li><strong>Con Destinatario y Horas:</strong> Con destinatario y 200 horas</li>
            </ul>
        </div>

        <div class="same-template-notice">
            <h3>📄 Plantilla Única</h3>
            <p style="margin: 10px 0; color: #495057;">
                <strong>Todas las variantes de carta usan el mismo archivo PDF:</strong> <code>Plantilla_CP.pdf</code>
            </p>
            <p style="margin: 10px 0; color: #495057;">
                El contenido (destinatario, horas, etc.) se agrega dinámicamente sobre la plantilla según la
                configuración de cada solicitud.
            </p>
        </div>

        <!-- Contador Global -->
        <?php
        $global_counter = isset($templates[0]['global_letter_counter']) ? $templates[0]['global_letter_counter'] : 0;
        $current_period = isset($templates[0]['academic_period']) ? $templates[0]['academic_period'] : '2025/2';
        ?>

        <div class="global-counter-card">
            <h2>📊 CONTADOR GLOBAL DE OFICIOS</h2>
            <div class="counter-number"><?= $global_counter ?></div>
            <p style="margin: 0; font-size: 16px; opacity: 0.9;">
                Cartas de presentación generadas
            </p>
            <div class="period">Periodo: <?= htmlspecialchars($current_period) ?></div>
        </div>

        <!-- Estado de las 4 variantes -->
        <h2 style="color: #004a99; margin-bottom: 20px;">📋 Variantes de Cartas Configuradas</h2>

        <div class="templates-grid">
            <?php foreach ($templates as $template): ?>
                <div class="template-card">
                    <h3><?= htmlspecialchars($template['template_name']) ?></h3>

                    <div class="template-info">
                        <strong>Código:</strong> <code><?= htmlspecialchars($template['template_type']) ?></code>
                    </div>

                    <div class="template-info">
                        <strong>Periodo:</strong> <?= htmlspecialchars($template['academic_period']) ?>
                    </div>

                    <div class="template-info">
                        <strong>Archivo:</strong><br>
                        <code style="font-size: 11px; color: #6c757d;">
                                <?= htmlspecialchars($template['template_file_path']) ?>
                            </code>
                    </div>

                    <div class="template-info">
                        <strong>Estado:</strong>
                        <?php if ($template['is_active']): ?>
                            <span class="badge badge-success">✓ Activa</span>
                        <?php else: ?>
                            <span class="badge badge-warning">⚠ Inactiva</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($template['updated_at']): ?>
                        <div class="template-info"
                            style="font-size: 12px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #dee2e6;">
                            <strong>Última actualización:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($template['updated_at'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Formulario para actualizar plantilla -->
        <div class="update-form">
            <h2>📤 Actualizar Plantilla y Periodo Académico</h2>

            <div class="alert alert-warning" style="margin-bottom: 20px;">
                <strong>⚠️ Importante:</strong> El archivo que subas se renombrará automáticamente a
                <code>Plantilla_CP.pdf</code> y <strong>sobrescribirá</strong> la plantilla actual para todas las
                variantes de carta.<br>
                <strong>📌 Asegúrate de tener un respaldo local antes de subir.</strong>
            </div>

            <form method="POST" action="/SIEP/public/index.php?action=uploadTemplate" enctype="multipart/form-data"
                id="uploadForm">

                <div class="form-group">
                    <label for="academic_period">
                        Periodo Académico Actual <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="text" id="academic_period" name="academic_period" placeholder="Ej: 2025/2"
                        pattern="\d{4}/[12]" value="<?= htmlspecialchars($current_period) ?>" required>
                    <small>
                        Formato: YYYY/X donde X es 1 (enero-junio) o 2 (agosto-diciembre)<br>
                        Ejemplos: 2025/1, 2025/2, 2026/1
                    </small>
                </div>

                <div class="form-group">
                    <label for="template_file">
                        Nueva Plantilla PDF <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="file" id="template_file" name="template_file" accept=".pdf" required>
                    <small>
                        📄 Solo archivos PDF | Tamaño máximo: 10 MB<br>
                        <strong>📌 El archivo se renombrará automáticamente a:</strong>
                        <code>Plantilla_CP.pdf</code><br>
                        ⚠️ La plantilla actual será <strong>reemplazada</strong>. Asegúrate de tener un respaldo.
                    </small>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                        📤 Actualizar Plantilla y Periodo
                    </button>
                </div>
            </form>
        </div>

        <!-- Sección de acciones peligrosas -->
        <div class="reset-section">
            <h3>⚠️ Zona de Administración Avanzada</h3>
            <p style="color: #856404; margin-bottom: 20px;">
                <strong>Atención:</strong> La siguiente acción reinicia el contador global de numeración de cartas.
            </p>

            <form method="POST" action="/SIEP/public/index.php?action=resetLetterCounters"
                onsubmit="return confirm('⚠️ ¿Estás seguro de reiniciar el contador global de numeración?\n\nEl contador actual es: <?= $global_counter ?>\n\nSe reiniciará a 0 y la próxima carta será No. 01-<?= $current_period ?>\n\nEsta acción no se puede deshacer.\n\n✅ Solo hazlo al inicio de un nuevo periodo académico.');"
                style="margin-top: 15px;">
                <button type="submit" class="btn btn-danger btn-block">
                    🔄 Reiniciar Contador Global (Inicio de Periodo)
                </button>
                <small style="display: block; margin-top: 10px; color: #856404;">
                    Reinicia a 0 el contador global de numeración compartido por todas las variantes.<br>
                    <strong>Solo usar al inicio de un nuevo periodo escolar.</strong>
                </small>
            </form>
        </div>

        <div style="margin-top: 30px; text-align: left;">
            <a href="/SIEP/public/index.php?action=upisDashboard" class="logout-btn">← Volver al Dashboard</a>
        </div>
    </div>

    <script>
        // Validar archivo antes de enviar
        document.getElementById('template_file').addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const maxSize = 10 * 1024 * 1024; // 10 MB
                if (file.size > maxSize) {
                    alert('⚠️ El archivo es demasiado grande. Tamaño máximo: 10 MB');
                    this.value = '';
                    return;
                }

                if (file.type !== 'application/pdf') {
                    alert('⚠️ Solo se permiten archivos PDF');
                    this.value = '';
                    return;
                }

                // Mostrar info del archivo
                const fileName = file.name;
                const fileSize = (file.size / 1024).toFixed(2);
                console.log(`✅ Archivo seleccionado: ${fileName} (${fileSize} KB)`);
                console.log('⚠️ Se renombrará a: Plantilla_CP.pdf');
            }
        });

        // Validar formato de periodo
        document.getElementById('academic_period').addEventListener('blur', function () {
            const period = this.value.trim();
            const regex = /^\d{4}\/[12]$/;

            if (period && !regex.test(period)) {
                alert('⚠️ Formato de periodo inválido.\nUsa el formato YYYY/X donde X es 1 o 2\nEjemplo: 2025/2');
                this.focus();
            }
        });

        // Confirmación antes de enviar
        document.getElementById('uploadForm').addEventListener('submit', function (e) {
            const period = document.getElementById('academic_period').value;
            const file = document.getElementById('template_file').files[0];

            if (!file) {
                e.preventDefault();
                alert('⚠️ Por favor selecciona un archivo PDF');
                return false;
            }

            const confirmed = confirm(
                `⚠️ ¿CONFIRMAS ACTUALIZAR LA PLANTILLA?\n\n` +
                `Nuevo periodo: ${period}\n` +
                `Archivo: ${file.name}\n` +
                `Se guardará como: Plantilla_CP.pdf\n` +
                `Tamaño: ${(file.size / 1024).toFixed(2)} KB\n\n` +
                `⚠️ ATENCIÓN: La plantilla actual será REEMPLAZADA.\n` +
                `Asegúrate de tener un respaldo antes de continuar.\n\n` +
                `Esta plantilla se usará para TODAS las cartas de presentación.`
            );

            if (!confirmed) {
                e.preventDefault();
                return false;
            }

            // Deshabilitar botón para evitar doble envío
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Subiendo plantilla...';

            return true;
        });
    </script>
</body>

</html>