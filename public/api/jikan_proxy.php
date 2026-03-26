<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$endpoint = $_GET['endpoint'] ?? '';
if (empty($endpoint)) {
    http_response_code(400);
    echo json_encode(['error' => 'Endpoint is required']);
    exit;
}

$cacheDir = __DIR__ . '/../../cache/jikan';
if (!is_dir($cacheDir)) {
    @mkdir($cacheDir, 0777, true);
}

// Generate a safe filename for the cache based on the endpoint path
$cacheKey = md5($endpoint);
$cacheFile = $cacheDir . '/' . $cacheKey . '.json';

// Serve from cache if available and younger than 48 hours
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < (48 * 3600)) {
    $data = @file_get_contents($cacheFile);
    // ensure valid JSON in cache
    if (!empty($data) && $data[0] === '{') {
        echo $data;
        exit;
    }
}

// Ensure rate limiting: only 1 request per 400ms across PHP sessions to avoid strict 429s from Jikan
$lockFile = $cacheDir . '/jikan.lock';
$fp = @fopen($lockFile, 'w+');
if ($fp) {
    if (@flock($fp, LOCK_EX)) {
        $lastReqFile = $cacheDir . '/last_request.txt';
        $lastReq = file_exists($lastReqFile) ? (float)@file_get_contents($lastReqFile) : 0;
        $now = microtime(true);
        if ($now - $lastReq < 0.4) {
            usleep((int)((0.4 - ($now - $lastReq)) * 1000000));
        }
        @file_put_contents($lastReqFile, microtime(true));
        @flock($fp, LOCK_UN);
    }
    @fclose($fp);
}

$url = 'https://api.jikan.moe/v4/' . ltrim($endpoint, '/');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'NekoraList WebAnime Proxy / 1.0 (Cache Enabled)');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && !empty($response)) {
    @file_put_contents($cacheFile, $response);
    echo $response;
} else if ($httpCode === 429) {
    // If rate limited, simply wait and redirect self to try again, or pass the 429.
    http_response_code(429);
    echo json_encode(['error' => 'Rate limited heavily, even behind proxy']);
} else {
    // If we have an expired cache file on generic error, serve it gracefully
    if (file_exists($cacheFile)) {
        echo @file_get_contents($cacheFile);
    } else {
        http_response_code($httpCode ?: 500);
        echo $response ? $response : json_encode(['error' => 'Jikan API failed']);
    }
}
