<?php
// Archivo: src/Views/student/vacancies.php (Versión Actualizada con Enlaces)

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
$session->guard(['student']); 

// La variable $approvedVacancies viene del StudentController.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacantes Disponibles</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        .vacancy-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-left: 5px solid #005a9c; /* Azul empresa */
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .vacancy-card h3 {
            margin-top: 0;
            color: #005a9c;
        }
        /* --- ESTILOS AÑADIDOS PARA EL ENLACE --- */
        .vacancy-card h3 a {
            color: inherit; /* Hereda el color azul del h3 */
            text-decoration: none;
        }
        .vacancy-card h3 a:hover {
            text-decoration: underline;
        }
        /* --- FIN DE ESTILOS AÑADIDOS --- */
        .vacancy-details span {
            display: inline-block;
            margin-right: 20px;
            color: #555;
        }
        .vacancy-description {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vacantes de Estancia Profesional</h1>
        <p>Explora las oportunidades disponibles y aprobadas por la UPIS.</p>
        
        <a href="/SIEP/public/index.php?action=studentDashboard" style="display: inline-block; margin-bottom: 20px;">← Volver al Panel</a>

        <?php if (empty($approvedVacancies)): ?>
            <div class="vacancy-card">
                <p>No hay vacantes disponibles en este momento. ¡Vuelve a consultar más tarde!</p>
            </div>
        <?php else: ?>
            <?php foreach ($approvedVacancies as $vacancy): ?>
                <div class="vacancy-card">
                    <!-- 
                        LA MODIFICACIÓN PRINCIPAL ESTÁ AQUÍ.
                        El título ahora es un enlace a la página de detalles.
                    -->
                    <h3>
                        <a href="/SIEP/public/index.php?action=showVacancyDetails&id=<?php echo $vacancy['id']; ?>" title="Ver detalles completos">
                            <?php echo htmlspecialchars($vacancy['title']); ?>
                        </a>
                    </h3>
                    <div class="vacancy-details">
                        <span><strong>Empresa:</strong> <?php echo htmlspecialchars($vacancy['company_name']); ?></span>
                        <span><strong>Modalidad:</strong> <?php echo htmlspecialchars($vacancy['modality']); ?></span>
                        <span><strong>Publicado:</strong> <?php echo date('d/m/Y', strtotime($vacancy['posted_at'])); ?></span>
                    </div>
                    <div class="vacancy-description">
                        <h4>Descripción del Perfil:</h4>
                        <p><?php echo nl2br(htmlspecialchars($vacancy['description'])); ?></p>
                        <h4>Actividades a Realizar:</h4>
                        <p><?php echo nl2br(htmlspecialchars($vacancy['activities'])); ?></p>
                    </div>
                    <?php if (!empty($vacancy['website'])): ?>
                        <a href="<?php echo htmlspecialchars($vacancy['website']); ?>" target="_blank" class="btn" style="max-width: 250px;">Visitar Sitio de la Empresa</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
