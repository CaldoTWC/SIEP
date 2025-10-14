<?php
// Archivo: src/Views/student/accreditation_form.php

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['student']); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acreditación de Estancia Profesional</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #004a99;
            text-decoration: none;
            font-weight: bold;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        /* Sección de opciones */
        .options-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }
        
        .option-card {
            border: 3px solid #004a99;
            border-radius: 10px;
            padding: 25px;
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
            position: relative;
        }
        
        .option-card h2 {
            color: #004a99;
            margin-top: 0;
            font-size: 24px;
            text-align: center;
            background: #004a99;
            color: white;
            padding: 10px;
            margin: -25px -25px 20px -25px;
            border-radius: 7px 7px 0 0;
        }
        
        .option-card h3 {
            color: #0066cc;
            font-size: 16px;
            margin-top: 20px;
        }
        
        .option-card ul {
            list-style: none;
            padding-left: 0;
        }
        
        .option-card li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }
        
        .option-card li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }
        
        /* Botón de infografía */
        .btn-infografia {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: bold;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .btn-infografia:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        
        .btn-infografia::before {
            content: "🖼️ ";
        }
        
        /* Sección de formulario */
        .form-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .form-section h2 {
            color: #004a99;
            border-bottom: 2px solid #004a99;
            padding-bottom: 10px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: #0c5460;
            color: #0c5460;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-color: #856404;
            color: #856404;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px dashed #004a99;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
        }
        
        .btn {
            background-color: #004a99;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #003366;
        }
        
        .btn-download {
            background-color: #28a745;
            margin-bottom: 20px;
        }
        
        .btn-download:hover {
            background-color: #218838;
        }
        
        .download-section {
            text-align: center;
            padding: 20px;
            background: #e7f3ff;
            border-radius: 10px;
            margin: 30px 0;
        }
        
        .download-section p {
            font-size: 16px;
            margin-bottom: 15px;
        }
        
        .importante {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        
        @media (max-width: 768px) {
            .options-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/SIEP/public/index.php?action=studentDashboard" class="back-link">← Volver al Panel</a>
        
        <h1>Acreditación de Estancia Profesional</h1>
        
        <div class="alert alert-info">
            <strong>📋 Información Importante:</strong><br>
            Existen dos opciones para acreditar tu Estancia Profesional. Selecciona la que aplique a tu caso.
        </div>
        
        <!-- OPCIONES A y B -->
        <div class="options-container">
            <!-- OPCIÓN A -->
            <div class="option-card">
                <h2>OPCIÓN A</h2>
                <p><strong>Para empresas NO REGISTRADAS en el Catálogo de Vacantes</strong></p>
                
                <h3>📄 Documentos Requeridos:</h3>
                <ul>
                    <li>Constancia laboral o de becaria</li>
                    <li>Recibo de nómina</li>
                    <li>Reporte Final de actividades</li>
                </ul>
                
                <h3>⏱️ Requisito:</h3>
                <ul>
                    <li>200 horas de estancia completadas</li>
                </ul>
                
                <!-- Botón de infografía -->
<a href="/SIEP/public/downloads/Infografia_Acreditacion_Opcion_A.pdf" 
   class="btn-infografia" 
   target="_blank">
   Para más información consulta la infografía
</a>
            </div>
            
            <!-- OPCIÓN B -->
            <div class="option-card">
                <h2>OPCIÓN B</h2>
                <p><strong>Para empresas REGISTRADAS en el Catálogo de Vacantes</strong></p>
                
                <h3>📄 Documentos Requeridos:</h3>
                <ul>
                    <li>Carta de Aceptación de la Estancia Profesional</li>
                    <li>Constancia de Validación de Horas</li>
                    <li>Reporte Final de actividades</li>
                </ul>
                
                <h3>⏱️ Requisito:</h3>
                <ul>
                    <li>200 horas de estancia completadas</li>
                </ul>
                
                <!-- Botón de infografía -->
<a href="/SIEP/public/downloads/Infografia_Acreditacion_Opcion_B.pdf" 
   class="btn-infografia" 
   target="_blank">
   Para más información consulta la infografía
</a>
            </div>
        </div>
        
        <div class="importante">
            <strong>⚠️ IMPORTANTE:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <li>Para acreditar la Estancia Profesional en el semestre 2026/1, solo se aceptará la documentación que se suba del <strong>2 de septiembre al 30 de noviembre de 2025</strong>.</li>
                <li>La emisión de la constancia NO debe ser mayor a un año desde la conclusión de la estancia.</li>
            </ul>
        </div>
        
        <!-- DESCARGA DE FORMULARIO -->
        <div class="download-section">
            <h3>📥 Descarga el Formulario Oficial</h3>
            <p>Antes de subir tu documentación, descarga y llena el formulario de acreditación:</p>
            <a href="/SIEP/public/downloads/Formulario_Acreditacion_EP_2026.docx" 
               class="btn btn-download" 
               download>
                📄 Descargar Formulario de Acreditación 2026
            </a>
        </div>
        
        <!-- FORMULARIO DE SUBIDA -->
        <div class="form-section">
            <h2>📤 Subir Documentación</h2>
            
            <div class="alert alert-warning">
                <strong>⚠️ Antes de continuar:</strong><br>
                Asegúrate de tener listos los siguientes documentos en formato PDF:
                <ol style="margin: 10px 0 0 20px;">
                    <li>Reporte Final de actividades (firmado por ti y la persona responsable en la empresa)</li>
                    <li>Constancia de Validación de Horas / Constancia Laboral (según tu opción)</li>
                </ol>
            </div>
            
            <form action="/SIEP/public/index.php?action=submitAccreditation" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="final_report">
                        <strong>1. Reporte Final de Actividades</strong> *
                    </label>
                    <input type="file" 
                           id="final_report" 
                           name="final_report" 
                           accept=".pdf" 
                           required>
                    <small>Formato: PDF | Firmado por ti y la persona responsable en la empresa</small>
                </div>
                
                <div class="form-group">
                    <label for="signed_validation_letter">
                        <strong>2. Constancia de Validación / Constancia Laboral</strong> *
                    </label>
                    <input type="file" 
                           id="signed_validation_letter" 
                           name="signed_validation_letter" 
                           accept=".pdf" 
                           required>
                    <small>
                        Formato: PDF<br>
                        <strong>Opción A:</strong> Constancia Laboral + Recibo de nómina<br>
                        <strong>Opción B:</strong> Carta de Aceptación + Constancia de Validación de Horas
                    </small>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">
                        ✅ Enviar Documentación
                    </button>
                </div>
                
                <small style="color: #666;">
                    * Campos obligatorios. Solo se aceptan archivos PDF.
                </small>
            </form>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <h3>📞 ¿Necesitas ayuda?</h3>
            <p>Si tienes dudas sobre el proceso de acreditación, consulta en el SAES o contacta a la UPIS-ESCOM.</p>
        </div>
        
    </div>
</body>
</html>