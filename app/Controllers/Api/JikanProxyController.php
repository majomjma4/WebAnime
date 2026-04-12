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
        // 1. Evitar bloqueos de sesión
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

        // 3. Sistema de Cache defensivo
        $isRanking = (strpos($endpoint, 'top/') !== false);
        $cacheSeconds = $isRanking ? 86400 : 7200; 
        $cachedResponse = null;

        if ($db) {
            try {
                // Intentamos con la columna de fecha
                $stmt = $db->prepare("SELECT response, created_at FROM jikan_cache WHERE cache_key = ? LIMIT 1");
                $stmt->execute(array($cacheKey));
                $cached = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($cached) {
                    $createdAt = isset($cached['created_at']) ? strtotime($cached['created_at']) : 0;
                    $age = time() - $createdAt;
                    $cachedResponse = $cached['response'];
                    
                    if ($age < $cacheSeconds && $age > 0) {
                        echo $cachedResponse;
                        exit;
                    }
                }
            } catch (Exception $e) {
                // Si falla (ej. no existe la columna), intentamos la versión simple
                try {
                    $stmt = $db->prepare("SELECT response FROM jikan_cache WHERE cache_key = ? LIMIT 1");
                    $stmt->execute(array($cacheKey));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        $cachedResponse = $row['response'];
                        // Si es el ranking, lo damos aunque no sepamos la fecha para que no salga error
                        if ($isRanking) {
                           echo $cachedResponse;
                           exit;
                        }
                    }
                } catch (Exception $e2) {}
            }
        }

        // 4. Petición a Jikan
        $url = 'https://api.jikan.moe/v4/' . ltrim($endpoint, '/');
        if (strpos($url, 'sfw=') === false) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'sfw=1';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) WebAnime/3.1');
        $timeout = $isRanking ? 15 : 8; 
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            if ($db) {
                // Intentamos guardar con fecha, si falla, guardamos simple
                try {
                    $db->prepare("REPLACE INTO jikan_cache (cache_key, endpoint, response, created_at) VALUES (?, ?, ?, NOW())")
                       ->execute(array($cacheKey, $endpoint, $response));
                } catch (Exception $e) {
                    $db->prepare("REPLACE INTO jikan_cache (cache_key, endpoint, response) VALUES (?, ?, ?)")
                       ->execute(array($cacheKey, $endpoint, $response));
                }
            }
            echo $response;
            exit;
        } 
        
        // 5. Fallback total
        if ($cachedResponse) {
            echo $cachedResponse;
            exit;
        }

        http_response_code($httpCode ? $httpCode : 500);
        echo json_encode(array('error' => 'Jikan API Error', 'status' => $httpCode));
    }
}
