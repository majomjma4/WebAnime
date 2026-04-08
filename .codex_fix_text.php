<?php
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS));
$map = [
    "á" => "á",
    "é" => "é",
    "í" => "í",
    "ó" => "ó",
    "ú" => "ú",
    "ñ" => "ñ",
    "á" => "á",
    "é" => "é",
    "í" => "í",
    "ó" => "ó",
    "ú" => "ú",
    "ñ" => "ñ",
    "á" => "á",
    "é" => "é",
    "í" => "í",
    "ó" => "ó",
    "ú" => "ú",
    "ñ" => "ñ",
    "¿" => "¿",
    "¡" => "¡",
    "¿" => "¿",
    "¡" => "¡",
    "á" => "á",
    "é" => "é",
    "í" => "í",
    "ó" => "ó",
    "ú" => "ú",
    "ñ" => "ñ",
    "ü" => "ü",
    "Ü" => "Ü",
    "Película" => "Película",
    "Películas" => "Películas",
    "Catálogo" => "Catálogo",
    "Puntuación" => "Puntuación",
    "Duración" => "Duración",
    "Título" => "Título",
    "Emisión" => "Emisión",
    "Clasificación" => "Clasificación",
    "Acción" => "Acción",
    "Fantasía" => "Fantasía",
    "Próximamente" => "Próximamente",
    "En emisión" => "En emisión",
    "Público" => "Público",
    "Todavía" => "Todavía",
    "más" => "más",
    "año" => "año",
    "años" => "años",
    "Inténtalo" => "Inténtalo",
    "sesión" => "sesión",
    "contraseña" => "contraseña",
    "información" => "información",
    "animación" => "animación",
    "publicación" => "publicación",
    "ocurrió" => "ocurrió",
    "catálogo" => "catálogo",
];
foreach ($rii as $file) {
    if (!$file->isFile()) continue;
    $path = $file->getPathname();
    if (!preg_match('/\\.(php|js|html|css)$/i', $path)) continue;
    if (strpos($path, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR) !== false) continue;
    $content = file_get_contents($path);
    $updated = strtr($content, $map);
    if ($updated !== $content) {
        file_put_contents($path, $updated);
        echo $path, PHP_EOL;
    }
}
?>
