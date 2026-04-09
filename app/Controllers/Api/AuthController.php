<?php
namespace Controllers\Api;

use Controllers\Controller;
use Exception;
use PDO;

class AuthController extends Controller
{
    public function handle(): void
    {
        $wantsRedirect = isset($_GET['redirect']) && $_GET['redirect'] === '1';
        if (!$wantsRedirect) {
            header('Content-Type: application/json; charset=UTF-8');
        }

        app_start_session();

        $action = (string) ($_GET['action'] ?? '');
        $data = app_get_json_input();

        if ($action === '') {
            echo json_encode(['success' => false, 'error' => 'Peticion invalida']);
            exit;
        }

        $readOnlyActions = ['check'];
        if (in_array($action, $readOnlyActions, true)) {
            app_require_method('GET');
        } else {
            app_require_method('POST');
            app_verify_csrf();
        }

        $dbConn = (new \Models\Database())->getConnection();
        try {
            $dbConn?->exec("UPDATE usuarios SET activo = 1, bloqueado = 0, bloqueado_en = NULL, motivo_bloqueo = NULL, penalizacion_hasta = NULL WHERE bloqueado = 1 AND penalizacion_hasta IS NOT NULL AND penalizacion_hasta <= NOW()");
        } catch (Exception $e) {
        }

        $generatePublicUserCode = static function (PDO $dbConn): string {
            do {
                $code = 'NK-' . random_int(100000, 999999);
                $stmt = $dbConn->prepare("SELECT COUNT(*) FROM usuarios WHERE codigo_publico = ?");
                $stmt->execute([$code]);
            } while ((int) $stmt->fetchColumn() > 0);

            return $code;
        };

        if (!$dbConn) {
            echo json_encode(['success' => false, 'error' => 'Error de conexion a la base de datos']);
            exit;
        }

        if ($action === 'register') {
            $username = trim((string) ($data['username'] ?? ''));
            $email = trim((string) ($data['email'] ?? ''));
            $pass = (string) ($data['password'] ?? '');

            if ($username === '' || $email === '' || $pass === '') {
                echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
                exit;
            }

            $stmt = $dbConn->prepare("SELECT id FROM usuarios WHERE correo = ? OR nombre_mostrar = ?");
            $stmt->execute([$email, $username]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'El usuario o correo ya esta registrado']);
                exit;
            }

