<?php
require 'app/bootstrap.php';
$db = (new \Models\Database())->getConnection();
$cols = $db->query("SHOW COLUMNS FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
echo "COLUMNAS_USUARIOS" . PHP_EOL;
foreach ($cols as $c) {
  echo $c['Field'] . ' | ' . $c['Type'] . PHP_EOL;
}
echo PHP_EOL . "USUARIOS_CLAVE" . PHP_EOL;
$q = $db->query("SELECT id, nombre_mostrar, rol_id, es_premium, premium_vence_en, activo, bloqueado, motivo_bloqueo FROM usuarios ORDER BY id");
foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
  echo implode(' | ', [
    'id=' . $row['id'],
    'user=' . $row['nombre_mostrar'],
    'rol_id=' . ($row['rol_id'] ?? 'NULL'),
    'premium=' . ($row['es_premium'] ?? 'NULL'),
    'vence=' . ($row['premium_vence_en'] ?? 'NULL'),
    'activo=' . ($row['activo'] ?? 'NULL'),
    'bloqueado=' . ($row['bloqueado'] ?? 'NULL')
  ]) . PHP_EOL;
}
