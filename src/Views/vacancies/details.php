<?php
// Archivo: src/Views/vacancies/details.php

require_once(__DIR__ . '/../../Lib/Session.php');
$session = new Session();
// Protegemos: solo estudiantes y empresas pueden ver esto
$session->guard(['student', 'company']); 

// $vacancy viene del controlador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de la Vacante</title>
    <link rel="stylesheet" href="/ProyectoTT/public/css/styles.css">
    <!-- Reutilizamos los estilos de las tarjetas de vacante -->
    <style>
        .vacancy-card { background-color: #fff; border: 1px solid #ddd; border-left: 5px solid #005a9c; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
        .vacancy-card h3 { margin-top: 0; color: #005a9c; }
        .vacancy-details span { display: inline-block; margin-right: 20px; color: #555; }
        .vacancy-description { margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detalle de la Vacante</h1>
        
        <?php if (empty($vacancy)): ?>
            <p>La vacante que buscas no existe o no está disponible.</p>
        <?php else: ?>
            <div class="vacancy-card">
                <h3><?php echo htmlspecialchars($vacancy['title']); ?></h3>
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
        <?php endif; ?>
        
        <!-- El enlace de "Volver" es dinámico según el rol del usuario -->
        <?php if ($_SESSION['user_role'] == 'student'): ?>
            <a href="/ProyectoTT/public/index.php?action=listVacancies">← Volver a la lista de vacantes</a>
        <?php elseif ($_SESSION['user_role'] == 'company'): ?>
            <a href="/ProyectoTT/public/index.php?action=companyDashboard">← Volver al panel de la empresa</a>
        <?php endif; ?>
    </div>
</body>
</html>