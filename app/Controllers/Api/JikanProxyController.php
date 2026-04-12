<?php
namespace Controllers\Api;

use Controllers\Controller;
use PDO;
use Models\Database;

class JikanProxyController extends Controller
{
    private $restrictedGenres = ['Hentai', 'Erotica', 'Ecchi', 'Yaoi', 'Yuri', 'Gore', 'Harem', 'Reverse Harem', 'Rx', 'Girls Love', 'Boys Love'];
    private $restrictedTitles = ['does it count if', 'futanari', 'e-p-h-o-r-i-a'];

    public function handle()
    {
        /**
         * jikan_proxy.php
         * Refactored to use MySQL jikan_cache instead of filesystem.
         */
        app_start_session();
        session_write_close();

        error_reporting(0);
        ini_set('display_errors', 0);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $endpoint = $_GET['endpoint'] ?? '';
        
        // Manual decoding just in case
        $endpoint = urldecode($endpoint);

        if (empty($endpoint)) {
            http_response_code(400);
            echo json_encode(['error' => 'Endpoint is required']);
            exit;
        }

        $db = (new Database())->getConnection();
        if (!$db) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }

        $cacheKey = md5($endpoint);

        // 1. Check DB Cache FIRST (48 hours expiry) - No waiting if cached
        $stmt = $db->prepare("SELECT response, updated_at FROM jikan_cache WHERE cache_key = ? LIMIT 1");
        $stmt->execute([$cacheKey]);
        $cached = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cached) {
            $updatedAt = strtotime($cached['updated_at']);
            if ((time() - $updatedAt) < (48 * 3600)) {
                echo $cached['response'];
                exit;
            }
        }

        // 2. Rate Limiting (Using temp files for atomicity across processes)
        $lockDir = sys_get_temp_dir() . '/webanime_locks_v3';
        if (!is_dir($lockDir)) {
            @mkdir($lockDir, 0777, true);
        }

        $lockFile = $lockDir . '/jikan.lock';
        $lastReqFile = $lockDir . '/last_req.txt';

        $fp = @fopen($lockFile, 'w+');
        if ($fp) {
            if (@flock($fp, LOCK_EX)) {
                $lastReq = file_exists($lastReqFile) ? (float) @file_get_contents($lastReqFile) : 0;
                $now = microtime(true);
                if ($now - $lastReq < 0.4) {
                    usleep((int) ((0.4 - ($now - $lastReq)) * 1000000));
                }
                @file_put_contents($lastReqFile, microtime(true));
                @flock($fp, LOCK_UN);
            }
            @fclose($fp);
        }

        // 3. Fetch from Jikan
        $url = 'https://api.jikan.moe/v4/' . ltrim($endpoint, '/');

        // Auto-append SFW filter for searches/lists if not present
        if (preg_match('/(anime|manga|top|seasons|search)/i', $url) && strpos($url, 'sfw=') === false) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'sfw=1';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'NekoraList WebAnime Proxy / 2.0 (DB Cache)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($response)) {
            $filteredResponse = $this->filterResponse($response);
            // 4. Save to DB Cache
            $save = $db->prepare("REPLACE INTO jikan_cache (cache_key, endpoint, response) VALUES (?, ?, ?)");
            $save->execute([$cacheKey, $endpoint, $filteredResponse]);
            echo $filteredResponse;
        } else if ($httpCode === 429) {
            http_response_code(429);
            echo json_encode(['error' => 'Rate limited by Jikan']);
        } else {
            // Graceful fallback to expired cache if available
            if ($cached) {
                echo $this->filterResponse($cached['response']);
            } else {
                http_response_code($httpCode ?: 500);
                echo $response ? $response : json_encode(['error' => 'Jikan API failed and no cache available']);
            }
        }

    }

    private function filterResponse($json)
    {
        $data = json_decode($json, true);
        if (!$data || !isset($data['data']))
            return $json;

        $items = $data['data'];
        $isSingle = isset($items['mal_id']); // check if it's a single item (not search list)

        if ($isSingle) {
            if ($this->isRestricted($items)) {
                return json_encode(['data' => null]);
            }
            return $json;
        }

        $filtered = array_values(array_filter($items, function ($item) {
            return !$this->isRestricted($item);
        }));

        $data['data'] = $filtered;
        return json_encode($data);
    }

    private function isRestricted($item)
    {
        if (!$item)
            return false;

        // 1. Title Check
        $title = strtolower($item['title'] ?? $item['title_english'] ?? '');
        foreach ($this->restrictedTitles as $restricted) {
            if (strpos($title, $restricted) !== false)
                return true;
        }

        // 2. Genre Check
        $genres = array_merge($item['genres'] ?? [], $item['explicit_genres'] ?? []);
        foreach ($genres as $genre) {
            $name = $genre['name'] ?? '';
            if (in_array($name, $this->restrictedGenres))
                return true;
        }

        // 3. Rating Check
        $rating = $item['rating'] ?? '';
        if (strpos($rating, 'Rx') !== false || strpos($rating, 'Hentai') !== false)
            return true;

        return false;
    }
}
