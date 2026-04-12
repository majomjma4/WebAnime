<?php
// TEMPORAL: Activar errores para diagnóstico
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/../app/bootstrap.php';

$requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/.');
$path = (string) parse_url($requestUri, PHP_URL_PATH);

if ($baseDir !== '' && $baseDir !== '/' && (strpos($path, $baseDir) === 0)) {
    $path = substr($path, strlen($baseDir));
}

$configuredBasePath = (string) parse_url(app_base_url(), PHP_URL_PATH);
$configuredBasePath = rtrim(str_replace('\\', '/', $configuredBasePath), '/');
if ($configuredBasePath !== '' && $configuredBasePath !== '/' && (strpos($path, $configuredBasePath) === 0)) {
    $path = substr($path, strlen($configuredBasePath));
}

$route = trim($path, '/');
if ($route === 'index.php') {
    $route = '';
}

$frontController = new Controllers\FrontController();
$frontController->handle($route);
