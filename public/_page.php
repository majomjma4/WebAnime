<?php
require __DIR__ . '/../app/bootstrap.php';

$view = $view ?? null;
if (!$view) {
    http_response_code(500);
    echo 'Vista no configurada';
    exit;
}

$controller = new Controllers\PageController();
$controller->render($view);
