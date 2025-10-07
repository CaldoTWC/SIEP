<?php
// Archivo: src/Views/auth/register_company_success.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Registro Enviada</title>
    <link rel="stylesheet" href="/SIEP/public/css/styles.css">
    <style>
        /* Estilos similares a la otra página de éxito, pero con un ícono y color diferente */
        .success-container {
            text-align: center;
            padding: 40px;
        }
        .success-icon {
            font-size: 5rem;
            color: var(--color-ipn-azul); /* Azul para 'información' */
        }
        .success-container h1 {
            color: var(--color-ipn-azul);
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .success-container p {
            font-size: 1.1rem;
            color: var(--color-texto-secundario);
            max-width: 550px;
            margin-left: auto;
            margin-right: auto;
        }
        .home-button {
            display: inline-block;
            width: auto;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container success-container">
        <!-- Un ícono de 'reloj' o 'enviado' sería ideal aquí -->
        <div class="success-icon">&#9203;</div> <!-- Símbolo de reloj de arena -->
        
        <h1>Solicitud de Registro Enviada</h1>
        
        <p>
            ¡Gracias por registrarse! Su solicitud ha sido enviada exitosamente y será revisada por el personal de la UPIS.
        </p>
        <p>
            Recibirá una notificación en su correo electrónico una vez que su cuenta sea activada.
        </p>
        
        <a href="/SIEP/public/index.php?action=home" class="btn home-button">Volver a la Página Principal</a>
    </div>
</body>
</html>