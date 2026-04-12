<?php
namespace Controllers\Api;

use Controllers\Controller;
use PDO;
use Models\Database;

class JikanProxyController extends Controller
{
    public function handle()
    {
        // 1. Evitar bloqueos de sesión para que las peticiones vuelen
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_write_close();

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';
        $endpoint = urldecode($endpoint);

        if (empty($endpoint)) {
            echo json_encode(array('error' => 'No endpoint provided'));
            exit;
        }

        // 2. Conexión rápida a DB para el Cache
        $dbObj = new Database();
        $db = $dbObj->getConnection();
        $cacheKey = md5($endpoint);

        // Intentar leer cache primero (48 horas)
        if ($db) {
            $stmt = $db->prepare("SELECT response FROM jikan_cache WHERE cache_key = ? LIMIT 1");
            $stmt->execute(array($cacheKey));
            $cached = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($cached) {
                echo $cached['response'];
                exit;
            }
        }

        // 3. Petición ultra-veloz a Jikan
        $url = 'https://api.jikan.moe/v4/' . ltrim($endpoint, '/');
        if (strpos($url, 'sfw=') === false) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'sfw=1';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) WebAnime/3.1');
        curl_setopt($ch, CURLOPT_TIMEOUT, 8); // Tiempo de espera corto para no colgar el buscador
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Evitar problemas de certificados en CPanel
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            // Guardar en cache para la próxima vez
            if ($db) {
                $db->prepare("REPLACE INTO jikan_cache (cache_key, endpoint, response) VALUES (?, ?, ?)")
                   ->execute(array($cacheKey, $endpoint, $response));
            }
            echo $response;
        } else {
            http_response_code($httpCode ? $httpCode : 500);
            echo json_encode(array('error' => 'Jikan API Error', 'status' => $httpCode));
        }
    }
}
