<?php
/**
 * ==============================================================================================
 * MÓDULO DE GESTIÓN DE VUELOS (VERSIÓN PROCEDURAL SIMPLE)
 * ==============================================================================================
 * Este archivo contiene todas las funciones para consultar, buscar y guardar vuelos
 * en la base de datos local del proyecto.
 * ==============================================================================================
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Función para obtener la lista completa de todos los vuelos registrados en la BD local.
 */
function obtener_todos_los_vuelos() {
    $conexion = conectar_db();
    if (!$conexion) return [];

    $sql = "SELECT v.*, 
                   a_origen.codigo_iata as origen_iata, a_origen.nombre as origen_nombre,
                   a_destino.codigo_iata as destino_iata, a_destino.nombre as destino_nombre, a_destino.ciudad as destino_ciudad,
                   al.nombre as aerolinea_nombre
            FROM vuelos v
            JOIN aeropuertos a_origen ON v.origen_aeropuerto_id = a_origen.id
            JOIN aeropuertos a_destino ON v.destino_aeropuerto_id = a_destino.id
            JOIN aerolineas al ON v.aerolinea_id = al.id";
    
    $consulta = $conexion->prepare($sql);
    $consulta->execute();
    
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Búsqueda de vuelos tradicionales en la base de datos local por nombre de ciudad o IATA.
 */
function buscar_vuelos_locales($destino_ciudad = '') {
    $conexion = conectar_db();
    if (!$conexion) return [];

    $sql = "SELECT v.*, 
                   a_origen.codigo_iata as origen_iata, a_origen.nombre as origen_nombre,
                   a_destino.codigo_iata as destino_iata, a_destino.nombre as destino_nombre, a_destino.ciudad as destino_ciudad,
                   al.nombre as aerolinea_nombre
            FROM vuelos v
            JOIN aeropuertos a_origen ON v.origen_aeropuerto_id = a_origen.id
            JOIN aeropuertos a_destino ON v.destino_aeropuerto_id = a_destino.id
            JOIN aerolineas al ON v.aerolinea_id = al.id";
              
    if (!empty($destino_ciudad)) {
        $sql .= " WHERE a_destino.ciudad LIKE :destino OR a_destino.codigo_iata LIKE :destino";
    }

    $consulta = $conexion->prepare($sql);
    
    if (!empty($destino_ciudad)) {
        $busqueda = "%" . $destino_ciudad . "%";
        $consulta->bindParam(':destino', $busqueda);
    }
    
    $consulta->execute();
    
    $resultados = [];
    while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $resultados[] = [
            'id' => $fila['id'],
            'airline' => $fila['aerolinea_nombre'],
            'flight_number' => $fila['numero_vuelo'],
            'departure_time' => date('H:i', strtotime($fila['hora_salida'])),
            'departure_airport' => $fila['origen_iata'],
            'departure_city' => $fila['origen_nombre'],
            'departure_airport_name' => $fila['origen_nombre'],
            
            'arrival_time' => date('H:i', strtotime($fila['hora_llegada'])),
            'arrival_next_day' => (bool)$fila['llegada_dia_siguiente'],
            'arrival_airport' => $fila['destino_iata'],
            'arrival_city' => $fila['destino_ciudad'],
            'arrival_airport_name' => $fila['destino_nombre'],
            'duration' => $fila['duracion'],
            'stops' => $fila['escalas'],
            'price' => $fila['precio'],
            'best_price' => (bool)$fila['es_mejor_precio']
        ];
    }
    return $resultados;
}

/**
 * Obtener un vuelo específico de la BD local por su ID numérico.
 */
