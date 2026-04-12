<?php
namespace Helpers;

class ApiResponse
{
    public static function json($payload, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload);
    }

    public static function success($data = array(), $statusCode = 200)
    {
        self::json(array_merge(array('success' => true), $data), $statusCode);
    }

    public static function error($message, $statusCode = 400, $extra = array())
    {
        self::json(array_merge(array('success' => false, 'error' => $message), $extra), $statusCode);
    }
}
