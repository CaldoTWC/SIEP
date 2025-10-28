<?php
/**
 * Configuración inicial del sistema
 * 
 * @package SIEP\Config
 */

// ✅ Configurar zona horaria para toda la aplicación
date_default_timezone_set('America/Mexico_City');

// ✅ Configurar locale para fechas en español
setlocale(LC_TIME, 'es_MX.UTF-8', 'Spanish_Mexico', 'Spanish');

// ✅ Configurar errores según el entorno
if (getenv('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// ✅ Configurar charset
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');