function obtener_vuelo_por_id($id_vuelo) {
    $conexion = conectar_db();
    if (!$conexion) return null;

    $sql = "SELECT v.*, 
                   a_origen.codigo_iata as origen_iata, a_origen.nombre as origen_nombre,
                   a_destino.codigo_iata as destino_iata, a_destino.nombre as destino_nombre, a_destino.ciudad as destino_ciudad,
                   al.nombre as aerolinea_nombre
            FROM vuelos v
            JOIN aeropuertos a_origen ON v.origen_aeropuerto_id = a_origen.id
            JOIN aeropuertos a_destino ON v.destino_aeropuerto_id = a_destino.id
            JOIN aerolineas al ON v.aerolinea_id = al.id
            WHERE v.id = :id";
    
    $consulta = $conexion->prepare($sql);
    $consulta->bindParam(':id', $id_vuelo);
    $consulta->execute();
    
    if ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
        return [
            'id' => $fila['id'],
            'airline' => $fila['aerolinea_nombre'],
            'flight_number' => $fila['numero_vuelo'],
            'departure_time' => date('H:i', strtotime($fila['hora_salida'])),
            'departure_airport' => $fila['origen_iata'],
            'departure_city' => $fila['origen_nombre'],
            'departure_airport_name' => $fila['origen_nombre'],
            
            'arrival_time' => date('H:i', strtotime($fila['hora_llegada'])),
            'arrival_next_day' => (bool)$fila['llegada_dia_siguiente'],
            'arrival_airport' => $fila['destino_iata'],
            'arrival_city' => $fila['destino_ciudad'],
            'arrival_airport_name' => $fila['destino_nombre'],
            'duration' => $fila['duracion'],
            'stops' => $fila['escalas'],
            'price' => $fila['precio'],
            'best_price' => (bool)$fila['es_mejor_precio']
        ];
    }
    return null;
}

/**
 * Obtiene la lista formateada de aeropuertos para el autocompletado del buscador.
 * Ejemplo de salida: "Lima (LIM) - Perú"
 */
function obtener_filtros_destinos() {
    $conexion = conectar_db();
    if (!$conexion) return [];

    $sql = "SELECT codigo_iata, ciudad, pais, nombre FROM aeropuertos ORDER BY ciudad ASC";
    $consulta = $conexion->prepare($sql);
    $consulta->execute();
    
    $resultados = [];
    while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $resultados[] = $fila['ciudad'] . ' (' . $fila['codigo_iata'] . ') - ' . $fila['pais'];
    }
    return $resultados;
}

/**
 * Obtiene la lista de nombres de aerolíneas disponibles para filtros.
 */
function obtener_filtros_aerolineas() {
    $conexion = conectar_db();
    if (!$conexion) return [];

    $sql = "SELECT nombre FROM aerolineas ORDER BY nombre ASC";
    $consulta = $conexion->prepare($sql);
    $consulta->execute();
    
    $resultados = [];
    while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $resultados[] = $fila['nombre'];
    }
    return $resultados;
}

/**
 * Convierte el nombre de una ciudad o texto al código IATA de 3 letras (ej: "Lima" -> "LIM").
 */
/**
 * Convierte el nombre de una ciudad o texto al código IATA de 3 letras (ej: "París" -> "CDG").
 */
