<?php
// Basic bootstrap (autoload + helpers)
if (!function_exists('app_load_env')) {
    function app_load_env(?string $envPath = null): void
    {
        static $loaded = false;
        static $loadedPath = null;

        $path = $envPath ?? dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
        if ($loaded && $loadedPath === $path) {
            return;
        }

        if (!is_file($path) || !is_readable($path)) {
            $loaded = true;
            $loadedPath = $path;
            return;
        }

        $envVars = parse_ini_file($path, false, INI_SCANNER_RAW);
        if (!is_array($envVars)) {
            $loaded = true;
            $loadedPath = $path;
            return;
        }

        foreach ($envVars as $key => $value) {
            if (!is_string($key) || $key === '') {
                continue;
            }

            $normalizedValue = is_scalar($value) ? (string) $value : '';

            if (getenv($key) === false) {
                putenv($key . '=' . $normalizedValue);
            }

            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $normalizedValue;
            }

            if (!array_key_exists($key, $_SERVER)) {
                $_SERVER[$key] = $normalizedValue;
            }
        }

        $loaded = true;
        $loadedPath = $path;
    }
}

app_load_env();

if (!function_exists('app_env')) {
    function app_env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return $value === false || $value === null ? $default : $value;
    }
}

if (!function_exists('app_is_https')) {
    function app_is_https(): bool
    {
        $https = strtolower((string) ($_SERVER['HTTPS'] ?? ''));
        $forwardedProto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
        $forwardedSsl = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? ''));
        $forwardedPort = (string) ($_SERVER['HTTP_X_FORWARDED_PORT'] ?? '');

        return $https !== '' && $https !== 'off'
            || (($_SERVER['SERVER_PORT'] ?? null) === '443')
            || $forwardedProto === 'https'
            || $forwardedSsl === 'on'
            || $forwardedPort === '443';
    }
}

if (!function_exists('app_base_url')) {
    function app_base_url(): string
    {
        $configured = trim((string) app_env('APP_URL', ''));
        if ($configured !== '') {
            return rtrim($configured, '/');
        }

        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $scheme = app_is_https() ? 'https' : 'http';
        $basePath = trim(app_base_path(), '/');

        return rtrim($scheme . '://' . $host . ($basePath !== '' ? '/' . $basePath : ''), '/');
    }
}

if (!function_exists('app_slugify')) {
    function app_slugify(string $value, string $fallback = ''): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return $fallback;
        }

        $trimmed = strtolower($trimmed);
        $trimmed = str_replace([' ', '/', '\\', '_'], '-', $trimmed);
        $trimmed = preg_replace('/-+/', '-', $trimmed);

        return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $trimmed)
            && (bool) preg_match('/[a-z]/', $trimmed)
            && (!preg_match('/^\d/', $trimmed) || strpos($trimmed, '-') !== false);
    }
}

if (!function_exists('app_send_security_headers')) {
    function app_send_security_headers(): void
    {
        static $sent = false;
        if ($sent || headers_sent()) {
            return;
        }
        $isHttps = app_is_https();
        $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
        $isLocalHost = in_array($host, ['localhost', 'localhost:80', 'nekoralist', 'nekoralist:80'], true)
            || (strpos($host, '127.0.0.1') === 0)
            || (strpos($host, '::1') === 0);

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

        if (!$isLocalHost && $isHttps) {
            header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
        }

        header("Content-Security-Policy: " . implode('; ', $cspParts));
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: SAMEORIGIN");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");

        $sent = true;
    }
}

if (!function_exists('app_start_session')) {
    function app_start_session(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $isHttps = app_is_https();
            session_set_cookie_params([
                'lifetime' => 86400 * 30, // 30 days
                'path' => '/',
                'domain' => '',
                'secure' => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }
}

if (!function_exists('app_publish_csrf_cookie')) {
    function app_publish_csrf_cookie(): void
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $isHttps = app_is_https();
        setcookie('CSRF-TOKEN', $_SESSION['csrf_token'], [
            'expires' => time() + 3600 * 2, // 2 hours
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => false, // JS needs to read this
            'samesite' => 'Lax'
        ]);
    }
}

if (!function_exists('app_detail_ref_from_input')) {
    function app_detail_ref_from_input(?string $malId, ?string $title, ?string $dbId): string
    {
        if ($malId && trim($malId) !== '') {
            return (string) $malId;
        }

        if ($dbId && trim($dbId) !== '') {
            return (string) $dbId;
        }

        if ($title && trim($title) !== '') {
            return app_slugify($title);
        }

        return '';
    }
}

app_start_session();
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
        $detailRef = app_detail_ref_from_input($malId, $title, $dbId);
        if ($detailRef === '') {
            return asset_path('detail');
        }

        return asset_path('detail/' . rawurlencode($detailRef));
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
