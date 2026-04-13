<?php
/**
 * bootstrap.php - Legacy Edition
 * Compatible con PHP 5.6, 7.0, 7.4 y 8.x
 */

// 1. Configuración de Codificación Global (UTF-8)
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

// 2. Mostrar errores si index.php lo activó
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
    
    $routeConfig = $routes[$name];
    $path = isset($routeConfig['path']) ? $routeConfig['path'] : '';
    $patterns = isset($routeConfig['patterns']) ? $routeConfig['patterns'] : array();
    
    // Intentar usar un patrón si los parámetros coinciden
    if (!empty($patterns)) {
        foreach ($patterns as $pattern) {
            $tempParams = $params;
            $placeholders = array();
            preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $pattern, $matches);
            
            $canUsePattern = true;
            if (!empty($matches[1])) {
                foreach ($matches[1] as $placeholder) {
                    if (!isset($tempParams[$placeholder])) {
                        $canUsePattern = false;
                        break;
                    }
                }
            }

            if ($canUsePattern && !empty($matches[1])) {
                $filledPath = $pattern;
                foreach ($matches[1] as $placeholder) {
                    $filledPath = str_replace('{' . $placeholder . '}', (string)$tempParams[$placeholder], $filledPath);
                    unset($tempParams[$placeholder]);
                }
                $path = $filledPath;
                $params = $tempParams; // Los parámetros usados se quitan de la query string
                break;
            }
        }
    }

    // Normalización de ruta 'index' a raíz
    if ($path === 'index') {
        $path = '';
    }

    if (!empty($params)) {
        $path .= (strpos($path, '?') === false ? '?' : '&') . http_build_query($params);
    }
    
    return app_base_url($path);
}

function app_base_url($path = '') {
    $protocol = app_is_https() ? 'https://' : 'http://';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
    
    // Detectamos la carpeta base eliminando el script y el segmento /public si existe
    $baseUrl = rtrim(dirname($scriptName), '/');
    $baseUrl = preg_replace('/\/public$/', '', $baseUrl);
    
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
    // Priorizamos el slug del título para URLs más limpias (SEO)
    $ref = app_slugify($title);
    if (!$ref) {
        $ref = $query;
    }
    // Si aún no hay nada (muy raro), fallback a 'anime'
    if (!$ref) {
        $ref = 'anime';
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
    
    // Si empieza con números pero tiene letras después sin un separador (como 59978abc), es sospechoso.
    // Un ID de MyAnimeList debe ser puramente numérico y actualmente no supera los 7 dígitos.
    if (preg_match('/^[0-9]+[a-zA-Z]+/', $ref) || (ctype_digit($ref) && strlen($ref) > 7)) {
        return false;
    }

    // Permitimos: puramente números OR formato slug válido (letras, números y guiones)
    return preg_match('/^[a-z0-9\-]+$/i', $ref);
}

function app_detail_ref_from_input($id, $title) {
    if ($id) return $id;
    if ($title) return app_slugify($title);
    return '';
}

// --- Helpers para API y Seguridad (Legacy v1) ---

function app_require_method($method) {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(array('error' => 'Method not allowed'));
        exit;
    }
}

function app_get_json_input() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

function app_verify_csrf() {
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    
    // Si no hay referer (visita directa), permitimos. 
    // Si es AJAX y el host está en el referer de alguna forma, permitimos.
    if (!empty($referer) && !empty($host)) {
        $cleanHost = strtolower(preg_replace('/^www\./', '', $host));
        $cleanRef = strtolower($referer);
        if (strpos($cleanRef, $cleanHost) === false) {
             header("HTTP/1.1 403 Forbidden");
             echo json_encode(array('error' => 'CSRF fail', 'h' => $cleanHost, 'r' => $cleanRef));
             exit;
        }
    }
}
