<?php
/**
 * ==============================================================================================
 * MÓDULO DE GESTIÓN DE USUARIOS (VERSIÓN PROCEDURAL SIMPLE)
 * ==============================================================================================
 * Este archivo contiene las funciones para el registro e inicio de sesión de usuarios.
 * Utiliza funciones en lugar de clases para mantener la máxima sencillez.
 * ==============================================================================================
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Función para verificar las credenciales de inicio de sesión de un usuario.
 * Retorna los datos del usuario si el login es correcto, o false si es incorrecto.
 */
function login_usuario($email, $password) {
    // 1. Obtenemos la conexión a la base de datos
    $conexion = conectar_db();
    if (!$conexion) {
        return false;
    }

    // 2. Preparamos la consulta SQL para buscar el usuario por su correo
    $sql = "SELECT id, nombre, email, rol, password FROM usuarios WHERE email = :email";
    $consulta = $conexion->prepare($sql);
    $consulta->bindParam(':email', $email);
    $consulta->execute();

    // 3. Verificamos si encontramos un usuario registrado con ese email
    if ($consulta->rowCount() > 0) {
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        // 4. Verificamos la contraseña encriptada usando password_verify
        if (password_verify($password, $usuario['password'])) {
            // Eliminamos la contraseña del arreglo antes de retornarlo por seguridad
            unset($usuario['password']);
            return $usuario;
        }
    }

    // Si no se encuentra el usuario o la contraseña es incorrecta
    return false;
}

/**
 * Función para registrar un nuevo usuario cliente en la base de datos.
 * Retorna true si se registró exitosamente, o false si hubo un error.
 */
function registrar_usuario($nombre, $email, $password) {
    // 1. Obtenemos la conexión a la base de datos
    $conexion = conectar_db();
    if (!$conexion) {
        return false;
    }

    // 2. Encriptamos la contraseña para guardarla de forma segura
    $password_encriptada = password_hash($password, PASSWORD_BCRYPT);

    // 3. Preparamos la consulta SQL de inserción
    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'cliente')";
    $consulta = $conexion->prepare($sql);

    $consulta->bindParam(':nombre', $nombre);
    $consulta->bindParam(':email', $email);
    $consulta->bindParam(':password', $password_encriptada);

    // 4. Ejecutamos la consulta y manejamos posibles errores (ejemplo: email duplicado)
    try {
        if ($consulta->execute()) {
            return true;
        }
    } catch (PDOException $e) {
        return false;
    }

    return false;
}
?>
