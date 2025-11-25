<?php
// Archivo: src/Views/student/my_documents.php

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['student']);

// La variable $myDocuments viene del StudentController.
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Documentos Finales</title>
    <link rel="stylesheet" href="/SIEP/public/css/student.css">
    <!-- Reutilizamos los estilos de tabla del dashboard de UPIS -->

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
            <h1>Mis Documentos Finales</h1>
            <p>Aquí encontrarás los documentos firmados y sellados por la UPIS, listos para su descarga.</p>
        </div>


        <a href="/SIEP/public/index.php?action=studentDashboard" class="logout-btn">←Volver al Panel</a><br><br>

        <?php if (empty($myDocuments)): ?>
            <p>Aún no tienes documentos finales disponibles.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="documents">
                    <thead>
                        <tr>
                            <th>Nombre del Documento</th>
                            <th>Tipo de Documento</th>
                            <th>Fecha de Carga</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($myDocuments as $doc): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($doc['original_filename']); ?></td>
                                <td><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($doc['document_type']))); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($doc['uploaded_at'])); ?></td>
                                <td>
                                    <!-- ✅ NUEVO: Descarga segura a través del controlador -->
                                    <a href="/SIEP/public/index.php?action=downloadDocument&id=<?php echo $doc['id']; ?>"
                                        class="btn">
                                        📥 Descargar
                                    </a>

                                    <!-- Opcional: Ver en navegador -->
                                    <a href="/SIEP/public/index.php?action=viewDocument&id=<?php echo $doc['id']; ?>"
                                        class="btn" style="background-color: #007bff;" target="_blank">
                                        👁️ Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>