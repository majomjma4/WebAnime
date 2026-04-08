<?php
namespace Controllers\Api;

use Controllers\Controller;
use Exception;
use PDO;

class RequestsController extends Controller
{
    public function handle(): void
    {
        app_start_session();
        header('Content-Type: application/json; charset=UTF-8');

        $action = (string) ($_GET['action'] ?? '');
        if ($action === '') {
            echo json_encode(['success' => false, 'error' => 'Accion invalida']);
            exit;
        }

        $writeActions = ['create', 'decide', 'delete', 'bulk_approve'];
        if (in_array($action, $writeActions, true)) {
            app_require_method('POST');
            app_verify_csrf();
        } else {
            app_require_method('GET');
        }

        $data = app_get_json_input();
        $dbConn = (new \Models\Database())->getConnection();
        if (!$dbConn) {
            echo json_encode(['success' => false, 'error' => 'Error de conexion a la base de datos']);
            exit;
        }

        $ensureRequestsTable = static function (PDO $dbConn): void {
            $dbConn->exec("CREATE TABLE IF NOT EXISTS solicitudes_anime (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NULL, user_name VARCHAR(120) NULL, titulo VARCHAR(255) NOT NULL, tipo VARCHAR(50) NOT NULL DEFAULT 'Anime', fuente VARCHAR(255) NULL, estado ENUM('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente', creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, resuelto_en DATETIME NULL, resuelto_por INT NULL, INDEX idx_estado (estado), INDEX idx_creado (creado_en), INDEX idx_user (user_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        };
        $normalizeSearchTerm = static function (string $value): string {
            $value = trim(mb_strtolower($value, 'UTF-8'));
            return strtr($value, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u']);
        };
        $requireAdmin = static function (): void {
            $isAdmin = isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] === 'Admin';
            if (!$isAdmin) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
                exit;
            }
        };

        $ensureRequestsTable($dbConn);

        if ($action === 'create') {
            $titulo = trim((string) ($data['titulo'] ?? ''));
            $tipo = trim((string) ($data['tipo'] ?? 'Anime'));
            $fuente = trim((string) ($data['fuente'] ?? ''));
            if ($titulo === '') {
                echo json_encode(['success' => false, 'error' => 'El titulo es obligatorio']);
                exit;
            }
            $userId = $_SESSION['user_id'] ?? null;
            $userName = $_SESSION['username'] ?? null;
            $isPremium = $_SESSION['premium'] ?? false;
            if (!$userId) {
                echo json_encode(['success' => false, 'error' => 'Debes iniciar sesiÃ³n']);
                exit;
            }
            if (!$isPremium) {
                echo json_encode(['success' => false, 'error' => 'Solo usuarios Premium pueden enviar solicitudes']);
                exit;
            }
            try {
                $stmt = $dbConn->prepare("INSERT INTO solicitudes_anime (user_id, user_name, titulo, tipo, fuente, estado, creado_en) VALUES (?, ?, ?, ?, ?, 'pendiente', NOW())");
                $stmt->execute([$userId, $userName, $titulo, $tipo, $fuente ?: null]);
                echo json_encode(['success' => true, 'id' => $dbConn->lastInsertId()]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'list') {
            $status = $_GET['status'] ?? 'pendiente';
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $size = max(1, min(50, (int) ($_GET['size'] ?? 4)));
            $q = trim((string) ($_GET['q'] ?? ''));
            $mine = (($_GET['mine'] ?? '') === '1');
            if (!$mine) {
                $requireAdmin();
            }

            $where = 'r.estado = ?';
            $params = [$status];
            if ($mine) {
                $userId = $_SESSION['user_id'] ?? null;
                if (!$userId) {
                    echo json_encode(['success' => false, 'error' => 'Debes iniciar sesiÃ³n']);
                    exit;
                }
                $where .= ' AND r.user_id = ?';
                $params[] = $userId;
            }
            if ($q !== '') {
                $normalizedQ = $normalizeSearchTerm($q);
                $searchClauses = ["r.titulo LIKE ?", "r.user_name LIKE ?", "DATE_FORMAT(r.creado_en, '%d/%m/%Y') LIKE ?", "DATE_FORMAT(r.creado_en, '%Y-%m-%d') LIKE ?", "DATE_FORMAT(r.creado_en, '%d-%m-%Y') LIKE ?", "DATE_FORMAT(r.creado_en, '%d %m %Y') LIKE ?", "DATE_FORMAT(r.creado_en, '%d/%m/%Y %H:%i') LIKE ?"];
                $like = '%' . $q . '%';
                array_push($params, $like, $like, $like, $like, $like, $like, $like);
                $monthMap = ['enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4, 'mayo' => 5, 'junio' => 6, 'julio' => 7, 'agosto' => 8, 'septiembre' => 9, 'setiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12];
                foreach ($monthMap as $monthName => $monthNumber) {
                    if (strpos($monthName, $normalizedQ) !== false) {
                        $searchClauses[] = 'MONTH(r.creado_en) = ?';
                        $params[] = $monthNumber;
                    }
                }
                $where .= ' AND (' . implode(' OR ', $searchClauses) . ')';
            }

            $countStmt = $dbConn->prepare("SELECT COUNT(*) AS total FROM solicitudes_anime r WHERE $where");
            $countStmt->execute($params);
            $total = (int) (($countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0));
            $offset = ($page - 1) * $size;
            $listStmt = $dbConn->prepare("SELECT r.id, r.titulo, r.tipo, r.fuente, r.estado, r.creado_en, COALESCE(u.nombre_mostrar, r.user_name, 'Usuario') AS user_display FROM solicitudes_anime r LEFT JOIN usuarios u ON u.id = r.user_id WHERE $where ORDER BY r.creado_en DESC LIMIT $size OFFSET $offset");
            $listStmt->execute($params);
            $items = $listStmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'total' => $total, 'page' => $page, 'size' => $size, 'items' => $items]);
            exit;
        }

        if ($action === 'decide') {
            $requireAdmin();
            $id = (int) ($data['id'] ?? 0);
            $decision = (string) ($data['decision'] ?? '');
            if (!$id || !in_array($decision, ['aprobado', 'rechazado'], true)) {
                echo json_encode(['success' => false, 'error' => 'Datos invalidos']);
                exit;
            }
            $adminId = $_SESSION['user_id'] ?? null;
            try {
                $stmt = $dbConn->prepare("UPDATE solicitudes_anime SET estado = ?, resuelto_en = NOW(), resuelto_por = ? WHERE id = ?");
                $stmt->execute([$decision, $adminId, $id]);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'delete') {
            $requireAdmin();
            $id = (int) ($data['id'] ?? 0);
            if (!$id) {
                echo json_encode(['success' => false, 'error' => 'ID invalido']);
                exit;
            }
            try {
                $stmt = $dbConn->prepare("DELETE FROM solicitudes_anime WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'bulk_approve') {
            $requireAdmin();
            $adminId = $_SESSION['user_id'] ?? null;
            try {
                $stmt = $dbConn->prepare("UPDATE solicitudes_anime SET estado = 'aprobado', resuelto_en = NOW(), resuelto_por = ? WHERE estado = 'pendiente'");
                $stmt->execute([$adminId]);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Error de BD: ' . $e->getMessage()]);
            }
            exit;
        }

        echo json_encode(['success' => false, 'error' => 'Accion no soportada']);
    }
}
