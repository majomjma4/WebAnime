<?php
require_once __DIR__ . '/../../app/bootstrap.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['mal_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

$dbConn = (new \Models\Database())->getConnection();
if (!$dbConn) {
    echo json_encode(['success' => false, 'error' => 'DB Connection Error']);
    exit;
}

$mal_id = (int)$data['mal_id'];
$titulo = trim($data['title_english'] ?? $data['title'] ?? '');
if (!$titulo) $titulo = $data['title'] ?? 'Unknown';

// Block +18 content
$rating = $data['rating'] ?? '';
if (stripos($rating, 'Rx') !== false || stripos($rating, 'Hentai') !== false || stripos($rating, 'Erotica') !== false) {
    echo json_encode(['success' => false, 'error' => 'Contenido restringido (+18) no permitido.']);
    exit;
}

$stmt = $dbConn->prepare("SELECT id FROM anime WHERE mal_id = ? LIMIT 1");
$stmt->execute([$mal_id]);
$existingAnime = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$existingAnime) {
    $stmt = $dbConn->prepare("SELECT id FROM anime WHERE titulo = ? LIMIT 1");
    $stmt->execute([$titulo]);
    $existingAnime = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($existingAnime) {
        $upStmt = $dbConn->prepare("UPDATE anime SET mal_id = ? WHERE id = ?");
        $upStmt->execute([$mal_id, $existingAnime['id']]);
    }
}

$new_id = $existingAnime ? $existingAnime['id'] : null;

if (!$new_id) {
    // Procede con el INSERT principal (línea 51 aprox)
} else {
    // Ya tenemos $new_id, podemos saltar a la ingesta de media
}

$tipo = $data['type'] ?? 'TV';
$estudio = '';
if (!empty($data['studios']) && is_array($data['studios'])) {
    $studioNames = array_filter(array_map(static function ($studio) {
        return trim((string) ($studio['name'] ?? ''));
    }, $data['studios']));
    $estudio = implode(', ', $studioNames);
}
$estadoRaw = trim((string) ($data['status'] ?? 'Unknown'));
$estadoLower = strtolower($estadoRaw);
if ($estadoLower === 'finished airing') {
    $estado = 'Finalizado';
} elseif ($estadoLower === 'currently airing' || $estadoLower === 'en emision') {
    $estado = 'En emision';
} elseif ($estadoLower === 'not yet aired') {
    $estado = 'Proximamente';
} else {
    $estado = $estadoRaw;
}
$episodios = (int)($data['episodes'] ?? 0);
$temporada = $data['season'] ?? 'Unknown';
$anio = (int)($data['year'] ?? 0);
if ($anio === 0 && isset($data['aired']['prop']['from']['year'])) {
    $anio = (int)$data['aired']['prop']['from']['year'];
}
$sinopsis = $data['synopsis'] ?? '';
$imagen_url = $data['images']['jpg']['large_image_url'] ?? $data['images']['jpg']['image_url'] ?? '';
$puntuacion = (float)($data['score'] ?? 0.0);

$dbConn->beginTransaction();
try {
    if (!$new_id) {
        $stmt = $dbConn->prepare("INSERT INTO anime (mal_id, titulo, tipo, estudio, estado, episodios, temporada, anio, sinopsis, imagen_url, puntuacion, activo, creado_en) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$mal_id, $titulo, $tipo, $estudio, $estado, $episodios, $temporada, $anio, $sinopsis, $imagen_url, $puntuacion]);
        $new_id = $dbConn->lastInsertId();

        $generos = $data['genres'] ?? [];
        foreach ($generos as $g) {
            $g_name = $g['name'];
            $gStmt = $dbConn->prepare("SELECT id FROM generos WHERE nombre = ?");
            $gStmt->execute([$g_name]);
            $g_row = $gStmt->fetch(PDO::FETCH_ASSOC);
            if ($g_row) {
                $g_id = $g_row['id'];
            } else {
                $iStmt = $dbConn->prepare("INSERT INTO generos (nombre) VALUES (?)");
                $iStmt->execute([$g_name]);
                $g_id = $dbConn->lastInsertId();
            }
            $lStmt = $dbConn->prepare("INSERT INTO anime_generos (anime_id, genero_id) VALUES (?, ?)");
            $lStmt->execute([$new_id, $g_id]);
        }
    }
    // Si ya existía, simplemente nos aseguramos que $new_id esté poblado (ya lo está arriba)


    $dbConn->commit();

    // Secondary Data Ingestion (Characters, Pictures, Videos)
    // We do this after the main transaction to ensure the anime exists.
    
    // Characters: keep only the first 10 principal entries from Jikan
    if (isset($data['characters']) && is_array($data['characters'])) {
        $characters = array_values(array_filter($data['characters'], static function ($character) {
            return !empty($character['character']['mal_id']);
        }));
        $characters = array_slice($characters, 0, 10);

        $deleteCharsStmt = $dbConn->prepare("DELETE FROM anime_characters WHERE anime_id = ?");
        $deleteCharsStmt->execute([$new_id]);

        $charStmt = $dbConn->prepare("INSERT INTO anime_characters (anime_id, mal_id, name, role, image_url) VALUES (?, ?, ?, ?, ?)");
        foreach ($characters as $c) {
            $c_mal_id = (int) ($c['character']['mal_id'] ?? 0);
            $c_name = trim((string) ($c['character']['name'] ?? 'Unknown'));
            $c_role = trim((string) ($c['role'] ?? 'Supporting'));
            $c_img = (string) ($c['character']['images']['jpg']['image_url'] ?? '');
            $charStmt->execute([$new_id, $c_mal_id, $c_name, $c_role, $c_img]);
        }
    }

    // Pictures
    if (isset($data['pictures']) && is_array($data['pictures'])) {
        $deletePicsStmt = $dbConn->prepare("DELETE FROM anime_pictures WHERE anime_id = ?");
        $deletePicsStmt->execute([$new_id]);

        $picStmt = $dbConn->prepare("INSERT INTO anime_pictures (anime_id, image_url) VALUES (?, ?)");
        foreach ($data['pictures'] as $p) {
            $p_url = $p['jpg']['large_image_url'] ?? $p['jpg']['image_url'] ?? '';
            if ($p_url) $picStmt->execute([$new_id, $p_url]);
        }
    }

    // Videos
    if (isset($data['videos']) && is_array($data['videos']['promo'])) {
        $deleteVideosStmt = $dbConn->prepare("DELETE FROM anime_videos WHERE anime_id = ?");
        $deleteVideosStmt->execute([$new_id]);

        $vidStmt = $dbConn->prepare("INSERT INTO anime_videos (anime_id, youtube_id, url, image_url) VALUES (?, ?, ?, ?)");
        foreach ($data['videos']['promo'] as $v) {
            $v_yt = $v['trailer']['youtube_id'] ?? '';
            $v_url = $v['trailer']['url'] ?? '';
            $v_img = $v['trailer']['images']['maximum_image_url'] ?? $v['trailer']['images']['large_image_url'] ?? '';
            if ($v_yt || $v_url) $vidStmt->execute([$new_id, $v_yt, $v_url, $v_img]);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Inserted new anime with deep data']);
} catch (Exception $e) {
    $dbConn->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

