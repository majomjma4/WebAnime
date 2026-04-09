<?php
namespace Core;

class Config
{
    private static ?array $items = null;

    public static function load(): void
    {
        if (self::$items !== null) {
            return;
        }

        self::$items = [];
        $envPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
        
        if (is_file($envPath)) {
            $parsed = parse_ini_file($envPath, false, INI_SCANNER_RAW);
            if ($parsed !== false) {
                foreach ($parsed as $key => $value) {
                    self::$items[(string) $key] = is_string($value) ? trim($value) : $value;
                }
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();
        
        // Prioritize actual environment variables
        $systemValue = getenv($key);
        if ($systemValue !== false) {
            return $systemValue;
        }

        return self::$items[$key] ?? $default;
    }
}