            try {
                $dbConn->beginTransaction();
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $publicCode = $generatePublicUserCode($dbConn);
                $stmt = $dbConn->prepare("INSERT INTO usuarios (codigo_publico, correo, hash_password, nombre_mostrar, activo, creado_en) VALUES (?, ?, ?, ?, 1, NOW())");
                $stmt->execute([$publicCode, $email, $hash, $username]);
                $userId = $dbConn->lastInsertId();

                $roleStmt = $dbConn->prepare("SELECT id FROM roles WHERE nombre = 'Registrado'");
                $roleStmt->execute();
                $roleId = $roleStmt->fetchColumn();
                if ($roleId) {
                    $dbConn->prepare("UPDATE usuarios SET rol_id = ? WHERE id = ?")->execute([$roleId, $userId]);
                }
                $dbConn->commit();

                session_regenerate_id(true);
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'Registrado';

                $dbConn->prepare("INSERT INTO usuarios_sesiones (usuario_id, inicio) VALUES (?, NOW())")->execute([$userId]);
                $_SESSION['session_log_id'] = $dbConn->lastInsertId();

                echo json_encode(['success' => true, 'username' => $username, 'role' => 'Registrado', 'userId' => $userId, 'publicUserId' => $publicCode]);
            } catch (Exception $e) {
                if ($dbConn->inTransaction()) {
                    $dbConn->rollBack();
                }
                echo json_encode(['success' => false, 'error' => 'Error al registrar el usuario: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'login') {
            $userOrEmail = trim((string) ($data['userOrEmail'] ?? ''));
            $pass = (string) ($data['password'] ?? '');

            if ($userOrEmail === '' || $pass === '') {
                echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
                exit;
            }

            $stmt = $dbConn->prepare("SELECT id, codigo_publico, hash_password, nombre_mostrar, es_premium, premium_vence_en, activo, bloqueado, motivo_bloqueo, penalizacion_hasta, password_actualizado_en FROM usuarios WHERE correo = ? OR nombre_mostrar = ?");
            $stmt->execute([$userOrEmail, $userOrEmail]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo json_encode(['success' => false, 'error' => 'Usuario no encontrado.']);
            } elseif (!password_verify($pass, $user['hash_password'])) {
                $lastPasswordChange = !empty($user['password_actualizado_en']) ? date('d/m/Y H:i', strtotime((string) $user['password_actualizado_en'])) : '';
                $errorMessage = 'La contraseña es incorrecta.';
                if ($lastPasswordChange !== '') {
                    $errorMessage .= ' La contraseña fue cambiada el ' . $lastPasswordChange . '.';
                }
                echo json_encode(['success' => false, 'error' => $errorMessage]);
            } elseif (((int) ($user['bloqueado'] ?? 0) === 1) || ((int) ($user['activo'] ?? 1) !== 1)) {
                $penaltyUntil = $user['penalizacion_hasta'] ? date('d/m/Y H:i', strtotime($user['penalizacion_hasta'])) : 'Permanente';
                $reason = trim((string) ($user['motivo_bloqueo'] ?? '')) ?: 'Sin motivo registrado';
                echo json_encode([
                    'success' => false,
                    'blocked' => true,
                    'error' => 'Tu cuenta esta bloqueada. Motivo: ' . $reason . '. Penalizacion: ' . $penaltyUntil,
                    'reason' => $reason,
                    'penaltyUntil' => $penaltyUntil,
                ]);
            } else {
                $roleStmt = $dbConn->prepare("SELECT nombre FROM roles WHERE id = (SELECT rol_id FROM usuarios WHERE id = ?)");
                $roleStmt->execute([$user['id']]);
                $role = $roleStmt->fetchColumn() ?: 'Registrado';
                $isPremium = ($role === 'Admin' || ($user['es_premium'] == 1 && (is_null($user['premium_vence_en']) || strtotime($user['premium_vence_en']) > time())));

                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['nombre_mostrar'];
                $_SESSION['role'] = $role;
                $_SESSION['premium'] = $isPremium;

                $dbConn->prepare("INSERT INTO usuarios_sesiones (usuario_id, inicio) VALUES (?, NOW())")->execute([$user['id']]);
                $_SESSION['session_log_id'] = $dbConn->lastInsertId();

                echo json_encode([
                    'success' => true,
                    'username' => $user['nombre_mostrar'],
                    'userId' => $user['id'],
                    'publicUserId' => $user['codigo_publico'],
                    'role' => $role,
                    'isAdmin' => ($role === 'Admin'),
                    'isPremium' => $isPremium,
                ]);
            }
            exit;
        }

        if ($action === 'check') {
            if (isset($_SESSION['user_id'])) {
                $username = $_SESSION['username'] ?? 'Usuario';
                $role = $_SESSION['role'] ?? 'Registrado';

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
                    'isPremium' => $isPremium,
                ]);
            } else {
                echo json_encode(['logged' => false, 'role' => 'Invitado', 'isPremium' => false]);
            }
            exit;
        }

        if ($action === 'buy_premium') {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión para comprar Premium']);
                exit;
            }

            try {
                $stmt = $dbConn->prepare("UPDATE usuarios SET es_premium = 1, premium_vence_en = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE id = ?");
                $stmt->execute([$userId]);
                $_SESSION['premium'] = true;
                echo json_encode(['success' => true, 'message' => 'Premium activado por 30 dias']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'logout') {
            if (isset($_SESSION['session_log_id'])) {
                try {
                    $dbConn->prepare("UPDATE usuarios_sesiones SET fin = NOW(), duracion = TIMESTAMPDIFF(SECOND, inicio, NOW()) WHERE id = ?")->execute([$_SESSION['session_log_id']]);
                } catch (Exception $e) {
                }
            }

            session_unset();
            session_destroy();
            setcookie(session_name(), '', time() - 3600, '/');
            setcookie('XSRF-TOKEN', '', time() - 3600, '/');

            if ($wantsRedirect) {
                header('Location: ../index?logout=1');
                exit;
            }

            echo json_encode(['success' => true]);
            exit;
        }

        if ($action === 'forgot_password') {
            $userOrEmail = trim((string) ($data['userOrEmail'] ?? ''));
            if ($userOrEmail === '') {
                echo json_encode(['success' => false, 'error' => 'Por favor compartenos tu correo o usuario.']);
                exit;
            }

            $stmt = $dbConn->prepare("SELECT id FROM usuarios WHERE correo = ? OR nombre_mostrar = ?");
            $stmt->execute([$userOrEmail, $userOrEmail]);
            echo json_encode($stmt->fetch() ? ['success' => true] : ['success' => false, 'error' => 'No encontramos ninguna cuenta con esos datos.']);
            exit;
        }

        echo json_encode(['success' => false, 'error' => 'Accion desconocida']);
    }
}


