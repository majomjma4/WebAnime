<?php
namespace Models;

use PDO;
use Exception;

class Anime {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Obtiene un anime por su ID (interno o mal_id)
     */
    public function getById($id) {
        if (!$this->db) return null;

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
     * Obtiene un anime por su título aproximado (Soporte Frontend Legacy)
     */
    public function getByTitle($title) {
        if (!$this->db) return null;
        
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
    public function getGeneros($anime_id) {
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
    private function translateToSpanish($text) {
        if (empty($text)) return '';
        $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=es&dt=t&q=" . urlencode($text);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
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
    private function importFromJikanByMalId($mal_id, $retries = 2) {
        $url = "https://api.jikan.moe/v4/anime/" . (int)$mal_id . "/full";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "WebAnimeAuto/1.0");
        $res = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status === 429 && $retries > 0) {
            sleep(2);
            return $this->importFromJikanByMalId($mal_id, $retries - 1);
        }

        if ($status !== 200 || !$res) return null;
        $data = json_decode($res, true);
        if (!isset($data['data'])) return null;

        return $this->saveJikanDataToDB($data['data']);
    }

    /**
     * Importa desde Jikan por Título con soporte para Rate Limit
     */
    private function importFromJikanByTitle($title, $retries = 2) {
        $url = "https://api.jikan.moe/v4/anime?q=" . urlencode($title) . "&limit=1";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "WebAnimeAuto/1.0");
        $res = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status === 429 && $retries > 0) {
            sleep(2);
            return $this->importFromJikanByTitle($title, $retries - 1);
        }

        if ($status !== 200 || !$res) return null;
        $data = json_decode($res, true);
        if (empty($data['data'][0])) return null;

        return $this->saveJikanDataToDB($data['data'][0]);
    }

    /**
     * Guarda la data de Jikan en la base de datos y la devuelve formateada
     */
    private function saveJikanDataToDB($a) {
        // Formatear campos
        $mal_id = $a['mal_id'] ?? null;
        if (!$mal_id) return null;

        // Verificar si por casualidad alguien ya lo ingresó en este milisegundo o mediante título previamente
        $check = $this->db->prepare("SELECT id FROM anime WHERE mal_id = ?");
        $check->execute([$mal_id]);
        if ($check->fetchColumn()) return $this->getById($mal_id);

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
                    if (!$gName) continue;
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
}
