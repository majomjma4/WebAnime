<?php
namespace Controllers\Api;

use Controllers\Controller;
use PDO;
use Helpers\ApiResponse;
use Services\EpisodeCacheService;
use Models\Database;

class AnimeDataController extends Controller
{
    private $restrictedGenres = array('Hentai', 'Erotica', 'Ecchi', 'Yaoi', 'Yuri', 'Gore', 'Harem', 'Reverse Harem', 'Rx', 'Girls Love', 'Boys Love');
    private $restrictedTitles = array('does it count if', 'futanari');

    public function handle()
    {
        app_start_session();
        session_write_close();

        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $mal_id = isset($_GET['mal_id']) ? trim($_GET['mal_id']) : '';
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';

        $dbObj = new Database();
        $dbConn = $dbObj->getConnection();
        if (!$dbConn) {
            ApiResponse::error('DB Connection Error', 500);
            exit;
        }

        $animeItem = null;

        if ($id) {
            $stmt = $dbConn->prepare("SELECT * FROM anime WHERE id = ? LIMIT 1");
            $stmt->execute(array($id));
            $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (!$animeItem && $mal_id) {
            $stmt = $dbConn->prepare("SELECT * FROM anime WHERE mal_id = ? LIMIT 1");
            $stmt->execute(array($mal_id));
            $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (!$animeItem && $q) {
            $stmt = $dbConn->prepare("SELECT * FROM anime WHERE titulo = ? LIMIT 1");
            $stmt->execute(array($q));
            $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$animeItem) {
                $stmt = $dbConn->prepare("SELECT * FROM anime WHERE titulo LIKE ? LIMIT 1");
                $stmt->execute(array("%$q%"));
                $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }

        if (!$animeItem) {
            ApiResponse::error('Not found', 404);
            exit;
        }

        // --- Verificación de Seguridad ---
        $title = strtolower(isset($animeItem['titulo']) ? $animeItem['titulo'] : '');
        foreach ($this->restrictedTitles as $rt) {
            if (strpos($title, $rt) !== false) {
                ApiResponse::error('Restricted', 403);
                exit;
            }
        }

        $genreStmt = $dbConn->prepare("
            SELECT g.nombre 
            FROM generos g 
            JOIN anime_generos ag ON g.id = ag.genero_id 
            WHERE ag.anime_id = ?
        ");
        $genreStmt->execute(array($animeItem['id']));
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

        ApiResponse::success(array(
            'anime' => $animeItem,
            'genres' => $genres,
            'episodes' => $episodes
        ));
    }
}
