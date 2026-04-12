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

        // 1. Buscar Anime Principal
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

        // --- Carga de Géneros ---
        $genreStmt = $dbConn->prepare("
            SELECT g.nombre 
            FROM generos g 
            JOIN anime_generos ag ON g.id = ag.genero_id 
            WHERE ag.anime_id = ?
        ");
        $genreStmt->execute(array($animeItem['id']));
        $genres = array();
        while ($row = $genreStmt->fetch(PDO::FETCH_ASSOC)) {
            $genres[] = $row['nombre'];
        }

        foreach ($genres as $gn) {
            if (in_array($gn, $this->restrictedGenres)) {
                ApiResponse::error('Restricted', 403);
                exit;
            }
        }

        // --- Carga de Episodios ---
        $episodeCache = new EpisodeCacheService($dbConn);
        $episodes = $episodeCache->getByAnimeId((int) $animeItem['id']);

        // --- CARGA DE DATOS PROFUNDOS (Para evitar llamadas externas) ---

        // Personajes
        $charStmt = $dbConn->prepare("SELECT mal_id, name, role, image_url as image FROM anime_characters WHERE anime_id = ?");
        $charStmt->execute(array($animeItem['id']));
        $charactersRaw = $charStmt->fetchAll(PDO::FETCH_ASSOC);
        $characters = array();
        foreach ($charactersRaw as $c) {
            $characters[] = array(
                'character' => array(
                    'mal_id' => $c['mal_id'],
                    'name' => $c['name'],
                    'images' => array('jpg' => array('image_url' => $c['image']))
                ),
                'role' => $c['role']
            );
        }

        // Imágenes/Galería
        $picStmt = $dbConn->prepare("SELECT image_url FROM anime_pictures WHERE anime_id = ?");
        $picStmt->execute(array($animeItem['id']));
        $picturesRaw = $picStmt->fetchAll(PDO::FETCH_ASSOC);
        $pictures = array();
        foreach ($picturesRaw as $p) {
            $pictures[] = array('jpg' => array('large_image_url' => $p['image_url']));
        }

        // Tráilers/Videos
        $vidStmt = $dbConn->prepare("SELECT youtube_id, url, image_url FROM anime_videos WHERE anime_id = ?");
        $vidStmt->execute(array($animeItem['id']));
        $videosRaw = $vidStmt->fetchAll(PDO::FETCH_ASSOC);
        $videos = array('promo' => array());
        foreach ($videosRaw as $v) {
            $videos['promo'][] = array(
                'trailer' => array(
                    'youtube_id' => $v['youtube_id'],
                    'url' => $v['url'],
                    'images' => array('maximum_image_url' => $v['image_url'])
                )
            );
        }

        ApiResponse::success(array(
            'anime' => $animeItem,
            'genres' => $genres,
            'episodes' => $episodes,
            'characters' => $characters,
            'pictures' => $pictures,
            'videos' => $videos,
            'is_complete' => count($characters) > 0 ? true : false
        ));
    }
}
