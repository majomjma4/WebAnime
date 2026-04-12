<?php
require_once __DIR__ . '/app/bootstrap.php';
use Models\Database;

$db = (new Database())->getConnection();
if (!$db) {
    die("Error connecting to database");
}

echo "Total records in 'anime' table: " . $db->query("SELECT COUNT(*) FROM anime")->fetchColumn() . "\n";

echo "\nLatest 5 records:\n";
$stmt = $db->query("SELECT id, mal_id, titulo, creado_en FROM anime ORDER BY id DESC LIMIT 5");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "ID: {$row['id']} | MAL_ID: {$row['mal_id']} | Title: {$row['titulo']} | Created: {$row['creado_en']}\n";
}

echo "\nChecking for records with mal_id = 0:\n";
echo "Count: " . $db->query("SELECT COUNT(*) FROM anime WHERE mal_id = 0")->fetchColumn() . "\n";

echo "\nChecking table columns for 'anime':\n";
$stmt = $db->query("DESCRIBE anime");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "Field: {$row['Field']} | Type: {$row['Type']}\n";
}
