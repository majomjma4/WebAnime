<?php
namespace Controllers\Api;

use Controllers\Controller;
use PDO;
use Models\Database;

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

        // 3. Sistema de Cache Inteligente (Ranking dura 24h, otros 2h)
        $isRanking = (strpos($endpoint, 'top/') !== false);
        $cacheSeconds = $isRanking ? 86400 : 7200; // 24h para el ranking, 2h para el resto

        if ($db) {
            // Buscamos si existe cache fresco
            $stmt = $db->prepare("SELECT response, created_at FROM jikan_cache WHERE cache_key = ? LIMIT 1");
            $stmt->execute(array($cacheKey));
            $cached = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cached) {
                $createdAt = strtotime($cached['created_at']);
                $age = time() - $createdAt;

                // Si el cache es suficientemente joven, lo entregamos de inmediato
                if ($age < $cacheSeconds) {
                    echo $cached['response'];
                    exit;
                }
            }
        }

        // 4. Petición a Jikan (solo si el cache expiró o no existe)
        $url = 'https://api.jikan.moe/v4/' . ltrim($endpoint, '/');

        // Agregar SFW por seguridad si no está presente
        if (strpos($url, 'sfw=') === false) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'sfw=1';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) WebAnime/3.1');

        // Timeout más largo para rankings, más corto para buscador
        $timeout = $isRanking ? 15 : 8;
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            // Guardar en cache para la próxima vez
            if ($db) {
                $db->prepare("REPLACE INTO jikan_cache (cache_key, endpoint, response, created_at) VALUES (?, ?, ?, NOW())")
                    ->execute(array($cacheKey, $endpoint, $response));
            }
            echo $response;
            exit;
        }

        // 5. Fallback: Si la API falla pero tenemos un cache viejo, lo entregamos aunque sea viejo
        if ($db && $cached) {
            echo $cached['response'];
            exit;
        }

        // Si nada funcionó, retornamos error
        http_response_code($httpCode ? $httpCode : 500);
        echo json_encode(array(
            'error' => 'Jikan API Error',
            'status' => $httpCode,
            'message' => 'No se pudo conectar con la API de Jikan y no hay cache disponible.'
        ));
    }
}
