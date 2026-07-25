<?php
// config/env.php
// Función súper simple para cargar el archivo .env en las variables de entorno nativas de PHP
function cargarEnv($path) {
    if (!file_exists($path)) {
        return false; // Si no existe el archivo .env, no hacemos nada (quizás en producción las variables ya están inyectadas en el servidor)
    }

    $lineas = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        // Ignorar comentarios
        if (strpos(trim($linea), '#') === 0) {
            continue;
        }

        // Separar clave y valor
        $partes = explode('=', $linea, 2);
        if (count($partes) == 2) {
            $clave = trim($partes[0]);
            $valor = trim($partes[1]);

            // Remover comillas si las tuviera (opcional pero buena práctica)
            $valor = trim($valor, "\"'");

            // Guardar en las variables globales de entorno de PHP
            putenv(sprintf('%s=%s', $clave, $valor));
            $_ENV[$clave] = $valor;
            $_SERVER[$clave] = $valor;
        }
    }
    return true;
}

// Ejecutamos la función inmediatamente al requerir este archivo
cargarEnv(__DIR__ . '/../.env');
?>
