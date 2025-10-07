<?php
/**
 * Inicialización de todas las librerías para reportes
 * Usar en cualquier archivo que necesite TCPDF o PHPSpreadsheet
 * 
 * Uso: require_once(__DIR__ . '/../vendor/init_libraries.php');
 */

// 1. PSR Simple Cache (dependencia de PHPSpreadsheet)
require_once(__DIR__ . '/simple-cache/src/CacheInterface.php');
require_once(__DIR__ . '/simple-cache/src/CacheException.php');
require_once(__DIR__ . '/simple-cache/src/InvalidArgumentException.php');

// 2. ZipStream (dependencia de PHPSpreadsheet para Excel)
spl_autoload_register(function ($class) {
    if (strpos($class, 'ZipStream\\') !== 0) {
        return;
    }
    $relativePath = str_replace('\\', '/', substr($class, 10));
    $file = __DIR__ . '/zipstream/src/' . $relativePath . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// 3. PHPSpreadsheet autoloader
require_once(__DIR__ . '/phpspreadsheet/autoload.php');

// 4. TCPDF
require_once(__DIR__ . '/tcpdf/tcpdf.php');