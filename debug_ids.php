<?php
require_once 'app/bootstrap.php';
use Models\Database;
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT id, titulo, mal_id FROM anime WHERE titulo LIKE '%Shingeki%' OR titulo LIKE '%Frieren%' LIMIT 15");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $r) {
    echo "ID: " . $r['id'] . " | MAL_ID: " . $r['mal_id'] . " | TITLE: " . $r['titulo'] . "\n";
}