function obtener_iata_por_ciudad($nombre_ciudad) {
    $nombre_ciudad = trim($nombre_ciudad);
    if (empty($nombre_ciudad)) {
        return 'LIM'; // Valor por defecto si el campo está vacío
    }

    // 1. Extraer código IATA si viene explícito entre paréntesis como "(CDG)" o "(LIM)"
    if (preg_match('/\(([A-Z]{3})\)/i', $nombre_ciudad, $coincidencias)) {
        return strtoupper($coincidencias[1]);
    }

    // 2. Si el texto ingresado ES un código IATA de 3 letras directamente (ej: "CDG", "LIM", "MAD")
    $texto_limpio = strtoupper($nombre_ciudad);
    if (preg_match('/^[A-Z]{3}$/', $texto_limpio)) {
        return $texto_limpio;
    }

    // 3. Normalizar texto para comparación sin acentos ni mayúsculas
    $normalizado = mb_strtolower($nombre_ciudad, 'UTF-8');
    $normalizado = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
        ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
        $normalizado
    );

    // 4. Mapeo de Diccionario Directo para Ciudades y Destinos Principales (Alta Prioridad)
    $diccionario_destinos = [
        // París / Francia
        'paris' => 'CDG', 'cdg' => 'CDG', 'orly' => 'ORY', 'francia' => 'CDG', 'charles de gaulle' => 'CDG',
        // Lima / Perú
        'lima' => 'LIM', 'lim' => 'LIM', 'peru' => 'LIM', 'jorge chavez' => 'LIM',
        // Madrid / España
        'madrid' => 'MAD', 'mad' => 'MAD', 'barajas' => 'MAD', 'espana' => 'MAD', 'espana' => 'MAD',
        // Miami / EE.UU.
        'miami' => 'MIA', 'mia' => 'MIA',
        // Cusco / Perú
        'cusco' => 'CUZ', 'cuzco' => 'CUZ', 'cuz' => 'CUZ',
        // Bogotá / Colombia
        'bogota' => 'BOG', 'bog' => 'BOG', 'colombia' => 'BOG', 'el dorado' => 'BOG',
        // Nueva York / EE.UU.
        'new york' => 'JFK', 'nueva york' => 'JFK', 'jfk' => 'JFK', 'nyc' => 'JFK',
        // Londres / Reino Unido
        'londres' => 'LHR', 'london' => 'LHR', 'lhr' => 'LHR',
        // Roma / Italia
        'roma' => 'FCO', 'rome' => 'FCO', 'fco' => 'FCO', 'italia' => 'FCO',
        // Tokio / Japón
        'tokio' => 'HND', 'tokyo' => 'HND', 'hnd' => 'HND', 'japon' => 'HND',
        // Berlín / Alemania
        'berlin' => 'BER', 'ber' => 'BER', 'alemania' => 'BER',
        // Buenos Aires / Argentina
        'buenos aires' => 'EZE', 'eze' => 'EZE', 'argentina' => 'EZE',
        // Seúl / Corea del Sur
        'seul' => 'ICN', 'icn' => 'ICN', 'corea' => 'ICN',
        // Ámsterdam / Países Bajos
        'amsterdam' => 'AMS', 'ams' => 'AMS',
        // Cancún / México
        'cancun' => 'CUN', 'cun' => 'CUN', 'mexico' => 'CUN',
        // Santiago / Chile
        'santiago' => 'SCL', 'scl' => 'SCL', 'chile' => 'SCL'
    ];

    foreach ($diccionario_destinos as $clave => $iata) {
        if ($normalizado === $clave || preg_match('/\b' . preg_quote($clave, '/') . '\b/u', $normalizado)) {
            return $iata;
        }
    }

    // 5. Búsqueda en la base de datos local de aeropuertos con ORDEN DE PRIORIDAD estricto
    // Se prioriza coincidencia exacta en código IATA y ciudad sobre coincidencia parcial en nombre de aeropuerto
    $conexion = conectar_db();
    if ($conexion) {
        $sql = "SELECT codigo_iata FROM aeropuertos 
                WHERE codigo_iata = :exacto 
                   OR ciudad LIKE :busqueda_exacta
                   OR ciudad LIKE :busqueda
                   OR nombre LIKE :busqueda
                ORDER BY 
                   (codigo_iata = :exacto) DESC,
                   (ciudad LIKE :busqueda_exacta) DESC,
                   (ciudad LIKE :busqueda) DESC
                LIMIT 1";
        $consulta = $conexion->prepare($sql);
        $texto_busqueda = "%" . $nombre_ciudad . "%";
        $busqueda_exacta = $nombre_ciudad;
        $codigo_exacto = strtoupper($nombre_ciudad);
        
        $consulta->bindParam(':exacto', $codigo_exacto);
        $consulta->bindParam(':busqueda_exacta', $busqueda_exacta);
        $consulta->bindParam(':busqueda', $texto_busqueda);
        $consulta->execute();
        
        if ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
            return $fila['codigo_iata'];
        }
    }

    // 6. Fallback dinámico usando la API de Duffel Places
    require_once __DIR__ . '/DuffelAPI.php';
    $iata_duffel = sugerir_iata_duffel($nombre_ciudad);
    if (!empty($iata_duffel)) {
        return $iata_duffel;
    }

    // 7. Si todo lo anterior falla, retornamos 'LIM' por defecto
    return 'LIM';
}

