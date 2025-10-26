<?php
/**
 * Inicializador de Librerías Externas
 * 
 * Carga todas las dependencias del directorio vendor/
 * Proyecto: SIEP - Sistema Integral de Estancias Profesionales
 * 
 * @package SIEP\Vendor
 * @version 2.0.0
 */

// ============================================
// PHPMAILER - Envío de correos electrónicos
// ============================================
if (file_exists(__DIR__ . '/phpmailer/src/PHPMailer.php')) {
    require_once __DIR__ . '/phpmailer/src/Exception.php';
    require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/phpmailer/src/SMTP.php';
    require_once __DIR__ . '/phpmailer/src/POP3.php';
    require_once __DIR__ . '/phpmailer/src/OAuth.php';
}

// ============================================
// TCPDF - Generación de PDFs avanzada
// ============================================
if (file_exists(__DIR__ . '/tecnickcom/tcpdf/tcpdf.php')) {
    require_once __DIR__ . '/tecnickcom/tcpdf/tcpdf.php';
}

// ============================================
// FPDF - Generación de PDFs simple
// ============================================
if (file_exists(__DIR__ . '/setasign/fpdf/fpdf.php')) {
    require_once __DIR__ . '/setasign/fpdf/fpdf.php';
}

// ============================================
// FPDI - Importación y manipulación de PDFs
// ============================================
if (file_exists(__DIR__ . '/setasign/fpdi/src/autoload.php')) {
    require_once __DIR__ . '/setasign/fpdi/src/autoload.php';
} elseif (file_exists(__DIR__ . '/setasign/fpdi/fpdi.php')) {
    require_once __DIR__ . '/setasign/fpdi/fpdi.php';
}

// ============================================
// FPDI - Verificar estructura alternativa
// ============================================
if (file_exists(__DIR__ . '/fpdi/src/autoload.php')) {
    require_once __DIR__ . '/fpdi/src/autoload.php';
} elseif (file_exists(__DIR__ . '/fpdi/fpdi.php')) {
    require_once __DIR__ . '/fpdi/fpdi.php';
}

// ============================================
// FPDF - Verificar estructura alternativa
// ============================================
if (file_exists(__DIR__ . '/fpdf/fpdf.php')) {
    require_once __DIR__ . '/fpdf/fpdf.php';
}

// ============================================
// PSR-16 SIMPLE CACHE - Sistema de caché
// ============================================
if (file_exists(__DIR__ . '/psr/simple-cache/src/CacheInterface.php')) {
    require_once __DIR__ . '/psr/simple-cache/src/CacheInterface.php';
    
    if (file_exists(__DIR__ . '/psr/simple-cache/src/CacheException.php')) {
        require_once __DIR__ . '/psr/simple-cache/src/CacheException.php';
    }
    
    if (file_exists(__DIR__ . '/psr/simple-cache/src/InvalidArgumentException.php')) {
        require_once __DIR__ . '/psr/simple-cache/src/InvalidArgumentException.php';
    }
}

// ============================================
// SIMPLE CACHE - Verificar estructura alternativa
// ============================================
if (file_exists(__DIR__ . '/simple-cache/src/CacheInterface.php')) {
    require_once __DIR__ . '/simple-cache/src/CacheInterface.php';
    
    if (file_exists(__DIR__ . '/simple-cache/src/CacheException.php')) {
        require_once __DIR__ . '/simple-cache/src/CacheException.php';
    }
    
    if (file_exists(__DIR__ . '/simple-cache/src/InvalidArgumentException.php')) {
        require_once __DIR__ . '/simple-cache/src/InvalidArgumentException.php';
    }
}

// ============================================
// PHPSPREADSHEET - Manejo de Excel
// ============================================
if (file_exists(__DIR__ . '/phpoffice/phpspreadsheet/src/Bootstrap.php')) {
    require_once __DIR__ . '/phpoffice/phpspreadsheet/src/Bootstrap.php';
} elseif (file_exists(__DIR__ . '/phpspreadsheet/src/Bootstrap.php')) {
    require_once __DIR__ . '/phpspreadsheet/src/Bootstrap.php';
}

// ============================================
// TCPDF - Verificar estructura alternativa
// ============================================
if (file_exists(__DIR__ . '/tcpdf/tcpdf.php')) {
    require_once __DIR__ . '/tcpdf/tcpdf.php';
}

// ============================================
// ZIPSTREAM - Compresión de archivos
// ============================================
if (file_exists(__DIR__ . '/maennchen/zipstream-php/src/ZipStream.php')) {
    spl_autoload_register(function ($class) {
        if (strpos($class, 'ZipStream\\') === 0) {
            $file = __DIR__ . '/maennchen/zipstream-php/src/' . str_replace('\\', '/', substr($class, 10)) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    });
} elseif (file_exists(__DIR__ . '/zipstream/src/ZipStream.php')) {
    spl_autoload_register(function ($class) {
        if (strpos($class, 'ZipStream\\') === 0) {
            $file = __DIR__ . '/zipstream/src/' . str_replace('\\', '/', substr($class, 10)) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    });
}

// ============================================
// AUTOLOADER GENÉRICO PSR-4
// ============================================
spl_autoload_register(function ($class) {
    // Mapeo de namespaces a directorios
    $prefix_to_path = [
        'PHPMailer\\PHPMailer\\' => __DIR__ . '/phpmailer/src/',
        'setasign\\Fpdi\\' => __DIR__ . '/setasign/fpdi/src/',
        'setasign\\Fpdi\\' => __DIR__ . '/fpdi/src/',
        'PhpOffice\\PhpSpreadsheet\\' => __DIR__ . '/phpoffice/phpspreadsheet/src/PhpSpreadsheet/',
        'PhpOffice\\PhpSpreadsheet\\' => __DIR__ . '/phpspreadsheet/src/PhpSpreadsheet/',
        'Psr\\SimpleCache\\' => __DIR__ . '/psr/simple-cache/src/',
        'Psr\\SimpleCache\\' => __DIR__ . '/simple-cache/src/',
        'ZipStream\\' => __DIR__ . '/maennchen/zipstream-php/src/',
        'ZipStream\\' => __DIR__ . '/zipstream/src/',
    ];
    
    foreach ($prefix_to_path as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }
    }
    
    return false;
});