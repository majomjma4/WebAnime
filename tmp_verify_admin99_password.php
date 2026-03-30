<?php
require 'app/bootstrap.php';
$db = (new \Models\Database())->getConnection();
$stmt = $db->prepare("SELECT id, hash_password FROM usuarios WHERE nombre_mostrar = ? LIMIT 1");
$stmt->execute(['Admin99']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo 'id=' . $row['id'] . PHP_EOL;
echo 'valid=' . (password_verify('1221', $row['hash_password']) ? 'yes' : 'no') . PHP_EOL;
