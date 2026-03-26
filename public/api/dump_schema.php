<?php
$mysqli = new mysqli("localhost", "root", "", "webanime");
if ($mysqli->connect_error) {
    die("Error connecting to DB");
}
$res = $mysqli->query("DESCRIBE anime");
$out = [];
while($row = $res->fetch_assoc()) $out[] = $row;
echo json_encode($out, JSON_PRETTY_PRINT);
