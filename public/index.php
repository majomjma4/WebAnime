<?php
require __DIR__ . '/../app/bootstrap.php';

$requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/.');
$path = (string) parse_url($requestUri, PHP_URL_PATH);

if ($baseDir !== '' && $baseDir !== '/' && str_starts_with($path, $baseDir)) {
    $path = substr($path, strlen($baseDir));
}

$configuredBasePath = (string) parse_url(app_base_url(), PHP_URL_PATH);
$configuredBasePath = rtrim(str_replace('\\', '/', $configuredBasePath), '/');
if ($configuredBasePath !== '' && $configuredBasePath !== '/' && str_starts_with($path, $configuredBasePath)) {
    $path = substr($path, strlen($configuredBasePath));
}

$route = trim($path, '/');
if ($route === 'index.php') {
    $route = '';
}

$frontController = new Controllers\FrontController();
$frontController->handle($route);
