<?php
require __DIR__ . '/app/bootstrap.php';
$db = (new \Models\Database())->getConnection();
$stmt = $db->query("SELECT id, mal_id, titulo, puntuacion, anio FROM anime WHERE tipo != 'Movie' AND id NOT IN (SELECT anime_id FROM anime_generos WHERE genero_id IN (SELECT id FROM generos WHERE nombre IN ('Hentai', 'Erotica', 'Ecchi', 'Yaoi', 'Yuri', 'Girls Love', 'Boys Love'))) ORDER BY puntuacion DESC, id DESC LIMIT 20");
foreach ($stmt as $row) {
  echo implode(' | ', [$row['id'], $row['mal_id'], $row['titulo'], $row['puntuacion'], $row['anio']]) . PHP_EOL;
}
?>
