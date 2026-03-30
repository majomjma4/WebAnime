<?php
namespace Helpers;

class ApiResponse
{
    public static function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }

    public static function success(array $data = [], int $statusCode = 200): void
    {
        self::json(array_merge(['success' => true], $data), $statusCode);
    }

    public static function error(string $message, int $statusCode = 400, array $extra = []): void
    {
        self::json(array_merge(['success' => false, 'error' => $message], $extra), $statusCode);
    }
}
