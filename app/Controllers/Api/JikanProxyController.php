<?php
namespace Controllers\Api;

use Controllers\Controller;
use PDO;
use Models\Database;

class JikanProxyController extends Controller
{
    public function handle()
    {
        /**
         * jikan_proxy.php - Legacy Edition (PHP 5.6+)
         */
        app_start_session();
        session_write_close();

        error_reporting(0);
        ini_set('display_errors', 0);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';
        
        // Manual decoding just in case
        $endpoint = urldecode($endpoint);

        if (empty($endpoint)) {
            http_response_code(400);
            echo json_encode(array('error' => 'Endpoint is required'));
            exit;
        }

        $dbObj = new Database();
        $db = $dbObj->getConnection();
        if (!$db) {
            http_response_code(500);
            echo json_encode(array('error' => 'Database connection failed'));
            exit;
        }

        $cacheKey = md5($endpoint);

        // 1. Check DB Cache FIRST (48 hours expiry)
        $stmt = $db->prepare("SELECT response, updated_at FROM jikan_cache WHERE cache_key = ? LIMIT 1");
        $stmt->execute(array($cacheKey));
        $cached = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cached) {
            $updatedAt = strtotime($cached['updated_at']);
            if ((time() - $updatedAt) < (48 * 3600)) {
                echo $cached['response'];
                exit;
            }
        }

        // 2. Fetch from Jikan
        $url = 'https://api.jikan.moe/v4/' . ltrim($endpoint, '/');

        // Auto-append SFW filter for searches/lists if not present
        if (preg_match('/(anime|manga|top|seasons|search)/i', $url) && strpos($url, 'sfw=') === false) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'sfw=1';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'NekoraList WebAnime Proxy / 3.0 (Legacy)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            // Save to DB Cache
            $save = $db->prepare("REPLACE INTO jikan_cache (cache_key, endpoint, response) VALUES (?, ?, ?)");
            $save->execute(array($cacheKey, $endpoint, $response));
            echo $response;
        } else if ($httpCode === 429) {
            http_response_code(429);
            echo json_encode(array('error' => 'Rate limited by Jikan'));
        } else {
            // Graceful fallback to expired cache if available
            if ($cached) {
                echo $cached['response'];
            } else {
                http_response_code($httpCode ? $httpCode : 500);
                echo $response ? $response : json_encode(array('error' => 'API failed', 'code' => $httpCode));
            }
        }

    }
}
