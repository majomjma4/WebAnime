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

        // Soporte para endpoints en el path (ej. api/jikan_proxy/anime/1/characters)
        if (empty($endpoint)) {
            $uri = $_SERVER['REQUEST_URI'];
            $base = 'api/jikan_proxy';
            $pos = strpos($uri, $base);
            if ($pos !== false) {
                $endpoint = substr($uri, $pos + strlen($base));
                $endpoint = ltrim($endpoint, '/');
                $endpoint = explode('?', $endpoint)[0]; // Quitar query params de la URL
            }
        }

        if (empty($endpoint)) {
            echo json_encode(array('error' => 'No endpoint provided'));
            exit;
        }

        $dbObj = new Database();
        $db = $dbObj->getConnection();
        $cacheKey = md5($endpoint);

        // 1. Prioridad absoluta al Cache para el Ranking
        $isRanking = (strpos($endpoint, 'top/') !== false);
        $cachedResponse = null;

        if ($db) {
            try {
                $stmt = $db->prepare("SELECT response, created_at FROM jikan_cache WHERE cache_key = ? LIMIT 1");
                $stmt->execute(array($cacheKey));
                $cached = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($cached) {
                    $cachedResponse = $cached['response'];
                    $age = time() - strtotime($cached['created_at']);
                    
                    // Si es Ranking y tiene menos de 12 horas, lo damos de inmediato
                    if ($isRanking && $age < 43200) {
                        echo $cachedResponse;
                        exit;
                    }
                    // Para otros, si tiene menos de 1 hora, lo damos de inmediato
                    if (!$isRanking && $age < 3600) {
                        echo $cachedResponse;
                        exit;
                    }
                }
            } catch (Exception $e) {}
        }

        // 2. Petición a Jikan con timeout agresivo para evitar Error 500 del servidor
        $url = 'https://api.jikan.moe/v4/' . ltrim($endpoint, '/');
        if (strpos($url, 'sfw=') === false) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'sfw=1';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'NekoraList-Bot/1.1');
        
        // El servidor suele matar procesos a los 10-15s. Seteamos curl a 8s para tener margen de responder.
        curl_setopt($ch, CURLOPT_TIMEOUT, 8); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
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
            exit;
        } 
        
        // 3. Si la API falló o tardó mucho, devolvemos el cache que tengamos (aunque sea viejo)
        if ($cachedResponse) {
            echo $cachedResponse;
            exit;
        }

        // 4. Si no hay nada, error final
        http_response_code($httpCode ? $httpCode : 504);
        echo json_encode(array(
            'error' => 'Timeout or API Error',
            'curl_error' => $curlError,
            'endpoint' => $endpoint
        ));
    }
}
