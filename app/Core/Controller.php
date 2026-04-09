<?php
namespace Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        $viewPath = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            App::abort(404, "Vista [$view] no encontrada.");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $output = (string) ob_get_clean();

        // Inject bootstrap markup if available
        if (function_exists('app_client_bootstrap_markup')) {
            $bootstrapMarkup = app_client_bootstrap_markup();
            if ($bootstrapMarkup !== '') {
                if (stripos($output, '</head>') !== false) {
                    $output = preg_replace('/<\/head>/i', $bootstrapMarkup . PHP_EOL . '</head>', $output, 1) ?? $output;
                } else {
                    $output = $bootstrapMarkup . PHP_EOL . $output;
                }
            }
        }

        echo $output;
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function redirectToRoute(string $routeName, array $query = []): void
    {
        $this->redirect(route_path($routeName, $query));
    }

    public function authorize(?string $guard): void
    {
        if (!$guard) {
            return;
        }

        if ($guard === 'auth' && empty($_SESSION['user_id'])) {
            $this->redirectToRoute('home');
        }

        if ($guard === 'admin') {
            $isAdmin = isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] === 'Admin';
            if (!$isAdmin) {
                $this->redirectToRoute('home');
            }
        }
    }
}
