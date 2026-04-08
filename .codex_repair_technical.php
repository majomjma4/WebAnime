<?php
$targets = [
  'app/Controllers/Api/ActivityController.php',
  'app/Controllers/Api/AdminController.php',
  'app/Controllers/Api/AnimeDataController.php',
  'app/Controllers/Api/AuthController.php',
  'app/Controllers/Api/CommentsController.php',
  'app/Controllers/Api/RequestsController.php',
  'app/Controllers/Api/SaveAnimeController.php',
  'app/Controllers/Api/UsersController.php',
  'app/Models/Anime.php',
  'app/Views/pages/peliculas.php',
  'app/Views/pages/series.php',
  'public/assets/js/i18n.js',
];
$map = [
  'título_ingles' => 'titulo_ingles',
  'título' => 'titulo',
  'puntuación' => 'puntuacion',
  'duración' => 'duracion',
  'acción' => 'accion',
  'sesiónes' => 'sesiones',
  'sesión_' => 'sesion_',
  'contraseña_' => 'contrasena_',
  'fantasía' => 'fantasia',
  'Interacciónes Recientes' => 'Interacciones Recientes',
];
foreach ($targets as $path) {
  $full = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
  $content = file_get_contents($full);
  $updated = strtr($content, $map);
  if ($updated !== $content) {
    file_put_contents($full, $updated);
    echo $path, PHP_EOL;
  }
}
?>
