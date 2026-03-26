<?php
try {
    $dbConn = new PDO('mysql:host=localhost;dbname=Webanime', 'root', '');
    $dbConn->exec("
        INSERT INTO comentarios (usuario_id, anime_id, cuerpo)
        SELECT u.id, a.id, '¡La animación en esta escena es absolutamente espectacular! MAPPA God.'
        FROM usuarios u CROSS JOIN anime a LIMIT 1;
        
        INSERT INTO comentarios (usuario_id, anime_id, cuerpo)
        SELECT u.id, a.id, 'El desarrollo del villano es espectacular. 10/10'
        FROM usuarios u CROSS JOIN anime a ORDER BY u.id DESC, a.id DESC LIMIT 1;
    ");
    echo "Seed Done";
} catch (Exception $e) { echo $e->getMessage(); }
