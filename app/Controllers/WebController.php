<?php
namespace Controllers;

class WebController extends PageController
{
    private const ROUTES = [
        '' => 'pages/index',
        'index' => 'pages/index',
        'admin' => 'pages/admin',
        'añadir' => 'pages/añadir',
        'character' => 'pages/character',
        'destacados' => 'pages/destacados',
        'detail' => 'pages/detail',
        'gesCom' => 'pages/gesCom',
        'gestion' => 'pages/gestion',
        'gesUs' => 'pages/gesUs',
        'ingresar' => 'pages/ingresar',
        'pago' => 'pages/pago',
        'peliculas' => 'pages/peliculas',
        'ranking' => 'pages/ranking',
        'registro' => 'pages/registro',
        'series' => 'pages/series',
        'user' => 'pages/user',
        'partials/layout' => 'partials/layout',
        'partials/admin-layout' => 'partials/admin-layout',
    ];

    public function dispatch(string $route): void
    {
        $normalized = trim($route, '/');
        $view = self::ROUTES[$normalized] ?? null;
        if (!$view) {
            http_response_code(404);
            echo 'Ruta no encontrada';
            return;
        }
        $this->render($view);
    }
}
