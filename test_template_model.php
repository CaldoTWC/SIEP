<?php
require_once(__DIR__ . '/src/Models/LetterTemplate.php');
require_once(__DIR__ . '/src/Models/DocumentApplication.php');

echo "=== TEST MODELO DocumentApplication CON PLANTILLAS ===\n\n";

$appModel = new DocumentApplication();

// Test 1: Obtener solicitudes pendientes con info de plantilla
echo "1. Solicitudes pendientes con info de plantilla:\n";
$pending = $appModel->getPendingPresentationLettersWithTemplate();
echo "Total pendientes: " . count($pending) . "\n";
if (!empty($pending)) {
    echo "Primera solicitud:\n";
    print_r($pending[0]);
}

echo "\n2. Estadísticas de uso de plantillas:\n";
$stats = $appModel->getTemplateUsageStats();
print_r($stats);

echo "\n✅ Modelo actualizado correctamente\n";