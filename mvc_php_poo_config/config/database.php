<?php
/**
 * ==============================================================================================
 * ARCHIVO DE CONEXIÓN A LA BASE DE DATOS (VERSIÓN PROCEDURAL SIMPLE)
 * ==============================================================================================
 * Este archivo establece la conexión entre PHP y la base de datos MySQL.
 * Está diseñado sin clases ni POO para que los estudiantes de nivel básico 
 * entiendan y puedan explicar fácilmente cada línea durante su sustentación.
 * ==============================================================================================
 */

// Cargamos la función para leer el archivo de configuración .env
require_once __DIR__ . '/env.php';

/**
 * Función principal para obtener una conexión activa a MySQL.
 * Retorna un objeto de conexión PDO o null si falla.
 */
function conectar_db() {
    // 1. Leemos las variables de configuración del entorno (o usamos valores por defecto)
    $servidor = $_ENV['DB_HOST'] ?? 'localhost';
    $base_datos = $_ENV['DB_NAME'] ?? 'novairlines_db';
    $usuario = $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['DB_PASS'] ?? '';

    try {
        // 2. Creamos la cadena de conexión PDO especificando el servidor y la base de datos
        $cadena_conexion = "mysql:host=" . $servidor . ";dbname=" . $base_datos . ";charset=utf8";

        // 3. Intentamos conectar a la base de datos
        $conexion = new PDO($cadena_conexion, $usuario, $password);

        // 4. Configuramos el modo de errores para que lance excepciones claras si hay fallos
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 5. Retornamos la conexión lista para ser usada en las consultas
        return $conexion;

    } catch (PDOException $error) {
        // En caso de error de conexión, mostramos un mensaje explicativo y retornamos null
        echo "Error al conectar con la Base de Datos: " . $error->getMessage();
        return null;
    }
}
?>
