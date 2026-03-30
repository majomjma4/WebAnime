<?php
$pdo = new PDO('mysql:host=localhost;dbname=Webanime;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
foreach (['anime','anime_studios','studios'] as $table) {
  echo "-- $table --\n";
  try {
    $stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
      echo $row['Field'] . ' | ' . $row['Type'] . "\n";
    }
  } catch (Exception $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
  }
}
