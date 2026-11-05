<?php
/**
 * MÓDULO DE LUGARES TURÍSTICOS Y JOYAS OCULTAS (VERSIÓN PROCEDURAL)
 * Orquesta la búsqueda de lugares en proveedores reales, sin datos de ejemplo:
 *
 *   Google Places (único con puntuaciones de usuarios)
 *   Geoapify + TomTom (se combinan y deduplican entre sí)
 *
 * Geoapify y TomTom no exponen puntuaciones: cuando no hay rating se muestra
 * "Sin puntaje todavía" en lugar de inventarlo. El módulo cachea resultados para
 * respetar los planes gratuitos y tolera la ausencia de MySQL.
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Lee un archivo JSON y devuelve un arreglo. Si algo falla, devuelve un arreglo vacío.
 */
function lugares_leer_json($ruta) {
    if (!is_file($ruta)) {
        return [];
    }

    $contenido = @file_get_contents($ruta);
    if ($contenido === false || trim($contenido) === '') {
        return [];
    }

    $datos = json_decode($contenido, true);
    return is_array($datos) ? $datos : [];
}

/**
 * Catálogo de destinos con coordenadas (Perú + internacional).
 */
function lugares_destinos_catalogo() {
    static $destinos = null;

    if ($destinos === null) {
        $destinos = lugares_leer_json(__DIR__ . '/../config/lugares_destinos.json');
    }

    return $destinos;
}

/**
 * Quita tildes y caracteres especiales para comparar textos sin depender de la ortografía.
 */
function lugares_normalizar($texto) {
    $texto = mb_strtolower(trim((string)$texto), 'UTF-8');

    $mapa = [
        'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a',
        'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
        'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o',
        'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
        'ñ' => 'n', 'ç' => 'c', 'ß' => 'ss',
    ];

    $texto = strtr($texto, $mapa);
    return preg_replace('/\s+/', ' ', $texto);
}

/**
 * Distancia en kilómetros entre dos coordenadas (fórmula de Haversine).
 */
function lugares_distancia_km($lat1, $lng1, $lat2, $lng2) {
    $radio_tierra = 6371.0;
    $d_lat = deg2rad((float)$lat2 - (float)$lat1);
    $d_lng = deg2rad((float)$lng2 - (float)$lng1);

    $a = sin($d_lat / 2) ** 2
       + cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) * sin($d_lng / 2) ** 2;

    return $radio_tierra * 2 * atan2(sqrt($a), sqrt(max(0.0, 1 - $a)));
}

/**
 * Clave fonética ligera para comparar nombres entre proveedores distintos.
 * TomTom y OpenStreetMap escriben lo mismo de formas diferentes ("Coricancha" y
 * "Qorikancha"), así que se unifican qu/c/k, z/s, y/i, v/b y se ignora la h.
 * Solo se usa para decidir si dos resultados son el mismo lugar.
 */
function lugares_clave_fonetica($texto) {
    $texto = lugares_normalizar($texto);
    $texto = preg_replace('/[^a-z0-9 ]/', ' ', (string)$texto);
    $texto = trim(preg_replace('/\s+/', ' ', (string)$texto));

    return trim(preg_replace('/\s+/', ' ', str_replace(
        ['qu', 'c', 'z', 'y', 'v', 'h'],
        ['k', 'k', 's', 'i', 'b', ''],
        $texto
    )));
}

function lugares_destino_por_slug($slug) {
    $slug = lugares_normalizar($slug);
    if ($slug === '') {
        return null;
    }

    foreach (lugares_destinos_catalogo() as $destino) {
        if (lugares_normalizar($destino['slug'] ?? '') === $slug) {
            return $destino;
        }
    }

    return null;
}

/**
 * Busca un destino por ciudad o combinación "ciudad, país".
 * No resuelve países solos: así "Perú" muestra todos los lugares peruanos del catálogo.
 */
function lugares_destino_por_texto($texto) {
    $q = lugares_normalizar($texto);
    if ($q === '') {
        return null;
    }

    $alias = [
        'new york' => 'nueva york',
        'nyc' => 'nueva york',
        'cuzco' => 'cusco',
        'rio' => 'rio de janeiro',
        'cdmx' => 'ciudad de mexico',
    ];

    if (isset($alias[$q])) {
        $q = $alias[$q];
    }

    $candidatos = [$q];
    foreach (preg_split('/[,;|]/', $q) as $parte) {
        $parte = trim($parte);
        if ($parte !== '' && $parte !== $q) {
            $candidatos[] = $parte;
        }
    }

    $mejor = null;
    $mejor_puntaje = 0;

    foreach (lugares_destinos_catalogo() as $destino) {
        $slug = lugares_normalizar($destino['slug'] ?? '');
        $ciudad = lugares_normalizar($destino['ciudad'] ?? '');
        $pais = lugares_normalizar($destino['pais'] ?? '');
        $ciudad_pais = trim($ciudad . ' ' . $pais);

        foreach ($candidatos as $candidato) {
            if ($candidato === $slug || $candidato === $ciudad || $candidato === $ciudad_pais) {
                return $destino;
            }

            $coincide = false;
            $peso = 0;
            $largo = mb_strlen($candidato);

            if ($largo >= 4 && str_starts_with($ciudad, $candidato)) {
                $coincide = true;
                $peso = 3;
            } elseif ($largo >= 4 && str_starts_with($slug, str_replace(' ', '-', $candidato))) {
                $coincide = true;
                $peso = 2;
            } elseif (mb_strlen($ciudad) >= 4 && str_starts_with($candidato, $ciudad)) {
                $coincide = true;
                $peso = 4;
            } elseif (mb_strlen($ciudad_pais) >= 4 && str_starts_with($candidato, $ciudad_pais)) {
                $coincide = true;
                $peso = 1;
            }

            if ($coincide) {
                $puntaje = mb_strlen($ciudad) + $peso;
                if ($puntaje > $mejor_puntaje) {
                    $mejor_puntaje = $puntaje;
                    $mejor = $destino;
                }
            }
        }
    }

    return $mejor;
}

/**
 * Devuelve los destinos de un país cuando el texto buscado ("Perú", "Japón")
 * no apunta a una ciudad concreta. Sin catálogo de lugares no tiene sentido listar
 * lugares de un país entero, así que la vista propone ciudades para elegir.
 */
function lugares_destinos_por_pais($texto) {
    $q = lugares_normalizar($texto);
    if ($q === '') {
        return [];
    }

    $encontrados = [];

    foreach (lugares_destinos_catalogo() as $destino) {
        $pais = lugares_normalizar($destino['pais'] ?? '');
        if ($pais === $q || str_contains($pais, $q) || str_contains($q, $pais)) {
            $encontrados[] = $destino;
        }
    }

    return $encontrados;
}

/**
 * Resuelve cualquier ciudad del mundo con el geocodificador de TomTom.
 * Evita depender de un catálogo cerrado: si el usuario escribe "Oaxaca" o
 * "Liubliana", se obtiene su coordenada real y se busca alrededor.
 * El resultado se guarda en sesión 7 días para no gastar cuota.
 */
