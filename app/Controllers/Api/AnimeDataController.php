<?php
namespace Controllers\Api;

use Controllers\Controller;
use PDO;
use Exception;
use Throwable;
use Helpers\ApiResponse;
use Services\EpisodeCacheService;


class AnimeDataController extends Controller
{
    private $restrictedGenres = ['Hentai', 'Erotica', 'Ecchi', 'Yaoi', 'Yuri', 'Gore', 'Harem', 'Reverse Harem', 'Rx', 'Girls Love', 'Boys Love'];
    private $restrictedTitles = ['does it count if', 'futanari'];

    public function handle(): void
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
            // Si falla o no hay ID, intentamos por mal_id que es lo más común desde fuera
            $stmt = $dbConn->prepare("SELECT * FROM anime WHERE mal_id = ? LIMIT 1");
            $stmt->execute([$mal_id]);
            $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si falla, intentamos por id interno (fallback por si mal_id era en realidad un id)
            if (!$animeItem && is_numeric($mal_id)) {
                $stmt = $dbConn->prepare("SELECT * FROM anime WHERE id = ? LIMIT 1");
                $stmt->execute([$mal_id]);
                $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }

        if (!$animeItem && $q) {
            // Intento 1: Búsqueda exacta
            $stmt = $dbConn->prepare("SELECT * FROM anime WHERE titulo = ? LIMIT 1");
            $stmt->execute([$q]);
            $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);

            // Intento 2: Búsqueda flexible (LIKE)
            if (!$animeItem) {
                $stmt = $dbConn->prepare("SELECT * FROM anime WHERE titulo LIKE ? LIMIT 1");
                $stmt->execute(["%$q%"]);
                $animeItem = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }

        if (!$animeItem) {
            ApiResponse::error('Not found in local DB', 404);
            exit;
        }

        // Security Check: +18 hard restriction
        $title = strtolower($animeItem['titulo'] ?? '');
        foreach ($this->restrictedTitles as $rt) {
            if (str_contains($title, $rt)) {
                ApiResponse::error('Restricted content', 403);
                exit;
            }
        }

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
                ApiResponse::error('Restricted content', 403);
                exit;
            }
        }

        $studioNames = array_values(array_filter(array_map('trim', explode(',', (string) ($animeItem['estudio'] ?? '')))));
        $titleEnglish = trim((string) ($animeItem['titulo_ingles'] ?? ''));
        $seasonValue = strtolower(trim((string) ($animeItem['temporada'] ?? '')));

        $jikanData = [
            'mal_id' => $animeItem['mal_id'] ?: $animeItem['id'],
            'title' => $animeItem['titulo'],
            'title_english' => $titleEnglish !== '' ? $titleEnglish : $animeItem['titulo'],
            'title_japanese' => $animeItem['titulo'],
            'type' => $animeItem['tipo'],
            'episodes' => (int) $animeItem['episodios'],
            'status' => $animeItem['estado'],
            'year' => (int) $animeItem['anio'],
            'season' => $seasonValue,
            'score' => (float) $animeItem['puntuacion'],
            'synopsis' => (string) ($animeItem['sinopsis'] ?? ''),
            'rating' => (string) ($animeItem['clasificacion'] ?? ''),
            'duration' => '',
            'rank' => null,
            'images' => [
                'jpg' => [
                    'large_image_url' => $animeItem['imagen_url'],
                    'image_url' => $animeItem['imagen_url']
                ]
            ],
            'genres' => array_map(function ($g) {
                return ['name' => $g]; }, $genres),
            'studios' => array_map(static function ($name) {
                return ['name' => $name];
            }, $studioNames)
        ];

        // Characters
        $charStmt = $dbConn->prepare("SELECT * FROM anime_characters WHERE anime_id = ?");
        $charStmt->execute([$animeItem['id']]);
        $jikanData['characters'] = [];
        while ($c = $charStmt->fetch(PDO::FETCH_ASSOC)) {
            $jikanData['characters'][] = [
                'character' => [
                    'mal_id' => $c['mal_id'],
                    'name' => $c['name'],
                    'images' => ['jpg' => ['image_url' => $c['image_url']]]
                ],
                'role' => $c['role']
            ];
        }

        // Pictures
        $picStmt = $dbConn->prepare("SELECT * FROM anime_pictures WHERE anime_id = ?");
        $picStmt->execute([$animeItem['id']]);
        $jikanData['pictures'] = [];
        while ($p = $picStmt->fetch(PDO::FETCH_ASSOC)) {
            $jikanData['pictures'][] = [
                'jpg' => ['image_url' => $p['image_url'], 'large_image_url' => $p['image_url']]
            ];
        }

        // Videos
        $vidStmt = $dbConn->prepare("SELECT * FROM anime_videos WHERE anime_id = ?");
        $vidStmt->execute([$animeItem['id']]);
        $jikanData['videos'] = ['promo' => []];
        while ($v = $vidStmt->fetch(PDO::FETCH_ASSOC)) {
            $jikanData['videos']['promo'][] = [
                'trailer' => [
                    'youtube_id' => $v['youtube_id'],
                    'url' => $v['url'],
                    'images' => ['maximum_image_url' => $v['image_url'], 'large_image_url' => $v['image_url']]
                ]
            ];
        }

        $episodeCache = new EpisodeCacheService($dbConn);
        try {
            $jikanData['episodes_data'] = $episodeCache->getByAnimeId((int) $animeItem['id']);
        } catch (Throwable $e) {
            $jikanData['episodes_data'] = [];
        }

        ApiResponse::success(['data' => $jikanData]);

    }
}
