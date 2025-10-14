<?php
/**
 * Script Simple de Migración - SIEP
 * No requiere FileHelper.php
 */

echo "\n========================================\n";
echo "  MIGRACIÓN DE ARCHIVOS SIEP\n";
echo "========================================\n\n";

// Configuración de BD
$dbHost = 'localhost';
$dbName = 'siep';
$dbUser = 'root';
$dbPass = '';

echo "Configuración:\n";
echo "  Host: $dbHost\n";
echo "  Base de datos: $dbName\n";
echo "  Usuario: $dbUser\n\n";

$continuar = readline("¿Es correcta la configuración? (s/n): ");
if (strtolower($continuar) !== 's') {
    die("Operación cancelada.\n");
}

// Conectar a BD
try {
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Conexión exitosa a la base de datos\n\n";
} catch(PDOException $e) {
    die("ERROR: No se pudo conectar a la BD: " . $e->getMessage() . "\n");
}

// Rutas
$oldBasePath = __DIR__ . '/public/uploads';
$newBasePath = __DIR__ . '/storage/students';

// Crear storage
if (!is_dir($newBasePath)) {
    mkdir($newBasePath, 0755, true);
    echo "✓ Carpeta storage/students creada\n\n";
}

// Función para limpiar nombres
function cleanString($str) {
    $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
    $str = preg_replace('/[^a-zA-Z]/', '', $str);
    return ucfirst(strtolower(substr($str, 0, 20)));
}

// Obtener estudiantes
echo "Obteniendo estudiantes...\n";
$sql = "SELECT u.id, sp.boleta, u.first_name, u.last_name_p 
        FROM users u 
        JOIN student_profiles sp ON u.id = sp.user_id 
        WHERE u.role = 'student'";
$stmt = $conn->query($sql);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$studentMap = [];
foreach ($students as $student) {
    $studentMap[$student['boleta']] = [
        'id' => $student['id'],
        'firstName' => $student['first_name'],
        'lastNameP' => $student['last_name_p']
    ];
}

echo "Encontrados " . count($students) . " estudiantes\n\n";

// Estadísticas
$stats = [
    'transcripts' => ['moved' => 0, 'errors' => 0],
    'signed' => ['moved' => 0, 'errors' => 0],
    'accreditation' => ['moved' => 0, 'errors' => 0]
];

// Función para migrar archivos
function migrateFiles($sourcePath, $type, &$studentMap, $newBasePath, &$stats) {
    if (!is_dir($sourcePath)) {
        echo "No se encontró carpeta: $sourcePath\n";
        return;
    }
    
    $files = glob($sourcePath . '/*.pdf');
    echo "Archivos encontrados: " . count($files) . "\n";
    
    foreach ($files as $file) {
        $filename = basename($file);
        
        if (preg_match('/^(\d{10})_/', $filename, $matches)) {
            $boleta = $matches[1];
            
            if (isset($studentMap[$boleta])) {
                $student = $studentMap[$boleta];
                
                // Crear carpeta del estudiante
                $cleanFirst = cleanString($student['firstName']);
                $cleanLast = cleanString($student['lastNameP']);
                $folderName = "{$boleta}_{$cleanFirst}{$cleanLast}";
                
                $studentFolder = "$newBasePath/$folderName";
                $destFolder = "$studentFolder/$type";
                
                if (!is_dir($destFolder)) {
                    mkdir($destFolder, 0755, true);
                }
                
                $destFile = "$destFolder/$filename";
                
                if (copy($file, $destFile)) {
                    echo "  ✓ $filename -> $boleta\n";
                    $stats[$type]['moved']++;
                } else {
                    echo "  ✗ ERROR: $filename\n";
                    $stats[$type]['errors']++;
                }
            } else {
                echo "  ! ADVERTENCIA: Estudiante no encontrado para boleta $boleta\n";
                $stats[$type]['errors']++;
            }
        } else {
            echo "  ! ADVERTENCIA: No se pudo extraer boleta de $filename\n";
            $stats[$type]['errors']++;
        }
    }
}

// MIGRAR
echo "========================================\n";
echo "  MIGRANDO BOLETAS GLOBALES\n";
echo "========================================\n";
migrateFiles("$oldBasePath/transcripts", 'transcripts', $studentMap, $newBasePath, $stats);

echo "\n========================================\n";
echo "  MIGRANDO DOCUMENTOS FIRMADOS\n";
echo "========================================\n";
migrateFiles("$oldBasePath/signed_documents", 'signed', $studentMap, $newBasePath, $stats);

echo "\n========================================\n";
echo "  MIGRANDO DOCUMENTOS DE ACREDITACIÓN\n";
echo "========================================\n";
migrateFiles("$oldBasePath/accreditation_docs", 'accreditation', $studentMap, $newBasePath, $stats);

// RESUMEN
echo "\n========================================\n";
echo "  RESUMEN\n";
echo "========================================\n\n";

$totalMoved = $stats['transcripts']['moved'] + $stats['signed']['moved'] + $stats['accreditation']['moved'];
$totalErrors = $stats['transcripts']['errors'] + $stats['signed']['errors'] + $stats['accreditation']['errors'];

echo "Boletas Globales:      {$stats['transcripts']['moved']} migrados, {$stats['transcripts']['errors']} errores\n";
echo "Documentos Firmados:   {$stats['signed']['moved']} migrados, {$stats['signed']['errors']} errores\n";
echo "Acreditación:          {$stats['accreditation']['moved']} migrados, {$stats['accreditation']['errors']} errores\n\n";

echo "TOTAL: $totalMoved archivos migrados\n";
echo "ERRORES: $totalErrors\n\n";

if ($totalErrors === 0) {
    echo "¡MIGRACIÓN COMPLETADA EXITOSAMENTE! ✓\n\n";
} else {
    echo "Migración completada con advertencias.\n\n";
}

echo "Los archivos originales NO han sido eliminados.\n";
echo "Verifica storage/students/ antes de borrar public/uploads/\n\n";