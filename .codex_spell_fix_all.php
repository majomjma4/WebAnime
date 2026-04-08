<?php
$targets = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS));
foreach ($it as $file) {
  if (!$file->isFile()) continue;
  $path = $file->getPathname();
  if (!preg_match('/\.(php|js|html|css)$/i', $path)) continue;
  if (strpos($path, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR) !== false) continue;
  $targets[] = $path;
}
$map = [
  'Géneros' => 'Géneros',
  'géneros' => 'géneros',
  'Año' => 'Año',
  'año' => 'año',
  'Catálogo' => 'Catálogo',
  'catálogo' => 'catálogo',
  'Películas' => 'Películas',
  'películas' => 'películas',
  'Película' => 'Película',
  'película' => 'película',
  'Ocurrió' => 'Ocurrió',
  'Puntuación' => 'Puntuación',
  'Duración' => 'Duración',
  'Acción' => 'Acción',
  'Fantasía' => 'Fantasía',
  'Añadir' => 'Añadir',
  'Información' => 'Información',
  'Galería' => 'Galería',
  'Título' => 'Título',
  'título' => 'título',
  'Títulos' => 'Títulos',
  'títulos' => 'títulos',
  'opinión' => 'opinión',
  'Sé' => 'Sé',
  'calificación' => 'calificación',
  'Cuéntanos por qué' => 'Cuéntanos por qué',
  'Todavía' => 'Todavía',
  'español' => 'español',
  'sesión' => 'sesión',
  'contraseña' => 'contraseña',
  'contraseña' => 'contraseña',
  'sesión' => 'sesión',
  'catálogo' => 'catálogo',
  'Catálogo' => 'Catálogo',
  'título' => 'título',
  'Título' => 'Título',
  'calificación' => 'calificación',
  'puntuación' => 'puntuación',
  'duración' => 'duración',
  'acción' => 'acción',
  'fantasía' => 'fantasía',
  'todavía' => 'todavía',
  'opinión' => 'opinión',
  'administración' => 'administración',
  'Aquí' => 'Aquí',
  'aquí' => 'aquí',
  'podrá' => 'podrá',
  'borrará' => 'borrará',
  'acción' => 'acción',
  'también' => 'también',
];
foreach ($targets as $path) {
  $content = file_get_contents($path);
  $updated = strtr($content, $map);
  if ($updated !== $content) {
    file_put_contents($path, $updated);
    echo str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $path), PHP_EOL;
  }
}
?>
