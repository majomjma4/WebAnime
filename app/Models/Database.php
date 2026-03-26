<?php
namespace Models;

use PDO;
use PDOException;

class Database {
    private $host = "localhost";
    private $db_name = "Webanime";
    private $username = "root";
    private $password = "";
    public $conn;

    /**
     * Retorna una instancia de la conexión a la BD usando PDO
     */
    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            // Configurar PDO para que lance excepciones cuando ocurran errores
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Hacer que los fetch devuelvan arrays asociativos por defecto
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Error de conexión PDO: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
