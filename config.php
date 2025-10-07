<?php
// Archivo: /config.php

// --- CONFIGURACIÓN DE LA BASE DE DATOS ---
// define() crea una constante global.
define('DB_HOST', 'localhost'); // El servidor donde está la base de datos.
define('DB_USER', 'root');      // El usuario de la base de datos (por defecto en XAMPP es 'root').
define('DB_PASS', '');          // La contraseña (por defecto en XAMPP está vacía).
define('DB_NAME', 'siep'); // El nombre de la base de datos que creamos.

// --- CONFIGURACIÓN GENERAL DEL SITIO ---
define('SITE_URL', 'http://localhost/SIEP/public'); // La URL base del sitio.