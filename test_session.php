<?php
session_start();

echo "<h2>🔍 Información de Sesión Actual</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<hr>";

if (isset($_SESSION['user_id'])) {
    echo "<p>✅ Usuario autenticado: <strong>" . $_SESSION['username'] . "</strong></p>";
    echo "<p>Role: <strong>" . $_SESSION['role'] . "</strong></p>";
    
    if ($_SESSION['role'] === 'upis') {
        echo "<p style='color: green;'>✅ Tienes acceso a reportes</p>";
        echo "<a href='/SIEP/src/Controllers/ReportController.php?action=dashboard'>Ir a Dashboard de Reportes</a>";
    } else {
        echo "<p style='color: red;'>❌ Tu rol es '<strong>" . $_SESSION['role'] . "</strong>', necesitas ser 'upis'</p>";
    }
} else {
    echo "<p style='color: red;'>❌ No estás autenticado</p>";
}
?>