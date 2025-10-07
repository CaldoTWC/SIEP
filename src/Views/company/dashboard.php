<?php
// Archivo: src/Views/company/dashboard.php (Versión 2.0)

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['company']); 

// La variable $vacancies ahora está disponible gracias al controlador.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Empresa</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <!-- Reutilizamos los estilos de la tabla del dashboard de la UPIS -->
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #005a9c; color: white; } /* Un azul diferente para la empresa */
        tr:nth-child(even) { background-color: #f2f2f2; }
        .status { font-weight: bold; padding: 5px; border-radius: 4px; color: white; text-align: center; }
        .status.pending_review { background-color: #ffc107; color: #333; } /* Amarillo */
        .status.approved { background-color: #28a745; } /* Verde */
        .status.rejected { background-color: #dc3545; } /* Rojo */
    </style>
</head>
<body>
    <div class="container">
        <h1>Panel de Administración de la Empresa</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>
        
        <div class="menu-buttons">
            <a href="/SIEP/public/index.php?action=showPostVacancyForm" class="btn">Publicar Nueva Vacante</a>
            <a href="/SIEP/public/index.php?action=showAcceptanceLetterForm" class="btn" style="background-color: #007bff;">Generar Carta de Aceptación</a>
            <a href="/SIEP/public/index.php?action=showValidationLetterForm" class="btn" style="background-color: #28a745;">Generar Constancia de Validación</a>
            <a href="#" class="btn" style="background-color: #005a9c;">Ver Mis Vacantes Publicadas</a>
        </div>
        
        <hr style="margin: 30px 0;">
        
        <h2>Mis Vacantes Publicadas</h2>
        <?php if (empty($vacancies)): ?>
            <p>Aún no has publicado ninguna vacante. ¡Crea la primera!</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Título del Puesto</th>
                        <th>Modalidad</th>
                        <th>Estado</th>
                        <th>Acción</th> <!-- <-- NUEVA CABECERA -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vacancies as $vacancy): ?>
                        <tr>
                            <td>
                                <a href="/SIEP/public/index.php?action=showVacancyDetails&id=<?php echo $vacancy['id']; ?>" title="Ver detalles">
                                    <?php echo htmlspecialchars($vacancy['title']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($vacancy['modality']); ?></td>
                            <td>
                                <span class="status <?php echo htmlspecialchars($vacancy['status']); ?>">
                                    <?php echo str_replace('_', ' ', ucfirst($vacancy['status'])); ?>
                                </span>
                            </td>
                            <!-- NUEVA CELDA DE ACCIONES -->
                            <td class="actions">
                                <!-- El enlace ahora apunta a la acción 'deleteVacancy' -->
                                <a href="/SIEP/public/index.php?action=deleteVacancy&id=<?php echo $vacancy['id']; ?>" class="delete" onclick="return confirm('¿Estás seguro de que deseas eliminar permanentemente esta vacante? Ya no será visible para los estudiantes.');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="/SIEP/public/index.php?action=logout" class="btn" style="background-color: #5a6a7e; margin-top: 30px;">Cerrar Sesión</a>
        <a href="/SIEP/public/index.php?action=showChangePasswordForm" class="btn btn-sm btn-outline-primary">🔐 Cambiar Contraseña</a>
    </div>
</body>
</html>