function lugares_resolver_destino_dinamico($texto) {
    $texto = trim((string)$texto);
    if (mb_strlen($texto) < 3) {
        return null;
    }

    $api_key = trim($_ENV['TOMTOM_API_KEY'] ?? '');
    if ($api_key === '') {
        return null;
    }

    $clave = 'din|' . mb_strtolower($texto, 'UTF-8');
    if (isset($_SESSION['lugares_destinos_din'])) {
        $entrada = $_SESSION['lugares_destinos_din'][$clave] ?? null;
        if (is_array($entrada) && ($entrada['expira'] ?? 0) > time()) {
            return $entrada['destino'];
        }
    }

    $parametros = [
        'key' => $api_key,
        'limit' => 1,
        'language' => 'es-ES',
        'typeahead' => 'false',
    ];

    $respuesta = lugares_http_json(
        'https://api.tomtom.com/search/2/geocode/' . rawurlencode($texto) . '.json?' . http_build_query($parametros)
    );
    lugares_registrar_llamada();

    $posicion = $respuesta['results'][0]['position'] ?? null;
    if (!is_array($posicion)) {
        return null;
    }

    $direccion = $respuesta['results'][0]['address'] ?? [];
    $destino = [
        'slug' => 'din-' . substr(md5(mb_strtolower($texto, 'UTF-8')), 0, 10),
        'ciudad' => trim((string)($direccion['municipality'] ?? ($direccion['localName'] ?? $texto))),
        'pais' => trim((string)($direccion['country'] ?? '')),
        'lat' => (float)$posicion['lat'],
        'lng' => (float)$posicion['lon'],
        'radio' => (int)($_ENV['LUGARES_RADIO_DINAMICO'] ?? 25000),
        'dinamico' => true,
    ];

    if (session_status() === PHP_SESSION_ACTIVE) {
        if (!isset($_SESSION['lugares_destinos_din']) || !is_array($_SESSION['lugares_destinos_din'])) {
            $_SESSION['lugares_destinos_din'] = [];
        }

        $_SESSION['lugares_destinos_din'][$clave] = ['expira' => time() + 7 * 86400, 'destino' => $destino];

        if (count($_SESSION['lugares_destinos_din']) > 20) {
            $_SESSION['lugares_destinos_din'] = array_slice($_SESSION['lugares_destinos_din'], -20, null, true);
        }
    }

    return $destino;
}

/**
 * Decide qué proveedor usar.
 *
 *   auto     -> Google si hay clave; si no, Geoapify + TomTom combinados.
 *   google   -> solo Google Places (único con puntuaciones reales).
 *   geoapify -> solo Geoapify.
 *   tomtom   -> solo TomTom.
 *   ambos    -> Geoapify + TomTom fusionados (comportamiento por defecto).
 *
 * Ya no existe modo "seed": todos los datos vienen de APIs reales.
 */
function lugares_proveedor_activo() {
    $modo = strtolower(trim($_ENV['LUGARES_PROVIDER'] ?? 'auto'));
    $tiene_google = trim((string)($_ENV['GOOGLE_MAPS_API_KEY'] ?? '')) !== '';
    $tiene_geoapify = trim((string)($_ENV['GEOAPIFY_API_KEY'] ?? '')) !== '';
    $tiene_tomtom = trim((string)($_ENV['TOMTOM_API_KEY'] ?? '')) !== '';

    if (in_array($modo, ['google', 'geoapify', 'tomtom', 'ambos'], true)) {
        return $modo;
    }

    if ($tiene_google) {
        return 'google';
    }

    if ($tiene_geoapify || $tiene_tomtom) {
        return 'ambos';
    }

    return 'ninguno';
}

function lugares_cache_clave($proveedor, $destino) {
    return $proveedor . '|' . ($destino['slug'] ?? 'destino') . '|' . (int)($destino['radio'] ?? 25000);
}

/**
 * Google exige no cachear más de 30 días. Geoapify permite caché sin caducidad obligatoria.
 */
function lugares_cache_ttl($proveedor) {
    if ($proveedor === 'google') {
        $dias = (int)($_ENV['LUGARES_CACHE_TTL_DIAS'] ?? 30);
        $dias = max(1, min(30, $dias));
        return $dias * 86400;
    }

    return 90 * 86400;
}

function lugares_cache_sesion_leer($clave) {
    $almacen = (session_status() === PHP_SESSION_ACTIVE)
        ? ($_SESSION['lugares_cache'] ?? [])
        : ($GLOBALS['__lugares_cache_memoria'] ?? []);

    if (!isset($almacen[$clave]['datos']) || !is_array($almacen[$clave]['datos'])) {
        return null;
    }

    if (($almacen[$clave]['expira'] ?? 0) < time()) {
        return null;
    }

    return $almacen[$clave]['datos'];
}

function lugares_cache_sesion_guardar($clave, $datos, $expira) {
    $entrada = ['expira' => (int)$expira, 'datos' => $datos];

    if (session_status() === PHP_SESSION_ACTIVE) {
        if (!isset($_SESSION['lugares_cache']) || !is_array($_SESSION['lugares_cache'])) {
            $_SESSION['lugares_cache'] = [];
        }

        $_SESSION['lugares_cache'][$clave] = $entrada;

        if (count($_SESSION['lugares_cache']) > 12) {
            $_SESSION['lugares_cache'] = array_slice($_SESSION['lugares_cache'], -12, null, true);
        }

        return;
    }

    if (!isset($GLOBALS['__lugares_cache_memoria'])) {
        $GLOBALS['__lugares_cache_memoria'] = [];
    }

    $GLOBALS['__lugares_cache_memoria'][$clave] = $entrada;
}

function lugares_cache_bd_leer($clave) {
    $pdo = conectar_db();
    if (!$pdo) {
        return null;
    }

    try {
        $stmt = $pdo->prepare('SELECT payload FROM lugares_cache WHERE cache_key = :clave AND expires_at > NOW() LIMIT 1');
        $stmt->execute([':clave' => $clave]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fila) {
            return null;
        }

        $datos = json_decode($fila['payload'], true);
        return is_array($datos) ? $datos : null;
    } catch (Throwable $error) {
        // La tabla puede no existir todavía: el fallback es la caché de sesión.
        return null;
    }
}

function lugares_cache_guardar($clave, $datos, $proveedor, $destino) {
    $ttl = lugares_cache_ttl($proveedor);
    lugares_cache_sesion_guardar($clave, $datos, time() + $ttl);

    $pdo = conectar_db();
    if (!$pdo) {
        return false;
    }

    $primero = $datos[0] ?? [];
    $rating = null;
    $reviews = null;
    $score = null;

    foreach ($datos as $lugar) {
        if (isset($lugar['rating']) && $lugar['rating'] !== null) {
            $rating = (float)$lugar['rating'];
            $reviews = isset($lugar['reviews']) ? (int)$lugar['reviews'] : null;
            $score = isset($lugar['score']) ? (int)$lugar['score'] : null;

            if ($score === null) {
                $calculado = lugares_calcular_score($rating, $reviews);
                $score = $calculado['score'];
            }

            break;
        }
    }

    try {
        $sql = 'INSERT INTO lugares_cache
                    (cache_key, proveedor, place_id, destino_slug, payload, rating, user_rating_count, score_final, fetched_at, expires_at)
                VALUES
                    (:cache_key, :proveedor, :place_id, :destino_slug, :payload, :rating, :reviews, :score, :fetched_at, :expires_at)
                ON DUPLICATE KEY UPDATE
                    proveedor = VALUES(proveedor),
                    payload = VALUES(payload),
                    rating = VALUES(rating),
                    user_rating_count = VALUES(user_rating_count),
                    score_final = VALUES(score_final),
                    fetched_at = VALUES(fetched_at),
                    expires_at = VALUES(expires_at)';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':cache_key' => $clave,
            ':proveedor' => $proveedor,
            ':place_id' => mb_substr((string)($primero['id'] ?? $destino['slug'] ?? 'lugar'), 0, 190),
            ':destino_slug' => mb_substr((string)($destino['slug'] ?? ''), 0, 120),
            ':payload' => json_encode($datos, JSON_UNESCAPED_UNICODE),
            ':rating' => $rating,
            ':reviews' => $reviews,
            ':score' => $score,
            ':fetched_at' => date('Y-m-d H:i:s'),
            ':expires_at' => date('Y-m-d H:i:s', time() + $ttl),
        ]);

        return true;
    } catch (Throwable $error) {
        // Sin tabla de caché el sistema sigue funcionando con la caché de sesión.
        return false;
    }
}

function lugares_cache_leer($clave) {
    $en_sesion = lugares_cache_sesion_leer($clave);
    if ($en_sesion !== null) {
        return $en_sesion;
    }

    $en_bd = lugares_cache_bd_leer($clave);
    if ($en_bd !== null) {
        lugares_cache_sesion_guardar($clave, $en_bd, time() + 3600);
        return $en_bd;
    }

    return null;
}

