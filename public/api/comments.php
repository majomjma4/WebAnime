<?php
require_once __DIR__ . '/../../app/bootstrap.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';
$data = json_decode(file_get_contents('php://input'), true);

$dbConn = (new \Models\Database())->getConnection();
if (!$dbConn) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

if ($action === 'list') {
    try {
        $stmt = $dbConn->query("
            SELECT c.id, c.cuerpo as msg, 0 as flagged, c.creado_en,
                   u.nombre_mostrar as user,
                   a.titulo as anime,
                   (SELECT r.nombre FROM roles r INNER JOIN usuarios_roles ur ON ur.rol_id = r.id WHERE ur.usuario_id = u.id LIMIT 1) as raw_role
            FROM comentarios c
            INNER JOIN usuarios u ON c.usuario_id = u.id
            INNER JOIN anime a ON c.anime_id = a.id
            ORDER BY c.creado_en DESC
        ");
        
        $comments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $date = date('M d, Y h:i A', strtotime($row['creado_en']));
            $roleStr = $row['raw_role'] === 'Admin' ? 'ADMINISTRADOR' : 'REGULAR';
            
            $comments[] = [
                'id' => $row['id'],
                'user' => '@' . $row['user'],
                'tag' => $row['flagged'] ? 'BANEADO / REVISIÓN' : $roleStr,
                'anime' => $row['anime'],
                'ep' => 'General Discussion',
                'msg' => $row['msg'],
                'date' => $date,
                'flagged' => (bool)$row['flagged']
            ];
        }
        
        echo json_encode(['success' => true, 'data' => $comments]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} 
elseif ($action === 'delete') {
    $commentId = (int)($data['id'] ?? 0);
    
    if ($commentId <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit;
    }
    
    try {
        $stmt = $dbConn->prepare("DELETE FROM comentarios WHERE id = ?");
        $stmt->execute([$commentId]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
elseif ($action === 'seed') {
    try {
        $dbConn->exec("
            INSERT INTO comentarios (usuario_id, anime_id, cuerpo)
            SELECT u.id, a.id, '¡La animación en esta escena es absolutamente espectacular! MAPPA God.'
            FROM usuarios u CROSS JOIN anime a LIMIT 1;
            
            INSERT INTO comentarios (usuario_id, anime_id, cuerpo)
            SELECT u.id, a.id, 'El desarrollo del villano es espectacular. 10/10'
            FROM usuarios u CROSS JOIN anime a ORDER BY u.id DESC, a.id DESC LIMIT 1;
        ");
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
else {
    echo json_encode(['success' => false, 'error' => 'Acción desconocida']);
}
