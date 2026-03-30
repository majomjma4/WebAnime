<?php
require __DIR__ . '/app/bootstrap.php';
$db = (new \Models\Database())->getConnection();
$stmt = $db->query("SELECT id, titulo, tipo, estado, anio, creado_en FROM anime ORDER BY id DESC LIMIT 15");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
