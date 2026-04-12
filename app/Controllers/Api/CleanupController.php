<?php
namespace Controllers\Api;

use Controllers\Controller;
use Helpers\ApiResponse;
use Models\Database;
use Exception;
use PDO;

class CleanupController extends Controller
{
    public function handle()
    {
        $dbObj = new Database();
        $dbConn = $dbObj->getConnection();
        if (!$dbConn) {
            ApiResponse::error('DB Connection Error', 500);
            exit;
        }

        try {
            // Buscamos los IDs de los animes marcados como 'Unknown'
            $stmt = $dbConn->prepare("SELECT id FROM anime WHERE titulo = 'Unknown' OR titulo IS NULL OR titulo = ''");
            $stmt->execute();
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($ids)) {
                ApiResponse::success(['message' => 'No "Unknown" records found. Database is clean.']);
                exit;
            }

            $count = count($ids);
            $dbConn->beginTransaction();

            // Borramos en orden de dependencias (si no hay cascada configurada)
            $placeholders = implode(',', array_fill(0, $count, '?'));
            
            $dbConn->prepare("DELETE FROM anime_characters WHERE anime_id IN ($placeholders)")->execute($ids);
            $dbConn->prepare("DELETE FROM anime_generos WHERE anime_id IN ($placeholders)")->execute($ids);
            $dbConn->prepare("DELETE FROM anime_pictures WHERE anime_id IN ($placeholders)")->execute($ids);
            $dbConn->prepare("DELETE FROM anime_videos WHERE anime_id IN ($placeholders)")->execute($ids);
            $dbConn->prepare("DELETE FROM anime_episodes WHERE anime_id IN ($placeholders)")->execute($ids);
            
            // Finalmente borramos el anime principal
            $dbConn->prepare("DELETE FROM anime WHERE id IN ($placeholders)")->execute($ids);

            $dbConn->commit();

            ApiResponse::success([
                'message' => "Successfully deleted $count garbage records and their associated data.",
                'deleted_ids' => $ids
            ]);

        } catch (Exception $e) {
            if ($dbConn->inTransaction()) $dbConn->rollBack();
            ApiResponse::error($e->getMessage(), 500);
        }
    }
}
