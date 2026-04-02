<?php
namespace Models;

use PDO;
use PDOException;

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Configuracion por defecto para entorno local
        $this->host = "localhost";
        $this->db_name = "Webanime";
        $this->username = "root";
        $this->password = "";

        // Intenta cargar configuraciones desde un archivo .env si existe en la raiz del proyecto
        $envPath = __DIR__ . '/../../.env';
        if (file_exists($envPath)) {
            $envVars = parse_ini_file($envPath);
            if ($envVars !== false) {
                $this->host = $envVars['DB_HOST'] ?? $this->host;
                $this->db_name = $envVars['DB_NAME'] ?? $this->db_name;
                $this->username = $envVars['DB_USER'] ?? $this->username;
                $this->password = $envVars['DB_PASS'] ?? $this->password;
            }
        }

        // Finalmente se reemplaza con Variables de Entorno del sistema (superando a .env) si el Servidor de Hosting las provee
        $this->host = getenv('DB_HOST') ?: $this->host;
        $this->db_name = getenv('DB_NAME') ?: $this->db_name;
        $this->username = getenv('DB_USER') ?: $this->username;
        $this->password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : $this->password;
    }

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
