<?php
require_once __DIR__ . '/../../app/bootstrap.php';
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true);

if (!$action && ($action !== 'check')) {
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

    $stmt = $dbConn->prepare("SELECT id FROM usuarios WHERE correo = ? OR nombre_mostrar = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'El usuario o correo ya está registrado']);
        exit;
    }

    try {
        $dbConn->beginTransaction();
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $dbConn->prepare("INSERT INTO usuarios (correo, hash_password, nombre_mostrar, activo, creado_en) VALUES (?, ?, ?, 1, NOW())");
        $stmt->execute([$email, $hash, $username]);
        $userId = $dbConn->lastInsertId();

        // Asignar rol Registrado por defecto
        $roleStmt = $dbConn->prepare("SELECT id FROM roles WHERE nombre = 'Registrado'");
        $roleStmt->execute();
        $roleId = $roleStmt->fetchColumn();
        
        if ($roleId) {
            $dbConn->prepare("UPDATE usuarios SET rol_id = ? WHERE id = ?")->execute([$roleId, $userId]);
        }
        $dbConn->commit();
        
        // Inicializar sesión inmediatamente tras registro exitoso
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'Registrado';

        // Registrar inicio de sesión en BD
        $dbConn->prepare("INSERT INTO usuarios_sesiones (usuario_id, inicio) VALUES (?, NOW())")->execute([$userId]);
        $_SESSION['session_log_id'] = $dbConn->lastInsertId();

        echo json_encode(['success' => true, 'username' => $username, 'role' => 'Registrado']);
    } catch (Exception $e) {
        $dbConn->rollBack();
        echo json_encode(['success' => false, 'error' => 'Error al registrar el usuario: ' . $e->getMessage()]);
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

    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado. ¿Ya tienes una cuenta?']);
    } elseif (!password_verify($pass, $user['hash_password'])) {
        echo json_encode(['success' => false, 'error' => 'La contraseña es incorrecta.']);
    } else {
        // Obtener el rol del usuario directamente de la columna rol_id
        $roleStmt = $dbConn->prepare("
            SELECT nombre 
            FROM roles 
            WHERE id = (SELECT rol_id FROM usuarios WHERE id = ?)
        ");
        $roleStmt->execute([$user['id']]);
        $role = $roleStmt->fetchColumn() ?: 'Registrado';
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['nombre_mostrar'];
        $_SESSION['role'] = $role;

        // Registrar inicio de sesión para tiempo
        $dbConn->prepare("INSERT INTO usuarios_sesiones (usuario_id, inicio) VALUES (?, NOW())")->execute([$user['id']]);
        $_SESSION['session_log_id'] = $dbConn->lastInsertId();

        echo json_encode([
            'success' => true, 
            'username' => $user['nombre_mostrar'], 
            'role' => $role,
            'isAdmin' => ($role === 'Admin')
        ]);
    }
} elseif ($action === 'check') {
    if (isset($_SESSION['user_id'])) {
        $username = $_SESSION['username'] ?? 'Usuario';
        $role = $_SESSION['role'] ?? 'Registrado';
        echo json_encode([
            'logged' => true,
            'username' => $username,
            'role' => $role,
            'isAdmin' => ($role === 'Admin')
        ]);
    } else {
        echo json_encode(['logged' => false, 'role' => 'Invitado']);
    }
} elseif ($action === 'logout') {
    if (isset($_SESSION['session_log_id'])) {
        // Calcular tiempo final
        try {
            $dbConn->prepare("UPDATE usuarios_sesiones SET fin = NOW(), duracion = TIMESTAMPDIFF(SECOND, inicio, NOW()) WHERE id = ?")
                   ->execute([$_SESSION['session_log_id']]);
        } catch (Exception $e) {
            // Ignorar el error del log para no bloquear el cierre de sesión
        }
    }
    session_unset();
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
    echo json_encode(['success' => true]);
} elseif ($action === 'forgot_password') {
    $userOrEmail = trim($data['userOrEmail'] ?? '');
    if (!$userOrEmail) {
        echo json_encode(['success' => false, 'error' => 'Por favor compártenos tu correo o usuario.']);
        exit;
    }
    
    $stmt = $dbConn->prepare("SELECT id FROM usuarios WHERE correo = ? OR nombre_mostrar = ?");
    $stmt->execute([$userOrEmail, $userOrEmail]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No encontramos ninguna cuenta con esos datos.']);
    }
}
