<?php
namespace Controllers\Api;

use Controllers\Controller;
use PDO;
use Exception;
use Throwable;
class AuthController extends Controller
{
    public function handle(): void
    {
        $wantsRedirect = isset($_GET['redirect']) && $_GET['redirect'] === '1';
        if (!$wantsRedirect) {
            header('Content-Type: application/json');
        }
        
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
        try {
            $dbConn?->exec("UPDATE usuarios SET activo = 1, bloqueado = 0, bloqueado_en = NULL, motivo_bloqueo = NULL, penalizacion_hasta = NULL WHERE bloqueado = 1 AND penalizacion_hasta IS NOT NULL AND penalizacion_hasta <= NOW()");
        } catch (Exception $e) {
            // Si falla el mantenimiento automatico, no detenemos la autenticacion.
        }
        function generatePublicUserCode(PDO $dbConn): string {
            do {
                $code = 'NK-' . random_int(100000, 999999);
                $stmt = $dbConn->prepare("SELECT COUNT(*) FROM usuarios WHERE codigo_publico = ?");
                $stmt->execute([$code]);
            } while ((int) $stmt->fetchColumn() > 0);
        
            return $code;
        }
        
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
                $publicCode = generatePublicUserCode($dbConn);
                $stmt = $dbConn->prepare("INSERT INTO usuarios (codigo_publico, correo, hash_password, nombre_mostrar, activo, creado_en) VALUES (?, ?, ?, ?, 1, NOW())");
                $stmt->execute([$publicCode, $email, $hash, $username]);
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
        
                echo json_encode(['success' => true, 'username' => $username, 'role' => 'Registrado', 'userId' => $userId, 'publicUserId' => $publicCode]);
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
        
            $stmt = $dbConn->prepare("SELECT id, codigo_publico, hash_password, nombre_mostrar, es_premium, premium_vence_en, activo, bloqueado, motivo_bloqueo, penalizacion_hasta FROM usuarios WHERE correo = ? OR nombre_mostrar = ?");
            $stmt->execute([$userOrEmail, $userOrEmail]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if (!$user) {
                echo json_encode(['success' => false, 'error' => 'Usuario no encontrado. ??Ya tienes una cuenta?']);
            } elseif (!password_verify($pass, $user['hash_password'])) {
                echo json_encode(['success' => false, 'error' => 'La contrase??a es incorrecta.']);
            } elseif (((int)($user['bloqueado'] ?? 0) === 1) || ((int)($user['activo'] ?? 1) !== 1)) {
                $penaltyUntil = $user['penalizacion_hasta'] ? date('d/m/Y H:i', strtotime($user['penalizacion_hasta'])) : 'Permanente';
                $reason = trim((string)($user['motivo_bloqueo'] ?? '')) ?: 'Sin motivo registrado';
                echo json_encode([
                    'success' => false,
                    'blocked' => true,
                    'error' => ('Tu cuenta esta bloqueada. Motivo: ' . $reason . '. Penalizacion: ' . $penaltyUntil),
                    'reason' => $reason,
                    'penaltyUntil' => $penaltyUntil
                ]);
            } else {
                $roleStmt = $dbConn->prepare("
                    SELECT nombre 
                    FROM roles 
                    WHERE id = (SELECT rol_id FROM usuarios WHERE id = ?)
                ");
                $roleStmt->execute([$user['id']]);
                $role = $roleStmt->fetchColumn() ?: 'Registrado';
                
                $isPremium = ($role === 'Admin' || ($user['es_premium'] == 1 && (is_null($user['premium_vence_en']) || strtotime($user['premium_vence_en']) > time())));
        
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['nombre_mostrar'];
                $_SESSION['role'] = $role;
                $_SESSION['premium'] = $isPremium;
        
                // Registrar inicio de sesión para tiempo
                $dbConn->prepare("INSERT INTO usuarios_sesiones (usuario_id, inicio) VALUES (?, NOW())")->execute([$user['id']]);
                $_SESSION['session_log_id'] = $dbConn->lastInsertId();
        
                echo json_encode([
                    'success' => true, 
                    'username' => $user['nombre_mostrar'], 
                    'userId' => $user['id'],
                    'publicUserId' => $user['codigo_publico'],
                    'role' => $role,
                    'isAdmin' => ($role === 'Admin'),
                    'isPremium' => $isPremium
                ]);
            }
        } elseif ($action === 'check') {
            if (isset($_SESSION['user_id'])) {
                $username = $_SESSION['username'] ?? 'Usuario';
                $role = $_SESSION['role'] ?? 'Registrado';
                
                // Re-verificar premium desde la DB para estar seguros
                $stmt = $dbConn->prepare("SELECT codigo_publico, es_premium, premium_vence_en FROM usuarios WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $uInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $isPremium = ($role === 'Admin' || ($uInfo && $uInfo['es_premium'] == 1 && (is_null($uInfo['premium_vence_en']) || strtotime($uInfo['premium_vence_en']) > time())));
                $_SESSION['premium'] = $isPremium;
        
                echo json_encode([
                    'logged' => true,
                    'username' => $username,
                    'userId' => $_SESSION['user_id'],
                    'publicUserId' => ($uInfo['codigo_publico'] ?? null),
                    'role' => $role,
                    'isAdmin' => ($role === 'Admin'),
                    'isPremium' => $isPremium
                ]);
            } else {
                echo json_encode(['logged' => false, 'role' => 'Invitado', 'isPremium' => false]);
            }
        } elseif ($action === 'buy_premium') {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión para comprar Premium']);
                exit;
            }
        
            try {
                // Activar premium por 30 días
                $stmt = $dbConn->prepare("UPDATE usuarios SET es_premium = 1, premium_vence_en = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE id = ?");
                $stmt->execute([$userId]);
                
                $_SESSION['premium'] = true;
                
                echo json_encode(['success' => true, 'message' => '¡Premium activado por 30 días!']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
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
            if ($wantsRedirect) {
                header('Location: ../index?logout=1');
                exit;
            }
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
        
        
        
        
    }
}
