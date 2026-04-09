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
            foreach ($this->candidatesFor($config) as $candidate) {
                $matched = $this->matchCandidate($normalized, (string) $candidate);
                if ($matched === null) {
                    continue;
                }

                $config['name'] = $name;
                if ($matched !== []) {
                    $config['params'] = $matched;
                }

                return $config;
            }
        }

        return null;
    }

    private function candidatesFor(array $config): array
    {
        $candidates = [$config['path'] ?? ''];

        foreach (($config['aliases'] ?? []) as $alias) {
            $candidates[] = $alias;
        }

        foreach (($config['patterns'] ?? []) as $pattern) {
            $candidates[] = $pattern;
        }

        return $candidates;
    }

    private function matchCandidate(string $normalizedRoute, string $candidate): ?array
    {
        $normalizedCandidate = $this->normalize($candidate);
        if ($normalizedCandidate !== null && $normalizedCandidate === $normalizedRoute) {
            return [];
        }

        return $this->extractPatternParams($normalizedRoute, $candidate);
    }

    private function extractPatternParams(string $normalizedRoute, string $candidate): ?array
    {
        if (!str_contains($candidate, '{')) {
            return null;
        }

        $normalizedCandidate = urldecode(trim($candidate));
        $normalizedCandidate = str_replace('\\', '/', $normalizedCandidate);
        $normalizedCandidate = trim($normalizedCandidate, '/');
        if ($normalizedCandidate === '' || str_contains($normalizedCandidate, '..') || preg_match('#//+#', $normalizedCandidate)) {
            return null;
        }

        $paramNames = [];
        $quotedPattern = preg_quote($normalizedCandidate, '#');
        $regex = preg_replace_callback('/\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\\}/', static function (array $matches) use (&$paramNames): string {
            $paramNames[] = $matches[1];
            return '([^/]+)';
        }, $quotedPattern);

        if ($regex === null || $paramNames === []) {
            return null;
        }

        if (!preg_match('#^' . $regex . '$#u', $normalizedRoute, $matches)) {
            return null;
        }

        $params = [];
        foreach ($paramNames as $index => $name) {
            $value = trim((string) urldecode($matches[$index + 1] ?? ''));
            if ($value === '') {
                return null;
            }
            $params[$name] = $value;
        }

        return $params;
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
