<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = (new \Models\Database())->getConnection();
$count = $db->query("SELECT COUNT(*) FROM anime")->fetchColumn();
echo "<h1>CONTEO REAL EN DB: $count</h1>";
$last = $db->query("SELECT id, mal_id, titulo FROM anime ORDER BY id DESC LIMIT 5")->fetchAll();
echo "<pre>";
print_r($last);
echo "</pre>";
unlink(__FILE__);
