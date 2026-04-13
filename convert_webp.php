<?php
$dir = __DIR__ . '/public/img';
$files = glob($dir . '/*.png');
foreach ($files as $file) {
    if (file_exists($file)) {
        $img = imagecreatefrompng($file);
        if ($img !== false) {
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);
            $webp_file = str_replace('.png', '.webp', $file);
            imagewebp($img, $webp_file, 85);
            imagedestroy($img);
            echo "Converted: " . basename($file) . " to .webp\n";
        } else {
            echo "Failed: " . basename($file) . "\n";
        }
    }
}
