<?php
require __DIR__ . '/../app/bootstrap.php';

$route = $_GET['__route'] ?? '';

$frontController = new Controllers\FrontController();
$frontController->handle($route);
