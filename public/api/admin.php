<?php
require_once __DIR__ . '/../../app/bootstrap.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true);

if (!$action || !$data) {
    echo json_encode(['success' => false, 'error' => 'Petición inválida']);
    exit;
}

$dbConn = (new \Models\Database())->getConnection();
if (!$dbConn) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

// Security: In a real app, verify admin session here.
// For now, we trust the frontend request (similar to the js mock).

if ($action === 'add_anime') {
    $titulo = trim($data['titulo'] ?? '');
    $sinopsis = trim($data['sinopsis'] ?? '');
    $estado = trim($data['estado'] ?? '');
    $temporada = trim($data['temporada'] ?? '');
    $anio = (int)($data['anio'] ?? 0);
    $episodios = (int)($data['episodios'] ?? 0);
    $imagen_url = trim($data['imagen_url'] ?? '');
    $generos = $data['generos'] ?? []; // Array of string

    if (!$titulo) {
        echo json_encode(['success' => false, 'error' => 'El título es obligatorio']);
        exit;
    }

    try {
        $dbConn->beginTransaction();

        $stmt = $dbConn->prepare("INSERT INTO anime (titulo, tipo, estado, episodios, temporada, anio, sinopsis, imagen_url, puntuacion, activo, creado_en) VALUES (?, 'TV', ?, ?, ?, ?, ?, ?, 0.0, 1, NOW())");
        $stmt->execute([$titulo, $estado, $episodios, $temporada, $anio, $sinopsis, $imagen_url]);
        
        $anime_id = $dbConn->lastInsertId();

        // Handle genres
        foreach ($generos as $g_name) {
            // Check if genre exists
            $gStmt = $dbConn->prepare("SELECT id FROM generos WHERE nombre = ?");
            $gStmt->execute([$g_name]);
            $g_row = $gStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($g_row) {
                $g_id = $g_row['id'];
            } else {
                // Insert genre
                $iStmt = $dbConn->prepare("INSERT INTO generos (nombre) VALUES (?)");
                $iStmt->execute([$g_name]);
                $g_id = $dbConn->lastInsertId();
            }

            // Link genre
            $lStmt = $dbConn->prepare("INSERT INTO anime_generos (anime_id, genero_id) VALUES (?, ?)");
            $lStmt->execute([$anime_id, $g_id]);
        }

        $dbConn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $dbConn->rollBack();
        echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
    }
}
