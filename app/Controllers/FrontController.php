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
            $this->renderNotFound($route);
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
            $this->abort(500, 'Controlador invalido');
            return;
        }

        $this->publishRouteParams($resolved['params'] ?? []);
        $controller->authorize($resolved['guard'] ?? null);

        if (!method_exists($controller, $action)) {
            $this->abort(500, 'Accion no encontrada');
            return;
        }

        $controller->{$action}();
    }

    private function publishRouteParams(array $params): void
    {
        unset($_GET['_detail_ref']);

        foreach ($params as $name => $value) {
            if (!is_string($name)) {
                continue;
            }

            if ($name === 'detail_ref') {
                $detailRef = trim((string) $value);
                if ($detailRef !== '' && app_is_valid_detail_ref($detailRef)) {
                    $_GET['_detail_ref'] = $detailRef;
                }
            }
        }
    }

}
