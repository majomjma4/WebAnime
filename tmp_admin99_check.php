<?php
require 'app/bootstrap.php';
$db = (new \Models\Database())->getConnection();
$stmt = $db->prepare("SELECT id, nombre_mostrar, rol_id, es_premium FROM usuarios WHERE nombre_mostrar = ? LIMIT 1");
$stmt->execute(['Admin99']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
var_export($row);
