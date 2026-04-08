<?php
namespace Routing;

class Router
{
    public function __construct(private readonly array $routes)
    {
    }

    public function match(string $route): ?array
    {
        $normalized = $this->normalize($route);
        if ($normalized === null) {
            return null;
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

        $normalized = preg_replace('/\.php$/i', '', $normalized) ?? $normalized;
        $normalized = trim($normalized, '/');

        if ($normalized === '') {
            return 'index';
        }

        return mb_strtolower($normalized, 'UTF-8');
    }
}
