<?php
namespace Controllers;

use Routing\Router;

class FrontController extends Controller
{
    public function handle(string $route): void
    {
        $trimmedRoute = trim($route, '/');

        if ($trimmedRoute === 'detail' || str_starts_with($trimmedRoute, 'detail/')) {
            $detailResolved = $this->resolveDetailRoute($trimmedRoute);
            if (!$detailResolved) {
                $this->renderNotFound($route);
                return;
            }
            $resolved = $detailResolved;
        } else {
            $router = new Router(app_routes());
            $resolved = $router->match($route);
            if (!$resolved) {
                $this->renderNotFound($route);
                return;
            }
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

        $controller->authorize($resolved['guard'] ?? null);

        if (!method_exists($controller, $action)) {
            $this->abort(500, 'Accion no encontrada');
            return;
        }

        $controller->{$action}();
    }

    private function resolveDetailRoute(string $route): ?array
    {
        if ($route === 'detail') {
            unset($_GET['_detail_ref']);
            return app_routes()['detail'] ?? null;
        }

        if (!preg_match('#^detail/([^/]+)$#u', $route, $matches)) {
            return null;
        }

        $detailRef = trim((string) urldecode($matches[1]));
        if ($detailRef === '') {
            return null;
        }

        $_GET['_detail_ref'] = $detailRef;
        return app_routes()['detail'] ?? null;
    }

    private function renderNotFound(string $route): void
    {
        http_response_code(404);
        $requestedPath = trim($route, '/');
        $this->render('pages/404', [
            'requestedPath' => $requestedPath,
        ]);
    }
}
