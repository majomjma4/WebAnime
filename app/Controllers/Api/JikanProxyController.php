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

        // 1. Intentar el Cache SIEMPRE primero para velocidad
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

        // 2. Si no hay cache, vamos a la API externa
        $url = 'https://api.jikan.moe/v4/' . ltrim($endpoint, '/');
        $url .= (strpos($url, '?') !== false ? '&' : '?') . 'sfw=1';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'NekoraList-Bot/1.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 20); // Damos 20 segundos para el Ranking
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
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
        } else {
            // Si falla la API, devolvemos un error detallado para saber qué pasa
            http_response_code(500);
            echo json_encode(array(
                'error' => 'API Error',
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'endpoint' => $endpoint
            ));
        }
    }
}
