<?php
namespace Controllers;

use Routing\Router;

class FrontController extends Controller
{
    public function handle(string $route): void
    {
        $router = new Router(app_routes());
        $resolved = $router->match($route);

        if (!$resolved) {
            $this->abort(404, 'Ruta no encontrada');
            return;
        }

        $controllerClass = $resolved['controller'] ?? null;
        $action = $resolved['action'] ?? null;

        if (!$controllerClass || !$action || !class_exists($controllerClass)) {
            $this->abort(500, 'Ruta mal configurada');
            return;
        }

        $controller = new $controllerClass();
        if (!$controller instanceof Controller) {
            $this->abort(500, 'Controlador inválido');
            return;
        }

        $controller->authorize($resolved['guard'] ?? null);

        if (!method_exists($controller, $action)) {
            $this->abort(500, 'Acción no encontrada');
            return;
        }

        $controller->{$action}();
    }
}
