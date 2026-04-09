<?php
namespace Core;

class Router
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function match(string $route): ?array
    {
        $normalized = $this->normalize($route);
        if ($normalized === null) {
            return null;
        }

        // Special handling for detail routes (dynamic)
        if ($normalized === 'detail' || str_starts_with($normalized, 'detail/')) {
             return $this->resolveDetailRoute($normalized);
        }

        foreach ($this->routes as $name => $config) {
            $candidates = [$config['path'] ?? ''];
            foreach (($config['aliases'] ?? []) as $alias) {
                $candidates[] = $alias;
            }

            foreach ($candidates as $candidate) {
                $normalizedCandidate = $this->normalize((string) $candidate);
                if ($normalizedCandidate !== null && $normalizedCandidate === $normalized) {
                    $config['name'] = $name;
                    return $config;
                }
            }
        }

        return null;
    }

    private function resolveDetailRoute(string $route): ?array
    {
        if ($route === 'detail') {
            unset($_GET['_detail_ref']);
            return $this->routes['detail'] ?? null;
        }

        if (!preg_match('#^detail/([^/]+)$#u', $route, $matches)) {
            return null;
        }
        $detailRef = trim((string) urldecode($matches[1]));
        if ($detailRef === '') {
            return null;
        }

        $_GET['_detail_ref'] = $detailRef;
        return $this->routes['detail'] ?? null;
    }

    private function normalize(string $route): ?string
    {
        $normalized = urldecode(trim($route));
        $normalized = str_replace('\\', '/', $normalized);
        $normalized = trim($normalized, '/');

        if ($normalized === '') {
            return 'index';
        }

        if (str_contains($normalized, '..') || preg_match('#//+#', $normalized)) {
            return null;
        }

        // Professional routing: strip .php if it exists to normalize
        $normalized = preg_replace('/\.php$/i', '', $normalized) ?? $normalized;
        $normalized = trim($normalized, '/');

        if ($normalized === '') {
            return 'index';
        }

        return mb_strtolower($normalized, 'UTF-8');
    }
}
