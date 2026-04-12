<?php
namespace Controllers\Api;

use Controllers\Controller;
use PDO;
use Models\Database;
use Exception;

class JikanProxyController extends Controller
{
    public function handle()
    {
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

        $dbObj = new Database();
        $db = $dbObj->getConnection();
        $cacheKey = md5($endpoint);

        // 1. Prioridad absoluta al Cache (para evitar bloqueos de red)
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT response FROM jikan_cache WHERE cache_key = ? LIMIT 1");
                $stmt->execute(array($cacheKey));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    echo $row['response'];
                    exit;
                }
            } catch (Exception $e) {}
        }

        // 2. Intento de conexión con la API (Ultra-rápida, 3s max)
        $url = 'https://api.jikan.moe/v4/' . ltrim($endpoint, '/');
        $url .= (strpos($url, '?') !== false ? '&' : '?') . 'sfw=1';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36');
        
        // Timeout muy corto. Si el servidor está bloqueado, fallará rápido y el Frontend hará el puente.
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            if ($db) {
                try {
                    $db->prepare("REPLACE INTO jikan_cache (cache_key, endpoint, response, created_at) VALUES (?, ?, ?, NOW())")
                       ->execute(array($cacheKey, $endpoint, $response));
                } catch (Exception $e) {
                    try {
                        $db->prepare("REPLACE INTO jikan_cache (cache_key, endpoint, response) VALUES (?, ?, ?)")
                           ->execute(array($cacheKey, $endpoint, $response));
                    } catch (Exception $e2) {}
                }
            }
            echo $response;
            exit;
        } 
        
        // Si falló (ej. por bloqueo de servidor), retornamos error rápido para que el Frontend use el puente
        http_response_code(504);
        echo json_encode(array('error' => 'API Timeout on Server', 'bridge_mode' => true));
    }
}
