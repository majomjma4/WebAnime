<?php
namespace Models;

use PDO;
use Exception;

class Anime
{
    private $db;

    public function __construct()
    {
        $dbConn = new Database();
        $this->db = $dbConn->getConnection();
    }

    /**
     * Obtiene un anime por su ID (interno o mal_id)
     */
    public function getById($id)
    {
        if (!$this->db)
            return null;

        // Cambiamos a parámetros posicionales (?) para evitar errores en servidores estrictos
        $stmt = $this->db->prepare("SELECT * FROM anime WHERE mal_id = ? OR id = ? LIMIT 1");
        $stmt->execute(array($id, $id));
        $anime = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($anime) {
            $anime['generos'] = $this->getGeneros($anime['id']);
            return $anime;
        }

        return null;
    }

    public function getGeneros($anime_id)
    {
        $stmt = $this->db->prepare("
            SELECT g.nombre 
            FROM generos g 
            INNER JOIN anime_generos ag ON g.id = ag.genero_id 
            WHERE ag.anime_id = ?
        ");
        $stmt->execute(array($anime_id));

        $generos = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $generos[] = $row['nombre'];
        }
        return $generos;
    }

    public function getFilteredGenres()
    {
        if (!$this->db)
            return array();
        $restricted = array("'Hentai'", "'Erotica'", "'Ecchi'", "'Yaoi'", "'Yuri'", "'Gore'", "'Harem'", "'Reverse Harem'", "'Rx'", "'Girls Love'", "'Boys Love'");
        $sql = "SELECT nombre FROM generos 
                WHERE nombre NOT IN (" . implode(",", $restricted) . ") 
                ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getMovies()
    {
        if (!$this->db)
            return array();
        $restrictedGenres = array("'Hentai'", "'Erotica'", "'Ecchi'", "'Yaoi'", "'Yuri'", "'Girls Love'", "'Boys Love'");
        $restrictedTitles = array("'%does it count if%'", "'%futanari%'");

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

        foreach ($animes as $k => $a) {
            $animes[$k]['generos_str'] = implode(',', array_map('strtolower', $this->getGeneros($a['id'])));
        }
        return $animes;
    }

    public function getSeries()
    {
        if (!$this->db)
            return array();
        $restrictedGenres = array("'Hentai'", "'Erotica'", "'Ecchi'", "'Yaoi'", "'Yuri'", "'Girls Love'", "'Boys Love'");
        $restrictedTitles = array("'%does it count if%'", "'%futanari%'");

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

        foreach ($animes as $k => $a) {
            $animes[$k]['generos_str'] = implode(',', array_map('strtolower', $this->getGeneros($a['id'])));
        }
        return $animes;
    }
}
