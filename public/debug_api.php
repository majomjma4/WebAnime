<?php
/**
 * debug_api.php - Diagnóstico de Conexión Jikan
 * Este archivo prueba diferentes configuraciones para saltar bloqueos de red.
 */

header('Content-Type: text/plain; charset=UTF-8');

function test_connection($name, $options = array()) {
    echo "--- Probando: $name ---\n";
    $url = 'https://api.jikan.moe/v4/top/anime?limit=1&sfw=1';
    $ch = curl_init($url);
    
    // Opciones base
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    
    // Aplicar opciones específicas
    foreach ($options as $opt => $val) {
        curl_setopt($ch, $opt, $val);
    }
    
    $start = microtime(true);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    $error = curl_error($ch);
    $time = round(microtime(true) - $start, 3);
    curl_close($ch);
    
    if ($info['http_code'] === 200) {
        echo "✅ RESULTADO: ÉXITO (200 OK)\n";
        echo "Tiempo: {$time}s\n";
    } else {
        echo "❌ RESULTADO: FALLÓ ({$info['http_code']})\n";
        echo "Error CURL: $error\n";
        echo "IP Destino: " . (isset($info['primary_ip']) ? $info['primary_ip'] : 'N/A') . "\n";
    }
    echo "\n";
}

echo "DIAGNÓSTICO DE CONEXIÓN JIKAN\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
echo "IP Servidor: " . $_SERVER['SERVER_ADDR'] . "\n\n";

// Prueba 1: Configuración estándar
test_connection("Estándar (CURL Default)", array(
    CURLOPT_USERAGENT => 'PHP/' . PHP_VERSION
));

// Prueba 2: Navegador Real (Chrome)
test_connection("Simulando Chrome (User-Agent real)", array(
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36'
));

// Prueba 3: Forzar IPv4
test_connection("Forzando IPv4 (Evitar errores de IPv6)", array(
    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    CURLOPT_USERAGENT => 'NekoraList-Bot/1.0'
));

// Prueba 4: Mix de Navegador + IPv4
test_connection("Mix (Chrome + IPv4 + Referer)", array(
    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
    CURLOPT_REFERER => 'https://www.google.com/'
));
