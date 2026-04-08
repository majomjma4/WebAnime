<?php
$files = [
  'app/Views/pages/gestion.php',
  'app/Views/pages/gesCom.php',
  'app/Views/pages/index.php',
];
$maps = [
  'app/Views/pages/gestion.php' => [
    "['título']" => "['titulo']",
    "Sin título" => "Sin titulo",
  ],
  'app/Views/pages/gesCom.php' => [
    'Interacciónes Recientes' => 'Interacciones Recientes',
  ],
  'app/Views/pages/index.php' => [
    'vjidzOAñoBQUw' => 'vjidzOAnoBQUw',
  ],
];
foreach ($files as $path) {
  $full = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
  $content = file_get_contents($full);
  $updated = strtr($content, $maps[$path]);
  if ($updated !== $content) {
    file_put_contents($full, $updated);
    echo $path, PHP_EOL;
  }
}
?>