/**
 * Contador diario simple (sesión) para no agotar las cuotas gratuitas.
 */
function lugares_contador_api() {
    $hoy = date('Y-m-d');

    if (!isset($_SESSION['lugares_api']) || ($_SESSION['lugares_api']['fecha'] ?? '') !== $hoy) {
        $_SESSION['lugares_api'] = ['fecha' => $hoy, 'total' => 0];
    }

    return (int)($_SESSION['lugares_api']['total'] ?? 0);
}

function lugares_cuota_disponible() {
    $maximo = (int)($_ENV['LUGARES_MAX_CALLS_DIA'] ?? 100);

    if ($maximo <= 0) {
        return true;
    }

    return lugares_contador_api() < $maximo;
}

function lugares_registrar_llamada() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $hoy = date('Y-m-d');
    if (!isset($_SESSION['lugares_api']) || ($_SESSION['lugares_api']['fecha'] ?? '') !== $hoy) {
        $_SESSION['lugares_api'] = ['fecha' => $hoy, 'total' => 0];
    }

    $_SESSION['lugares_api']['total'] = (int)($_SESSION['lugares_api']['total'] ?? 0) + 1;
}

/**
 * Petición HTTP con cURL. Devuelve el JSON decodificado o null si falla.
 * Nota: se mantiene CURLOPT_SSL_VERIFYPEER en true (a diferencia del módulo Gemini antiguo).
 */
function lugares_http_json($url, $metodo = 'GET', $cabeceras = [], $cuerpo = null) {
    $ch = curl_init($url);
    if ($ch === false) {
        return null;
    }

    $opciones = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER => $cabeceras,
        CURLOPT_USERAGENT => 'NovAirlines/1.0 (Lugares Turisticos)',
    ];

    if ($metodo === 'POST') {
        $opciones[CURLOPT_POST] = true;
        $opciones[CURLOPT_POSTFIELDS] = $cuerpo;
    }

    curl_setopt_array($ch, $opciones);
    $respuesta = curl_exec($ch);
    $codigo = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    unset($ch);

    if ($respuesta === false || $codigo < 200 || $codigo >= 300) {
        error_log('Lugares API HTTP ' . $codigo . ($error !== '' ? ' - ' . $error : ''));
        return null;
    }

    $datos = json_decode($respuesta, true);
    return is_array($datos) ? $datos : null;
}

/**
 * Traduce categorías de Google/Geoapify a los tipos usados por la interfaz.
 */
function lugares_mapear_tipo($etiquetas) {
    $texto = lugares_normalizar(implode(' ', is_array($etiquetas) ? $etiquetas : [(string)$etiquetas]));

    if (preg_match('/beach|playa/', $texto)) {
        return 'playa';
    }
    if (preg_match('/viewpoint|mirador|observatory/', $texto)) {
        return 'mirador';
    }
    if (preg_match('/museum|gallery|museo/', $texto)) {
        return 'museo';
    }
    if (preg_match('/park|natural|nature|national_park|reserve|garden|mountain|lake|waterfall|valley/', $texto)) {
        return 'naturaleza';
    }
    if (preg_match('/archaeolog|ruins|historic|heritage|monument|castle|fort/', $texto)) {
        return 'arqueologico';
    }
    if (preg_match('/hiking|trail|adventure|sports|climbing/', $texto)) {
        return 'aventura';
    }

    return 'cultural';
}

/**
 * Categorías de Geoapify ordenadas por señal. `heritage` devuelve monumentos y
 * conjuntos históricos reconocibles; el resto son categorías de relleno para
 * ciudades con poco patrimonio catalogado (Cusco, Lima, Arequipa...).
 */
function lugares_geoapify_grupos() {
    return [
        [
            'categorias' => 'heritage',
            'radio_max'  => 12000,
            'limite'     => 20,
            'minimo'     => 8,
        ],
        [
            'categorias' => implode(',', [
                'tourism.sights.place_of_worship',
                'tourism.attraction.viewpoint',
                'tourism.attraction',
                'national_park',
                'leisure.park.nature_reserve',
                'natural',
                'beach',
            ]),
            'radio_max'  => 12000,
            'limite'     => 30,
            'minimo'     => 0,
        ],
    ];
}

/**
 * Descarta ruido típico de OpenStreetMap: placas conmemorativas, monumentos a
 * personas y memoriales de guerra, que no son destinos turísticos.
 */
function lugares_geoapify_es_ruido($nombre, $categorias) {
    $texto = lugares_normalizar($nombre);

    if (preg_match('/^(plaque|placa|memorial|memorandum)\b/', $texto)) {
        return true;
    }

    if (preg_match('/plaque (commemorative|commemoratif)|monument aux morts|war memorial|hommage (a|aux)\b/', $texto)) {
        return true;
    }

    if (preg_match('/^monumento (a|al|a los)\b/', $texto)) {
        return true;
    }

    $categorias_texto = lugares_normalizar(implode(' ', is_array($categorias) ? $categorias : []));

    // Estatuas y murales sueltos dominan el resultado en ciudades grandes
    // (bustos, monumentos a personas) y no son destinos en sí mismos.
    if (str_contains($categorias_texto, 'tourism.attraction.artwork')) {
        return true;
    }

    return str_contains($categorias_texto, 'historic.memorial');
}

/**
 * Petición única a Geoapify Places v2 con un grupo de categorías.
 * Devuelve las features crudas (o [] si falla).
 */
function lugares_geoapify_features($categorias, $lat, $lng, $radio, $limite, $api_key) {
    $parametros = [
        'categories' => $categorias,
        'filter' => 'circle:' . (float)$lng . ',' . (float)$lat . ',' . (int)$radio,
        'limit' => max(1, min(50, (int)$limite)),
        'lang' => 'es',
        'apiKey' => $api_key,
    ];

    $respuesta = lugares_http_json('https://api.geoapify.com/v2/places?' . http_build_query($parametros));
    lugares_registrar_llamada();

    if (!$respuesta || empty($respuesta['features'])) {
        return [];
    }

    return $respuesta['features'];
}

/**
 * Convierte una feature de Geoapify en un lugar normalizado.
 * Devuelve null si no sirve (sin nombre, ruido, fuera del radio o duplicado).
 */
function lugares_geoapify_a_lugar($feature, $destino, $radio_limite, $ids_vistos) {
    $props = $feature['properties'] ?? [];
    $coordenadas = $feature['geometry']['coordinates'] ?? null;

    if (!is_array($coordenadas) || count($coordenadas) < 2) {
        return null;
    }

    $nombre = trim((string)($props['name'] ?? ''));
    if ($nombre === '' || lugares_normalizar($nombre) === 'unnamed') {
        return null;
    }

    $categorias_lugar = is_array($props['categories'] ?? null) ? $props['categories'] : [];
    if (lugares_geoapify_es_ruido($nombre, $categorias_lugar)) {
        return null;
    }

    $lng = (float)$coordenadas[0];
    $lat = (float)$coordenadas[1];

    // Geoapify incluye relaciones gigantes (rutas, conjuntos UNESCO) cuyo centroide
    // puede caer a cientos de kilómetros: se descartan por distancia real.
    $distancia_km = lugares_distancia_km((float)$destino['lat'], (float)$destino['lng'], $lat, $lng);
    if ($distancia_km > ($radio_limite / 1000.0) * 1.05) {
        return null;
    }

    $id = 'geo:' . ($props['place_id'] ?? md5($nombre . $lat . $lng));
    if (isset($ids_vistos[$id])) {
        return null;
    }

    return [
        'id' => $id,
        'nombre' => $nombre,
        'direccion' => trim((string)($props['formatted'] ?? ($props['address_line1'] ?? ''))),
        'ciudad' => trim((string)($props['city'] ?? ($destino['ciudad'] ?? ''))),
        'pais' => trim((string)($props['country'] ?? ($destino['pais'] ?? ''))),
        'lat' => $lat,
        'lng' => $lng,
        'tipo' => lugares_mapear_tipo($categorias_lugar),
        'rating' => null,
        'reviews' => null,
        'descripcion' => '',
        'mejor_momento' => '',
        'fuente_rating' => '',
        'fuente' => 'geoapify',
        'google_maps_uri' => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($lat . ',' . $lng),
    ];
}

