<?php
namespace Controllers\Api;

use Controllers\Controller;
use Exception;
use Helpers\ApiResponse;
use PDO;
use Services\EpisodeCacheService;

class SaveAnimeController extends Controller
{
    private $restrictedGenres = array('Hentai', 'Erotica', 'Ecchi', 'Yaoi', 'Yuri', 'Gore', 'Harem', 'Reverse Harem', 'Rx', 'Girls Love', 'Boys Love', 'Explicit Genres');
    private $restrictedTitles = array('does it count if', 'futanari', 'hentai', 'dick', 'pussy', 'sex', 'porn', 'cock', 'blowjob');

    public function handle()
    {
        // Silenciamos advertencias para evitar corromper la respuesta JSON con HTML de errores PHP
        @error_reporting(0);
        @ini_set('display_errors', 0);

        header('Content-Type: application/json; charset=UTF-8');

        // Verificamos método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::error('Method not allowed', 405);
            exit;
        }

        app_start_session();

        // Verificamos permisos (Admin puede guardar todo, invitados solo completar)
        $isAdmin = isset($_SESSION['user_id'], $_SESSION['role']) && ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Admin99');

        $data = app_get_json_input();
        if (!$data || (!isset($data['mal_id']) && !isset($data['id_anime']))) {
            ApiResponse::error('Invalid data');
            exit;
        }

        $dbConn = (new \Models\Database())->getConnection();
        if (!$dbConn) {
            ApiResponse::error('DB Connection Error', 500);
            exit;
        }

        $mal_id = (int) ($data['mal_id'] ?? ($data['id_anime'] ?? 0));
        
        // Mapeo Híbrido: Jikan (title) vs Local (titulo)
        $titulo = $data['title_english'] ?? ($data['titulo_ingles'] ?? ($data['title'] ?? ($data['titulo'] ?? 'Unknown')));
        $titulo = trim((string) $titulo);

        // LOG DE DEPURACIÓN (Temporal) enviando a la raíz
        $logFile = __DIR__ . '/../../../persistence_debug.log';
        $logData = date('[Y-m-d H:i:s]') . " | MAL_ID: $mal_id | Titulo: $titulo | RAW_KEYS: " . implode(',', array_keys($data)) . "\n";
        @file_put_contents($logFile, $logData, FILE_APPEND);

        if (empty($titulo) || strtolower($titulo) === 'unknown') {
            ApiResponse::error('Invalid title: cannot save anime with unknown title.');
            exit;
        }

        // Bloqueo de contenido +18
        $rating = (string) ($data['rating'] ?? ($data['clasificacion'] ?? ''));
        $restrictedRating = array('Rx', 'Hentai', 'Erotica', 'Adults');
        foreach ($restrictedRating as $kw) {
            if (stripos($rating, $kw) !== false) {
                ApiResponse::error('Contenido restringido (Rating) no permitido.');
                exit;
            }
        }

        // Bloqueo por Título
        $title_ja = $data['title_japanese'] ?? ($data['titulo_japones'] ?? '');
        $fullTitle = strtolower($titulo . ' ' . $title_ja);
        foreach ($this->restrictedTitles as $rt) {
            if (strpos($fullTitle, $rt) !== false) {
                ApiResponse::error('Contenido restringido (Título) no permitido.');
                exit;
            }
        }

        // Bloqueo por Género
        $genres = $data['genres'] ?? ($data['generos'] ?? array());
        if (is_array($genres)) {
            foreach ($genres as $g) {
                $gn = isset($g['name']) ? (string)$g['name'] : (is_string($g) ? $g : '');
                if (in_array($gn, $this->restrictedGenres)) {
                    ApiResponse::error('Contenido restringido (Género) no permitido.');
                    exit;
                }
            }
        }

        // Validamos que el mal_id sea válido (> 0)
        if ($mal_id <= 0) {
            ApiResponse::error('Invalid MyAnimeList ID: ' . $mal_id);
            exit;
        }

