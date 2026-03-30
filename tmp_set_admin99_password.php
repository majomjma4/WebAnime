<?php
require 'app/bootstrap.php';
$db = (new \Models\Database())->getConnection();
$hash = password_hash('1221', PASSWORD_DEFAULT);
$stmt = $db->prepare("UPDATE usuarios SET hash_password = ? WHERE nombre_mostrar = ?");
$stmt->execute([$hash, 'Admin99']);
echo 'updated=' . $stmt->rowCount();
