<?php
require 'app/bootstrap.php';
$db = (new \Models\Database())->getConnection();
$count = $db->query('SELECT COUNT(*) FROM anime')->fetchColumn();
echo "TOTAL_COUNT: " . $count . "\n";
$last = $db->query('SELECT id, mal_id, titulo, anio, estado FROM anime ORDER BY id DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
foreach($last as $row) {
    echo "ID: {$row['id']} | MAL: {$row['mal_id']} | T: {$row['titulo']} | Y: {$row['anio']} | E: {$row['estado']}\n";
}
