<?php
// Archivo temporal: /SIEP/fix_password.php

// Incluimos nuestra clase de base de datos
require_once(__DIR__ . '/src/Config/Database.php');

// --- DATOS A CONFIGURAR ---
$email_to_fix = 'upis.prueba@escom.ipn.mx';
$new_password = 'upis'; // La contraseña que SÍ usaremos para iniciar sesión
// --------------------------

echo "<h1>Actualizador de Contraseña</h1>";

try {
    // Obtenemos la conexión a la BD
    $conn = Database::getInstance()->getConnection();

    // 1. Generamos el hash de PHP para la nueva contraseña
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    echo "<p>Contraseña a establecer: " . htmlspecialchars($new_password) . "</p>";
    echo "<p>Hash generado: " . htmlspecialchars($hashed_password) . "</p>";

    // 2. Preparamos la consulta para actualizar la contraseña en la BD
    $query = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $hashed_password, $email_to_fix);

    // 3. Ejecutamos la actualización
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<p style='color: green; font-weight: bold;'>¡ÉXITO! La contraseña para el usuario " . htmlspecialchars($email_to_fix) . " ha sido actualizada.</p>";
        } else {
            echo "<p style='color: orange; font-weight: bold;'>AVISO: No se encontró ningún usuario con el email " . htmlspecialchars($email_to_fix) . ".</p>";
        }
    } else {
        echo "<p style='color: red; font-weight: bold;'>ERROR: No se pudo ejecutar la actualización. " . $stmt->error . "</p>";
    }

    $conn->close();

} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>ERROR DE CONEXIÓN: " . $e->getMessage() . "</p>";
}