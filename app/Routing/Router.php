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

        foreach ($this->routes as $name => $config) {
            $candidates = [$config['path'] ?? ''];
            foreach (($config['aliases'] ?? []) as $alias) {
                $candidates[] = $alias;
            }

            foreach ($candidates as $candidate) {
                if ($this->normalize((string) $candidate) === $normalized) {
                    $config['name'] = $name;
                    return $config;
                }
            }
        }

        return null;
    }

    private function normalize(string $route): string
    {
        $normalized = urldecode(trim($route, '/'));
        if ($normalized === '') {
            return 'index';
        }

        $normalized = preg_replace('/\.php$/i', '', $normalized) ?? $normalized;
        return mb_strtolower($normalized, 'UTF-8');
    }
}
