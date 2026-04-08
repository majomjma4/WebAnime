<?php
// Basic bootstrap (autoload + helpers)
if (session_status() === PHP_SESSION_NONE) {
    session_name('NekoraSession_V1');
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? null) === '443');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

if (!function_exists('app_send_security_headers')) {
    function app_send_security_headers(): void
    {
        static $sent = false;
        if ($sent || headers_sent()) {
            return;
        }
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? null) === '443');
        $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
        $isLocalHost = in_array($host, ['localhost', 'localhost:80', 'nekoralist', 'nekoralist:80'], true)
            || str_starts_with($host, '127.0.0.1')
            || str_starts_with($host, '::1');

        $cspParts = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "object-src 'none'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https:",
            "connect-src 'self' https://api.jikan.moe https://translate.googleapis.com",
            "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com",
            "media-src 'self' https:",
        ];

        if ($isHttps && !$isLocalHost) {
            $cspParts[] = "upgrade-insecure-requests";
        }
        $csp = implode('; ', $cspParts);

        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: accelerometer=(), autoplay=(), camera=(), geolocation=(), gyroscope=(), microphone=(), payment=(self), usb=()');
        header('Cross-Origin-Opener-Policy: same-origin-allow-popups');
        header('Cross-Origin-Resource-Policy: same-origin');
        header('Content-Security-Policy: ' . $csp);

        $sent = true;
    }
}

if (!function_exists('app_start_session')) {
    function app_start_session(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['_session_initialized'])) {
            session_regenerate_id(true);
            $_SESSION['_session_initialized'] = time();
        }
    }
}

if (!function_exists('app_csrf_token')) {
    function app_csrf_token(): string
    {
        app_start_session();

        if (empty($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('app_publish_csrf_cookie')) {
    function app_publish_csrf_cookie(): void
    {
        if (headers_sent()) {
            return;
        }

        $token = app_csrf_token();
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? null) === '443');
        setcookie('XSRF-TOKEN', $token, [
            'expires' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
    }
}

if (!function_exists('app_get_json_input')) {
    function app_get_json_input(): array
    {
        $raw = file_get_contents('php://input');
        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}

if (!function_exists('app_require_method')) {
    function app_require_method(array|string $methods): void
    {
        $allowed = array_map('strtoupper', (array) $methods);
        $actual = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if (!in_array($actual, $allowed, true)) {
            http_response_code(405);
            header('Allow: ' . implode(', ', $allowed));
            exit(json_encode(['success' => false, 'error' => 'Metodo no permitido']));
        }
    }
}

if (!function_exists('app_verify_csrf')) {
    function app_verify_csrf(): void
    {
        app_start_session();

        $sessionToken = (string) ($_SESSION['_csrf_token'] ?? '');
        $headerToken = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        $cookieToken = (string) ($_COOKIE['XSRF-TOKEN'] ?? '');

        if (
            $sessionToken === ''
            || $headerToken === ''
            || $cookieToken === ''
            || !hash_equals($sessionToken, $headerToken)
            || !hash_equals($sessionToken, $cookieToken)
        ) {
            http_response_code(419);
            exit(json_encode(['success' => false, 'error' => 'Token CSRF invalido']));
        }
    }
}

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

app_send_security_headers();
app_publish_csrf_cookie();

// Simple autoload for app classes
spl_autoload_register(function ($class) {
    $base = __DIR__;
    $path = $base . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

if (!function_exists('app_routes')) {
    function app_routes(): array
    {
        static $routes = null;
        if ($routes === null) {
            $routes = require __DIR__ . '/routes.php';
        }
        return $routes;
    }
}

if (!function_exists('app_base_path')) {
    function app_base_path(): string
    {
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/.');

        if ($baseDir === '' || $baseDir === '.') {
            return '/';
        }

        return $baseDir === '/' ? '/' : $baseDir . '/';
    }
}

if (!function_exists('asset_path')) {
    function asset_path(string $path = ''): string
    {
        $base = app_base_path();
        $trimmed = ltrim($path, '/');

        if ($trimmed === '') {
            return rtrim($base, '/');
        }

        return $base . $trimmed;
    }
}


if (!function_exists('detail_path')) {
    function detail_path(string $malId = '', string $title = '', string $dbId = ''): string
    {
        $candidate = trim($malId) !== '' ? trim($malId) : trim($dbId);
        if ($candidate !== '' && ctype_digit($candidate)) {
            return asset_path('detail/' . rawurlencode($candidate));
        }

        $rawTitle = trim($title);
        if ($rawTitle !== '') {
            $slug = strtolower(trim((string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $rawTitle)));
            $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
            $slug = trim($slug, '-');
            if ($slug !== '') {
                return asset_path('detail/' . rawurlencode($slug));
            }
        }

        return asset_path('detail');
    }
}
if (!function_exists('route_path')) {
    function route_path(string $name, array $query = []): string
    {
        $routes = app_routes();
        if (!isset($routes[$name])) {
            throw new InvalidArgumentException("Route [$name] is not defined.");
        }

        $routePath = trim((string) ($routes[$name]['path'] ?? 'index'), '/');
        $url = $routePath === '' ? asset_path('index') : asset_path($routePath);

        if ($query) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }
}