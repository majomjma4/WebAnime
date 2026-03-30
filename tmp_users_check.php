<?php
require 'app/bootstrap.php';
$db = (new \Models\Database())->getConnection();
$q = $db->query("SELECT u.id, u.nombre_mostrar, COALESCE(r.nombre,'') AS rol FROM usuarios u LEFT JOIN roles r ON r.id=u.rol_id ORDER BY u.id");
foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
  echo $row['id'] . ' | ' . $row['nombre_mostrar'] . ' | ' . $row['rol'] . PHP_EOL;
}
