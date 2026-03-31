<?php
// Basic bootstrap (autoload + helpers)
if (session_status() === PHP_SESSION_NONE) {
    session_name('NekoraSession_V1');
    session_set_cookie_params(['path' => '/']);
}

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

if (!function_exists('route_path')) {
    function route_path(string $name, array $query = []): string
    {
        $routes = app_routes();
        if (!isset($routes[$name])) {
            throw new InvalidArgumentException("Route [$name] is not defined.");
        }

        $path = $routes[$name]['path'] ?? 'index';
        $url = $path === '' ? 'index' : $path;

        if ($query) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }
}
