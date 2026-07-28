<?php
/**
 * ==============================================================================================
 * MÓDULO DE ADMINISTRACIÓN DE NOVAIRLINES (VERSIÓN PROCEDURAL SIMPLE)
 * ==============================================================================================
 * Este archivo contiene las funciones para que los usuarios con rol 'admin' puedan
 * obtener estadísticas, gestionar reservas, modificar estados, listar usuarios y eliminarlos.
 * ==============================================================================================
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Obtiene las métricas generales de la plataforma (Ventas, Reservas, Pasajeros, Usuarios, IA).
 */
function obtener_estadisticas_admin() {
    $conexion = conectar_db();
    if (!$conexion) return [];

    $stats = [
        'total_ventas' => 0.00,
        'total_reservas' => 0,
        'total_pasajeros' => 0,
        'total_usuarios' => 0,
        'total_consultas_ia' => 0
    ];

    try {
        // 1. Total ventas (excluyendo canceladas)
        $sql_ventas = "SELECT COALESCE(SUM(precio_total), 0) FROM reservas WHERE estado != 'Cancelada'";
        $stats['total_ventas'] = (float)$conexion->query($sql_ventas)->fetchColumn();

        // 2. Total de reservas
        $sql_res = "SELECT COUNT(*) FROM reservas";
        $stats['total_reservas'] = (int)$conexion->query($sql_res)->fetchColumn();

        // 3. Total de pasajeros registrados
        $sql_pas = "SELECT COUNT(*) FROM pasajeros";
        $stats['total_pasajeros'] = (int)$conexion->query($sql_pas)->fetchColumn();

        // 4. Total de usuarios registrados
        $sql_usr = "SELECT COUNT(*) FROM usuarios";
        $stats['total_usuarios'] = (int)$conexion->query($sql_usr)->fetchColumn();

        // 5. Total de consultas a la IA
        $sql_ia = "SELECT COUNT(*) FROM consultas_ia";
        $stats['total_consultas_ia'] = (int)$conexion->query($sql_ia)->fetchColumn();

    } catch (PDOException $e) {
        // En caso de error se retornan los valores inicializados en 0
    }

    return $stats;
}

/**
 * Obtiene todas las reservas de la aerolínea con los detalles de vuelo y pasajero(s).
 */
function obtener_todas_reservas_admin() {
    $conexion = conectar_db();
    if (!$conexion) return [];

    $sql = "SELECT r.*, u.nombre AS usuario_nombre, u.email AS usuario_email,
                   v.numero_vuelo, v.duracion, v.precio AS precio_vuelo,
                   a_dep.codigo_iata AS origen_iata, a_dep.ciudad AS origen_ciudad,
                   a_arr.codigo_iata AS destino_iata, a_arr.ciudad AS destino_ciudad,
                   air.nombre AS aerolinea_nombre
            FROM reservas r
            LEFT JOIN usuarios u ON r.usuario_id = u.id
            LEFT JOIN vuelos v ON r.vuelo_id = v.id
            LEFT JOIN aeropuertos a_dep ON v.origen_aeropuerto_id = a_dep.id
            LEFT JOIN aeropuertos a_arr ON v.destino_aeropuerto_id = a_arr.id
            LEFT JOIN aerolineas air ON v.aerolinea_id = air.id
            ORDER BY r.fecha_reserva DESC";

    $consulta = $conexion->query($sql);
    $reservas = $consulta ? $consulta->fetchAll(PDO::FETCH_ASSOC) : [];

    // Adjuntamos la lista de pasajeros a cada reserva
    foreach ($reservas as &$r) {
        $sql_p = "SELECT * FROM pasajeros WHERE reserva_id = :reserva_id";
        $stmt_p = $conexion->prepare($sql_p);
        $stmt_p->bindParam(':reserva_id', $r['id']);
        $stmt_p->execute();
        $r['pasajeros'] = $stmt_p->fetchAll(PDO::FETCH_ASSOC);
    }

    return $reservas;
}

/**
 * Obtiene la lista completa de usuarios registrados y la cantidad de compras que han realizado.
 */
function obtener_todos_usuarios_admin() {
    $conexion = conectar_db();
    if (!$conexion) return [];

    $sql = "SELECT u.id, u.nombre, u.email, u.rol, u.creado_en,
                   COUNT(r.id) AS total_compras,
                   COALESCE(SUM(r.precio_total), 0) AS total_gastado
            FROM usuarios u
            LEFT JOIN reservas r ON u.id = r.usuario_id AND r.estado != 'Cancelada'
            GROUP BY u.id
            ORDER BY u.id ASC";

    $consulta = $conexion->query($sql);
    return $consulta ? $consulta->fetchAll(PDO::FETCH_ASSOC) : [];
}

/**
 * Obtiene el historial de consultas registradas por la Inteligencia Artificial.
 */
function obtener_consultas_ia_admin() {
    $conexion = conectar_db();
    if (!$conexion) return [];

    $sql = "SELECT c.*, u.nombre AS usuario_nombre, u.email AS usuario_email
            FROM consultas_ia c
            LEFT JOIN usuarios u ON c.usuario_id = u.id
            ORDER BY c.fecha_consulta DESC
            LIMIT 50";

    $consulta = $conexion->query($sql);
    return $consulta ? $consulta->fetchAll(PDO::FETCH_ASSOC) : [];
}

