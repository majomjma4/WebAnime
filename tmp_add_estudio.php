<?php
$pdo = new PDO('mysql:host=localhost;dbname=Webanime;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$columns = $pdo->query("SHOW COLUMNS FROM anime LIKE 'estudio'")->fetchAll(PDO::FETCH_ASSOC);
if (!$columns) {
    $pdo->exec("ALTER TABLE anime ADD COLUMN estudio VARCHAR(255) NULL AFTER tipo");
    echo "added\n";
} else {
    echo "exists\n";
}
