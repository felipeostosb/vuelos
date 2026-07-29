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

/**
 * Obtener perfil completo del usuario por su ID
 */
function obtener_usuario_por_id($usuario_id) {
    $conexion = conectar_db();
    if (!$conexion) return null;
    $sql = "SELECT id, nombre, email, rol, modo_autopilot, tipo_documento_pref, numero_documento_pref, tarjeta_mascarada_pref, creado_en FROM usuarios WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id' => $usuario_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/**
 * Actualizar preferencias de Modo Auto-Pilot del usuario
 */
function actualizar_config_autopilot($usuario_id, $modo, $doc_tipo, $doc_num, $tarjeta) {
    $conexion = conectar_db();
    if (!$conexion) return false;
    $sql = "UPDATE usuarios 
            SET modo_autopilot = :modo, 
                tipo_documento_pref = :doc_tipo, 
                numero_documento_pref = :doc_num, 
                tarjeta_mascarada_pref = :tarjeta 
            WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([
        ':modo' => (int)$modo,
        ':doc_tipo' => $doc_tipo,
        ':doc_num' => $doc_num,
        ':tarjeta' => $tarjeta,
        ':id' => $usuario_id
    ]);
}

/**
 * Obtener lista de acompañantes guardados del usuario
 */
function obtener_acompanantes_usuario($usuario_id) {
    $conexion = conectar_db();
    if (!$conexion) return [];
    $sql = "SELECT * FROM usuario_acompanantes WHERE usuario_id = :usuario_id ORDER BY id DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':usuario_id' => $usuario_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Registrar un nuevo acompañante habitual
 */
function agregar_acompanante($usuario_id, $nombre, $apellido, $tipo_doc, $num_doc) {
    $conexion = conectar_db();
    if (!$conexion) return false;
    $sql = "INSERT INTO usuario_acompanantes (usuario_id, nombre, apellido, tipo_documento, numero_documento) 
            VALUES (:usuario_id, :nombre, :apellido, :tipo_doc, :num_doc)";
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':tipo_doc' => $tipo_doc,
        ':num_doc' => $num_doc
    ]);
}

/**
 * Eliminar un acompañante por ID y usuario_id
 */
function eliminar_acompanante($id, $usuario_id) {
    $conexion = conectar_db();
    if (!$conexion) return false;
    $sql = "DELETE FROM usuario_acompanantes WHERE id = :id AND usuario_id = :usuario_id";
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([':id' => $id, ':usuario_id' => $usuario_id]);
}
?>
