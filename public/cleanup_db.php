<?php
require_once __DIR__ . '/../app/bootstrap.php';
use Models\Database;

$db = (new Database())->getConnection();
if (!$db) die("ERR_DB_CONNECT");

echo "<pre>--- LIMPIEZA DE BASE DE DATOS ---\n";

// 1. Identificar registros fantasma (mal_id = 0)
$ghosts = $db->query("SELECT id, titulo FROM anime WHERE mal_id = 0 OR mal_id IS NULL")->fetchAll();
$count = count($ghosts);

if ($count > 0) {
    echo "Se encontraron $count registros con mal_id inválido.\n";
    foreach ($ghosts as $g) {
        echo "Eliminando registro huérfano: ID {$g['id']} - " . htmlspecialchars($g['titulo'] ?? 'Sin Titulo') . "\n";
        
        $db->prepare("DELETE FROM anime_generos WHERE anime_id = ?")->execute([$g['id']]);
        $db->prepare("DELETE FROM anime_characters WHERE anime_id = ?")->execute([$g['id']]);
        $db->prepare("DELETE FROM anime_pictures WHERE anime_id = ?")->execute([$g['id']]);
        $db->prepare("DELETE FROM anime_videos WHERE anime_id = ?")->execute([$g['id']]);
        $db->prepare("DELETE FROM anime_episodes WHERE anime_id = ?")->execute([$g['id']]);
        $db->prepare("DELETE FROM anime WHERE id = ?")->execute([$g['id']]);
    }
    echo "Limpieza completada.\n";
} else {
    echo "No se encontraron registros con mal_id = 0. La base de datos está limpia de fantasmas.\n";
}

// 2. Informe final de conteo
$newTotal = $db->query("SELECT COUNT(*) FROM anime")->fetchColumn();
echo "CONTEO FINAL DE ANIMES: $newTotal\n";

// 3. Verificar duplicados
$dupes = $db->query("SELECT mal_id, COUNT(*) as c FROM anime GROUP BY mal_id HAVING c > 1 AND mal_id > 0")->fetchAll();
if (count($dupes) > 0) {
    echo "\nADVERTENCIA: Se encontraron mal_id duplicados (estos deben corregirse manualmente o abriendo sus detalles):\n";
    foreach ($dupes as $d) {
        echo "MAL_ID: {$d['mal_id']} aparece {$d['c']} veces.\n";
    }
}
echo "</pre>";
// No eliminamos el archivo todavía para que el usuario pueda cargarlo en el navegador si el CLI falla
