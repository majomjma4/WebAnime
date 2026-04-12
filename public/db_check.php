<?php
require_once __DIR__ . '/app/bootstrap.php';
header('Content-Type: text/html; charset=UTF-8');

try {
    $db = (new \Models\Database())->getConnection();
    if (!$db) throw new Exception("No se pudo conectar a la base de datos.");

    // 1. Contador Real
    $total = $db->query("SELECT COUNT(*) FROM anime")->fetchColumn();
    $activos = $db->query("SELECT COUNT(*) FROM anime WHERE activo = 1")->fetchColumn();
    
    // 2. Otras Tablas
    $chars = $db->query("SELECT COUNT(*) FROM anime_characters")->fetchColumn();
    $pics = $db->query("SELECT COUNT(*) FROM anime_pictures")->fetchColumn();
    $gens = $db->query("SELECT COUNT(*) FROM anime_generos")->fetchColumn();

    echo "<h1>Auditoría de Base de Datos - WebAnime</h1>";
    echo "<ul>";
    echo "<li><b>Total Animes:</b> $total</li>";
    echo "<li><b>Animes Activos:</b> $activos</li>";
    echo "<li><b>Total Personajes:</b> $chars</li>";
    echo "<li><b>Total Imágenes:</b> $pics</li>";
    echo "<li><b>Total Relaciones Género:</b> $gens</li>";
    echo "</ul>";

    // 3. Últimos 10 agregados
    $lastTen = $db->query("SELECT id, mal_id, titulo, creado_en FROM anime ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Últimos 10 Animes en la DB</h2>";
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr><th>ID DB</th><th>MAL ID</th><th>Título</th><th>Fecha Creación</th></tr>";
    foreach ($lastTen as $anime) {
        echo "<tr>";
        echo "<td>{$anime['id']}</td>";
        echo "<td>{$anime['mal_id']}</td>";
        echo "<td>" . htmlspecialchars($anime['titulo']) . "</td>";
        echo "<td>{$anime['creado_en']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<p><br><i>Este archivo es temporal para diagnóstico.</i></p>";

} catch (Exception $e) {
    echo "<h1>Error de Diagnóstico</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
