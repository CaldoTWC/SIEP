<?php
// Archivo: src/Views/student/request_presentation_letter.php (Versión con Nombres Separados)
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
    <title>Solicitar Carta de Presentación</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #004a99;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .intro-text {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            padding: 15px;
            background: #f0f8ff;
            border-radius: 5px;
        }
        
        .section-title {
            color: #004a99;
            border-bottom: 2px solid #004a99;
            padding-bottom: 10px;
            margin-top: 30px;
            margin-bottom: 20px;
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
        .form-group input[type="email"],
        .form-group input[type="number"],
        .form-group select,
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-group input[readonly] {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }
        
        .editable-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #856404;
        }
        
        .readonly-notice {
            background: #e7f3ff;
            border: 1px solid #2196F3;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #0d47a1;
        }
        
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #8b1538;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }
        
        .btn-submit:hover {
            background: #6d1028;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #004a99;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .required {
            color: red;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }
        
        @media (max-width: 768px) {
            .form-row, .form-row-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>📝 Solicitud de Carta de Presentación</h1>
            <p class="intro-text">
                Por favor, confirma tus datos, actualiza tu información académica y adjunta tu boleta global. 
                Esta información será validada por la UPIS.
            </p>

            <form action="/SIEP/public/index.php?action=submitDetailedLetterRequest" method="post" enctype="multipart/form-data">
                
                <!-- Sección: Datos del Estudiante -->
                <h2 class="section-title">👤 Datos del Estudiante</h2>
                
                <div class="readonly-notice">
                    ℹ️ <strong>Nota:</strong> Si algún dato es incorrecto, contacta a la UPIS para actualizarlo antes de continuar.
                </div>

                <!-- CAMBIO: Campos separados para nombre, apellido paterno y materno -->
                <div class="form-row-3">
                    <div class="form-group">
                        <label>Nombre(s):</label>
                        <input 
                            type="text" 
                            value="<?php echo htmlspecialchars($profile_data['first_name'] ?? 'No disponible'); ?>" 
                            readonly
                            style="font-weight: 600; color: #004a99;">
                    </div>

                    <div class="form-group">
                        <label>Apellido Paterno:</label>
                        <input 
                            type="text" 
                            value="<?php echo htmlspecialchars($profile_data['last_name_p'] ?? 'No disponible'); ?>" 
                            readonly
                            style="font-weight: 600; color: #004a99;">
                    </div>

                    <div class="form-group">
                        <label>Apellido Materno:</label>
                        <input 
                            type="text" 
                            value="<?php echo htmlspecialchars($profile_data['last_name_m'] ?? 'No disponible'); ?>" 
                            readonly
                            style="font-weight: 600; color: #004a99;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Número de Boleta:</label>
                        <input 
                            type="text" 
                            value="<?php echo htmlspecialchars($profile_data['boleta'] ?? ''); ?>" 
                            readonly>
                    </div>

                    <div class="form-group">
                        <label>Correo Institucional:</label>
                        <input 
                            type="email" 
                            value="<?php echo htmlspecialchars($profile_data['email'] ?? ''); ?>" 
                            readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label>Programa Académico:</label>
                    <input 
                        type="text" 
                        value="<?php echo htmlspecialchars($profile_data['career'] ?? ''); ?>" 
                        readonly>
                </div>

                <!-- Sección: Información Académica Actualizada -->
                <h2 class="section-title">📚 Información Académica Actualizada</h2>
                
                <div class="editable-notice">
                    ⚠️ <strong>Importante:</strong> Proporciona tu información académica más reciente. Será verificada con la boleta global que adjuntes.
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="credits_percentage">
                            Porcentaje de créditos cubiertos <span class="required">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="credits_percentage" 
                            name="credits_percentage" 
                            min="1" 
                            max="100" 
                            step="0.01" 
                            required
                            placeholder="Ej: 75.50">
                        <small style="color: #666;">Ingresa el porcentaje actual de tu avance académico</small>
                    </div>

                    <div class="form-group">
                        <label for="semester">
                            Semestre actual <span class="required">*</span>
                        </label>
                        <select id="semester" name="semester" required>
                            <option value="" disabled selected>-- Selecciona --</option>
                            <option value="6">6to Semestre</option>
                            <option value="7">7mo Semestre</option>
                            <option value="8">8vo Semestre</option>
                            <option value="9">9no Semestre</option>
                            <option value="10">10mo Semestre</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="transcript">
                        📄 Adjuntar Boleta Global Actualizada (PDF) <span class="required">*</span>
                    </label>
                    <input 
                        type="file" 
                        id="transcript" 
                        name="transcript" 
                        required 
                        accept=".pdf">
                    <small style="color: #666;">
                        Tamaño máximo: 5MB. Solo archivos PDF.
                    </small>
                </div>

                <button type="submit" class="btn-submit">
                    📤 Enviar Solicitud para Revisión
                </button>
            </form>
            
            <a href="/SIEP/public/index.php?action=studentDashboard" class="back-link">
                ← Volver al Panel
            </a>
        </div>
    </div>

    <script>
        // Validación del tamaño del archivo
        document.getElementById('transcript').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // MB
                if (fileSize > 5) {
                    alert('El archivo es demasiado grande. Tamaño máximo: 5MB');
                    e.target.value = '';
                }
                
                if (file.type !== 'application/pdf') {
                    alert('Solo se permiten archivos PDF');
                    e.target.value = '';
                }
            }
        });

        // Validación del porcentaje
        document.getElementById('credits_percentage').addEventListener('blur', function(e) {
            const value = parseFloat(e.target.value);
            if (value < 1 || value > 100) {
                alert('El porcentaje debe estar entre 1 y 100');
                e.target.value = '';
            }
        });
    </script>
</body>
</html>