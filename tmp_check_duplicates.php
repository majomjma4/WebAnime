<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = (new \Models\Database())->getConnection();

echo "Checking for duplicates in anime_characters table...\n";

// Query to find duplicate mal_id for the same anime_id
$stmt = $db->query("
    SELECT anime_id, mal_id, COUNT(*) as count, GROUP_CONCAT(name) as names
    FROM anime_characters
    GROUP BY anime_id, mal_id
    HAVING count > 1
");

$duplicates = $stmt->fetchAll();

if (empty($duplicates)) {
    echo "No duplicates found based on anime_id and mal_id.\n";
} else {
    echo "Found " . count($duplicates) . " sets of duplicates.\n";
    foreach ($duplicates as $i => $d) {
        if ($i < 10) {
            echo "- Anime ID {$d['anime_id']}, MAL ID {$d['mal_id']}: Count {$d['count']}\n";
        }
    }
}

// Total count
$total = $db->query("SELECT COUNT(*) FROM anime_characters")->fetchColumn();
echo "\nTotal characters in table: $total\n";
?>
