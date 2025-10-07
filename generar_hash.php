<?php
// Archivo temporal: /ProyectoTT/generar_hash.php

$passwordParaHashear = 'upis123';
$nuevoHash = password_hash($passwordParaHashear, PASSWORD_BCRYPT);

echo "La contraseña es: " . $passwordParaHashear . "<br>";
echo "El nuevo HASH es: <br>";
echo "<strong>" . $nuevoHash . "</strong>";
?>