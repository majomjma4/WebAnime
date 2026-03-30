<?php
$pdo = new PDO('mysql:host=localhost;dbname=Webanime;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->query("SELECT id, titulo, mal_id, tipo, estado, anio, creado_en FROM anime WHERE titulo LIKE '%Kimetsu%' OR titulo LIKE '%Demon Slayer%' ORDER BY id DESC");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
