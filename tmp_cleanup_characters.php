<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = (new \Models\Database())->getConnection();

echo "Starting cleanup of duplicated characters...\n";

try {
    // Delete duplicate records keeping only the one with the lowest ID
    $sql = "
        DELETE c1 FROM anime_characters c1
        INNER JOIN anime_characters c2 
        WHERE c1.id > c2.id 
          AND c1.anime_id = c2.anime_id 
          AND c1.mal_id = c2.mal_id
    ";
    
    $affected = $db->exec($sql);
    echo "Successfully deleted $affected duplicated records.\n";
    
    // Check final count
    $total = $db->query("SELECT COUNT(*) FROM anime_characters")->fetchColumn();
    echo "Total characters remaining: $total\n";
    
} catch (Exception $e) {
    echo "Error during cleanup: " . $e->getMessage() . "\n";
}
?>
