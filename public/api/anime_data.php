<?php
require_once __DIR__ . '/../../app/bootstrap.php';
header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');
$mal_id = trim($_GET['mal_id'] ?? '');

$dbConn = (new \Models\Database())->getConnection();
if (!$dbConn) {
    echo json_encode(['success' => false, 'error' => 'DB Connection Error']);
    exit;
}

$animeItem = null;

if ($q) {
    $stmt = $dbConn->prepare("SELECT * FROM anime WHERE titulo = ? LIMIT 1");
    $stmt->execute([$q]);
    $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$animeItem && $mal_id) {
    $stmt = $dbConn->prepare("SELECT * FROM anime WHERE id = ? LIMIT 1");
    $stmt->execute([$mal_id]);
    $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$animeItem) {
    echo json_encode(['success' => false, 'error' => 'Not found in local DB']);
    exit;
}

$jikanData = [
    'mal_id' => $animeItem['id'],
    'title' => $animeItem['titulo'],
    'title_english' => $animeItem['titulo'],
    'title_japanese' => $animeItem['titulo'],
    'type' => $animeItem['tipo'],
    'episodes' => (int)$animeItem['episodios'],
    'status' => $animeItem['estado'],
    'year' => (int)$animeItem['anio'],
    'season' => strtolower($animeItem['temporada']),
    'score' => (float)$animeItem['puntuacion'],
    'synopsis' => $animeItem['sinopsis'],
    'images' => [
        'jpg' => [
            'large_image_url' => $animeItem['imagen_url'],
            'image_url' => $animeItem['imagen_url']
        ]
    ],
    'genres' => [],
    'studios' => [['name' => 'Local']]
];

$genreStmt = $dbConn->prepare("
    SELECT g.nombre 
    FROM generos g
    JOIN anime_generos ag ON g.id = ag.genero_id
    WHERE ag.anime_id = ?
");
$genreStmt->execute([$animeItem['id']]);
while ($g = $genreStmt->fetch(PDO::FETCH_ASSOC)) {
    $jikanData['genres'][] = ['name' => $g['nombre']];
}

echo json_encode(['success' => true, 'data' => $jikanData]);
