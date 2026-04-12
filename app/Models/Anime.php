<?php
namespace Models;

use PDO;
use Exception;

class Anime
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Obtiene un anime por su ID (interno o mal_id)
     */
    public function getById($id)
    {
        if (!$this->db)
            return null;

        // Intentar primero por mal_id, si no por id interno
        $stmt = $this->db->prepare("SELECT * FROM anime WHERE mal_id = :id OR id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $anime = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($anime) {
            $anime['generos'] = $this->getGeneros($anime['id']);
            return $anime;
        }

        // Auto-importación si no existe en BD (asumiendo que $id que llega del front casi siempre es mal_id)
        if (is_numeric($id) && $id > 0) {
            return $this->importFromJikanByMalId($id);
        }

        return null;
    }

    /**
     * Obtiene un anime por su titulo aproximado (Soporte Frontend Legacy)
     */
    public function getByTitle($title)
    {
        if (!$this->db)
            return null;

        $stmt = $this->db->prepare("SELECT * FROM anime WHERE titulo = :title OR titulo_ingles = :title LIMIT 1");
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->execute();
        $anime = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$anime) {
            $stmt = $this->db->prepare("SELECT * FROM anime WHERE titulo LIKE :liketitle OR titulo_ingles LIKE :liketitle LIMIT 1");
            $likeStr = "%" . $title . "%";
            $stmt->bindParam(':liketitle', $likeStr, PDO::PARAM_STR);
            $stmt->execute();
            $anime = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($anime) {
            $anime['generos'] = $this->getGeneros($anime['id']);
            return $anime;
        }

        // Auto-importación si no está en BD
        return $this->importFromJikanByTitle($title);
    }

    /**
     * Obtiene los géneros asociados a un anime_id
     */
    public function getGeneros($anime_id)
    {
        $stmt = $this->db->prepare("
            SELECT g.nombre 
            FROM generos g 
            INNER JOIN anime_generos ag ON g.id = ag.genero_id 
            WHERE ag.anime_id = :anime_id
        ");
        $stmt->bindParam(':anime_id', $anime_id, PDO::PARAM_INT);
        $stmt->execute();

        $generos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $generos[] = $row['nombre'];
        }
        return $generos;
    }

    /**
     * Traduce texto usando Google Translate (gratuito)
     */
    private function translateToSpanish($text)
    {
        if (empty($text))
            return '';
        $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=es&dt=t&q=" . urlencode($text);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        $res = curl_exec($ch);
        curl_close($ch);

        $translated = '';
        if ($res) {
            $json = json_decode($res, true);
            if (isset($json[0]) && is_array($json[0])) {
                foreach ($json[0] as $segment) {
                    $translated .= $segment[0] ?? '';
                }
            }
        }
        return $translated ?: $text;
    }

    /**
     * Importa desde Jikan por MAL_ID con soporte para Rate Limit
     */
    private function importFromJikanByMalId($mal_id, $retries = 2)
    {
        $url = "https://api.jikan.moe/v4/anime/" . (int) $mal_id . "/full";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "WebAnimeAuto/1.0");
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        $res = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status === 429 && $retries > 0) {
            sleep(2);
            return $this->importFromJikanByMalId($mal_id, $retries - 1);
        }

        if ($status !== 200 || !$res)
            return null;
        $data = json_decode($res, true);
        if (!isset($data['data']))
            return null;

        return $this->saveJikanDataToDB($data['data']);
    }

    /**
     * Importa desde Jikan por Título con soporte para Rate Limit
     */
    private function importFromJikanByTitle($title, $retries = 2)
    {
        $url = "https://api.jikan.moe/v4/anime?q=" . urlencode($title) . "&limit=1";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "WebAnimeAuto/1.0");
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        $res = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status === 429 && $retries > 0) {
            sleep(2);
            return $this->importFromJikanByTitle($title, $retries - 1);
        }

        if ($status !== 200 || !$res)
            return null;
        $data = json_decode($res, true);
        if (empty($data['data'][0]))
            return null;

        return $this->saveJikanDataToDB($data['data'][0]);
    }

    /**
     * Guarda la data de Jikan en la base de datos y la devuelve formateada
     */
    private function saveJikanDataToDB($a)
    {
        // Formatear campos
        $mal_id = $a['mal_id'] ?? null;
        if (!$mal_id)
            return null;

        // Verificar si por casualidad alguien ya lo ingresó en este milisegundo o mediante titulo previamente
        $check = $this->db->prepare("SELECT id FROM anime WHERE mal_id = ?");
        $check->execute([$mal_id]);
        if ($check->fetchColumn())
            return $this->getById($mal_id);

        $titulo = substr($a['title'] ?? 'Desconocido', 0, 255);
        $titulo_ingles = substr($a['title_english'] ?? '', 0, 255);
        $tipo = $a['type'] ?? 'TV';
        $estadoRaw = $a['status'] ?? '';
        $estado = substr($estadoRaw, 0, 50);
        $episodes = $a['episodes'] ?? null;
        $anio = $a['year'] ?? $a['aired']['prop']['from']['year'] ?? null;
        $rating = substr($a['rating'] ?? '', 0, 50);

        $synopsisEn = $a['synopsis'] ?? null;
        $synopsisEs = $this->translateToSpanish($synopsisEn);

        $img = $a['images']['webp']['large_image_url'] ?? $a['images']['jpg']['large_image_url'] ?? null;
        $trailer_url = $a['trailer']['url'] ?? null;
        $score = $a['score'] ?? null;

        try {
            $insert = $this->db->prepare("INSERT INTO anime (mal_id, titulo, titulo_ingles, tipo, estado, episodios, anio, clasificacion, sinopsis, imagen_url, trailer_url, puntuacion, activo) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,1)");
            $insert->execute([$mal_id, $titulo, $titulo_ingles, $tipo, $estado, $episodes, $anio, $rating, $synopsisEs, $img, $trailer_url, $score]);

            $internal_id = $this->db->lastInsertId();

            // Géneros
            if (isset($a['genres']) && is_array($a['genres'])) {
                foreach ($a['genres'] as $g) {
                    $gName = substr($g['name'] ?? '', 0, 50);
                    if (!$gName)
                        continue;
                    $s = $this->db->prepare("SELECT id FROM generos WHERE nombre = ?");
                    $s->execute([$gName]);
                    $gId = $s->fetchColumn();
                    if (!$gId) {
                        $this->db->prepare("INSERT INTO generos (nombre) VALUES (?)")->execute([$gName]);
                        $gId = $this->db->lastInsertId();
                    }
                    $this->db->prepare("INSERT INTO anime_generos (anime_id, genero_id) VALUES (?, ?)")->execute([$internal_id, $gId]);
                }
            }

            return $this->getById($mal_id);

        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../db_import_error.log', "JIKAN IMPORT ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            return null;
        }
    }

    /**
     * Obtiene los nombres de géneros seguros (excluyendo +18)
     */
    public function getFilteredGenres()
    {
        if (!$this->db)
            return [];
        $restricted = ["'Hentai'", "'Erotica'", "'Ecchi'", "'Yaoi'", "'Yuri'", "'Gore'", "'Harem'", "'Reverse Harem'", "'Rx'", "'Girls Love'", "'Boys Love'"];
        $sql = "SELECT nombre FROM generos 
                WHERE nombre NOT IN (" . implode(",", $restricted) . ") 
                ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtiene películas seguras (excluyendo +18)
     */
    public function getMovies()
    {
        if (!$this->db)
            return [];
        $restrictedGenres = ["'Hentai'", "'Erotica'", "'Ecchi'", "'Yaoi'", "'Yuri'", "'Girls Love'", "'Boys Love'"];
        $restrictedTitles = ["'%does it count if%'", "'%futanari%'"];

        $sql = "SELECT * FROM anime 
                WHERE tipo = 'Movie' 
                  AND id NOT IN (
                    SELECT anime_id FROM anime_generos 
                    WHERE genero_id IN (SELECT id FROM generos WHERE nombre IN (" . implode(",", $restrictedGenres) . "))
                  ) 
                  AND LOWER(titulo) NOT LIKE " . implode(" AND LOWER(titulo) NOT LIKE ", $restrictedTitles) . "
                ORDER BY puntuacion DESC, id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($animes as &$a) {
            $a['generos_str'] = implode(',', array_map('strtolower', $this->getGeneros($a['id'])));
        }
        return $animes;
    }

    /**
     * Obtiene series seguras (excluyendo +18) con orden de prioridad
     */
    public function getSeries()
    {
        if (!$this->db)
            return [];
        $restrictedGenres = ["'Hentai'", "'Erotica'", "'Ecchi'", "'Yaoi'", "'Yuri'", "'Girls Love'", "'Boys Love'"];
        $restrictedTitles = ["'%does it count if%'", "'%futanari%'"];

        $sql = "SELECT * FROM anime 
                WHERE tipo != 'Movie' 
                  AND id NOT IN (
                    SELECT anime_id FROM anime_generos 
                    WHERE genero_id IN (SELECT id FROM generos WHERE nombre IN (" . implode(",", $restrictedGenres) . "))
                  ) 
                  AND LOWER(titulo) NOT LIKE " . implode(" AND LOWER(titulo) NOT LIKE ", $restrictedTitles) . "
                ORDER BY 
                  CASE 
                    WHEN titulo LIKE 'Shingeki no Kyojin%' THEN 0 
                    WHEN titulo = 'Fullmetal Alchemist: Brotherhood' THEN 1 
                    WHEN titulo = 'Steins;Gate' THEN 2 
                    WHEN titulo LIKE 'Hunter x Hunter%' THEN 3 
                    WHEN titulo LIKE 'Kimetsu no Yaiba%' THEN 4 
                    WHEN titulo LIKE 'Jujutsu Kaisen%' THEN 5 
                    WHEN titulo LIKE 'Chainsaw Man%' THEN 6 
                    WHEN titulo LIKE 'Spy x Family%' THEN 7 
                    WHEN titulo LIKE 'Haikyuu!!%' THEN 8 
                    WHEN titulo LIKE 'Boku no Hero Academia%' THEN 9 
                    WHEN titulo LIKE 'One Piece%' THEN 10 
                    WHEN titulo LIKE 'Naruto%' THEN 11 
                    WHEN titulo LIKE 'Bleach%' THEN 12 
                    WHEN titulo LIKE 'Sousou no Frieren%' THEN 13 
                    WHEN titulo = 'Gintama' THEN 80 
                    WHEN titulo LIKE 'Gintama%' THEN 90 
                    ELSE 40 
                  END ASC, 
                  puntuacion DESC, 
                  id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $animes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($animes as &$a) {
            $a['generos_str'] = implode(',', array_map('strtolower', $this->getGeneros($a['id'])));
        }
        return $animes;
    }

    /**
     * Obtiene catálogo administrativo aplicando restricciones de seguridad
     */
    public function getCatalog($page = 1, $perPage = 50, $search = '', $status = 'ALL', $type = 'ALL', $year = '')
    {
        if (!$this->db)
            return ['data' => [], 'total' => 0];

        $restrictedGenres = ["'Hentai'", "'Erotica'", "'Ecchi'", "'Yaoi'", "'Yuri'", "'Girls Love'", "'Boys Love'"];
        $restrictedTitles = ["'%does it count if%'", "'%futanari%'"];

        $where = [
            "id NOT IN (SELECT anime_id FROM anime_generos WHERE genero_id IN (SELECT id FROM generos WHERE nombre IN (" . implode(",", $restrictedGenres) . ")))",
            "LOWER(titulo) NOT LIKE " . implode(" AND LOWER(titulo) NOT LIKE ", $restrictedTitles)
        ];
        $params = [];

        if (!empty($search)) {
            $where[] = "(titulo LIKE :search OR tipo LIKE :search OR estado LIKE :search OR CAST(anio AS CHAR) LIKE :search OR estudio LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($status !== 'ALL' && !empty($status)) {
            $statusMap = [
                'EN EMISION' => ['en emision', 'en emisi?n', 'currently airing'],
                'FINALIZADO' => ['finished airing', 'finalizado', 'finalizada'],
                'PROXIMAMENTE' => ['not yet aired', 'proximamente', 'pr?ximamente'],
            ];
            $normalizedStatus = strtoupper($status);
            if (isset($statusMap[$normalizedStatus])) {
                $parts = [];
                foreach ($statusMap[$normalizedStatus] as $index => $statusValue) {
                    $key = ':status_' . $index;
                    $parts[] = 'LOWER(estado) = ' . $key;
                    $params[$key] = $statusValue;
                }
                $where[] = '(' . implode(' OR ', $parts) . ')';
            }
        }

        if ($type !== 'ALL' && !empty($type)) {
            $where[] = "UPPER(tipo) = :type";
            $params[':type'] = strtoupper($type);
        }

        if (!empty($year)) {
            $where[] = "CAST(anio AS CHAR) LIKE :year";
            $params[':year'] = '%' . $year . '%';
        }

        $whereSql = " WHERE " . implode(" AND ", $where);

        // Contar el total
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM anime" . $whereSql);
        foreach ($params as $k => $v)
            $countStmt->bindValue($k, $v);
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $totalPages);
        $offset = ($page - 1) * $perPage;

        // Obtener lista
        $listStmt = $this->db->prepare("SELECT * FROM anime" . $whereSql . " ORDER BY id DESC LIMIT :limit OFFSET :offset");
        foreach ($params as $k => $v)
            $listStmt->bindValue($k, $v);
        $listStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $listStmt->execute();
        $animes = $listStmt->fetchAll(PDO::FETCH_ASSOC);

        // Conteo rápido de "En emisión" para el dashboard
        $airingCount = (int) $this->db->query("SELECT COUNT(*) FROM anime WHERE LOWER(estado) IN ('en emision', 'currently airing') AND id NOT IN (SELECT anime_id FROM anime_generos WHERE genero_id IN (SELECT id FROM generos WHERE nombre IN (" . implode(",", $restrictedGenres) . ")))")->fetchColumn();

        return [
            'data' => $animes,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'airingCount' => $airingCount
        ];
    }
}