/**
 * Adaptador Geoapify Places v2 (3,000 request/día en plan gratuito, sin tarjeta).
 *
 * Geoapify no expone un ranking de importancia y devuelve las features en orden
 * arbitrario, así que se consulta por capas: primero patrimonio y, si no alcanza,
 * una segunda consulta con miradores, templos, parques y playas en un radio menor.
 */
function lugares_buscar_geoapify($destino) {
    $api_key = trim($_ENV['GEOAPIFY_API_KEY'] ?? '');
    if ($api_key === '') {
        return null;
    }

    $lat = (float)$destino['lat'];
    $lng = (float)$destino['lng'];
    $radio_base = (int)($destino['radio'] ?? 25000);
    $grupos = lugares_geoapify_grupos();
    $minimo_primera = (int)($grupos[0]['minimo'] ?? 0);

    $lugares = [];
    $ids = [];

    foreach ($grupos as $indice => $grupo) {
        if ($indice > 0 && count($lugares) >= $minimo_primera) {
            break;
        }

        $radio = min($radio_base, (int)$grupo['radio_max']);
        $features = lugares_geoapify_features(
            $grupo['categorias'],
            $lat,
            $lng,
            $radio,
            (int)($grupo['limite'] ?? 30),
            $api_key
        );

        foreach ($features as $feature) {
            $lugar = lugares_geoapify_a_lugar($feature, $destino, $radio, $ids);
            if ($lugar === null) {
                continue;
            }

            $ids[$lugar['id']] = true;
            $lugares[] = $lugar;
        }
    }

    if (count($lugares) === 0) {
        return null;
    }

    // Los resultados cercanos al centro son más relevantes que los de la periferia.
    usort($lugares, function ($a, $b) use ($lat, $lng) {
        $dist_a = lugares_distancia_km($lat, $lng, (float)$a['lat'], (float)$a['lng']);
        $dist_b = lugares_distancia_km($lat, $lng, (float)$b['lat'], (float)$b['lng']);

        return $dist_a <=> $dist_b;
    });

    return $lugares;
}

/**
 * Adaptador Google Places API (New) Text Search.
 * El field mask pide rating, que pertenece al SKU Enterprise (1,000 gratis/mes).
 */
function lugares_buscar_google($destino) {
    $api_key = trim($_ENV['GOOGLE_MAPS_API_KEY'] ?? '');
    if ($api_key === '') {
        return null;
    }

    $cuerpo = json_encode([
        'textQuery' => 'atracciones turísticas y joyas ocultas en ' . ($destino['ciudad'] ?? '') . ', ' . ($destino['pais'] ?? ''),
        'languageCode' => 'es',
        'maxResultCount' => 20,
        'locationBias' => [
            'circle' => [
                'center' => [
                    'latitude' => (float)$destino['lat'],
                    'longitude' => (float)$destino['lng'],
                ],
                'radius' => (float)($destino['radio'] ?? 25000),
            ],
        ],
    ], JSON_UNESCAPED_UNICODE);

    $cabeceras = [
        'Content-Type: application/json',
        'X-Goog-Api-Key: ' . $api_key,
        'X-Goog-FieldMask: places.id,places.displayName,places.formattedAddress,places.location,places.rating,places.userRatingCount,places.types,places.businessStatus,places.googleMapsUri',
    ];

    $respuesta = lugares_http_json('https://places.googleapis.com/v1/places:searchText', 'POST', $cabeceras, $cuerpo);
    lugares_registrar_llamada();

    if (!$respuesta || empty($respuesta['places'])) {
        return null;
    }

    $lugares = [];

    foreach ($respuesta['places'] as $place) {
        if (($place['businessStatus'] ?? 'OPERATIONAL') !== 'OPERATIONAL') {
            continue;
        }

        $nombre = trim((string)($place['displayName']['text'] ?? ''));
        if ($nombre === '') {
            continue;
        }

        $lat = isset($place['location']['latitude']) ? (float)$place['location']['latitude'] : null;
        $lng = isset($place['location']['longitude']) ? (float)$place['location']['longitude'] : null;

        $lugares[] = [
            'id' => 'google:' . ($place['id'] ?? md5($nombre . $lat . $lng)),
            'nombre' => $nombre,
            'direccion' => trim((string)($place['formattedAddress'] ?? '')),
            'ciudad' => (string)($destino['ciudad'] ?? ''),
            'pais' => (string)($destino['pais'] ?? ''),
            'lat' => $lat,
            'lng' => $lng,
            'tipo' => lugares_mapear_tipo($place['types'] ?? []),
            'rating' => isset($place['rating']) ? (float)$place['rating'] : null,
            'reviews' => isset($place['userRatingCount']) ? (int)$place['userRatingCount'] : null,
            'descripcion' => '',
            'mejor_momento' => '',
            'fuente_rating' => 'google',
            'fuente' => 'google',
            'google_maps_uri' => (string)($place['googleMapsUri'] ?? ''),
        ];
    }

    return count($lugares) > 0 ? $lugares : null;
}

/**
 * Capas de consulta de TomTom. Se usa "tourist attraction" como capa base y
 * "place of worship" porque su categoría es limpia (templos reales), a
 * diferencia de "park", que devuelve ruido comercial.
 */
function lugares_tomtom_grupos($incluir_museos = false) {
    $grupos = [
        ['consulta' => 'tourist attraction', 'radio_max' => 20000, 'limite' => 60, 'minimo' => 12],
        ['consulta' => 'place of worship',   'radio_max' => 15000, 'limite' => 25, 'minimo' => 0],
    ];

    if ($incluir_museos) {
        $grupos[] = ['consulta' => 'museum', 'radio_max' => 15000, 'limite' => 25, 'minimo' => 0];
    }

    return $grupos;
}

/**
 * Descarta el ruido de TomTom: su categoría "tourist attraction" incluye
 * estadios, universidades, calles, agencias de viaje y comercios.
 *
 * El orden importa. Primero se descarta lo que casi nunca es un sitio que se
 * visita, aunque su nombre suene bien ("Estadio Parque Zonal", "Jardim de La
 * Cerveza", "Galería ... en Scotiabank"). Después se protege lo que sí es un
 * atractivo real y, por último, se filtra por categoría devuelta por TomTom.
 */
function lugares_tomtom_es_ruido($nombre, $categorias) {
    $texto = lugares_normalizar($nombre);
    $cats = lugares_normalizar(implode(' ', is_array($categorias) ? $categorias : []));

    // 1) Infraestructura y servicios: no son puntos de interés turístico.
    $infraestructura = '/\b(estadio|stadium|coliseo|coliseum|celiseo|polideportivo|'
        . 'complejo deportivo|complexo deportivo|deportivo|gimnasio|cancha|'
        . 'avenida|calle|callejon|jiron|pasaje|street|road|carretera|autopista|'
        . 'universidad|university|facultad|campus|colegio|escuela|escola|instituto|academia|'
        . 'municipalidad|municipal|ministerio|prefectura|gobernacion|'
        . 'delegacion|oficina|agencia|hospital|clinica|cementerio|cemetery|'
        . 'scotiabank|interbank|bbva|banco|financiera|cajero)\b/';

    if (preg_match($infraestructura, $texto)) {
        return true;
    }

    // 2) Nombres que sí son destino turístico: se conservan aunque la categoría
    //    sea genérica o el edificio se use para otra cosa.
    if (preg_match('/\b(museo|museum|iglesia|church|catedral|cathedral|basilica|convento|monasterio|abadia|capilla|templo|palacio|castillo|fortaleza|torre|ruina|ruinas|arqueologic|mirador|plaza|parque|jardin|fuente|teatro|acuario|zoologico|monumento|estatua|statue|sitio|casa)\b/', $texto)) {
        if (!preg_match('/^monumento (a|al|a los)\b/', $texto)) {
            return false;
        }
    }

    // 3) Comercio, alojamiento y servicios turísticos.
    $comercial = '/\b(agencia|agency|viajes|travel|adventours|operator|excursion|excursions|ticket|entradas|'
        . 'golf|picnic|rent a car|alquiler|camping|hotel|hostal|hostel|hospedaje|lodge|resort|'
        . 'restaurante|restaurant|cafeteria|bar|pub|cerveceria|cerveza|brewery|pizzeria|grill|'
        . 'tienda|shop|store|libro|supermercado|farmacia|estacion|aeropuerto|terminal|parking|'
        . 'gasolinera|centro comercial|fiera|feria|festival|festividad|circuito|casino|spa|'
        . 'turismo|tours|expedition|expeditions|experience)\b/';

    if (preg_match($comercial, $texto) || preg_match('/(adventour|tourin|mytour|tour operator|tour peru)/', $texto)) {
        return true;
    }

    if (preg_match('/\b(travel|hotel|restaurant|shop|gas station|parking lot|pharmacy|hospital|company|service)\b/', $cats)) {
        return true;
    }

    // TomTom marca como "building" estadios, facultades y sedes administrativas.
    return str_contains($cats, 'building');
}

