<?php
/**
 * Simple .env loader (no dependencies)
 * Usage: require_once(__DIR__ . '/env.php'); load_dotenv(__DIR__ . '/../../.env');
 *
 * Loads KEY=VALUE lines into getenv(), $_ENV and $_SERVER.
 */
function load_dotenv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        // remove optional surrounding quotes
        if ((substr($value,0,1) === '"' && substr($value,-1) === '"') ||
            (substr($value,0,1) === "'" && substr($value,-1) === "'")) {
            $value = substr($value,1,-1);
        }
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}