/**
 * Verifica si un aeropuerto existe en la BD local; si no existe, lo registra y retorna su ID.
 */
function garantizar_aeropuerto($codigo_iata, $nombre, $ciudad = '', $pais = '') {
    $conexion = conectar_db();
    if (!$conexion) return null;

    // Buscar si ya existe
    $sql = "SELECT id FROM aeropuertos WHERE codigo_iata = :iata LIMIT 1";
    $consulta = $conexion->prepare($sql);
    $consulta->bindParam(':iata', $codigo_iata);
    $consulta->execute();
    
    if ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
        return $fila['id'];
    }

    // Insertar nuevo aeropuerto si no existe
    $sql_insert = "INSERT INTO aeropuertos (codigo_iata, nombre, ciudad, pais) VALUES (:iata, :nombre, :ciudad, :pais)";
    $consulta_insert = $conexion->prepare($sql_insert);
    
    if (empty($ciudad)) $ciudad = $nombre;
    if (empty($pais)) $pais = 'Desconocido';
    
    $consulta_insert->bindParam(':iata', $codigo_iata);
    $consulta_insert->bindParam(':nombre', $nombre);
    $consulta_insert->bindParam(':ciudad', $ciudad);
    $consulta_insert->bindParam(':pais', $pais);
    $consulta_insert->execute();
    
    return $conexion->lastInsertId();
}

/**
 * Verifica si una aerolínea existe en la BD local; si no existe, la registra y retorna su ID.
 */
function garantizar_aerolinea($codigo_iata, $nombre) {
    $conexion = conectar_db();
    if (!$conexion) return null;

    $sql = "SELECT id FROM aerolineas WHERE codigo_iata = :iata LIMIT 1";
    $consulta = $conexion->prepare($sql);
    $consulta->bindParam(':iata', $codigo_iata);
    $consulta->execute();
    
    if ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
        return $fila['id'];
    }

    $sql_insert = "INSERT INTO aerolineas (codigo_iata, nombre) VALUES (:iata, :nombre)";
    $consulta_insert = $conexion->prepare($sql_insert);
    $consulta_insert->bindParam(':iata', $codigo_iata);
    $consulta_insert->bindParam(':nombre', $nombre);
    $consulta_insert->execute();
    
    return $conexion->lastInsertId();
}

/**
 * Guarda los datos de una oferta proveniente de Duffel en la BD local para mantener el historial.
 */
