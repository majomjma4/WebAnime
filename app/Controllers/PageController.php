<?php
namespace Controllers;

class PageController
{
    public function render(string $view, array $data = []): void
    {
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo 'Vista no encontrada: ' . htmlspecialchars($view);
            return;
        }
        extract($data, EXTR_SKIP);
        require $viewPath;
    }
}