        $stmt = $dbConn->prepare("SELECT id FROM anime WHERE mal_id = ? LIMIT 1");
        $stmt->execute(array($mal_id));
        $existingAnime = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$isAdmin) {
            if ($existingAnime) {
                $stmtC = $dbConn->prepare("SELECT COUNT(*) FROM anime_characters WHERE anime_id = ?");
                $stmtC->execute(array($existingAnime['id']));
                if ($stmtC->fetchColumn() > 0 && !isset($data['force_update'])) {
                    ApiResponse::success(array('message' => 'Anime already populated, no update needed by guest', 'id' => $existingAnime['id']));
                    exit;
                }
            }
        }

        $new_id = $existingAnime ? $existingAnime['id'] : null;
        $episodeCacheService = new EpisodeCacheService($dbConn);

        // 1. Datos básicos - SOPORTE HÍBRIDO
        $tipo = $data['type'] ?? ($data['tipo'] ?? 'TV');
        $estudio = '';
        if (!empty($data['studios']) && is_array($data['studios'])) {
            $studioNames = array();
            foreach ($data['studios'] as $studio) {
                if (!empty($studio['name']))
                    $studioNames[] = trim($studio['name']);
            }
            $estudio = implode(', ', $studioNames);
        } else {
            $estudio = $data['estudio'] ?? '';
        }

        $estadoRaw = $data['status'] ?? ($data['estado'] ?? 'Unknown');
        $estadoLower = strtolower(trim((string)$estadoRaw));
        if ($estadoLower === 'finished airing' || $estadoLower === 'finalizado')
            $estado = 'Finalizado';
        elseif (strpos($estadoLower, 'emision') !== false || strpos($estadoLower, 'emisi') !== false)
            $estado = 'En emision';
        elseif (strpos($estadoLower, 'proximamente') !== false || strpos($estadoLower, 'pr') !== false)
            $estado = 'Proximamente';
        else
            $estado = $estadoRaw;

        $episodios = (int) ($data['episodes'] ?? ($data['episodios'] ?? 0));
        $temporada = $data['season'] ?? ($data['temporada'] ?? 'Unknown');
        $anio = (int) ($data['year'] ?? ($data['anio'] ?? 
             ($data['aired']['prop']['from']['year'] ?? 0)));

        $sinopsis = $data['synopsis'] ?? ($data['sinopsis'] ?? '');
        $imagen_url = $data['images']['jpg']['large_image_url'] ?? 
                     ($data['images']['jpg']['image_url'] ?? 
                     ($data['imagen_url'] ?? ''));
        
        $puntuacion = (float) ($data['score'] ?? ($data['puntuacion'] ?? 0.0));
        $titulo_ingles = $data['title_english'] ?? ($data['titulo_ingles'] ?? '');
        $clasificacion = $data['rating'] ?? ($data['clasificacion'] ?? '');
        $trailer_url = $data['trailer']['url'] ?? ($data['trailer_url'] ?? '');

        $action = 'none';
        $dbConn->beginTransaction();
        try {
            if (!$new_id) {
                $sql = "INSERT INTO anime (mal_id, titulo, titulo_ingles, tipo, estudio, estado, episodios, temporada, anio, clasificacion, sinopsis, imagen_url, trailer_url, puntuacion, activo, creado_en) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";
                $stmt = $dbConn->prepare($sql);
                $stmt->execute(array($mal_id, $titulo, $titulo_ingles, $tipo, $estudio, $estado, $episodios, $temporada, $anio, $clasificacion, $sinopsis, $imagen_url, $trailer_url, $puntuacion));
                $new_id = $dbConn->lastInsertId();
                if (!$new_id || $new_id == 0) throw new Exception("Error al obtener ID de inserción para MAL " . $mal_id);
                $action = 'insert';
            } else {
                $sql = "UPDATE anime SET mal_id = ?, titulo = ?, titulo_ingles = ?, tipo = ?, estudio = ?, estado = ?, episodios = ?, temporada = ?, anio = ?, clasificacion = ?, sinopsis = ?, imagen_url = ?, trailer_url = ?, puntuacion = ?, activo = 1 WHERE id = ?";
                $stmt = $dbConn->prepare($sql);
                $stmt->execute(array($mal_id, $titulo, $titulo_ingles, $tipo, $estudio, $estado, $episodios, $temporada, $anio, $clasificacion, $sinopsis, $imagen_url, $trailer_url, $puntuacion, $new_id));
                $action = 'update';

                // Limpiar géneros previos para actualizar
                $dbConn->prepare("DELETE FROM anime_generos WHERE anime_id = ?")->execute(array($new_id));
            }

            // Guardar Géneros
            $generos = isset($data['genres']) && is_array($data['genres']) ? $data['genres'] : array();
            foreach ($generos as $g) {
                $g_name = isset($g['name']) ? (string) $g['name'] : '';
                if ($g_name === '')
                    continue;

                $gStmt = $dbConn->prepare("SELECT id FROM generos WHERE nombre = ?");
                $gStmt->execute(array($g_name));
                $g_row = $gStmt->fetch(PDO::FETCH_ASSOC);
                $g_id = $g_row ? $g_row['id'] : null;

                if (!$g_id) {
                    $iStmt = $dbConn->prepare("INSERT INTO generos (nombre) VALUES (?)");
                    $iStmt->execute(array($g_name));
                    $g_id = $dbConn->lastInsertId();
                }
                $dbConn->prepare("INSERT INTO anime_generos (anime_id, genero_id) VALUES (?, ?)")->execute(array($new_id, $g_id));
            }

            $dbConn->commit();

            // Guardar Personajes (Solo los primeros 10)
            if ($new_id && isset($data['characters']) && is_array($data['characters'])) {
                $dbConn->prepare("DELETE FROM anime_characters WHERE anime_id = ?")->execute(array($new_id));
                $charStmt = $dbConn->prepare("INSERT INTO anime_characters (anime_id, mal_id, name, role, image_url) VALUES (?, ?, ?, ?, ?)");
                $count = 0;
                foreach ($data['characters'] as $c) {
                    if ($count >= 10)
                        break;
                    $c_mal_id = isset($c['character']['mal_id']) ? (int) $c['character']['mal_id'] : 0;
                    $c_name = isset($c['character']['name']) ? trim((string) $c['character']['name']) : 'Unknown';
                    $c_role = isset($c['role']) ? trim((string) $c['role']) : 'Supporting';
                    $c_img = isset($c['character']['images']['jpg']['image_url']) ? (string) $c['character']['images']['jpg']['image_url'] : '';

                    if ($c_mal_id) {
                        $charStmt->execute(array($new_id, $c_mal_id, $c_name, $c_role, $c_img));
                        $count++;
                    }
                }
            }

            // Guardar Imágenes
            if ($new_id && isset($data['pictures']) && is_array($data['pictures'])) {
                $dbConn->prepare("DELETE FROM anime_pictures WHERE anime_id = ?")->execute(array($new_id));
                $picStmt = $dbConn->prepare("INSERT INTO anime_pictures (anime_id, image_url) VALUES (?, ?)");
                foreach ($data['pictures'] as $p) {
                    $p_url = isset($p['jpg']['large_image_url']) ? $p['jpg']['large_image_url'] : (isset($p['jpg']['image_url']) ? $p['jpg']['image_url'] : '');
                    if ($p_url)
                        $picStmt->execute(array($new_id, $p_url));
                }
            }

            // Guardar Promos/Trailers
            if (!$new_id) return;
            if (isset($data['videos']) && isset($data['videos']['promo']) && is_array($data['videos']['promo'])) {
                $dbConn->prepare("DELETE FROM anime_videos WHERE anime_id = ?")->execute(array($new_id));
                $vidStmt = $dbConn->prepare("INSERT INTO anime_videos (anime_id, youtube_id, url, image_url) VALUES (?, ?, ?, ?)");
                foreach ($data['videos']['promo'] as $v) {
                    $v_yt = isset($v['trailer']['youtube_id']) ? $v['trailer']['youtube_id'] : '';
                    $v_url = isset($v['trailer']['url']) ? $v['trailer']['url'] : '';
                    $v_img = isset($v['trailer']['images']['maximum_image_url']) ? $v['trailer']['images']['maximum_image_url'] :
                        (isset($v['trailer']['images']['large_image_url']) ? $v['trailer']['images']['large_image_url'] : '');
                    if ($v_yt || $v_url)
                        $vidStmt->execute(array($new_id, $v_yt, $v_url, $v_img));
                }
            }

            // Guardar Episodios
            if ($new_id && isset($data['episodes_data']) && is_array($data['episodes_data'])) {
                $episodeCacheService->saveForAnime((int) $new_id, $data['episodes_data']);
            }

            ApiResponse::success(array('message' => 'Anime processed successfully', 'id' => $new_id, 'action' => $action));

        } catch (Exception $e) {
            if (isset($dbConn) && $dbConn->inTransaction()) {
                $dbConn->rollBack();
            }
            error_log("SaveAnime Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            ApiResponse::error("Error en servidor: " . $e->getMessage(), 500, array(
                'file' => basename($e->getFile()),
                'line' => $e->getLine()
            ));
        }
    }
}
