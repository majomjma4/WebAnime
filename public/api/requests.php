<?php
require_once __DIR__ . '/../../app/bootstrap.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) $data = [];

if (!$action) {
    echo json_encode(['success' => false, 'error' => 'AcciÃ³n invÃ¡lida']);
    exit;
}

$dbConn = (new \Models\Database())->getConnection();
if (!$dbConn) {
    echo json_encode(['success' => false, 'error' => 'Error de conexiÃ³n a la base de datos']);
    exit;
}

function ensureRequestsTable(PDO $dbConn) {
    $dbConn->exec("
        CREATE TABLE IF NOT EXISTS solicitudes_anime (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            user_name VARCHAR(120) NULL,
            titulo VARCHAR(255) NOT NULL,
            tipo VARCHAR(50) NOT NULL DEFAULT 'Anime',
            fuente VARCHAR(255) NULL,
            estado ENUM('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
            creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            resuelto_en DATETIME NULL,
            resuelto_por INT NULL,
            INDEX idx_estado (estado),
            INDEX idx_creado (creado_en),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

function normalizeSearchTerm(string $value): string
{
    $value = trim(mb_strtolower($value, 'UTF-8'));
    $map = [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ú' => 'u',
        'ü' => 'u',
    ];

    return strtr($value, $map);
}

ensureRequestsTable($dbConn);

if ($action === 'create') {
    $titulo = trim($data['titulo'] ?? '');
    $tipo = trim($data['tipo'] ?? 'Anime');
    $fuente = trim($data['fuente'] ?? '');

    if (!$titulo) {
        echo json_encode(['success' => false, 'error' => 'El t?tulo es obligatorio']);
        exit;
    }

    $userId = $_SESSION['user_id'] ?? null;
    $userName = $_SESSION['username'] ?? null;
    $isPremium = $_SESSION['premium'] ?? false;
    if (!$userId) {
        echo json_encode(['success' => false, 'error' => 'Debes iniciar sesi?n']);
        exit;
    }
    if (!$isPremium) {
        echo json_encode(['success' => false, 'error' => 'Solo usuarios Premium pueden enviar solicitudes']);
        exit;
    }

    try {
        $stmt = $dbConn->prepare("
            INSERT INTO solicitudes_anime (user_id, user_name, titulo, tipo, fuente, estado, creado_en)
            VALUES (?, ?, ?, ?, ?, 'pendiente', NOW())
        ");
        $stmt->execute([$userId, $userName, $titulo, $tipo, $fuente ?: null]);
        echo json_encode(['success' => true, 'id' => $dbConn->lastInsertId()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'list') {
    $status = $_GET['status'] ?? 'pendiente';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $size = max(1, min(50, (int)($_GET['size'] ?? 4)));
    $q = trim($_GET['q'] ?? '');
    $mine = ($_GET['mine'] ?? '') === '1';

    $where = "r.estado = ?";
    $params = [$status];

    if ($mine) {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión']);
            exit;
        }
        $where .= " AND r.user_id = ?";
        $params[] = $userId;
    }
    if ($q !== '') {
        $normalizedQ = normalizeSearchTerm($q);
        $searchClauses = [
            "r.titulo LIKE ?",
            "r.user_name LIKE ?",
            "DATE_FORMAT(r.creado_en, '%d/%m/%Y') LIKE ?",
            "DATE_FORMAT(r.creado_en, '%Y-%m-%d') LIKE ?",
            "DATE_FORMAT(r.creado_en, '%d-%m-%Y') LIKE ?",
            "DATE_FORMAT(r.creado_en, '%d %m %Y') LIKE ?",
            "DATE_FORMAT(r.creado_en, '%d/%m/%Y %H:%i') LIKE ?",
        ];
        $like = '%' . $q . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;

        $monthMap = [
            'enero' => 1,
            'febrero' => 2,
            'marzo' => 3,
            'abril' => 4,
            'mayo' => 5,
            'junio' => 6,
            'julio' => 7,
            'agosto' => 8,
            'septiembre' => 9,
            'setiembre' => 9,
            'octubre' => 10,
            'noviembre' => 11,
            'diciembre' => 12,
        ];

        foreach ($monthMap as $monthName => $monthNumber) {
            if (strpos($monthName, $normalizedQ) !== false) {
                $searchClauses[] = "MONTH(r.creado_en) = ?";
                $params[] = $monthNumber;
            }
        }

        $where .= " AND (" . implode(' OR ', $searchClauses) . ")";
    }

    $countStmt = $dbConn->prepare("SELECT COUNT(*) AS total FROM solicitudes_anime r WHERE $where");
    $countStmt->execute($params);
    $total = (int)($countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    $offset = ($page - 1) * $size;
    $listStmt = $dbConn->prepare("
        SELECT r.id, r.titulo, r.tipo, r.fuente, r.estado, r.creado_en,
               COALESCE(u.nombre_mostrar, r.user_name, 'Usuario') AS user_display
        FROM solicitudes_anime r
        LEFT JOIN usuarios u ON u.id = r.user_id
        WHERE $where
        ORDER BY r.creado_en DESC
        LIMIT $size OFFSET $offset
    ");
    $listStmt->execute($params);
    $items = $listStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'total' => $total,
        'page' => $page,
        'size' => $size,
        'items' => $items
    ]);
    exit;
}

if ($action === 'decide') {
    $id = (int)($data['id'] ?? 0);
    $decision = $data['decision'] ?? '';
    if (!$id || !in_array($decision, ['aprobado', 'rechazado'], true)) {
        echo json_encode(['success' => false, 'error' => 'Datos invÃ¡lidos']);
        exit;
    }

    $adminId = $_SESSION['user_id'] ?? null;
    try {
        $stmt = $dbConn->prepare("
            UPDATE solicitudes_anime
            SET estado = ?, resuelto_en = NOW(), resuelto_por = ?
            WHERE id = ?
        ");
        $stmt->execute([$decision, $adminId, $id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete') {
    $id = (int)($data['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit;
    }

    try {
        $stmt = $dbConn->prepare("DELETE FROM solicitudes_anime WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'bulk_approve') {
    $adminId = $_SESSION['user_id'] ?? null;
    try {
        $stmt = $dbConn->prepare("
            UPDATE solicitudes_anime
            SET estado = 'aprobado', resuelto_en = NOW(), resuelto_por = ?
            WHERE estado = 'pendiente'
        ");
        $stmt->execute([$adminId]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'AcciÃ³n no soportada']);