/**
 * Actualiza el estado de una reserva (Confirmada, Checked-in, Cancelada).
 */
function actualizar_estado_reserva_admin($pnr, $nuevo_estado) {
    $conexion = conectar_db();
    if (!$conexion) return false;

    $estados_validos = ['Pendiente', 'Confirmada', 'Checked-in', 'Cancelada'];
    if (!in_array($nuevo_estado, $estados_validos)) return false;

    $sql = "UPDATE reservas SET estado = :estado WHERE pnr = :pnr";
    $consulta = $conexion->prepare($sql);
    $consulta->bindParam(':estado', $nuevo_estado);
    $consulta->bindParam(':pnr', $pnr);

    return $consulta->execute();
}

/**
 * Actualiza el rol de un usuario ('admin' o 'cliente').
 */
function actualizar_rol_usuario_admin($usuario_id, $nuevo_rol) {
    $conexion = conectar_db();
    if (!$conexion) return false;

    $roles_validos = ['cliente', 'admin'];
    if (!in_array($nuevo_rol, $roles_validos)) return false;

    $sql = "UPDATE usuarios SET rol = :rol WHERE id = :id";
    $consulta = $conexion->prepare($sql);
    $consulta->bindParam(':rol', $nuevo_rol);
    $consulta->bindParam(':id', $usuario_id);

    return $consulta->execute();
}

/**
 * Elimina de forma segura un usuario de la base de datos.
 * Evita la auto-eliminación del administrador actual y mantiene la integridad de las reservas asignando usuario_id = NULL.
 */
function eliminar_usuario_seguro_admin($usuario_id_a_eliminar, $usuario_admin_actual_id) {
    $conexion = conectar_db();
    if (!$conexion) return false;

    // 1. Protección: El administrador no puede eliminarse a sí mismo
    if ((int)$usuario_id_a_eliminar === (int)$usuario_admin_actual_id) {
        return false;
    }

    try {
        // 2. Desvinculamos el usuario de las reservas para preservar el historial financiero
        $sql_res = "UPDATE reservas SET usuario_id = NULL WHERE usuario_id = :id";
        $stmt_res = $conexion->prepare($sql_res);
        $stmt_res->bindParam(':id', $usuario_id_a_eliminar);
        $stmt_res->execute();

        // 3. Desvinculamos las consultas de IA
        $sql_ia = "UPDATE consultas_ia SET usuario_id = NULL WHERE usuario_id = :id";
        $stmt_ia = $conexion->prepare($sql_ia);
        $stmt_ia->bindParam(':id', $usuario_id_a_eliminar);
        $stmt_ia->execute();

        // 4. Eliminamos el usuario de la tabla usuarios
        $sql_del = "DELETE FROM usuarios WHERE id = :id";
        $stmt_del = $conexion->prepare($sql_del);
        $stmt_del->bindParam(':id', $usuario_id_a_eliminar);

        return $stmt_del->execute();

    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Permite la creación de un nuevo usuario cliente o administrador desde el panel de control.
 */
function crear_usuario_admin($nombre, $email, $password, $rol = 'cliente') {
    $conexion = conectar_db();
    if (!$conexion) return false;

    $password_encriptada = password_hash($password, PASSWORD_BCRYPT);
    $rol_final = ($rol === 'admin') ? 'admin' : 'cliente';

    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)";
    $consulta = $conexion->prepare($sql);
    $consulta->bindParam(':nombre', $nombre);
    $consulta->bindParam(':email', $email);
    $consulta->bindParam(':password', $password_encriptada);
    $consulta->bindParam(':rol', $rol_final);

    try {
        return $consulta->execute();
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Permite subir/reemplazar la fotografía de un destino desde el panel de administración.
 */
function subir_imagen_destino_admin($ciudad_slug, $archivo_file) {
    if (empty($archivo_file['name']) || $archivo_file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $ext = strtolower(pathinfo($archivo_file['name'], PATHINFO_EXTENSION));
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
    
    if (!in_array($ext, $extensiones_permitidas)) {
        return false;
    }

    $dir_destinos = __DIR__ . '/../assets/img/destinos/';
    if (!is_dir($dir_destinos)) {
        mkdir($dir_destinos, 0755, true);
    }

    $slug_limpio = preg_replace('/[^a-z0-9_-]/', '', strtolower($ciudad_slug));
    if (empty($slug_limpio)) {
        return false;
    }

    // Eliminar versiones anteriores con otras extensiones si existieran
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $old_ext) {
        $old_file = $dir_destinos . $slug_limpio . '.' . $old_ext;
        if (file_exists($old_file)) {
            @unlink($old_file);
        }
    }

    $extension_guardar = ($ext === 'jpeg') ? 'jpg' : $ext;
    $target_file = $dir_destinos . $slug_limpio . '.' . $extension_guardar;

    return move_uploaded_file($archivo_file['tmp_name'], $target_file);
}
?>

