<?php
// Archivo: src/Views/upis/manage_templates.php
// Vista para gestionar plantillas de cartas de presentación
// VERSIÓN 2.0: Contador global unificado

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
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .page-header {
            background: linear-gradient(135deg, #004a99 0%, #8b1538 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .page-header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }

        .page-header p {
            margin: 0;
            opacity: 0.9;
        }

        .info-box {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 5px;
        }

        .info-box strong {
            color: #0c5460;
        }

        .global-counter-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .global-counter-card h2 {
            margin: 0 0 10px 0;
            font-size: 18px;
            opacity: 0.9;
        }

        .global-counter-card .counter-number {
            font-size: 72px;
            font-weight: bold;
            margin: 20px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .global-counter-card .period {
            font-size: 24px;
            opacity: 0.95;
            margin-top: 10px;
        }

        .templates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .template-card {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .template-card h3 {
            color: #004a99;
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .template-info {
            margin: 10px 0;
            font-size: 14px;
            color: #6c757d;
        }

        .template-info strong {
            color: #495057;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin: 5px 0;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .update-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .update-form h2 {
            color: #004a99;
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #004a99;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group select,
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: #6c757d;
            font-size: 13px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #004a99;
            color: white;
        }

        .btn-primary:hover {
            background: #003d7a;
        }

        .btn-danger {
            background: #8b1538;
            color: white;
        }

        .btn-danger:hover {
            background: #6d1028;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-block {
            width: 100%;
            display: block;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #004a99;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .actions {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .reset-section {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }

        .reset-section h3 {
            color: #856404;
            margin-top: 0;
        }

        .same-template-notice {
            background: #e7f3ff;
            border: 2px solid #004a99;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .same-template-notice h3 {
            color: #004a99;
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/SIEP/public/index.php?action=upisDashboard" class="back-link">← Volver al Dashboard</a>

        <div class="page-header">
            <h1>🎨 Gestión de Plantillas de Cartas de Presentación</h1>
            <p>Administra la plantilla PDF y configura el periodo académico actual</p>
        </div>

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
                El contenido (destinatario, horas, etc.) se agrega dinámicamente sobre la plantilla según la configuración de cada solicitud.
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
                        <div class="template-info" style="font-size: 12px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #dee2e6;">
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
                <strong>⚠️ Importante:</strong> El archivo que subas se renombrará automáticamente a <code>Plantilla_CP.pdf</code> y reemplazará la plantilla actual para todas las variantes de carta.
            </div>
            
            <form method="POST" action="/SIEP/public/index.php?action=uploadTemplate" enctype="multipart/form-data" id="uploadForm">
                
                <div class="form-group">
                    <label for="academic_period">
                        Periodo Académico Actual <span style="color: #dc3545;">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="academic_period" 
                        name="academic_period"
                        placeholder="Ej: 2025/2"
                        pattern="\d{4}/[12]"
                        value="<?= htmlspecialchars($current_period) ?>"
                        required
                    >
                    <small>
                        Formato: YYYY/X donde X es 1 (enero-junio) o 2 (agosto-diciembre)<br>
                        Ejemplos: 2025/1, 2025/2, 2026/1
                    </small>
                </div>

                <div class="form-group">
                    <label for="template_file">
                        Nueva Plantilla PDF <span style="color: #dc3545;">*</span>
                    </label>
                    <input 
                        type="file" 
                        id="template_file" 
                        name="template_file"
                        accept=".pdf"
                        required
                    >
                    <small>
                        📄 Solo archivos PDF | Tamaño máximo: 10 MB<br>
                        <strong>📌 El archivo se renombrará automáticamente a:</strong> <code>Plantilla_CP.pdf</code><br>
                        Se creará un backup de la plantilla anterior con la fecha actual.
                    </small>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary btn-block">
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

        <div style="margin-top: 30px; text-align: center;">
            <a href="/SIEP/public/index.php?action=upisDashboard" class="btn btn-secondary">
                ← Volver al Dashboard
            </a>
        </div>
    </div>

    <script>
        // Validar archivo antes de enviar
        document.getElementById('template_file').addEventListener('change', function() {
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
        document.getElementById('academic_period').addEventListener('blur', function() {
            const period = this.value.trim();
            const regex = /^\d{4}\/[12]$/;
            
            if (period && !regex.test(period)) {
                alert('⚠️ Formato de periodo inválido.\nUsa el formato YYYY/X donde X es 1 o 2\nEjemplo: 2025/2');
                this.focus();
            }
        });

        // Confirmación antes de enviar
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const period = document.getElementById('academic_period').value;
            const file = document.getElementById('template_file').files[0];
            
            if (!file) {
                e.preventDefault();
                alert('⚠️ Por favor selecciona un archivo PDF');
                return false;
            }

            const confirmed = confirm(
                `¿Confirmas actualizar la plantilla?\n\n` +
                `Nuevo periodo: ${period}\n` +
                `Archivo original: ${file.name}\n` +
                `Se renombrará a: Plantilla_CP.pdf\n` +
                `Tamaño: ${(file.size / 1024).toFixed(2)} KB\n\n` +
                `⚠️ La plantilla actual se respaldará automáticamente.\n\n` +
                `Esta plantilla se usará para TODAS las cartas de presentación.`
            );

            if (!confirmed) {
                e.preventDefault();
                return false;
            }

            // Deshabilitar botón para evitar doble envío
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Subiendo plantilla...';

            return true;
        });
    </script>
</body>
</html>