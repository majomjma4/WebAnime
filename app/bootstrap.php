<?php
/**
 * bootstrap.php - Legacy Edition
 * Compatible con PHP 5.6, 7.0, 7.4 y 8.x
 */

// 1. Mostrar errores si index.php lo activó
if (ini_get('display_errors')) {
    error_reporting(E_ALL);
}

// 2. Autoloader Simple (Compatible con carpetas Case-Sensitive en Linux)
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR;
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// 3. Cargar .env manualmente (Sin dependencias externas)
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        putenv(trim($name) . "=" . trim($value));
    }
}

// --- Funciones Globales (Legacy Syntax) ---

function app_env($key, $default = null) {
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];
    $val = getenv($key);
    return ($val !== false) ? $val : $default;
}

function app_start_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function app_routes() {
    static $routes = null;
    if ($routes === null) {
        $routesFile = __DIR__ . '/routes.php';
        $routes = file_exists($routesFile) ? require($routesFile) : array();
    }
    return $routes;
}

function route_path($name, $params = array()) {
    $routes = app_routes();
    if (!isset($routes[$name])) return '#';
    $path = $routes[$name]['path'];
    if (!empty($params)) {
        $path .= '?' . http_build_query($params);
    }
    return app_base_url($path);
}

function app_base_url($path = '') {
    $protocol = app_is_https() ? 'https://' : 'http://';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $baseUrl = rtrim(dirname($scriptName), '/\\');
    return $protocol . $host . $baseUrl . '/' . ltrim($path, '/');
}

function asset_path($path) {
    return app_base_url($path);
}

function app_is_https() {
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) return true;
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') return true;
    return false;
}

function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function detail_path($query = '', $title = '') {
    $ref = $query;
    if (!$ref) {
        $ref = app_slugify($title);
    }
    return route_path('detail', array('detail_ref' => $ref));
}

function app_slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text);
}

function app_is_valid_detail_ref($ref) {
    if (empty($ref)) return false;
    return preg_match('/^[a-z0-9\-]+$/i', $ref);
}

function app_detail_ref_from_input($id, $title) {
    if ($id) return $id;
    if ($title) return app_slugify($title);
    return '';
}
