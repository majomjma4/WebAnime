<?php
namespace Controllers;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            $this->abort(404, 'Vista no encontrada: ' . htmlspecialchars($view));
            return;
        }

        extract($data, EXTR_SKIP);
        require $viewPath;
    }

    protected function redirectToRoute(string $routeName, array $query = []): void
    {
        header('Location: ' . route_path($routeName, $query));
        exit;
    }

    protected function abort(int $statusCode, string $message): void
    {
        http_response_code($statusCode);
        echo $message;
    }

    protected function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function authorize(?string $guard): void
    {
        if (!$guard) {
            return;
        }

        $this->ensureSessionStarted();

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
