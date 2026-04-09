<?php
namespace Core;

use Throwable;

class App
{
    private static string $baseDir = '';

    public static function run(): void
    {
        try {
            self::init();
            self::dispatch();
        } catch (Throwable $e) {
            self::handleException($e);
        }
    }

    private static function init(): void
    {
        // Load configuration
        Config::load();

        // Set error reporting based on debug mode
        $debug = Config::get('APP_DEBUG', 'false') === 'true';
        if ($debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }
        
        // Start session if needed
        if (session_status() === PHP_SESSION_NONE) {
            session_name('NekoraSession_V1');
            session_start();
        }

        // Detect Base Directory with a fail-safe fallback to .env/Config
        $base = Config::get('APP_BASE_URL');
        if ($base === null) {
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $base = str_replace('\\', '/', dirname($scriptName));
            if ($base === '/' || $base === '\\') $base = '';
            if (str_ends_with($base, '/public')) {
                $base = substr($base, 0, -7);
            }
        }
        
        self::$baseDir = rtrim((string)$base, '/');
    }

    public static function getBaseDir(): string
    {
        return self::$baseDir;
    }

    public static function url(string $path = ''): string
    {
        $fullPath = self::$baseDir . '/' . ltrim($path, '/');
        return ($fullPath === '/') ? '/' : rtrim($fullPath, '/');
    }

    private static function dispatch(): void
    {
        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = (string) parse_url($requestUri, PHP_URL_PATH);

        // Strip Base Directory from path
        if (self::$baseDir !== '' && str_starts_with($path, self::$baseDir)) {
            $path = substr($path, strlen(self::$baseDir));
        }

        $route = trim($path, '/');
        
        // Remove 'public/' or 'index.php' if they leaked
        if (str_starts_with($route, 'public/')) $route = substr($route, 7);
        if ($route === 'index.php' || $route === 'public/index.php') $route = '';

        $router = new Router(require dirname(__DIR__) . '/routes.php');
        $resolved = $router->match($route);
        
        if (!$resolved) {
            self::abort(404);
            return;
        }

        $controllerClass = $resolved['controller'];
        $action = $resolved['action'];

        if (!class_exists($controllerClass)) {
            throw new \Exception("Controlador [$controllerClass] no encontrado.");
        }

        $controller = new $controllerClass();
        $controller->authorize($resolved['guard'] ?? null);

        if (!method_exists($controller, $action)) {
             throw new \Exception("Acción [$action] no encontrada en el controlador [$controllerClass].");
        }

        $controller->{$action}();
    }

    public static function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        
        $viewPath = dirname(__DIR__) . "/Views/pages/{$code}.php";
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Error {$code}: " . ($message ?: "El recurso solicitado no existe.");
        }
        exit;
    }

    public static function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');
        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private static function handleException(Throwable $e): void
    {
        error_log("Unhandled Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        
        // Always show the fatal error in a structured way if debug is on
        $debug = Config::get('APP_DEBUG', 'false') === 'true';
        if ($debug) {
            if (!headers_sent()) {
                http_response_code(500);
            }
            echo "<div style='background:#1a1a1a; color:#eee; padding:20px; font-family:sans-serif; border-radius:8px; margin:20px; border:1px solid #ff4444;'>";
            echo "<h1 style='color:#ff4444; margin-top:0;'>Excepción no manejada</h1>";
            echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><strong>Archivo:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
            echo "<pre style='background:#000; padding:10px; overflow:auto; max-height:400px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "</div>";
        } else {
            self::abort(500, "Ha ocurrido un error interno en el servidor.");
        }
    }
}
