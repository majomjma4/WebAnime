<?php
$pdo = new PDO('mysql:host=localhost;dbname=Webanime;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query("SELECT id, mal_id, titulo FROM anime WHERE (estudio IS NULL OR estudio = '') AND mal_id IS NOT NULL AND mal_id > 0 ORDER BY id DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updated = 0;
$stillMissing = 0;
$checked = 0;

$updateStmt = $pdo->prepare("UPDATE anime SET estudio = ? WHERE id = ?");

foreach ($rows as $row) {
    $checked++;
    $url = 'http://localhost/WebAnime/public/api/jikan_proxy.php?endpoint=' . urlencode('anime/' . (int)$row['mal_id'] . '/full');
    $json = @file_get_contents($url);
    if ($json === false) {
        $stillMissing++;
        continue;
    }

    $data = json_decode($json, true);
    $studios = $data['data']['studios'] ?? [];
    $names = [];
    foreach ($studios as $studio) {
        $name = trim((string)($studio['name'] ?? ''));
        if ($name !== '') {
            $names[] = $name;
        }
    }
    $names = array_values(array_unique($names));

    if (!$names) {
        $stillMissing++;
        continue;
    }

    $estudio = implode(', ', $names);
    $updateStmt->execute([$estudio, (int)$row['id']]);
    $updated++;
}

echo json_encode([
    'checked' => $checked,
    'updated' => $updated,
    'still_missing' => $stillMissing,
], JSON_UNESCAPED_UNICODE) . PHP_EOL;