/**
 * Petición única a TomTom Search v2 (categorySearch).
 * Devuelve los resultados crudos (o [] si falla).
 */
function lugares_tomtom_features($consulta, $lat, $lng, $radio, $limite, $api_key) {
    $parametros = [
        'key' => $api_key,
        'lat' => (float)$lat,
        'lon' => (float)$lng,
        'radius' => max(500, min(40000, (int)$radio)),
        'limit' => max(1, min(100, (int)$limite)),
        'language' => 'es-ES',
    ];

    $url = 'https://api.tomtom.com/search/2/categorySearch/'
        . rawurlencode($consulta) . '.json?' . http_build_query($parametros);

    $respuesta = lugares_http_json($url);
    lugares_registrar_llamada();

    if (!$respuesta || empty($respuesta['results'])) {
        return [];
    }

    return $respuesta['results'];
}

/**
 * Convierte un resultado de TomTom en un lugar normalizado.
 * Devuelve null si no sirve (sin nombre, ruido, fuera del radio o duplicado).
 */
function lugares_tomtom_a_lugar($resultado, $destino, $radio_limite, $ids_vistos) {
    $poi = $resultado['poi'] ?? [];
    $posicion = $resultado['position'] ?? null;

    if (!is_array($posicion) || !isset($posicion['lat'], $posicion['lon'])) {
        return null;
    }

    $nombre = trim((string)($poi['name'] ?? ''));
    if ($nombre === '') {
        return null;
    }

    $categorias = array_merge(
        is_array($poi['categories'] ?? null) ? $poi['categories'] : [],
        is_array($poi['classifications'] ?? null) ? $poi['classifications'] : []
    );

    $categorias_texto = [];
    foreach ($categorias as $categoria) {
        if (is_array($categoria)) {
            foreach ($categoria['names'] ?? [] as $nombre_categoria) {
                $categorias_texto[] = (string)($nombre_categoria['name'] ?? '');
            }
            $categorias_texto[] = (string)($categoria['code'] ?? '');
        } else {
            $categorias_texto[] = (string)$categoria;
        }
    }

    if (lugares_tomtom_es_ruido($nombre, $categorias_texto)) {
        return null;
    }

    $lat = (float)$posicion['lat'];
    $lng = (float)$posicion['lon'];

    // TomTom ignora el radio en algunos resultados lejanos: se valida por distancia real.
    $distancia_km = lugares_distancia_km((float)$destino['lat'], (float)$destino['lng'], $lat, $lng);
    if ($distancia_km > ($radio_limite / 1000.0) * 1.05) {
        return null;
    }

    $id = 'tt:' . ($resultado['id'] ?? md5($nombre . $lat . $lng));
    if (isset($ids_vistos[$id])) {
        return null;
    }

    $direccion = $resultado['address'] ?? [];

    return [
        'id' => $id,
        'nombre' => $nombre,
        'direccion' => trim((string)($direccion['freeformAddress'] ?? '')),
        'ciudad' => trim((string)($direccion['municipality'] ?? ($destino['ciudad'] ?? ''))),
        'pais' => trim((string)($direccion['country'] ?? ($destino['pais'] ?? ''))),
        'lat' => $lat,
        'lng' => $lng,
        'tipo' => lugares_mapear_tipo($categorias_texto),
        'rating' => null,
        'reviews' => null,
        'descripcion' => '',
        'mejor_momento' => '',
        'fuente_rating' => '',
        'fuente' => 'tomtom',
        'fuentes' => ['tomtom'],
        'google_maps_uri' => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($lat . ',' . $lng),
    ];
}

/**
 * Adaptador TomTom Search v2 (plan gratuito sin tarjeta).
 *
 * Consulta por capas y filtra el ruido comercial. No aporta puntuaciones: sirve
 * para ampliar la cobertura con plazas, iglesias, parques y monumentos que la
 * capa de patrimonio de Geoapify no devuelve.
 */
function lugares_buscar_tomtom($destino, $incluir_museos = true) {
    $api_key = trim((string)($_ENV['TOMTOM_API_KEY'] ?? ''));
    if ($api_key === '') {
        return null;
    }

    $lat = (float)$destino['lat'];
    $lng = (float)$destino['lng'];
    $radio_base = (int)($destino['radio'] ?? 25000);
    $grupos = lugares_tomtom_grupos($incluir_museos);
    $minimo_primera = (int)($grupos[0]['minimo'] ?? 0);

    $lugares = [];
    $ids = [];

    foreach ($grupos as $indice => $grupo) {
        if ($indice > 0 && count($lugares) >= $minimo_primera) {
            break;
        }

        $radio = min($radio_base, (int)$grupo['radio_max']);
        $resultados = lugares_tomtom_features(
            $grupo['consulta'],
            $lat,
            $lng,
            $radio,
            (int)($grupo['limite'] ?? 60),
            $api_key
        );

        foreach ($resultados as $resultado) {
            $lugar = lugares_tomtom_a_lugar($resultado, $destino, $radio, $ids);
            if ($lugar === null) {
                continue;
            }

            $ids[$lugar['id']] = true;
            $lugares[] = $lugar;
        }
    }

    if (count($lugares) === 0) {
        return null;
    }

    usort($lugares, function ($a, $b) use ($lat, $lng) {
        $dist_a = lugares_distancia_km($lat, $lng, (float)$a['lat'], (float)$a['lng']);
        $dist_b = lugares_distancia_km($lat, $lng, (float)$b['lat'], (float)$b['lng']);

        return $dist_a <=> $dist_b;
    });

    return $lugares;
}

/**
 * Decide si dos resultados de proveedores distintos son el mismo lugar.
 * Usa nombre (con clave fonética) + distancia, porque cada API lo geocodifica
 * en un punto ligeramente diferente.
 */
function lugares_mismo_sitio($a, $b) {
    if ($a['lat'] === null || $a['lng'] === null || $b['lat'] === null || $b['lng'] === null) {
        return false;
    }

    $distancia = lugares_distancia_km((float)$a['lat'], (float)$a['lng'], (float)$b['lat'], (float)$b['lng']);
    if ($distancia > 0.45) {
        return false;
    }

    $clave_a = lugares_clave_fonetica($a['nombre'] ?? '');
    $clave_b = lugares_clave_fonetica($b['nombre'] ?? '');

    if ($clave_a === '' || $clave_b === '') {
        return false;
    }

    if ($clave_a === $clave_b) {
        return true;
    }

    if (mb_strlen($clave_a) >= 6 && str_contains($clave_b, $clave_a)) {
        return true;
    }

    if (mb_strlen($clave_b) >= 6 && str_contains($clave_a, $clave_b)) {
        return true;
    }

    $similitud = 0.0;
    similar_text($clave_a, $clave_b, $similitud);

    return $similitud >= 82.0;
}

