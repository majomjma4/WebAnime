<?php
require_once __DIR__ . '/../../app/bootstrap.php';
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'No session']);
    exit;
}

$dbConn = (new \Models\Database())->getConnection();
$action = $_GET['action'] ?? 'get';

try {
    if ($action === 'get') {
        // Obtenemos los meta del usuario (nombre, desc, color, etc)
        $stmt = $dbConn->prepare("SELECT meta_key, meta_value FROM usuarios_meta WHERE usuario_id = ?");
        $stmt->execute([$userId]);
        $meta = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        echo json_encode([
            'success' => true,
            'data' => [
                'profile' => [
                    'name' => $meta['profile_name'] ?? $_SESSION['username'],
                    'desc' => $meta['profile_desc'] ?? '',
                    'color' => $meta['profile_color'] ?? '',
                    'avatar' => $meta['profile_avatar'] ?? '',
                    'member_since' => $meta['profile_member_since'] ?? date('Y')
                ],
                'my_list' => json_decode($meta['my_list'] ?? '[]', true),
                'favorites' => json_decode($meta['favorites'] ?? '[]', true),
                'status' => json_decode($meta['status_list'] ?? '{}', true)
            ]
        ]);
    } elseif ($action === 'save') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) throw new Exception("No data provided");

        $dbConn->beginTransaction();
        $stmt = $dbConn->prepare("INSERT INTO usuarios_meta (usuario_id, meta_key, meta_value) 
                                 VALUES (?, ?, ?) 
                                 ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value)");
        
        foreach ($data as $key => $value) {
            $valStr = is_array($value) ? json_encode($value) : $value;
            $stmt->execute([$userId, $key, $valStr]);
        }
        $dbConn->commit();
        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    if ($dbConn->inTransaction()) $dbConn->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
