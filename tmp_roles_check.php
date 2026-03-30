<?php
require 'app/bootstrap.php';
$db = (new \Models\Database())->getConnection();
$q1 = $db->query("SELECT id, nombre FROM roles ORDER BY id");
echo "ROLES" . PHP_EOL;
foreach ($q1->fetchAll(PDO::FETCH_ASSOC) as $row) {
  echo $row['id'] . ' | ' . $row['nombre'] . PHP_EOL;
}
echo PHP_EOL . "USUARIOS" . PHP_EOL;
$q2 = $db->query("SELECT id, nombre_mostrar, rol_id FROM usuarios ORDER BY id");
foreach ($q2->fetchAll(PDO::FETCH_ASSOC) as $row) {
  echo $row['id'] . ' | ' . $row['nombre_mostrar'] . ' | rol_id=' . $row['rol_id'] . PHP_EOL;
}
