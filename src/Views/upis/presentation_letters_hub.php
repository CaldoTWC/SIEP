<?php
/**
 * Hub de Gestión de Cartas de Presentación
 * 
 * Vista integrada con 4 secciones en tabs:
 * 1. Solicitudes Pendientes - Revisar y aprobar/rechazar individualmente
 * 2. Cartas Aprobadas - Descargar ZIP masivo o individual
 * 3. Subir Cartas Firmadas - Upload de cartas firmadas (marca como completadas)
 * 4. Cartas Completadas - Historial de cartas entregadas
 * 
 * @package SIEP\Views\Upis
 * @version 1.0.0
 * @date 2025-11-11
 */

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['upis', 'admin']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cartas de Presentación - SIEP UPIS</title>
    <link rel="stylesheet" href="/SIEP/public/css/upis.css">

</head>

<body>
    <!-- Encabezado bonito -->
    <div class="upis-header">
        <h1>Panel de Administración de UPIS</h1>

    </div>
    <div class="container">
        <!-- Header del Hub -->
        <div class="hub-header">
            <h1>📝 Gestión de Cartas de Presentación</h1>
            <p>Hub centralizado para gestionar todo el ciclo de vida de las cartas de presentación</p>
        </div>
        <a href="/SIEP/public/index.php?action=upisDashboard" class="logout-btn" style="margin-bottom: 20px;">
            ← Volver al Panel Principal
        </a>

        <!-- Mensajes de Estado -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['warning'])): ?>
            <div class="alert alert-warning">
                ⚠️ <?php echo htmlspecialchars($_SESSION['warning']);
                unset($_SESSION['warning']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['upload_errors'])): ?>
            <div class="alert alert-error">
                <strong>Errores en la subida:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <?php foreach ($_SESSION['upload_errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['upload_errors']); ?>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card pending">
                <div class="stat-label">Pendientes</div>
                <div class="stat-number"><?php echo $stats['pending_count']; ?></div>
            </div>
            <div class="stat-card approved">
                <div class="stat-label">Aprobadas</div>
                <div class="stat-number"><?php echo $stats['approved_count']; ?></div>
            </div>
            <div class="stat-card completed">
                <div class="stat-label">Completadas</div>
                <div class="stat-number"><?php echo $stats['completed_count']; ?></div>
            </div>
        </div>

        <!-- Tabs Container -->
        <div class="tabs-container">
            <div class="tabs-header">
                <button
                    class="tab-button <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'pending') ? 'active' : ''; ?>"
                    onclick="openTab(event, 'pending')">
                    ⏳ Pendientes (<?php echo $stats['pending_count']; ?>)
                </button>
                <button
                    class="tab-button <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'approved') ? 'active' : ''; ?>"
                    onclick="openTab(event, 'approved')">
                    ✅ Aprobadas (<?php echo $stats['approved_count']; ?>)
                </button>
                <button
                    class="tab-button <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'upload') ? 'active' : ''; ?>"
                    onclick="openTab(event, 'upload')">
                    📤 Subir Firmadas
                </button>
                <button
                    class="tab-button <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'completed') ? 'active' : ''; ?>"
                    onclick="openTab(event, 'completed')">
                    📋 Completadas (<?php echo $stats['completed_count']; ?>)
                </button>
            </div>

            <!-- TAB 1: Solicitudes Pendientes -->
            <div id="pending"
                class="tab-content <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'pending') ? 'active' : ''; ?>">
                <h2>⏳ Solicitudes Pendientes de Revisión</h2>
                <p style="color: #666; margin-bottom: 20px;">
                    Revisa cada solicitud individualmente y decide si aprobar o rechazar.
                </p>

                <?php if (empty($pendingLetters)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">🎉</div>
                        <h3>¡No hay solicitudes pendientes!</h3>
                        <p>Todas las solicitudes han sido revisadas.</p>
                    </div>
                <?php else: ?>
                    <table class="letters-table">
                        <thead>
                            <tr>
                                <th>Boleta</th>
                                <th>Nombre Completo</th>
                                <th>Carrera</th>
                                <th>Empresa</th>
                                <th>Fecha de Solicitud</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingLetters as $letter): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($letter['boleta']); ?></strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($letter['first_name'] . ' ' . $letter['last_name_p'] . ' ' . $letter['last_name_m']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($letter['career']); ?></td>
                                    <td><?php echo htmlspecialchars($letter['company_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($letter['request_date'])); ?></td>
                                    <td>
                                        <button class="btn btn-info btn-small"
                                            onclick="viewLetterDetails(<?php echo $letter['id']; ?>)">
                                            👁️ Ver Detalles
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- TAB 2: Cartas Aprobadas -->
            <div id="approved"
                class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'approved') ? 'active' : ''; ?>">
                <h2>✅ Cartas Aprobadas</h2>
                <p style="color: #666; margin-bottom: 20px;">
                    Descarga cartas individuales o todas en un archivo ZIP. Las cartas permanecen aquí hasta que sean
                    marcadas como completadas al subir la versión firmada.
                </p>

                <?php if (!empty($approvedLetters)): ?>
                    <div style="margin-bottom: 20px;">
                        <a href="/SIEP/public/index.php?action=downloadAllApprovedLettersFromHub" class="btn btn-success"
                            style="font-size: 16px; padding: 12px 24px;">
                            📦 Descargar Todas en ZIP (<?php echo count($approvedLetters); ?> cartas)
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (empty($approvedLetters)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📭</div>
                        <h3>No hay cartas aprobadas</h3>
                        <p>Las cartas aprobadas aparecerán aquí listas para descargar.</p>
                    </div>
                <?php else: ?>
                    <table class="letters-table">
                        <thead>
                            <tr>
                                <th>Boleta</th>
                                <th>Nombre Completo</th>
                                <th>Carrera</th>
                                <th>No. Oficio</th>
                                <th>Fecha Aprobación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($approvedLetters as $letter): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($letter['boleta']); ?></strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($letter['first_name'] . ' ' . $letter['last_name_p'] . ' ' . $letter['last_name_m']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($letter['career']); ?></td>
                                    <td>
                                        <?php echo !empty($letter['letter_number']) ? htmlspecialchars($letter['letter_number']) : '<em>Generando...</em>'; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($letter['reviewed_at'])); ?></td>
                                    <td>
                                        <a href="/SIEP/public/index.php?action=downloadSingleApprovedLetter&id=<?php echo $letter['id']; ?>"
                                            class="btn btn-info btn-small">
                                            📄 Descargar PDF
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- TAB 3: Subir Cartas Firmadas -->
            <div id="upload"
                class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'upload') ? 'active' : ''; ?>">
                <h2>📤 Subir Cartas de Presentación Firmadas</h2>

                <div class="instructions-box">
                    <h3>📋 Instrucciones:</h3>
                    <ol>
                        <li>Descarga el ZIP con las cartas desde la pestaña "Aprobadas"</li>
                        <li>Imprime, firma y sella cada carta</li>
                        <li>Escanea cada carta firmada como PDF</li>
                        <li>Renombra con formato: <strong>BOLETA_CPSF.pdf</strong></li>
                        <li>Selecciona todos los archivos y súbelos aquí</li>
                        <li><strong>⚠️ Al subir, las cartas se marcarán automáticamente como "completadas"</strong></li>
                    </ol>
                </div>

                <div class="format-example">
                    <strong>⚠️ Formato de Nombres:</strong><br>
                    ✅ Correcto: <span style="color: green;">2022630554_CP.pdf</span><br>
                    ❌ Incorrecto: <span style="color: red;">carta_juan.pdf, 2022630554_CPSF.pdf, 2022630554.pdf</span>
                </div>

                <div class="upload-section">
                    <form action="/SIEP/public/index.php?action=uploadSignedLettersToHub" method="post"
                        enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="signed_letters">
                                <strong>Seleccionar Archivos PDF:</strong>
                            </label>
                            <input type="file" id="signed_letters" name="signed_letters[]" multiple required
                                accept=".pdf">
                        </div>

                        <button type="submit" class="btn btn-success" style="font-size: 16px; padding: 12px 24px;">
                            ✅ Subir y Procesar Archivos
                        </button>
                    </form>
                </div>
            </div>

            <!-- TAB 4: Cartas Completadas -->
            <div id="completed"
                class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'completed') ? 'active' : ''; ?>">
                <h2>📋 Historial de Cartas Completadas</h2>
                <p style="color: #666; margin-bottom: 20px;">
                    Cartas que ya fueron firmadas por UPIS y entregadas a los estudiantes. Puedes re-descargar cualquier
                    carta si es necesario.
                </p>

                <?php if (empty($completedLetters)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📋</div>
                        <h3>No hay cartas completadas</h3>
                        <p>Las cartas completadas aparecerán aquí para su consulta.</p>
                    </div>
                <?php else: ?>
                    <table class="letters-table">
                        <thead>
                            <tr>
                                <th>Boleta</th>
                                <th>Nombre Completo</th>
                                <th>Carrera</th>
                                <th>No. Oficio</th>
                                <th>Fecha Completada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completedLetters as $letter): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($letter['boleta']); ?></strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($letter['first_name'] . ' ' . $letter['last_name_p'] . ' ' . $letter['last_name_m']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($letter['career']); ?></td>
                                    <td><?php echo htmlspecialchars($letter['letter_number']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($letter['completed_at'])); ?></td>
                                    <td>
                                        <a href="/SIEP/public/index.php?action=downloadCompletedLetter&id=<?php echo $letter['id']; ?>"
                                            class="btn btn-info btn-small">
                                            📄 Re-Descargar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Modal para Ver Detalles de Solicitud -->
                    <div id="letterDetailsModal" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2>📄 Detalles de la Solicitud</h2>
                                <button class="close-button" onclick="closeModal()">&times;</button>
                            </div>
                            <div class="modal-body" id="modalBody">
                                <!-- Contenido cargado dinámicamente -->
                            </div>
                        </div>
                    </div>

                    <!-- Botón opcional para limpiar historial -->
                    <div
                        style="margin-top: 30px; text-align: center; padding: 20px; background: #fff3cd; border-radius: 8px;">
                        <p style="color: #856404; margin-bottom: 15px;">
                            ⚠️ <strong>Limpieza de Historial:</strong> Puedes eliminar permanentemente las cartas
                            completadas para liberar espacio.
                        </p>
                        <form action="/SIEP/public/index.php?action=clearCompletedLetters" method="post"
                            onsubmit="return confirm('¿Estás seguro de eliminar TODAS las cartas completadas del historial? Esta acción no se puede deshacer.');">
                            <button type="submit" class="btn btn-danger">
                                🗑️ Limpiar Historial (<?php echo count($completedLetters); ?> cartas)
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Detalles de Solicitud -->
    <div id="letterDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>📄 Detalles de la Solicitud</h2>
                <button class="close-button" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Contenido cargado dinámicamente -->
            </div>
        </div>
    </div>

    <script>
        // Función para cambiar de tab
        function openTab(evt, tabName) {
            var i, tabcontent, tabbuttons;

            // Ocultar todos los tabs
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }

            // Remover clase active de todos los botones
            tabbuttons = document.getElementsByClassName("tab-button");
            for (i = 0; i < tabbuttons.length; i++) {
                tabbuttons[i].classList.remove("active");
            }

            // Mostrar el tab seleccionado
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");

            // Actualizar URL sin recargar
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        }

        // Función para ver detalles de una solicitud
        function viewLetterDetails(letterId) {
            // Redireccionar a la página de detalles
            window.location.href = '/SIEP/public/index.php?action=viewLetterDetails&id=' + letterId;
        }

        // Función para cerrar modal
        function closeModal() {
            document.getElementById('letterDetailsModal').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function (event) {
            const modal = document.getElementById('letterDetailsModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>

</html>