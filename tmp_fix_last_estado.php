<?php
$pdo = new PDO('mysql:host=localhost;dbname=Webanime;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("UPDATE anime SET estado = 'Finalizado' WHERE LOWER(estado) = 'finished'");
$stmt = $pdo->query("SELECT estado, COUNT(*) AS total FROM anime GROUP BY estado ORDER BY total DESC");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo ($row['estado'] ?? 'NULL') . ' => ' . $row['total'] . PHP_EOL;
}
