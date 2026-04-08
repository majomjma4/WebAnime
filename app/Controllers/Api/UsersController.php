<?php
namespace Controllers\Api;

use Controllers\Controller;
use Exception;
use PDO;

class UsersController extends Controller
{
    public function handle(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        app_start_session();

        $action = (string) ($_GET['action'] ?? 'list');
        $writeActions = ['toggle_status', 'block', 'delete'];
        if (in_array($action, $writeActions, true)) {
            app_require_method('POST');
            app_verify_csrf();
        } else {
            app_require_method('GET');
        }

        $isAdmin = isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] === 'Admin';
        if (!$isAdmin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
            exit;
        }

        $data = app_get_json_input();
        $dbConn = (new \Models\Database())->getConnection();
        if (!$dbConn) {
            echo json_encode(['success' => false, 'error' => 'Error de conexion a la base de datos']);
            exit;
        }

        try {
            $dbConn->exec("UPDATE usuarios SET activo = 1, bloqueado = 0, bloqueado_en = NULL, motivo_bloqueo = NULL, penalizacion_hasta = NULL WHERE bloqueado = 1 AND penalizacion_hasta IS NOT NULL AND penalizacion_hasta <= NOW()");
        } catch (Exception $e) {
        }

        if ($action === 'list') {
            try {
                $stmt = $dbConn->query("SELECT u.id, u.codigo_publico AS public_id, u.nombre_mostrar AS name, u.correo AS email, u.creado_en, u.activo, u.bloqueado, u.motivo_bloqueo, u.penalizacion_hasta, u.es_premium, COALESCE(r.nombre, 'Registrado') AS base_role FROM usuarios u LEFT JOIN roles r ON r.id = u.rol_id WHERE LOWER(COALESCE(r.nombre, 'registrado')) <> 'admin' ORDER BY u.creado_en DESC");
                $users = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $isBlocked = ((int) $row['activo'] !== 1) || ((int) $row['bloqueado'] === 1);
                    $users[] = ['id' => (int) $row['id'], 'public_id' => $row['public_id'], 'name' => $row['name'] ?: 'Usuario', 'role' => ((int) $row['es_premium'] === 1) ? 'Premium' : 'Registrado', 'email' => $row['email'], 'date' => date('d/m/Y', strtotime($row['creado_en'])), 'status' => $isBlocked ? 'BLOQUEADO' : 'ACTIVO', 'is_premium' => ((int) $row['es_premium'] === 1), 'block_reason' => $row['motivo_bloqueo'] ?: '', 'penalty_until' => $row['penalizacion_hasta'] ? date('d/m/Y H:i', strtotime($row['penalizacion_hasta'])) : null];
                }

                $statsStmt = $dbConn->query("SELECT SUM(CASE WHEN LOWER(COALESCE(r.nombre, 'registrado')) <> 'admin' THEN 1 ELSE 0 END) AS total_users, SUM(CASE WHEN LOWER(COALESCE(r.nombre, 'registrado')) = 'admin' THEN 1 ELSE 0 END) AS admin_users, SUM(CASE WHEN LOWER(COALESCE(r.nombre, 'registrado')) <> 'admin' AND u.activo = 1 AND (u.bloqueado = 0 OR u.bloqueado IS NULL) THEN 1 ELSE 0 END) AS active_accounts, SUM(CASE WHEN LOWER(COALESCE(r.nombre, 'registrado')) <> 'admin' AND (u.activo <> 1 OR u.bloqueado = 1) THEN 1 ELSE 0 END) AS blocked_users, SUM(CASE WHEN LOWER(COALESCE(r.nombre, 'registrado')) <> 'admin' AND u.es_premium = 1 THEN 1 ELSE 0 END) AS premium_users, SUM(CASE WHEN LOWER(COALESCE(r.nombre, 'registrado')) <> 'admin' AND EXISTS (SELECT 1 FROM usuarios_sesiones us WHERE us.usuario_id = u.id AND us.fin IS NULL AND us.inicio >= DATE_SUB(NOW(), INTERVAL 24 HOUR)) THEN 1 ELSE 0 END) AS active_now FROM usuarios u LEFT JOIN roles r ON r.id = u.rol_id");
                $stats = $statsStmt->fetch(PDO::FETCH_ASSOC) ?: [];
                echo json_encode(['success' => true, 'data' => $users, 'stats' => ['total_users' => (int) ($stats['total_users'] ?? 0), 'admin_users' => (int) ($stats['admin_users'] ?? 0), 'active_accounts' => (int) ($stats['active_accounts'] ?? 0), 'blocked_users' => (int) ($stats['blocked_users'] ?? 0), 'premium_users' => (int) ($stats['premium_users'] ?? 0), 'active_now' => (int) ($stats['active_now'] ?? 0)]]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'toggle_status') {
            $userId = (int) ($data['id'] ?? 0);
            $status = (int) ($data['activo'] ?? 1);
            if ($userId <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID de usuario invalido']);
                exit;
            }
            try {
                $isBlocked = $status === 1 ? 0 : 1;
                $stmt = $dbConn->prepare("UPDATE usuarios SET activo = ?, bloqueado = ?, bloqueado_en = CASE WHEN ? = 1 THEN NOW() ELSE NULL END, motivo_bloqueo = CASE WHEN ? = 1 THEN motivo_bloqueo ELSE NULL END, penalizacion_hasta = CASE WHEN ? = 1 THEN penalizacion_hasta ELSE NULL END WHERE id = ?");
                $stmt->execute([$status, $isBlocked, $isBlocked, $isBlocked, $isBlocked, $userId]);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'block') {
            $userId = (int) ($data['id'] ?? 0);
            $presetReason = trim((string) ($data['reason'] ?? ''));
            $customReason = trim((string) ($data['custom_reason'] ?? ''));
            $penalty = trim((string) ($data['penalty'] ?? ''));
            if ($userId <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID de usuario invalido']);
                exit;
            }
            $finalReason = $customReason !== '' ? $customReason : $presetReason;
            if ($finalReason === '') {
                echo json_encode(['success' => false, 'error' => 'Debes indicar un motivo de bloqueo']);
                exit;
            }
            $penaltyUntil = null;
            $penaltyMap = ['24h' => '+1 day', '3d' => '+3 days', '7d' => '+7 days', '30d' => '+30 days', 'permanente' => null];
            if (!array_key_exists($penalty, $penaltyMap)) {
                echo json_encode(['success' => false, 'error' => 'Debes elegir un tiempo de penalizacion valido']);
                exit;
            }
            if ($penaltyMap[$penalty] !== null) {
                $penaltyUntil = date('Y-m-d H:i:s', strtotime($penaltyMap[$penalty]));
            }
            try {
                $stmt = $dbConn->prepare("UPDATE usuarios SET activo = 0, bloqueado = 1, bloqueado_en = NOW(), motivo_bloqueo = ?, penalizacion_hasta = ? WHERE id = ?");
                $stmt->execute([$finalReason, $penaltyUntil, $userId]);
                echo json_encode(['success' => true, 'penalty_until' => $penaltyUntil, 'reason' => $finalReason]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'delete') {
            $userId = (int) ($data['id'] ?? 0);
            if ($userId <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID de usuario invalido']);
                exit;
            }
            try {
                $dbConn->beginTransaction();
                $dbConn->prepare("DELETE FROM usuarios_meta WHERE usuario_id = ?")->execute([$userId]);
                $dbConn->prepare("DELETE FROM usuarios_actividad WHERE usuario_id = ?")->execute([$userId]);
                $dbConn->prepare("DELETE FROM usuarios_reportes WHERE usuario_id = ?")->execute([$userId]);
                $dbConn->prepare("DELETE FROM usuarios_sesiones WHERE usuario_id = ?")->execute([$userId]);
                $dbConn->prepare("DELETE FROM usuarios_vistas WHERE usuario_id = ?")->execute([$userId]);
                $dbConn->prepare("DELETE FROM comentarios WHERE usuario_id = ?")->execute([$userId]);
                $dbConn->prepare("DELETE FROM solicitudes_anime WHERE usuario_id = ?")->execute([$userId]);
                $dbConn->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$userId]);
                $dbConn->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                if ($dbConn->inTransaction()) {
                    $dbConn->rollBack();
                }
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }

        echo json_encode(['success' => false, 'error' => 'Accion desconocida']);
    }
}