function guardar_vuelo_duffel($vuelo_oferta) {
    $conexion = conectar_db();
    if (!$conexion) return null;

    // Verificar si ya se guardó por el offer_id
    $sql_check = "SELECT id FROM vuelos WHERE duffel_offer_id = :offer_id LIMIT 1";
    $consulta_check = $conexion->prepare($sql_check);
    $consulta_check->bindParam(':offer_id', $vuelo_oferta['offer_id']);
    $consulta_check->execute();
    if ($fila = $consulta_check->fetch(PDO::FETCH_ASSOC)) {
        return $fila['id'];
    }

    // Datos del aeropuerto de origen y destino
    $nombre_origen = $vuelo_oferta['outbound']['departure_airport_name'] ?? $vuelo_oferta['departure_airport'];
    $ciudad_origen = $vuelo_oferta['outbound']['departure_city'] ?? $vuelo_oferta['departure_airport'];
    $pais_origen = $vuelo_oferta['outbound']['departure_country'] ?? '';
    $id_origen = garantizar_aeropuerto($vuelo_oferta['departure_airport'], $nombre_origen, $ciudad_origen, $pais_origen);

    $nombre_destino = $vuelo_oferta['outbound']['arrival_airport_name'] ?? $vuelo_oferta['arrival_airport'];
    $ciudad_destino = $vuelo_oferta['outbound']['arrival_city'] ?? $vuelo_oferta['arrival_airport'];
    $pais_destino = $vuelo_oferta['outbound']['arrival_country'] ?? '';
    $id_destino = garantizar_aeropuerto($vuelo_oferta['arrival_airport'], $nombre_destino, $ciudad_destino, $pais_destino);

    // Garantizar aerolínea
    $codigo_aero = substr($vuelo_oferta['flight_number'], 0, 2);
    $id_aerolinea = garantizar_aerolinea($codigo_aero, $vuelo_oferta['airline']);

    // Insertar vuelo
    $sql_insert = "INSERT INTO vuelos (aerolinea_id, numero_vuelo, origen_aeropuerto_id, destino_aeropuerto_id, hora_salida, hora_llegada, llegada_dia_siguiente, duracion, escalas, precio, duffel_offer_id) 
                   VALUES (:aero_id, :num_vuelo, :orig_id, :dest_id, :h_salida, :h_llegada, :dia_sig, :duracion, :escalas, :precio, :offer_id)";
    
    $consulta_insert = $conexion->prepare($sql_insert);
    
    $hora_salida = date('H:i:s', strtotime($vuelo_oferta['departure_time']));
    $hora_llegada = date('H:i:s', strtotime($vuelo_oferta['arrival_time']));
    $dia_siguiente = !empty($vuelo_oferta['arrival_next_day']) ? 1 : 0;
    
    $consulta_insert->bindParam(':aero_id', $id_aerolinea);
    $consulta_insert->bindParam(':num_vuelo', $vuelo_oferta['flight_number']);
    $consulta_insert->bindParam(':orig_id', $id_origen);
    $consulta_insert->bindParam(':dest_id', $id_destino);
    $consulta_insert->bindParam(':h_salida', $hora_salida);
    $consulta_insert->bindParam(':h_llegada', $hora_llegada);
    $consulta_insert->bindParam(':dia_sig', $dia_siguiente, PDO::PARAM_INT);
    $consulta_insert->bindParam(':duracion', $vuelo_oferta['duration']);
    $consulta_insert->bindParam(':escalas', $vuelo_oferta['stops']);
    $consulta_insert->bindParam(':precio', $vuelo_oferta['price']);
    $consulta_insert->bindParam(':offer_id', $vuelo_oferta['offer_id']);
    
    $consulta_insert->execute();
    return $conexion->lastInsertId();
}

/**
 * Obtiene el modo actual de ofertas configurado en el sistema ('peru_destacadas' o 'duffel_api').
 */
function obtener_modo_ofertas() {
    $archivo = __DIR__ . '/../config/settings.json';
    if (file_exists($archivo)) {
        $json = json_decode(file_get_contents($archivo), true);
        if (isset($json['modo_ofertas'])) {
            return $json['modo_ofertas'];
        }
    }
    return 'peru_destacadas';
}

/**
 * Actualiza el modo de ofertas en el archivo de configuración settings.json.
 */
function actualizar_modo_ofertas($nuevo_modo) {
    $archivo = __DIR__ . '/../config/settings.json';
    $modos_validos = ['peru_destacadas', 'duffel_api'];
    if (!in_array($nuevo_modo, $modos_validos)) {
        return false;
    }
    $datos = ['modo_ofertas' => $nuevo_modo];
    return file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Obtiene la ruta física de la imagen de un destino o null si no existe.
 */
function obtener_imagen_destino($ciudad) {
    $ciudad_norm = mb_strtolower(trim($ciudad), 'UTF-8');
    $ciudad_norm = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $ciudad_norm);
    
    // Mapeo específico por ciudad
    if (strpos($ciudad_norm, 'tarapoto') !== false) {
        $slug = 'tarapoto';
    } elseif (strpos($ciudad_norm, 'cusco') !== false || strpos($ciudad_norm, 'cuzco') !== false) {
        $slug = 'cusco';
    } elseif (strpos($ciudad_norm, 'arequipa') !== false) {
        $slug = 'arequipa';
    } elseif (strpos($ciudad_norm, 'bogota') !== false) {
        $slug = 'bogota';
    } elseif (strpos($ciudad_norm, 'paris') !== false) {
        $slug = 'hero_paris';
    } elseif (strpos($ciudad_norm, 'lima') !== false || strpos($ciudad_norm, 'peru') !== false) {
        $slug = 'hero_peru';
    } else {
        $slug = preg_replace('/[^a-z0-9]/', '', $ciudad_norm);
    }

    $base_dir = __DIR__ . '/../assets/img/';
    
    // Comprobar primero en subcarpeta destinos/
    if (file_exists($base_dir . 'destinos/' . $slug . '.png')) {
        return 'assets/img/destinos/' . $slug . '.png';
    }
    if (file_exists($base_dir . 'destinos/' . $slug . '.jpg')) {
        return 'assets/img/destinos/' . $slug . '.jpg';
    }
    // Comprobar en raíz de img/
    if (file_exists($base_dir . $slug . '.png')) {
        return 'assets/img/' . $slug . '.png';
    }
    if (file_exists($base_dir . $slug . '.jpg')) {
        return 'assets/img/' . $slug . '.jpg';
    }

    return null; // Fallback automático a ícono
}

