<?php
$ids = [62841,2471,1943,23755,22535,33674];
foreach ($ids as $id) {
  $url = 'http://localhost/WebAnime/public/api/jikan_proxy.php?endpoint=' . urlencode('anime/' . $id . '/full');
  $json = @file_get_contents($url);
  if ($json === false) {
    echo $id . ' => fetch_failed' . PHP_EOL;
    continue;
  }
  $data = json_decode($json, true);
  $studios = $data['data']['studios'] ?? [];
  $names = array_map(fn($s) => $s['name'] ?? '', $studios);
  echo $id . ' => ' . json_encode($names, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
