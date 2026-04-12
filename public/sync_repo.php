<?php
/**
 * Standalone Deployment Script for CPanel users without Terminal access.
 * This script pulls the latest changes from the repository.
 */

// Configuración del comando facilitado anteriormente
$workingDir = '/home/jcsfacyt/public_html/Nekoralist/';
$command = 'git fetch --all && git reset --hard origin/master 2>&1';

header('Content-Type: text/plain; charset=UTF-8');

echo "--- Iniciando Sincronización de Repositorio ---\n";
echo "Directorio: $workingDir\n";

if (!is_dir($workingDir)) {
    echo "ERROR: El directorio no existe o no es accesible.\n";
    exit;
}

chdir($workingDir);

echo "Ejecutando: $command\n\n";

$output = shell_exec($command);

if ($output === null) {
    echo "ERROR: No se pudo ejecutar el comando. shell_exec está deshabilitado o git no está disponible.\n";
} else {
    echo "RESULTADO:\n";
    echo $output;
}

echo "\n--- Sincronización Finalizada ---";
