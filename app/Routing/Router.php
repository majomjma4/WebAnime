<?php
namespace Routing;

class Router
{
    private $routes;

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function match($route)
    {
        $normalized = $this->normalize($route);
        if ($normalized === null) {
            return null;
        }

        foreach ($this->routes as $name => $config) {
            $candidates = $this->candidatesFor($config);
            foreach ($candidates as $candidate) {
                $matched = $this->matchCandidate($normalized, (string)$candidate);
                if ($matched === null) {
                    continue;
                }

                $config['name'] = $name;
                if (!empty($matched)) {
                    $config['params'] = $matched;
                }

                return $config;
            }
        }

        return null;
    }

    private function candidatesFor($config)
    {
        $candidates = array(isset($config['path']) ? $config['path'] : '');

        $aliases = isset($config['aliases']) ? $config['aliases'] : array();
        foreach ($aliases as $alias) {
            $candidates[] = $alias;
        }

        $patterns = isset($config['patterns']) ? $config['patterns'] : array();
        foreach ($patterns as $pattern) {
            $candidates[] = $pattern;
        }

        return $candidates;
    }

    private function matchCandidate($normalizedRoute, $candidate)
    {
        $normalizedCandidate = $this->normalize($candidate);
        if ($normalizedCandidate !== null && $normalizedCandidate === $normalizedRoute) {
            return array();
        }

        return $this->extractPatternParams($normalizedRoute, $candidate);
    }

    private function extractPatternParams($normalizedRoute, $candidate)
    {
        if (strpos($candidate, '{') === false) {
            return null;
        }

        $normalizedCandidate = urldecode(trim($candidate));
        $normalizedCandidate = str_replace('\\', '/', $normalizedCandidate);
        $normalizedCandidate = trim($normalizedCandidate, '/');
        if ($normalizedCandidate === '' || strpos($normalizedCandidate, '..') !== false || preg_match('#//+#', $normalizedCandidate)) {
            return null;
        }

        $paramNames = array();
        $quotedPattern = preg_quote($normalizedCandidate, '#');
        $regex = preg_replace_callback('/\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\\}/', function ($matches) use (&$paramNames) {
            $paramNames[] = $matches[1];
            return '([^/]+)';
        }, $quotedPattern);

        if ($regex === null || empty($paramNames)) {
            return null;
        }

        if (!preg_match('#^' . $regex . '$#u', $normalizedRoute, $matches)) {
            return null;
        }

        $params = array();
        foreach ($paramNames as $index => $name) {
            $valIdx = $index + 1;
            $value = trim((string)urldecode(isset($matches[$valIdx]) ? $matches[$valIdx] : ''));
            if ($value === '') {
                return null;
            }
            $params[$name] = $value;
        }

        return $params;
    }

    private function normalize($route)
    {
        $normalized = urldecode(trim($route));
        $normalized = str_replace('\\', '/', $normalized);
        $normalized = trim($normalized, '/');

        if ($normalized === '') {
            return 'index';
        }

        if (strpos($normalized, '..') !== false || preg_match('#//+#', $normalized)) {
            return null;
        }

        $normalized = preg_replace('/\.php$/i', '', $normalized);
        $normalized = trim($normalized, '/');

        if ($normalized === '') {
            return 'index';
        }

        return mb_strtolower($normalized, 'UTF-8');
    }
}
