<?php
$pdo = new PDO('mysql:host=localhost;dbname=Webanime;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->query("SHOW TABLES");
foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $table) {
  if (stripos($table, 'studio') !== false || stripos($table, 'producer') !== false || stripos($table, 'licensor') !== false || stripos($table, 'anime_') !== false) {
    echo $table, PHP_EOL;
  }
}
