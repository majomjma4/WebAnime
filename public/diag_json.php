<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = (new \Models\Database())->getConnection();
$stmt = $db->query("DESCRIBE anime");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
unlink(__FILE__);