/**
 * Deja todos los lugares con la misma estructura, sin importar el proveedor.
 */
function lugares_normalizar_lugar($lugar, $destino = null) {
    $lat = (isset($lugar['lat']) && $lugar['lat'] !== null) ? (float)$lugar['lat'] : null;
    $lng = (isset($lugar['lng']) && $lugar['lng'] !== null) ? (float)$lugar['lng'] : null;
    $nombre = trim((string)($lugar['nombre'] ?? ''));

    $fuentes = [];
    if (isset($lugar['fuentes']) && is_array($lugar['fuentes'])) {
        foreach ($lugar['fuentes'] as $fuente) {
            $fuente = (string)$fuente;
            if ($fuente !== '' && !in_array($fuente, $fuentes, true)) {
                $fuentes[] = $fuente;
            }
        }
    }

    $fuente_principal = (string)($lugar['fuente'] ?? ($fuentes[0] ?? ''));
    if ($fuente_principal !== '' && !in_array($fuente_principal, $fuentes, true)) {
        array_unshift($fuentes, $fuente_principal);
    }

    $resultado = [
        'id' => (string)($lugar['id'] ?? ('lugar:' . md5($nombre . '|' . $lat . '|' . $lng))),
        'nombre' => $nombre,
        'ciudad' => trim((string)($lugar['ciudad'] ?? ($destino['ciudad'] ?? ''))),
        'pais' => trim((string)($lugar['pais'] ?? ($destino['pais'] ?? ''))),
        'direccion' => trim((string)($lugar['direccion'] ?? '')),
        'lat' => $lat,
        'lng' => $lng,
        'tipo' => (string)($lugar['tipo'] ?? 'cultural'),
        'rating' => (isset($lugar['rating']) && $lugar['rating'] !== null) ? (float)$lugar['rating'] : null,
        'reviews' => (isset($lugar['reviews']) && $lugar['reviews'] !== null) ? (int)$lugar['reviews'] : null,
        'descripcion' => trim((string)($lugar['descripcion'] ?? '')),
        'mejor_momento' => trim((string)($lugar['mejor_momento'] ?? '')),
        'fuente_rating' => (string)($lugar['fuente_rating'] ?? ''),
        'fuente' => $fuente_principal,
        'fuentes' => $fuentes,
        'google_maps_uri' => trim((string)($lugar['google_maps_uri'] ?? '')),
    ];

    if ($resultado['google_maps_uri'] === '' && $lat !== null && $lng !== null) {
        $resultado['google_maps_uri'] = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($lat . ',' . $lng);
    }

    $resultado['distancia_km'] = ($destino && $lat !== null && $lng !== null)
        ? round(lugares_distancia_km((float)$destino['lat'], (float)$destino['lng'], $lat, $lng), 1)
        : null;

    return $resultado;
}

/**
 * Fusiona dos fuentes reales (Geoapify + TomTom).
 *
 * El primer arreglo manda: sus lugares conservan id, categoría y enlace de
 * Wikipedia. Los de la segunda fuente que coinciden en nombre y ubicación se
 * descartan como duplicados, pero antes se aprovechan sus datos que falten
 * (dirección, ciudad, país). Los que no coinciden se añaden como lugares nuevos.
 */
function lugares_combinar_fuentes($primarios, $secundarios) {
    $lugares = is_array($primarios) ? array_values($primarios) : [];
    $secundarios = is_array($secundarios) ? array_values($secundarios) : [];

    foreach ($secundarios as $candidato) {
        $duplicado = null;

        foreach ($lugares as $indice => $existente) {
            if (lugares_mismo_sitio($existente, $candidato)) {
                $duplicado = $indice;
                break;
            }
        }

        if ($duplicado === null) {
            $lugares[] = $candidato;
            continue;
        }

        $destino_indice = &$lugares[$duplicado];

        if (trim((string)($destino_indice['direccion'] ?? '')) === '' && trim((string)($candidato['direccion'] ?? '')) !== '') {
            $destino_indice['direccion'] = $candidato['direccion'];
        }

        if (trim((string)($destino_indice['ciudad'] ?? '')) === '' && trim((string)($candidato['ciudad'] ?? '')) !== '') {
            $destino_indice['ciudad'] = $candidato['ciudad'];
        }

        if (trim((string)($destino_indice['pais'] ?? '')) === '' && trim((string)($candidato['pais'] ?? '')) !== '') {
            $destino_indice['pais'] = $candidato['pais'];
        }

        $fuentes = is_array($destino_indice['fuentes'] ?? null) ? $destino_indice['fuentes'] : [$destino_indice['fuente'] ?? ''];
        foreach ((array)($candidato['fuentes'] ?? [$candidato['fuente'] ?? '']) as $fuente) {
            $fuente = (string)$fuente;
            if ($fuente !== '' && !in_array($fuente, $fuentes, true)) {
                $fuentes[] = $fuente;
            }
        }
        $destino_indice['fuentes'] = array_values(array_filter($fuentes));

        unset($destino_indice);
    }

    return $lugares;
}

/**
 * Devuelve la categoría genérica de un lugar según su nombre, para no fusionar
 * cosas distintas que comparten una palabra (una plaza y una iglesia dedicadas
 * al mismo santo no son el mismo sitio).
 */
function lugares_categoria_nombre($nombre) {
    $texto = lugares_normalizar($nombre);

    $grupos = [
        'religioso' => '/\b(iglesia|church|convento|monasterio|templo|capilla|catedral|cathedral|basilica|abadia|sinagoga|mezquita|parroquia|santuario)\b/',
        'plaza'     => '/\b(plaza|plazoleta|square)\b/',
        'museo'     => '/\b(museo|museum)\b/',
        'parque'    => '/\b(parque|park|jardin|garden|alameda)\b/',
        'sitio'     => '/\b(ruina|ruinas|sitio|complejo arqueologico|ciudadela|zona arqueologica|conjunto arqueologico|acueducto)\b/',
        'monumento' => '/\b(monumento|monument|estatua|statue|arco)\b/',
        'mirador'   => '/\b(mirador|viewpoint|belvedere)\b/',
        'casa'      => '/\b(casa|palacio|casona|mansion)\b/',
        'mercado'   => '/\b(mercado|market|feria)\b/',
    ];

    foreach ($grupos as $categoria => $patron) {
        if (preg_match($patron, $texto)) {
            return $categoria;
        }
    }

    return '';
}

/**
 * Palabras con valor propio dentro de un nombre. Se descartan los conectores y
 * las palabras que solo dicen el tipo de sitio ("iglesia", "plaza", "museo"...),
 * para quedarnos con lo que de verdad identifica al lugar ("domingo", "blas").
 */
function lugares_tokens_distintivos($nombre) {
    $genericas = [
        'de', 'del', 'la', 'el', 'los', 'las', 'y', 'e', 'of', 'the', 'and', 'san', 'santo', 'santa',
        'iglesia', 'church', 'convento', 'monasterio', 'templo', 'capilla', 'catedral', 'cathedral',
        'basilica', 'abadia', 'parroquia', 'santuario', 'plaza', 'plazoleta', 'square', 'parque', 'park',
        'jardin', 'garden', 'alameda', 'museo', 'museum', 'monumento', 'monument', 'estatua', 'statue',
        'arco', 'casa', 'palacio', 'casona', 'mansion', 'mirador', 'viewpoint', 'ruina', 'ruinas', 'sitio',
        'arqueologico', 'arqueologica', 'complejo', 'conjunto', 'ciudadela', 'centro', 'historico',
        'historica', 'grande', 'gran', 'nuevo', 'nueva', 'viejo', 'vieja', 'antiguo', 'antigua', 'real',
        'nacional', 'regional', 'popular', 'capital', 'calle', 'avenida', 'barrio', 'mercado', 'market',
    ];

    $tokens = [];
    foreach (preg_split('/\s+/', lugares_normalizar($nombre)) as $palabra) {
        if ($palabra !== '' && mb_strlen($palabra) >= 4 && !in_array($palabra, $genericas, true)) {
            $tokens[$palabra] = true;
        }
    }

    return array_keys($tokens);
}

