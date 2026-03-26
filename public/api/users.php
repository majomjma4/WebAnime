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
        // Obtenemos todos los usuarios y verificamos si son admin a través de la relación de roles
        $stmt = $dbConn->query("
            SELECT u.id, u.nombre_mostrar as name, u.correo as email, u.creado_en, u.activo, 
                   COALESCE(r.nombre, 'Regular') as role
            FROM usuarios u
            LEFT JOIN usuarios_roles ur ON u.id = ur.usuario_id
            LEFT JOIN roles r ON ur.rol_id = r.id
            ORDER BY u.creado_en DESC
        ");
        
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Formatear la fecha a un string legible
            $date = date('M d, Y', strtotime($row['creado_en']));
            
            $users[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'role' => $row['role'],
                'email' => $row['email'],
                'date' => $date,
                'status' => $row['activo'] ? 'ACTIVO' : 'BLOQUEADO'
            ];
        }
        
        echo json_encode(['success' => true, 'data' => $users]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} 
elseif ($action === 'toggle_status') {
    $userId = (int)($data['id'] ?? 0);
    $status = (int)($data['activo'] ?? 1);
    
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID de usuario inválido']);
        exit;
    }
    
    try {
        $stmt = $dbConn->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
        $stmt->execute([$status, $userId]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
elseif ($action === 'delete') {
    $userId = (int)($data['id'] ?? 0);
    
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID de usuario inválido']);
        exit;
    }
    
    try {
        $dbConn->beginTransaction();
        // Delete roles relationship first to avoid foreign key constraints
        $dbConn->prepare("DELETE FROM usuarios_roles WHERE usuario_id = ?")->execute([$userId]);
        $dbConn->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$userId]);
        $dbConn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $dbConn->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
else {
    echo json_encode(['success' => false, 'error' => 'Acción desconocida']);
}
