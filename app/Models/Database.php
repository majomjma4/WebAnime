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
    private $conn;

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
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            );

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            return $this->conn;
        } catch (PDOException $e) {
            // Log error but don't expose credentials
            error_log("Database Connection Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Prueba la conexión y verifica si la base de datos es accesible.
     * @return array array('success' => bool, 'message' => string)
     */
    public function testConnection()
    {
        $conn = $this->getConnection();
        if (!$conn) {
            return array('success' => false, 'message' => 'No se pudo establecer conexión con el servidor de base de datos.');
        }

        try {
            $stmt = $conn->query("SELECT 1");
            return array('success' => true, 'message' => 'Conexión exitosa y base de datos accesible.');
        } catch (PDOException $e) {
            return array('success' => false, 'message' => 'Conexión establecida pero error al consultar: ' . $e->getMessage());
        }
    }
}
