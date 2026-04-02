<?php
namespace Controllers\Api;

use Controllers\Controller;
use PDO;
use Exception;
use Throwable;
class ProfileController extends Controller
{
    public function handle(): void
    {
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
                $userStmt = $dbConn->prepare("SELECT codigo_publico FROM usuarios WHERE id = ?");
                $userStmt->execute([$userId]);
                $userRow = $userStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        
                // Obtenemos los meta del usuario (nombre, desc, color, etc)
                $stmt = $dbConn->prepare("SELECT meta_key, meta_value FROM usuarios_meta WHERE usuario_id = ?");
                $stmt->execute([$userId]);
                $meta = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'anidex_profile_name' => $meta['profile_name'] ?? ($_SESSION['username'] ?? ''),
                        'anidex_profile_desc' => $meta['profile_desc'] ?? 'Explorador de animes en NekoraList',
                        'anidex_profile_color' => $meta['profile_color'] ?? '',
                        'anidex_profile_avatar' => $meta['profile_avatar'] ?? '',
                        'anidex_profile_member_since' => $meta['profile_member_since'] ?? date('Y'),
                        'anidex_user_id' => (string)$userId,
                        'anidex_public_user_id' => $userRow['codigo_publico'] ?? null,
                        'anidex_profile_hours' => $meta['profile_hours'] ?? '0',
                        'anidex_profile_prefs' => json_decode($meta['anidex_profile_prefs'] ?? '[]', true),
                        'anidex_continue_v1' => json_decode($meta['anidex_continue_v1'] ?? '[]', true),
                        'anidex_my_list_v1' => json_decode($meta['my_list'] ?? '[]', true),
                        'anidex_favorites_v1' => json_decode($meta['favorites'] ?? '[]', true),
                        'anidex_status_v1' => json_decode($meta['status_list'] ?? '[]', true)
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
        
    }
}