/**
 * Copia en el lugar que se conserva los datos que le falten desde el descartado
 * (dirección, ciudad, país, puntaje) y suma las fuentes usadas.
 */
function lugares_fusionar_resto($conservar, $descartar) {
    foreach (['direccion', 'ciudad', 'pais', 'mejor_momento', 'google_maps_uri'] as $campo) {
        if (trim((string)($conservar[$campo] ?? '')) === '' && trim((string)($descartar[$campo] ?? '')) !== '') {
            $conservar[$campo] = $descartar[$campo];
        }
    }

    if (($conservar['rating'] ?? null) === null && ($descartar['rating'] ?? null) !== null) {
        $conservar['rating'] = $descartar['rating'];
        $conservar['reviews'] = $descartar['reviews'] ?? null;
        $conservar['fuente_rating'] = $descartar['fuente_rating'] ?? '';
    }

    $fuentes = is_array($conservar['fuentes'] ?? null)
        ? $conservar['fuentes']
        : [$conservar['fuente'] ?? ''];

    foreach ((array)($descartar['fuentes'] ?? [$descartar['fuente'] ?? '']) as $fuente) {
        $fuente = (string)$fuente;
        if ($fuente !== '' && !in_array($fuente, $fuentes, true)) {
            $fuentes[] = $fuente;
        }
    }

    $conservar['fuentes'] = array_values(array_filter($fuentes));

    return $conservar;
}

/**
 * Elimina duplicados del mismo lugar geocodificado varias veces.
 *
 * Cada proveedor coloca el mismo sitio en un punto ligeramente distinto, así que
 * no basta con comparar coordenadas exactas. Se descarta un resultado cuando:
 *
 *   a) tiene el mismo nombre (clave fonética) y está a menos de ~350 m, o
 *   b) comparte una palabra que lo identifica ("domingo", "blas") con otro de su
 *      misma categoría (ambos religiosos, ambos plazas...) y está a menos de ~150 m.
 *
 * El punto (b) evita tarjetas repetidas como "Iglesia de Santo Domingo" e
 * "Iglesia y Convento de Santo Domingo", pero no fusiona una plaza con la iglesia
 * del mismo nombre. Al descartar, los datos útiles pasan al lugar que se conserva.
 */
function lugares_deduplicar($lugares) {
    $salida = [];

    foreach ($lugares as $lugar) {
        $clave = lugares_clave_fonetica($lugar['nombre'] ?? '');
        $categoria = lugares_categoria_nombre($lugar['nombre'] ?? '');
        $tokens = lugares_tokens_distintivos($lugar['nombre'] ?? '');
        $indice_dup = null;

        foreach ($salida as $indice => $existente) {
            $cercano = null;

            if ($lugar['lat'] === null || $lugar['lng'] === null
                || $existente['lat'] === null || $existente['lng'] === null) {
                $sin_coordenadas = true;
            } else {
                $sin_coordenadas = false;
                $cercano = lugares_distancia_km(
                    (float)$existente['lat'],
                    (float)$existente['lng'],
                    (float)$lugar['lat'],
                    (float)$lugar['lng']
                );
            }

            // a) Mismo nombre.
            if ($clave !== '' && $clave === lugares_clave_fonetica($existente['nombre'] ?? '')) {
                if ($sin_coordenadas || $cercano <= 0.35) {
                    $indice_dup = $indice;
                    break;
                }
                continue;
            }

            // b) Misma categoría con una palabra en común y muy cerca.
            if ($categoria !== '' && $categoria === lugares_categoria_nombre($existente['nombre'] ?? '')) {
                if (!$sin_coordenadas && $cercano <= 0.15
                    && count(array_intersect($tokens, lugares_tokens_distintivos($existente['nombre'] ?? ''))) > 0) {
                    $indice_dup = $indice;
                    break;
                }
            }
        }

        if ($indice_dup === null) {
            $salida[] = $lugar;
            continue;
        }

        $salida[$indice_dup] = lugares_fusionar_resto($salida[$indice_dup], $lugar);
    }

    return $salida;
}

/**
 * Calcula un índice de tranquilidad (proxy de poca afluencia) y el score final.
 * No representa afluencia en tiempo real: se basa en el volumen de reseñas.
 */
function lugares_calcular_score($rating, $reviews) {
    if ($rating === null || $reviews === null) {
        return [
            'score' => null,
            'calidad' => null,
            'tranquilidad' => null,
            'badge' => 'sin_puntaje',
            'badge_texto' => 'Sin puntaje todavía',
            'es_joya' => false,
            'concurrencia' => null,
        ];
    }

    $rating = max(0.0, min(5.0, (float)$rating));
    $reviews = max(0, (int)$reviews);

    $calidad = max(0.0, min(100.0, (($rating - 3.5) / 1.5) * 100));
    $popularidad = log10($reviews + 1);
    $tranquilidad = max(0.0, min(100.0, 100 - (($popularidad - 1.7) * 35)));
    $score = (int)round(0.55 * $calidad + 0.45 * $tranquilidad);

    $badge = 'normal';
    $badge_texto = 'Normal';

    if ($reviews < 20) {
        $badge = 'pocas_resenas';
        $badge_texto = 'Pocas reseñas';
    } elseif ($rating >= 4.5 && $reviews <= 800) {
        $badge = 'joya';
        $badge_texto = 'Joya oculta';
    } elseif ($rating >= 4.5 && $reviews > 5000) {
        $badge = 'imperdible';
        $badge_texto = 'Imperdible (concurrido)';
    } elseif ($rating >= 4.0) {
        $badge = 'recomendado';
        $badge_texto = 'Recomendado';
    }

    $concurrencia = $reviews <= 500
        ? 'baja'
        : ($reviews <= 3000 ? 'media' : ($reviews <= 15000 ? 'alta' : 'muy_alta'));

    return [
        'score' => $score,
        'calidad' => (int)round($calidad),
        'tranquilidad' => (int)round($tranquilidad),
        'badge' => $badge,
        'badge_texto' => $badge_texto,
        'es_joya' => ($badge === 'joya'),
        'concurrencia' => $concurrencia,
    ];
}

function lugares_aplicar_scoring($lugares) {
    foreach ($lugares as &$lugar) {
        $resultado = lugares_calcular_score($lugar['rating'] ?? null, $lugar['reviews'] ?? null);
        $lugar = array_merge($lugar, $resultado);
    }
    unset($lugar);

    return $lugares;
}

/**
 * Devuelve todo lo necesario para pintar la vista o responder la API.
 *
 * @param string $texto        Texto buscado ("Cusco", "París", "Perú"...).
 * @param string $destino_slug Slug del catálogo (opcional).
 * @param int    $limite       Máximo de lugares a devolver.
 */
function lugares_proveedor_desde_resultados($lugares) {
    $fuentes = [];

    foreach ((array)$lugares as $lugar) {
        foreach ((array)($lugar['fuentes'] ?? []) as $fuente) {
            $fuente = (string)$fuente;
            if ($fuente !== '') {
                $fuentes[$fuente] = true;
            }
        }
    }

    if (isset($fuentes['google'])) {
        return 'google';
    }

    $geoapify = isset($fuentes['geoapify']);
    $tomtom = isset($fuentes['tomtom']);

    if ($geoapify && $tomtom) {
        return 'ambos';
    }

    if ($geoapify) {
        return 'geoapify';
    }

    if ($tomtom) {
        return 'tomtom';
    }

    return 'ninguno';
}

/**
 * Ejecuta los proveedores según el modo y devuelve lugares + proveedor real.
 * En modo "ambos" fusiona Geoapify con TomTom; si Geoapify ya devuelve una
 * lista amplia, TomTom se limita a su capa principal para ahorrar cuota.
 */
