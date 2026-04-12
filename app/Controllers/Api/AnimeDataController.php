<?php
namespace Controllers\Api;

use Controllers\Controller;
use PDO;
use Helpers\ApiResponse;
use Services\EpisodeCacheService;

class AnimeDataController extends Controller
{
    private $restrictedGenres = ['Hentai', 'Erotica', 'Ecchi', 'Yaoi', 'Yuri', 'Gore', 'Harem', 'Reverse Harem', 'Rx', 'Girls Love', 'Boys Love'];
    private $restrictedTitles = ['does it count if', 'futanari'];

    public function handle()
    {
        app_start_session();
        session_write_close();

        $q = trim($_GET['q'] ?? '');
        $mal_id = trim($_GET['mal_id'] ?? '');
        $id = trim($_GET['id'] ?? '');

        $dbConn = (new \Models\Database())->getConnection();
        if (!$dbConn) {
            ApiResponse::error('DB Connection Error', 500);
            exit;
        }

        $animeItem = null;

        if ($id) {
            // Si tenemos un ID interno, es la fuente más confiable
            $stmt = $dbConn->prepare("SELECT * FROM anime WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (!$animeItem && $mal_id) {
            // Si no, buscamos por mal_id
            $stmt = $dbConn->prepare("SELECT * FROM anime WHERE mal_id = ? LIMIT 1");
            $stmt->execute([$mal_id]);
            $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (!$animeItem && $q) {
            // Último recurso: búsqueda por título exacto o aproximado (Legacy Support)
            $stmt = $dbConn->prepare("SELECT * FROM anime WHERE titulo = ? LIMIT 1");
            $stmt->execute([$q]);
            $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$animeItem) {
                $stmt = $dbConn->prepare("SELECT * FROM anime WHERE titulo LIKE ? LIMIT 1");
                $stmt->execute(["%$q%"]);
                $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }

        if (!$animeItem) {
            ApiResponse::error('Not found', 404);
            exit;
        }

        // --- Verificación de Seguridad (Restricción de Contenido +18) ---
        $title = strtolower($animeItem['titulo'] ?? '');
        foreach ($this->restrictedTitles as $rt) {
            if (strpos($title, $rt) !== false) {
                ApiResponse::error('Restricted', 403);
                exit;
            }
        }

        // Obtener géneros para verificar restricciones
        $genreStmt = $dbConn->prepare("
            SELECT g.nombre 
            FROM generos g 
            JOIN anime_generos ag ON g.id = ag.genero_id 
            WHERE ag.anime_id = ?
        ");
        $genreStmt->execute([$animeItem['id']]);
        $genres = $genreStmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($genres as $gn) {
            if (in_array($gn, $this->restrictedGenres)) {
                ApiResponse::error('Restricted', 403);
                exit;
            }
        }

        // --- Carga de Episodios ---
        $episodeCache = new EpisodeCacheService($dbConn);
        $episodes = $episodeCache->getByAnimeId((int)$animeItem['id']);

        ApiResponse::success([
            'anime' => $animeItem,
            'genres' => $genres,
            'episodes' => $episodes
        ]);
    }
}
