<?php
require_once(__DIR__ . '/src/Config/Database.php');

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "✅ Conexión exitosa a la base de datos!";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}