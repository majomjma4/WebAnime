<?php
// PHP 5.6+ Compatible Entry Point

// TEMPORAL: Activar errores para diagnóstico
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/../app/bootstrap.php';

$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
$scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
$baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/.');

$path = '/' . ltrim(parse_url($requestUri, PHP_URL_PATH), '/');

// Normalización de rutas
if ($baseDir !== '' && $baseDir !== '/' && strpos($path, $baseDir) === 0) {
    $path = '/' . ltrim(substr($path, strlen($baseDir)), '/');
}

$configuredBasePath = rtrim(app_env('APP_BASE_PATH', ''), '/');
if ($configuredBasePath !== '' && $configuredBasePath !== '/' && strpos($path, $configuredBasePath) === 0) {
    $path = '/' . ltrim(substr($path, strlen($configuredBasePath)), '/');
}

$route = trim($path, '/');
if ($route === '') {
    $route = 'index';
}

$frontController = new Controllers\FrontController();
$frontController->handle($route);