function lugares_consultar_proveedor($modo, $destino) {
    $tiene_geoapify = trim((string)($_ENV['GEOAPIFY_API_KEY'] ?? '')) !== '';
    $tiene_tomtom = trim((string)($_ENV['TOMTOM_API_KEY'] ?? '')) !== '';

    if ($modo === 'google') {
        $lugares = lugares_buscar_google($destino) ?? [];
        return ['lugares' => $lugares, 'proveedor' => count($lugares) > 0 ? 'google' : 'ninguno'];
    }

    if ($modo === 'geoapify') {
        $lugares = $tiene_geoapify ? (lugares_buscar_geoapify($destino) ?? []) : [];
        return ['lugares' => $lugares, 'proveedor' => count($lugares) > 0 ? 'geoapify' : 'ninguno'];
    }

    if ($modo === 'tomtom') {
        $lugares = $tiene_tomtom ? (lugares_buscar_tomtom($destino, true) ?? []) : [];
        return ['lugares' => $lugares, 'proveedor' => count($lugares) > 0 ? 'tomtom' : 'ninguno'];
    }

    $de_geoapify = $tiene_geoapify ? (lugares_buscar_geoapify($destino) ?? []) : [];
    $de_tomtom = $tiene_tomtom
        ? (lugares_buscar_tomtom($destino, count($de_geoapify) < 15) ?? [])
        : [];

    if (count($de_geoapify) > 0 && count($de_tomtom) > 0) {
        return ['lugares' => lugares_combinar_fuentes($de_geoapify, $de_tomtom), 'proveedor' => 'ambos'];
    }

    if (count($de_geoapify) > 0) {
        return ['lugares' => $de_geoapify, 'proveedor' => 'geoapify'];
    }

    if (count($de_tomtom) > 0) {
        return ['lugares' => $de_tomtom, 'proveedor' => 'tomtom'];
    }

    return ['lugares' => [], 'proveedor' => 'ninguno'];
}

/**
 * Devuelve todo lo necesario para pintar la vista o responder la API.
 *
 * @param string $texto        Texto buscado ("Cusco", "París", "Perú"...).
 * @param string $destino_slug Slug del catálogo (opcional).
 * @param int    $limite       Máximo de lugares a devolver.
 */
function listar_lugares_turisticos($texto = '', $destino_slug = '', $limite = 60) {
    $texto = trim((string)$texto);
    $destino_slug = trim((string)$destino_slug);

    $destino = $destino_slug !== '' ? lugares_destino_por_slug($destino_slug) : null;
    if (!$destino && $texto !== '') {
        $destino = lugares_destino_por_texto($texto);
    }

    $solicitado = lugares_proveedor_activo();
    $proveedor = $solicitado;
    $aviso = '';
    $cache_estado = 'miss';
    $lugares = [];
    $sugerencias = [];

    // Si el texto es un país, no se inventan lugares: se proponen sus ciudades.
    if (!$destino && $texto !== '') {
        $sugerencias = lugares_destinos_por_pais($texto);
    }

    // Cualquier ciudad del mundo: se resuelve con el geocodificador de TomTom.
    if (!$destino && $texto !== '' && count($sugerencias) === 0) {
        $dinamico = lugares_resolver_destino_dinamico($texto);
        if (is_array($dinamico)) {
            $destino = $dinamico;
        }
    }

    if ($solicitado === 'ninguno') {
        $proveedor = 'ninguno';
        $aviso = 'No hay ninguna API configurada. Añade GEOAPIFY_API_KEY y TOMTOM_API_KEY en el archivo .env.';
    } elseif ($destino) {
        $clave = lugares_cache_clave($solicitado, $destino);
        $cacheado = lugares_cache_leer($clave);

        if (is_array($cacheado) && count($cacheado) > 0) {
            $lugares = $cacheado;
            $cache_estado = 'hit';
            $proveedor = lugares_proveedor_desde_resultados($lugares);
        } elseif (lugares_cuota_disponible()) {
            $consulta = lugares_consultar_proveedor($solicitado, $destino);
            $proveedor = $consulta['proveedor'];

            if (count($consulta['lugares']) > 0) {
                foreach ($consulta['lugares'] as $item) {
                    $lugares[] = lugares_normalizar_lugar($item, $destino);
                }

                lugares_cache_guardar($clave, $lugares, $proveedor, $destino);
                $cache_estado = 'miss';
            } else {
                $aviso = 'Las fuentes consultadas no devolvieron lugares para '
                    . ($destino['ciudad'] !== '' ? $destino['ciudad'] : $texto) . ' en este momento.';
            }
        } else {
            $aviso = 'Se alcanzó el límite diario de consultas configurado. Sube LUGARES_MAX_CALLS_DIA o espera al día siguiente.';
        }
    } elseif ($texto !== '' && count($sugerencias) === 0) {
        $aviso = 'No encontramos ese lugar. Prueba con una ciudad como "Cusco" o "Oaxaca".';
    }

    $lugares = lugares_deduplicar($lugares);
    $lugares = lugares_aplicar_scoring($lugares);

    usort($lugares, function ($a, $b) {
        $score_a = $a['score'] ?? null;
        $score_b = $b['score'] ?? null;

        // Los lugares sin puntaje van al final, ordenados por cercanía al centro.
        if ($score_a === null || $score_b === null) {
            if ($score_a === null && $score_b === null) {
                $dist_a = $a['distancia_km'] ?? 9999.0;
                $dist_b = $b['distancia_km'] ?? 9999.0;
                if ($dist_a !== $dist_b) {
                    return $dist_a <=> $dist_b;
                }
                return strcmp((string)($a['nombre'] ?? ''), (string)($b['nombre'] ?? ''));
            }

            return $score_a === null ? 1 : -1;
        }

        if ($score_a === $score_b) {
            $rating_a = $a['rating'] ?? 0;
            $rating_b = $b['rating'] ?? 0;

            if ($rating_a === $rating_b) {
                return ($b['reviews'] ?? 0) <=> ($a['reviews'] ?? 0);
            }

            return $rating_b <=> $rating_a;
        }

        return $score_b <=> $score_a;
    });

    $total = count($lugares);
    $lugares = array_slice($lugares, 0, max(1, (int)$limite));

    $joyas = 0;
    $con_rating = 0;
    $paises = [];
    $por_fuente = [];

    foreach ($lugares as $lugar) {
        if (!empty($lugar['es_joya'])) {
            $joyas++;
        }
        if ($lugar['rating'] !== null) {
            $con_rating++;
        }
        if ($lugar['pais'] !== '') {
            $paises[lugares_normalizar($lugar['pais'])] = $lugar['pais'];
        }

        foreach ((array)($lugar['fuentes'] ?? []) as $fuente) {
            $fuente = (string)$fuente;
            if ($fuente === '') {
                continue;
            }
            $por_fuente[$fuente] = ($por_fuente[$fuente] ?? 0) + 1;
        }
    }

    if ($proveedor === 'geoapify' && $solicitado === 'ambos' && count($lugares) > 0 && $aviso === '') {
        $aviso = 'TomTom no respondió en esta búsqueda; se muestran los lugares de Geoapify.';
    }

    return [
        'q' => $texto,
        'destino' => $destino,
        'destinos' => lugares_destinos_catalogo(),
        'lugares' => $lugares,
        'sugerencias' => $sugerencias,
        'total' => $total,
        'proveedor' => $proveedor,
        'proveedor_solicitado' => $solicitado,
        'cache' => $cache_estado,
        'aviso' => $aviso,
        'estadisticas' => [
            'joyas' => $joyas,
            'con_rating' => $con_rating,
            'sin_rating' => count($lugares) - $con_rating,
            'paises' => count($paises),
            'por_fuente' => $por_fuente,
            'llamadas_hoy' => lugares_contador_api(),
            'limite_diario' => (int)($_ENV['LUGARES_MAX_CALLS_DIA'] ?? 100),
        ],
    ];
}
