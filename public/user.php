<?php
require __DIR__ . '/../app/bootstrap.php';
$controller = new Controllers\PageController();
$controller->render('pages/user');
