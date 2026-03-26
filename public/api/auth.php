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

if ($action === 'register') {
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $pass = $data['password'] ?? '';

    if (!$username || !$email || !$pass) {
        echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
        exit;
    }

    // Check if user exists
    $stmt = $dbConn->prepare("SELECT id FROM usuarios WHERE correo = ? OR nombre_mostrar = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'El usuario o correo ya está registrado']);
        exit;
    }

    // Insert user
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $dbConn->prepare("INSERT INTO usuarios (correo, hash_password, nombre_mostrar, activo, creado_en) VALUES (?, ?, ?, 1, NOW())");
    $success = $stmt->execute([$email, $hash, $username]);

    if ($success) {
        echo json_encode(['success' => true, 'username' => $username, 'isAdmin' => false]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al registrar el usuario']);
    }
} elseif ($action === 'login') {
    $userOrEmail = trim($data['userOrEmail'] ?? '');
    $pass = $data['password'] ?? '';

    if (!$userOrEmail || !$pass) {
        echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
        exit;
    }

    $stmt = $dbConn->prepare("SELECT id, hash_password, nombre_mostrar FROM usuarios WHERE correo = ? OR nombre_mostrar = ?");
    $stmt->execute([$userOrEmail, $userOrEmail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($pass, $user['hash_password'])) {
        // Check if user is admin
        $adminStmt = $dbConn->prepare("SELECT rol_id FROM usuarios_roles WHERE usuario_id = ? AND rol_id = (SELECT id FROM roles WHERE nombre = 'Admin')");
        $adminStmt->execute([$user['id']]);
        $isAdmin = $adminStmt->fetch() ? true : false;
        
        // Also support the hardcoded admin test for dev
        if ($user['nombre_mostrar'] === 'Admin99') {
            $isAdmin = true;
        }

        echo json_encode(['success' => true, 'username' => $user['nombre_mostrar'], 'isAdmin' => $isAdmin]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas']);
    }
}
