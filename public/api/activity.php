<?php
require_once __DIR__ . '/../../app/bootstrap.php';
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? 'Invitado';

// Modo Invitado no guarda nada
if (!$userId || $role === 'Invitado') {
    echo json_encode(['success' => true, 'message' => 'Modo invitado: no se registra actividad']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

if (!$action) {
    echo json_encode(['success' => false, 'error' => 'Acción no especificada']);
    exit;
}

$dbConn = (new \Models\Database())->getConnection();
if (!$dbConn) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión']);
    exit;
}

try {
    if ($action === 'view') {
        $animeId = $data['anime_id'] ?? null;
        if ($animeId) {
            $stmt = $dbConn->prepare("INSERT INTO usuarios_vistas (usuario_id, anime_id) VALUES (?, ?)");
            $stmt->execute([$userId, $animeId]);
        }
    } elseif ($action === 'report') {
        $animeId = $data['anime_id'] ?? null;
        $message = $data['message'] ?? '';
        if ($animeId && $message) {
            $stmt = $dbConn->prepare("INSERT INTO usuarios_reportes (usuario_id, anime_id, mensaje) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $animeId, $message]);
        }
    } else {
        // Actividad general
        $details = $data['details'] ?? '';
        $stmt = $dbConn->prepare("INSERT INTO usuarios_actividad (usuario_id, accion, detalles) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $action, $details]);
    }
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