/**
 * Obtiene una imagen de alta resolución para la sección de Destinos Populares
 */
function obtener_imagen_popular($ciudad) {
    $img_local = obtener_imagen_destino($ciudad);
    if (!empty($img_local)) return $img_local;

    $ciudad_norm = mb_strtolower(trim($ciudad), 'UTF-8');
    if (strpos($ciudad_norm, 'miami') !== false) {
        return 'https://images.unsplash.com/photo-1506966953377-3f925a26eedc?auto=format&fit=crop&w=600&q=80';
    } elseif (strpos($ciudad_norm, 'madrid') !== false) {
        return 'https://images.unsplash.com/photo-1539037116277-4db20889f2d4?auto=format&fit=crop&w=600&q=80';
    } elseif (strpos($ciudad_norm, 'bogota') !== false) {
        return 'https://images.unsplash.com/photo-1599309329204-ed13cf3e3bdf?auto=format&fit=crop&w=600&q=80';
    } elseif (strpos($ciudad_norm, 'buenos') !== false) {
        return 'https://images.unsplash.com/photo-1589909202802-8f4aadce1849?auto=format&fit=crop&w=600&q=80';
    } elseif (strpos($ciudad_norm, 'iquitos') !== false) {
        return 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&w=600&q=80';
    }
    
    return 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=600&q=80';
}

/**
 * Retorna las 3 ofertas exclusivas destacadas de Perú (Tarapoto, Cusco, Arequipa)
 */
function obtener_ofertas_peru_destacadas() {
    return [
        [
            'id' => 'peru_tpp',
            'origen_iata' => 'LIM',
            'origen_nombre' => 'Lima',
            'destino_ciudad' => 'Tarapoto',
            'destino_iata' => 'TPP',
            'aerolinea_nombre' => 'NovAirlines Boutique',
            'escalas' => 0,
            'precio' => 189.00,
            'tag' => 'Selva & Palmeras',
            'desc' => 'Disfrute de la magia de la Laguna Azul y la selva alta en un vuelo directo exclusivo.',
            'imagen' => obtener_imagen_destino('Tarapoto')
        ],
        [
            'id' => 'peru_cuz',
            'origen_iata' => 'LIM',
            'origen_nombre' => 'Lima',
            'destino_ciudad' => 'Cusco',
            'destino_iata' => 'CUZ',
            'aerolinea_nombre' => 'NovAirlines Boutique',
            'escalas' => 0,
            'precio' => 219.00,
            'tag' => 'Machu Picchu & Valle Sagrado',
            'desc' => 'Conecte con la capital del Imperio Inca y la majestuosidad de los Andes con servicio de primera.',
            'imagen' => obtener_imagen_destino('Cusco')
        ],
        [
            'id' => 'peru_aqp',
            'origen_iata' => 'LIM',
            'origen_nombre' => 'Lima',
            'destino_ciudad' => 'Arequipa',
            'destino_iata' => 'AQP',
            'aerolinea_nombre' => 'NovAirlines Boutique',
            'escalas' => 0,
            'precio' => 199.00,
            'tag' => 'Ciudad Blanca & Colca',
            'desc' => 'Admire los majestuosos volcanes Misti y Chachani y la mejor arquitectura en sillar de Sudamérica.',
            'imagen' => obtener_imagen_destino('Arequipa')
        ]
    ];
}
?>

