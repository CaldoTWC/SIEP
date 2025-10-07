<?php
/**
 * Autoloader para PHPSpreadsheet + PSR Simple Cache
 * Estructura: vendor/simple-cache/ (sin carpeta psr/)
 */

// Autoloader para PSR Simple Cache (PRIMERO)
spl_autoload_register(function ($class) {
    // Solo procesar clases del namespace Psr\SimpleCache
    if (strpos($class, 'Psr\\SimpleCache\\') !== 0) {
        return;
    }
    
    // Convertir Psr\SimpleCache\CacheInterface a simple-cache/src/CacheInterface.php
    // Quitamos "Psr\" (4 caracteres)
    $relativePath = substr($class, 4); // SimpleCache\CacheInterface
    
    // Reemplazar \ por /
    $relativePath = str_replace('\\', '/', $relativePath);
    
    // Construir ruta completa (SIN carpeta psr/)
    $file = __DIR__ . '/../simple-cache/src/' . $relativePath . '.php';
    
    // Cargar si existe
    if (file_exists($file)) {
        require_once $file;
        return;
    }
});

// Autoloader para PHPSpreadsheet (DESPUÉS)
spl_autoload_register(function ($class) {
    // Solo procesar clases del namespace PhpOffice\PhpSpreadsheet
    if (strpos($class, 'PhpOffice\\PhpSpreadsheet\\') !== 0) {
        return;
    }
    
    // Convertir PhpOffice\PhpSpreadsheet\Spreadsheet a PhpOffice/PhpSpreadsheet/Spreadsheet.php
    // Quitamos "PhpOffice\" (10 caracteres)
    $relativePath = substr($class, 10);
    
    // Reemplazar \ por /
    $relativePath = str_replace('\\', '/', $relativePath);
    
    // Construir ruta completa
    $file = __DIR__ . '/src/' . $relativePath . '.php';
    
    // Cargar si existe
    if (file_exists($file)) {
        require_once $file;
        return;
    }
});