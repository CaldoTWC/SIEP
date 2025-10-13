<?php
// Archivo: src/Views/upis/review_letters.php (Versión Corregida)
require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Cartas de Presentación</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        /* Estilos para modales */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close-button:hover,
        .close-button:focus {
            color: #000;
        }
        
        .modal-details p {
            margin: 15px 0;
            font-size: 15px;
        }
        
        .modal-details hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #ddd;
        }
        
        .btn-details {
            background-color: #2196F3;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-details:hover {
            background-color: #0b7dda;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        
        th {
            background-color: #004a99;
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .actions {
            text-align: center;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        h2 {
            color: #004a99;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Gestión de Cartas de Presentación</h1>
        <a href="/SIEP/public/index.php?action=upisDashboard" style="display: inline-block; margin-bottom: 20px;">← Volver al Panel Principal</a>
        <!-- Mensajes de Estado -->
<?php if (isset($_GET['status'])): ?>
    <?php
        $message = '';
        $message_class = '';
        $count = $_GET['count'] ?? 0;
        
        switch ($_GET['status']) {
            case 'letters_approved':
                $message = "✅ Se aprobaron {$count} solicitud(es) exitosamente.";
                $message_class = 'success';
                break;
            case 'letters_rejected':
                $message = "❌ Se rechazaron {$count} solicitud(es).";
                $message_class = 'warning';
                break;
            case 'no_selection':
                $message = "⚠️ No seleccionaste ninguna solicitud.";
                $message_class = 'warning';
                break;
            case 'error':
                $message = "❌ Ocurrió un error al procesar las solicitudes.";
                $message_class = 'error';
                break;
        }
    ?>
    <?php if ($message): ?>
        <div style="padding: 15px; margin: 20px 0; border-radius: 5px; 
             background-color: <?php echo $message_class === 'success' ? '#d4edda' : ($message_class === 'warning' ? '#fff3cd' : '#f8d7da'); ?>; 
             color: <?php echo $message_class === 'success' ? '#155724' : ($message_class === 'warning' ? '#856404' : '#721c24'); ?>; 
             border: 1px solid <?php echo $message_class === 'success' ? '#c3e6cb' : ($message_class === 'warning' ? '#ffeaa7' : '#f5c6cb'); ?>;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
        <!-- TABLA DE SOLICITUDES PENDIENTES -->
        <h2>⏳ Pendientes de Revisión</h2>
        <?php if (empty($pendingLetters)): ?>
            <p style="padding: 20px; background: #f0f8ff; border-radius: 5px; text-align: center;">
                ℹ️ No hay solicitudes pendientes de revisión.
            </p>
        <?php else: ?>
            <form action="/SIEP/public/index.php?action=processLetterRequests" method="post">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50px;"><input type="checkbox" id="selectAllPending"></th>
                                <th>Estudiante</th>
                                <th>Boleta</th>
                                <th>Carrera</th>
                                <th>Semestre</th>
                                <th>Créditos</th>
                                <th>Fecha Solicitud</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingLetters as $index => $letter): ?>
                                <tr>
                                    <td>
                                        <!-- CORRECCIÓN: Cambiar 'application_id' por 'id' -->
                                        <input type="checkbox" name="request_ids[]" value="<?php echo htmlspecialchars($letter['id']); ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($letter['first_name'] . ' ' . $letter['last_name_p'] . ' ' . $letter['last_name_m']); ?></td>
                                    <td><?php echo htmlspecialchars($letter['boleta']); ?></td>
                                    <td><?php echo htmlspecialchars($letter['career']); ?></td>
                                    <td><?php echo htmlspecialchars($letter['current_semester']); ?>°</td>
                                    <td><?php echo htmlspecialchars(number_format($letter['credits_percentage'], 2)); ?>%</td>
                                    <td><?php echo date('d/m/Y', strtotime($letter['created_at'])); ?></td>
                                    <td class="actions">
                                        <button type="button" class="btn-details" onclick="openModal('modal-let-<?php echo $index; ?>')">
                                            🔍 Verificar Datos
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 20px; text-align: right;">
                    <button type="submit" name="bulk_action" value="approve" class="btn" style="background-color: #28a745;">
                        ✅ Aprobar Seleccionadas
                    </button>
                    <button type="submit" name="bulk_action" value="reject" class="btn" style="background-color: #dc3545;">
                        ❌ Rechazar Seleccionadas
                    </button>
                </div>
            </form>
        <?php endif; ?>
        
        <!-- SECCIÓN DE CARTAS APROBADAS -->
<h2>✅ Solicitudes Aprobadas (Listas para Procesar)</h2>
<?php if (empty($approvedLetters)): ?>
    <p style="padding: 20px; background: #d4edda; border-radius: 5px; text-align: center; color: #155724;">
        ✅ No hay solicitudes aprobadas pendientes. Todas han sido procesadas.
    </p>
<?php else: ?>
    <p style="background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;">
        ℹ️ <strong>Total: <?php echo count($approvedLetters); ?> carta(s) aprobada(s)</strong> lista(s) para generar, firmar y devolver.
    </p>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Boleta</th>
                    <th>Carrera</th>
                    <th>Semestre</th>
                    <th>Créditos</th>
                    <th>Fecha Aprobación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($approvedLetters as $letter): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($letter['first_name'] . ' ' . $letter['last_name_p'] . ' ' . $letter['last_name_m']); ?></td>
                        <td><strong><?php echo htmlspecialchars($letter['boleta']); ?></strong></td>
                        <td><?php echo htmlspecialchars($letter['career']); ?></td>
                        <td><?php echo htmlspecialchars($letter['current_semester']); ?>°</td>
                        <td><?php echo htmlspecialchars(number_format($letter['credits_percentage'], 2)); ?>%</td>
                        <td><?php echo isset($letter['reviewed_at']) ? date('d/m/Y H:i', strtotime($letter['reviewed_at'])) : 'N/A'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Botones de Acción -->
    <div style="margin: 30px 0; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
        <a href="/SIEP/public/index.php?action=downloadAllApprovedLetters" 
           class="btn" target="_blank" 
           style="background-color: #007bff; padding: 15px 25px; font-size: 16px;">
            📦 1. Descargar Todas (ZIP)
        </a>
        
        <a href="/SIEP/public/index.php?action=showUploadDocumentsForm" 
           class="btn" 
           style="background-color: #28a745; padding: 15px 25px; font-size: 16px;">
            📤 2. Subir Cartas Firmadas
        </a>
        
        <a href="/SIEP/public/index.php?action=clearAllApprovedLetters" 
           class="btn" 
           style="background-color: #6c757d; padding: 15px 25px; font-size: 16px;"
           onclick="return confirm('⚠️ ¿Ya subiste todas las cartas firmadas?\n\nEsto eliminará las solicitudes de la lista.');">
            🗑️ 3. Limpiar Lista
        </a>
    </div>
    
    <!-- Instrucciones del proceso -->
    <div style="background: #e7f3ff; border-left: 4px solid #2196F3; padding: 20px; border-radius: 5px; margin-top: 20px;">
        <h4 style="margin-top: 0;">📋 Proceso de Gestión:</h4>
        <ol style="margin: 10px 0 0 20px;">
            <li>Click en <strong>"Descargar Todas (ZIP)"</strong> para obtener los PDFs</li>
            <li>Imprime, firma y sella cada carta</li>
            <li>Escanea cada carta firmada como PDF con formato: <code>BOLETA_CP.pdf</code></li>
            <li>Click en <strong>"Subir Cartas Firmadas"</strong> para devolverlas al sistema</li>
            <li>Los estudiantes podrán descargar sus cartas desde su panel</li>
            <li>Cuando hayas terminado, click en <strong>"Limpiar Lista"</strong></li>
        </ol>
    </div>
<?php endif; ?>

    </div>

    <!-- ESTRUCTURA DE LOS MODALES PARA CARTAS -->
    <?php if (!empty($pendingLetters)): ?>
        <?php foreach ($pendingLetters as $index => $letter): ?>
            <div id="modal-let-<?php echo $index; ?>" class="modal">
                <div class="modal-content">
                    <span class="close-button" onclick="closeModal('modal-let-<?php echo $index; ?>')">&times;</span>
                    <h2>🔍 Verificación de Datos del Estudiante</h2>
                    <div class="modal-details">
                        <p><strong>Nombre Completo:</strong> <?php echo htmlspecialchars($letter['first_name'] . ' ' . $letter['last_name_p'] . ' ' . $letter['last_name_m']); ?></p>
                        <p><strong>Correo:</strong> <?php echo htmlspecialchars($letter['email']); ?></p>
                        <p><strong>Boleta:</strong> <?php echo htmlspecialchars($letter['boleta']); ?></p>
                        <p><strong>Carrera:</strong> <?php echo htmlspecialchars($letter['career']); ?></p>
                        <p><strong>Semestre Declarado:</strong> <?php echo htmlspecialchars($letter['current_semester']); ?>° semestre</p>
                        <p><strong>Avance de Créditos:</strong> <?php echo htmlspecialchars(number_format($letter['credits_percentage'], 2)); ?>%</p>
                        <p><strong>Fecha de Solicitud:</strong> <?php echo date('d/m/Y H:i', strtotime($letter['created_at'])); ?></p>
                        <hr>
                        <p><strong>📄 Documento de Respaldo:</strong></p>
                        <a href="/SIEP/<?php echo htmlspecialchars($letter['transcript_path']); ?>" target="_blank" class="btn" style="background-color: #2196F3; display: inline-block; margin-top: 10px;">
                            📥 Ver Boleta Global (PDF)
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- JAVASCRIPT PARA CONTROLAR LOS MODALES Y CHECKBOXES -->
    <script>
        function openModal(modalId) { 
            document.getElementById(modalId).style.display = 'block'; 
        }
        
        function closeModal(modalId) { 
            document.getElementById(modalId).style.display = 'none'; 
        }
        
        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) { 
            if (event.target.classList.contains('modal')) { 
                event.target.style.display = 'none'; 
            } 
        }
        
        // Seleccionar todas las checkboxes
        document.getElementById('selectAllPending').addEventListener('click', function(event) {
            let checkboxes = this.closest('form').querySelectorAll('input[name="request_ids[]"]');
            for (let checkbox of checkboxes) { 
                checkbox.checked = event.target.checked; 
            }
        });
    </script>
</body>
</html>