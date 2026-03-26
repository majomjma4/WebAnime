<?php
// Basic bootstrap (autoload + helpers)

// Simple autoload for app classes
spl_autoload_register(function ($class) {
    $base = __DIR__;
    $path = $base . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});
