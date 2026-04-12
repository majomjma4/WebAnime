<?php
namespace Models;

use PDO;
use PDOException;

class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        // Configuracion por defecto para entorno local
        $this->host = "localhost";
        $this->db_name = "Webanime";
        $this->username = "root";
        $this->password = "";

        // Prioridad: variables del sistema > .env > defaults locales.
        $this->host = (string) app_env('DB_HOST', $this->host);
        $this->db_name = (string) app_env('DB_NAME', $this->db_name);
        $this->username = (string) app_env('DB_USER', $this->username);
        $this->password = (string) app_env('DB_PASS', $this->password);
    }

    /**
     * Retorna una instancia de la conexión a la BD usando PDO
     */
    public function getConnection()
    {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_CONNECT_TIMEOUT => 5, // 5 seconds timeout for faster failing
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $exception) {
            echo "Error de conexión PDO: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
