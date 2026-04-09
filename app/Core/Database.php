<?php
namespace Core;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = Config::get('DB_HOST', 'localhost');
            $name = Config::get('DB_NAME', '');
            $user = Config::get('DB_USER', '');
            $pass = Config::get('DB_PASS', '');

            if (empty($name)) {
                throw new Exception("Configuración de base de datos incompleta (DB_NAME).");
            }

            try {
                $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // In a professional setup, we log this and show a generic error
                error_log("Error de conexión a la base de datos: " . $e->getMessage());
                throw new Exception("Error al conectar con la base de datos.");
            }
        }

        return self::$instance;
    }

    /**
     * Helper para ejecutar consultas preparadas fácilmente
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
