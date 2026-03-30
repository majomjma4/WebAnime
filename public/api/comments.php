<?php
require_once __DIR__ . '/../../app/bootstrap.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';
$data = json_decode(file_get_contents('php://input'), true);

if (($action === 'report' || $action === 'delete' || $action === 'add' || $action === 'moderate') && session_status() === PHP_SESSION_NONE) {
    session_name('NekoraSession_V1');
    @session_start();
}

$dbConn = (new \Models\Database())->getConnection();
if (!$dbConn) {
    echo json_encode(['success' => false, 'error' => 'Error de conexion a la base de datos']);
    exit;
}

if ($action === 'list') {
    try {
        $animeMalId = isset($_GET['anime_mal_id']) ? (int) $_GET['anime_mal_id'] : 0;
        $sql = "
            SELECT c.id, c.cuerpo AS msg, c.puntuacion AS rating,
                   CASE WHEN rr.report_count > 0 THEN 1 ELSE 0 END AS flagged,
                   c.creado_en, c.autor_externo,
                   u.nombre_mostrar AS user,
                   u.es_premium,
                   a.titulo AS anime,
                   a.mal_id AS anime_mal_id,
                   c.fuente AS source,
                   COALESCE(r.nombre, 'Registrado') AS raw_role,
                   COALESCE(rr.report_count, 0) AS report_count,
                   COALESCE(lr.razon, '') AS report_reason,
                   COALESCE(reporter.nombre_mostrar, '') AS reported_by,
                   COALESCE(dr.estado, '') AS deleted_status,
                   COALESCE(deleter.nombre_mostrar, '') AS deleted_by,
                   COALESCE(mr.estado, '') AS reviewed_status,
                   COALESCE(reviewer.nombre_mostrar, '') AS reviewed_by
            FROM comentarios c
            INNER JOIN usuarios u ON c.usuario_id = u.id
            INNER JOIN anime a ON c.anime_id = a.id
            LEFT JOIN roles r ON r.id = u.rol_id
            LEFT JOIN (
                SELECT comentario_id, COUNT(*) AS report_count
                FROM reportes_comentarios
                WHERE estado NOT IN ('eliminado_usuario', 'eliminado_admin')
                GROUP BY comentario_id
            ) rr ON rr.comentario_id = c.id
            LEFT JOIN (
                SELECT rc1.comentario_id, rc1.razon, rc1.reportado_por
                FROM reportes_comentarios rc1
                INNER JOIN (
                    SELECT comentario_id, MAX(id) AS max_id
                    FROM reportes_comentarios
                    WHERE estado NOT IN ('eliminado_usuario', 'eliminado_admin')
                    GROUP BY comentario_id
                ) latest ON latest.max_id = rc1.id
            ) lr ON lr.comentario_id = c.id
            LEFT JOIN usuarios reporter ON reporter.id = lr.reportado_por
            LEFT JOIN (
                SELECT rc2.comentario_id, rc2.estado, rc2.reportado_por
                FROM reportes_comentarios rc2
                INNER JOIN (
                    SELECT comentario_id, MAX(id) AS max_id
                    FROM reportes_comentarios
                    WHERE estado IN ('eliminado_usuario', 'eliminado_admin')
                    GROUP BY comentario_id
                ) deleted_latest ON deleted_latest.max_id = rc2.id
            ) dr ON dr.comentario_id = c.id
            LEFT JOIN usuarios deleter ON deleter.id = dr.reportado_por
            LEFT JOIN (
                SELECT rc3.comentario_id, rc3.estado, rc3.reportado_por
                FROM reportes_comentarios rc3
                INNER JOIN (
                    SELECT comentario_id, MAX(id) AS max_id
                    FROM reportes_comentarios
                    WHERE estado = 'revisado_admin'
                    GROUP BY comentario_id
                ) reviewed_latest ON reviewed_latest.max_id = rc3.id
            ) mr ON mr.comentario_id = c.id
            LEFT JOIN usuarios reviewer ON reviewer.id = mr.reportado_por
        ";

        if ($animeMalId > 0) {
            $sql .= " WHERE a.mal_id = :anime_mal_id
                      AND NOT EXISTS (
                          SELECT 1
                          FROM reportes_comentarios hidden_rc
                          WHERE hidden_rc.comentario_id = c.id
                            AND hidden_rc.estado IN ('eliminado_usuario', 'eliminado_admin')
                      )";
        }

        $sql .= " ORDER BY c.creado_en DESC";
        $stmt = $dbConn->prepare($sql);

        if ($animeMalId > 0) {
            $stmt->bindValue(':anime_mal_id', $animeMalId, PDO::PARAM_INT);
        }

        $stmt->execute();

        $comments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $date = date('M d, Y h:i A', strtotime($row['creado_en']));
            $isAdmin = strtolower((string) ($row['raw_role'] ?? '')) === 'admin';
            $isPremium = (int) ($row['es_premium'] ?? 0) === 1;
            $roleStr = $isAdmin ? 'ADMINISTRADOR' : ($isPremium ? 'PREMIUM' : 'REGISTRADO');
            $displayName = trim((string) ($row['source'] ?? '')) === 'jikan' && !empty($row['autor_externo'])
                ? $row['autor_externo']
                : $row['user'];

            $comments[] = [
                'id' => (int) $row['id'],
                'user' => '@' . $displayName,
                'tag' => $row['flagged'] ? 'REPORTADO' : $roleStr,
                'anime' => $row['anime'],
                'anime_mal_id' => (int) ($row['anime_mal_id'] ?? 0),
                'ep' => 'General Discussion',
                'msg' => $row['msg'],
                'rating' => (int) ($row['rating'] ?? 0),
                'source' => $row['source'] ?: 'usuario',
                'date' => $date,
                'flagged' => (bool) $row['flagged'],
                'report_count' => (int) ($row['report_count'] ?? 0),
                'report_reason' => $row['report_reason'] ?? '',
                'reported_by' => !empty($row['reported_by']) ? '@' . $row['reported_by'] : '',
                'deleted_status' => $row['deleted_status'] ?? '',
                'deleted_by' => !empty($row['deleted_by']) ? '@' . $row['deleted_by'] : '',
                'reviewed_status' => $row['reviewed_status'] ?? '',
                'reviewed_by' => !empty($row['reviewed_by']) ? '@' . $row['reviewed_by'] : ''
            ];
        }

        echo json_encode(['success' => true, 'data' => $comments]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'add') {
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $animeMalId = (int) ($data['anime_mal_id'] ?? 0);
    $rating = (int) ($data['rating'] ?? 0);
    $message = trim((string) ($data['message'] ?? ''));

    if ($userId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Debes iniciar sesion para comentar']);
        exit;
    }

    if ($animeMalId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Anime invalido']);
        exit;
    }

    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'error' => 'Selecciona una puntuacion valida']);
        exit;
    }

    if ($message === '') {
        echo json_encode(['success' => false, 'error' => 'Escribe un comentario antes de publicar']);
        exit;
    }

    try {
        $animeStmt = $dbConn->prepare("SELECT id FROM anime WHERE mal_id = ? OR id = ? LIMIT 1");
        $animeStmt->execute([$animeMalId, $animeMalId]);
        $animeId = (int) ($animeStmt->fetchColumn() ?: 0);

        if ($animeId <= 0) {
            echo json_encode(['success' => false, 'error' => 'No se encontro el anime en la base de datos']);
            exit;
        }

        $stmt = $dbConn->prepare("INSERT INTO comentarios (usuario_id, anime_id, cuerpo, puntuacion, fuente, creado_en) VALUES (?, ?, ?, ?, 'usuario', NOW())");
        $stmt->execute([$userId, $animeId, $message, $rating]);

        echo json_encode([
            'success' => true,
            'id' => (int) $dbConn->lastInsertId()
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'report') {
    $commentId = (int) ($data['comment_id'] ?? 0);
    $reason = trim((string) ($data['reason'] ?? ''));
    $reporterId = (int) ($_SESSION['user_id'] ?? 0);

    if ($reporterId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Debes iniciar sesion para reportar comentarios']);
        exit;
    }

    if ($commentId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Comentario invalido']);
        exit;
    }

    if ($reason === '') {
        echo json_encode(['success' => false, 'error' => 'Debes seleccionar una razon']);
        exit;
    }

    try {
        $checkStmt = $dbConn->prepare("SELECT id FROM comentarios WHERE id = ?");
        $checkStmt->execute([$commentId]);
        if (!$checkStmt->fetchColumn()) {
            echo json_encode(['success' => false, 'error' => 'El comentario no existe']);
            exit;
        }

        $dupStmt = $dbConn->prepare("SELECT id FROM reportes_comentarios WHERE comentario_id = ? AND reportado_por = ? AND estado IN ('pendiente', 'en_revision')");
        $dupStmt->execute([$commentId, $reporterId]);
        if ($dupStmt->fetchColumn()) {
            echo json_encode(['success' => false, 'error' => 'Ya reportaste este comentario']);
            exit;
        }

        $stmt = $dbConn->prepare("INSERT INTO reportes_comentarios (comentario_id, reportado_por, razon, estado, creado_en) VALUES (?, ?, ?, 'pendiente', NOW())");
        $stmt->execute([$commentId, $reporterId, $reason]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'delete') {
    $commentId = (int) ($data['id'] ?? 0);
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $role = (string) ($_SESSION['role'] ?? 'Registrado');

    if ($commentId <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID invalido']);
        exit;
    }

    if ($userId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Debes iniciar sesion para eliminar comentarios']);
        exit;
    }

    try {
        $ownerStmt = $dbConn->prepare("SELECT usuario_id FROM comentarios WHERE id = ?");
        $ownerStmt->execute([$commentId]);
        $ownerId = (int) ($ownerStmt->fetchColumn() ?: 0);

        if ($ownerId <= 0) {
            echo json_encode(['success' => false, 'error' => 'El comentario no existe']);
            exit;
        }

        $normalizedRole = strtolower(trim((string) $role));
        $isAdmin = in_array($normalizedRole, ['admin', 'administrador'], true);
        if ($ownerId !== $userId && !$isAdmin) {
            echo json_encode(['success' => false, 'error' => 'Solo puedes ocultar tus propios comentarios o administrar como admin']);
            exit;
        }

        $deleteState = $isAdmin ? 'eliminado_admin' : 'eliminado_usuario';
        $deleteReason = $isAdmin ? 'Comentario eliminado por administrador' : 'Comentario eliminado por su autor';

        $dupStmt = $dbConn->prepare("SELECT id FROM reportes_comentarios WHERE comentario_id = ? AND estado IN ('eliminado_usuario', 'eliminado_admin')");
        $dupStmt->execute([$commentId]);
        if (!$dupStmt->fetchColumn()) {
            $stmt = $dbConn->prepare("INSERT INTO reportes_comentarios (comentario_id, reportado_por, razon, estado, creado_en) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$commentId, $userId, $deleteReason, $deleteState]);
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'moderate') {
    $commentId = (int) ($data['id'] ?? 0);
    $decision = trim((string) ($data['decision'] ?? ''));
    $adminId = (int) ($_SESSION['user_id'] ?? 0);
    $role = strtolower(trim((string) ($_SESSION['role'] ?? '')));
    $isModerator = in_array($role, ['admin', 'administrador'], true);

    if ($commentId <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID invalido']);
        exit;
    }

    if ($adminId <= 0 || !$isModerator) {
        echo json_encode(['success' => false, 'error' => 'Solo un administrador puede moderar comentarios']);
        exit;
    }

    if (!in_array($decision, ['review', 'delete'], true)) {
        echo json_encode(['success' => false, 'error' => 'Decision invalida']);
        exit;
    }

    try {
        $checkStmt = $dbConn->prepare("SELECT id FROM comentarios WHERE id = ?");
        $checkStmt->execute([$commentId]);
        if (!$checkStmt->fetchColumn()) {
            echo json_encode(['success' => false, 'error' => 'El comentario no existe']);
            exit;
        }

        if ($decision === 'review') {
            $stmt = $dbConn->prepare("INSERT INTO reportes_comentarios (comentario_id, reportado_por, razon, estado, creado_en) VALUES (?, ?, ?, 'revisado_admin', NOW())");
            $stmt->execute([$commentId, $adminId, 'Comentario revisado por administrador']);
            echo json_encode(['success' => true, 'status' => 'revisado_admin']);
            exit;
        }

        $dupStmt = $dbConn->prepare("SELECT id FROM reportes_comentarios WHERE comentario_id = ? AND estado = 'eliminado_admin'");
        $dupStmt->execute([$commentId]);
        if (!$dupStmt->fetchColumn()) {
            $stmt = $dbConn->prepare("INSERT INTO reportes_comentarios (comentario_id, reportado_por, razon, estado, creado_en) VALUES (?, ?, ?, 'eliminado_admin', NOW())");
            $stmt->execute([$commentId, $adminId, 'Comentario eliminado por administrador']);
        }
        echo json_encode(['success' => true, 'status' => 'eliminado_admin']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'seed') {
    try {
        $dbConn->exec("
            INSERT INTO comentarios (usuario_id, anime_id, cuerpo)
            SELECT u.id, a.id, 'La animacion en esta escena es absolutamente espectacular! MAPPA God.'
            FROM usuarios u CROSS JOIN anime a LIMIT 1;

            INSERT INTO comentarios (usuario_id, anime_id, cuerpo)
            SELECT u.id, a.id, 'El desarrollo del villano es espectacular. 10/10'
            FROM usuarios u CROSS JOIN anime a ORDER BY u.id DESC, a.id DESC LIMIT 1;
        ");
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Accion desconocida']);
}


