<?php
namespace Controllers\Api;

use Controllers\Controller;
use Exception;
use PDO;

class ActivityController extends Controller
{
    public function handle(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        app_require_method('POST');
        app_verify_csrf();
        app_start_session();

        $userId = $_SESSION['user_id'] ?? null;
        $role = $_SESSION['role'] ?? 'Invitado';

        if (!$userId || $role === 'Invitado') {
            echo json_encode(['success' => true, 'message' => 'Modo invitado: no se registra actividad']);
            exit;
        }

        $data = app_get_json_input();
        $action = $data['action'] ?? '';

        if (!$action) {
            echo json_encode(['success' => false, 'error' => 'Accion no especificada']);
            exit;
        }

        $dbConn = (new \Models\Database())->getConnection();
        if (!$dbConn) {
            echo json_encode(['success' => false, 'error' => 'Error de conexion']);
            exit;
        }

        try {
            if ($action === 'view') {
                $animeId = $data['anime_id'] ?? null;
                if ($animeId) {
                    $stmt = $dbConn->prepare("INSERT INTO usuarios_vistas (usuario_id, anime_id) VALUES (?, ?)");
                    $stmt->execute([$userId, $animeId]);

                    $stmtHours = $dbConn->prepare("INSERT INTO usuarios_meta (usuario_id, meta_key, meta_value) VALUES (?, 'profile_hours', '0.01') ON DUPLICATE KEY UPDATE meta_value = CAST(meta_value AS DECIMAL(10,2)) + 0.01");
                    $stmtHours->execute([$userId]);
                }
            } elseif ($action === 'time_sync') {
                $delta = floatval($data['delta'] ?? 0);
                if ($delta > 0) {
                    $stmtHours = $dbConn->prepare("INSERT INTO usuarios_meta (usuario_id, meta_key, meta_value) VALUES (?, 'profile_hours', ?) ON DUPLICATE KEY UPDATE meta_value = CAST(meta_value AS DECIMAL(10,1)) + ?");
                    $stmtHours->execute([$userId, $delta, $delta]);
                }
            } elseif ($action === 'report') {
                $animeId = $data['anime_id'] ?? null;
                $message = trim((string) ($data['message'] ?? ''));
                if ($animeId && $message !== '') {
                    $stmt = $dbConn->prepare("INSERT INTO usuarios_reportes (usuario_id, anime_id, mensaje) VALUES (?, ?, ?)");
                    $stmt->execute([$userId, $animeId, $message]);
                }
            } else {
                $details = $data['details'] ?? '';
                $stmt = $dbConn->prepare("INSERT INTO usuarios_actividad (usuario_id, accion, detalles) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $action, $details]);
            }

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
