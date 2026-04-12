<?php
namespace Services;

use PDO;

class EpisodeCacheService
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function ensureTable()
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS anime_episodes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            anime_id INT NOT NULL,
            episode_number INT NOT NULL,
            title VARCHAR(255) DEFAULT NULL,
            synopsis TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_anime_episode (anime_id, episode_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function getByAnimeId($animeId)
    {
        if ($animeId <= 0) {
            return array();
        }

        $stmt = $this->db->prepare('SELECT episode_number, title, synopsis FROM anime_episodes WHERE anime_id = ? ORDER BY episode_number ASC');
        $stmt->execute(array($animeId));

        $items = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = array(
                'number' => (int) (isset($row['episode_number']) ? $row['episode_number'] : 0),
                'title' => (string) (isset($row['title']) ? $row['title'] : ''),
                'synopsis' => (string) (isset($row['synopsis']) ? $row['synopsis'] : ''),
            );
        }

        return $items;
    }

    public function saveForAnime($animeId, $episodesData)
    {
        if ($animeId <= 0 || !$episodesData) {
            return;
        }

        $this->ensureTable();
        $stmt = $this->db->prepare("INSERT INTO anime_episodes (anime_id, episode_number, title, synopsis) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                title = CASE WHEN VALUES(title) IS NOT NULL AND VALUES(title) <> '' THEN VALUES(title) ELSE title END,
                synopsis = CASE WHEN VALUES(synopsis) IS NOT NULL AND VALUES(synopsis) <> '' THEN VALUES(synopsis) ELSE synopsis END,
                updated_at = CURRENT_TIMESTAMP");

        foreach ($episodesData as $episode) {
            $episodeNumber = (int) (isset($episode['number']) ? $episode['number'] : (isset($episode['episode_number']) ? $episode['episode_number'] : 0));
            if ($episodeNumber <= 0) {
                continue;
            }

            $title = trim((string) (isset($episode['title']) ? $episode['title'] : ''));
            $synopsis = trim((string) (isset($episode['synopsis']) ? $episode['synopsis'] : ''));
            if ($title === '' && $synopsis === '') {
                continue;
            }

            $stmt->execute(array($animeId, $episodeNumber, $title, $synopsis));
        }
    }
}
