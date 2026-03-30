<?php
$pdo = new PDO('mysql:host=localhost;dbname=Webanime;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$before = $pdo->query("SELECT estado, COUNT(*) AS total FROM anime GROUP BY estado ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);
echo "ANTES\n";
foreach ($before as $row) {
    echo ($row['estado'] ?? 'NULL') . ' => ' . $row['total'] . PHP_EOL;
}
$pdo->exec("UPDATE anime SET estado = 'Finalizado' WHERE LOWER(estado) = 'finished airing'");
$pdo->exec("UPDATE anime SET estado = 'En emision' WHERE LOWER(estado) IN ('currently airing', 'en emision')");
$pdo->exec("UPDATE anime SET estado = 'Proximamente' WHERE LOWER(estado) = 'not yet aired'");
$after = $pdo->query("SELECT estado, COUNT(*) AS total FROM anime GROUP BY estado ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);
echo "DESPUES\n";
foreach ($after as $row) {
    echo ($row['estado'] ?? 'NULL') . ' => ' . $row['total'] . PHP_EOL;
}
