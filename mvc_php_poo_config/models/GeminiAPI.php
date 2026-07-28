<?php
/**
 * ==============================================================================================
 * MÓDULO DE INTELIGENCIA ARTIFICIAL GEMINI (VERSIÓN PROCEDURAL SIMPLE)
 * ==============================================================================================
 * Este archivo contiene las funciones para procesar peticiones en lenguaje natural del usuario
 * mediante el modelo Google Gemini AI.
 * ==============================================================================================
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Función auxiliar para extraer el número de pasajeros mediante expresiones regulares.
 */
function extraer_pasajeros_regex($prompt) {
    $prompt_low = mb_strtolower($prompt, 'UTF-8');

    // 1. Números explícitos seguidos de términos de pasaje/pasajeros/personas
    if (preg_match('/(\d+)\s*(?:personas?|pasajeros?|pasajes?|boletos?|adultos?|viajeros?)/i', $prompt_low, $matches)) {
        return (int)$matches[1];
    }
    // 2. Patrón "para X personas" o "para X"
    if (preg_match('/para\s+(\d+)/i', $prompt_low, $matches)) {
        return (int)$matches[1];
    }
    // 3. Patrón "somos X"
    if (preg_match('/somos\s+(\d+)/i', $prompt_low, $matches)) {
        return (int)$matches[1];
    }
    // 4. Expresiones relacionales ("con mi esposa", "con mi pareja", "con mi esposa y 2 hijos")
    if (preg_match('/con\s+mi\s+(?:esposa|esposo|pareja|novia|novio|mama|mamá|papa|papá|hijo|hija|amigo|amiga)\b/i', $prompt_low)) {
        if (preg_match('/y\s+(\d+)\s+hijos?/i', $prompt_low, $m_hijos)) {
            return 2 + (int)$m_hijos[1];
        }
        return 2;
    }
    // 5. "familia de X" o "nosotros X"
    if (preg_match('/familia\s+de\s+(\d+)/i', $prompt_low, $matches)) {
        return (int)$matches[1];
    }
    if (preg_match('/nosotros\s+(\d+)/i', $prompt_low, $matches)) {
        return (int)$matches[1];
    }
    return 1;
}

/**
 * Función que envía el texto en lenguaje natural ingresado por el usuario a Google Gemini AI
 * para extraer parámetros estructurados (origen, destino, fechas, pasajeros, etc.).
 */
function interpretar_busqueda_ia($prompt, $usuario_id = null) {
    // 1. Obtenemos las claves de API de las variables de entorno
    $keys_cadena = $_ENV['GEMINI_API_KEY'] ?? '';
    $lista_keys = array_filter(array_map('trim', explode(',', $keys_cadena)));

    // Si no hay llaves configuradas, usamos el modo de simulación local
    if (empty($lista_keys)) {
        return simular_busqueda_ia($prompt, $usuario_id);
    }

    $fecha_actual = date('Y-m-d');
    
    // Instrucción del sistema para forzar a la IA a responder un JSON limpio
    $instruccion_sistema = "Eres un asistente experto de reservas de la aerolínea NovAirlines. Tu tarea es extraer la intención de búsqueda de vuelos del usuario y devolver un JSON puro (sin formato markdown ni bloques de código).
Hoy es $fecha_actual.
Extrae los siguientes campos:
- origen (ciudad, por defecto 'Lima' si no se menciona)
- destino (ciudad)
- fecha_salida (en formato YYYY-MM-DD, asume una fecha futura razonable si usa palabras como 'mañana', 'próximo mes', o usa mañana si no dice nada)
- fecha_vuelta (en formato YYYY-MM-DD, déjalo vacío si es solo ida)
- tipo_viaje (debe ser strictly 'solo_ida' o 'ida_vuelta')
- pasajeros (número entero positivo total de personas viajando, ej: 'con 3 personas' => 3, 'con mi esposa' => 2, '4 pasajes' => 4, por defecto 1)

Ejemplo de salida:
{\"origen\": \"Lima\", \"destino\": \"Miami\", \"fecha_salida\": \"2026-08-10\", \"fecha_vuelta\": \"2026-08-20\", \"tipo_viaje\": \"ida_vuelta\", \"pasajeros\": 2}";

    $datos_peticion = [
        "contents" => [
            [
                "role" => "user",
                "parts" => [
                    ["text" => $instruccion_sistema . "\nFrase del usuario: " . $prompt]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.1,
            "responseMimeType" => "application/json"
        ]
    ];

    $codigo_http = 0;
    $respuesta_raw = null;

    // Modelos Gemini estables a probar en caso de falla o alta demanda
    $modelos_gemini = ['gemini-3.5-flash-lite', 'gemini-2.0-flash-lite', 'gemini-2.0-flash','gemini-flash-latest'];

    // 2. Probamos con cada API key y modelo disponible hasta recibir respuesta exitosa (200)
    foreach ($lista_keys as $key) {
        foreach ($modelos_gemini as $modelo) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $modelo . ":generateContent?key=" . $key;
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos_peticion));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $respuesta_raw = curl_exec($ch);
            $codigo_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($codigo_http == 200 && $respuesta_raw) {
                break 2;
            }
        }
    }

    // Estructura por defecto con extracción inteligente de pasajeros vía regex
    $pasajeros_detectados = extraer_pasajeros_regex($prompt);

    $datos_extraidos = [
        'origen' => 'Lima',
        'destino' => '',
        'fecha_salida' => date('Y-m-d', strtotime('+1 day')),
        'fecha_vuelta' => '',
        'tipo_viaje' => 'solo_ida',
        'pasajeros' => $pasajeros_detectados
    ];
    
    $parametros_json = json_encode($datos_extraidos);
    $respuesta_historial = json_encode(['error' => 'No response']);

    // 3. Procesamos la respuesta de la IA si fue exitosa
    if ($codigo_http == 200 && $respuesta_raw) {
        $json_respuesta = json_decode($respuesta_raw, true);
        $texto_ia = $json_respuesta['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        
        $datos_ia = json_decode(trim($texto_ia), true);
        if (is_array($datos_ia)) {
            // Aseguramos que la cantidad de pasajeros sea un entero válido >= 1
            if (isset($datos_ia['pasajeros']) && (int)$datos_ia['pasajeros'] > 0) {
                $datos_ia['pasajeros'] = (int)$datos_ia['pasajeros'];
            } else {
                $datos_ia['pasajeros'] = $pasajeros_detectados;
            }
            $datos_extraidos = array_merge($datos_extraidos, array_filter($datos_ia));
            $parametros_json = json_encode($datos_extraidos);
        }
        $respuesta_historial = $respuesta_raw;
    } else {
        // 4. Fallback manual por expresiones regulares en caso la API falle
        $respuesta_historial = json_encode(['error' => 'API falló', 'code' => $codigo_http, 'raw' => $respuesta_raw]);
        
        if (preg_match('/(?:desde|de)\s+([a-zA-Z\s]+?)\s+(?:a|hacia|para)/i', $prompt, $coincidencias)) {
            $datos_extraidos['origen'] = trim($coincidencias[1]);
        } elseif (stripos($prompt, 'new york') !== false || stripos($prompt, 'nueva york') !== false) {
            if (stripos($prompt, 'a new york') !== false || stripos($prompt, 'para new york') !== false) {
                $datos_extraidos['destino'] = 'New York';
            } else {
                $datos_extraidos['origen'] = 'New York';
            }
        }

        if (stripos($prompt, 'Miami') !== false) {
            $datos_extraidos['destino'] = 'Miami';
        } elseif (stripos($prompt, 'París') !== false || stripos($prompt, 'Paris') !== false) {
            $datos_extraidos['destino'] = 'París';
        } elseif (stripos($prompt, 'Madrid') !== false) {
            $datos_extraidos['destino'] = 'Madrid';
        } elseif (stripos($prompt, 'Cusco') !== false || stripos($prompt, 'Cuzco') !== false) {
            $datos_extraidos['destino'] = 'Cusco';
        } elseif (stripos($prompt, 'Corea') !== false || stripos($prompt, 'Seoul') !== false || stripos($prompt, 'Seul') !== false) {
            $datos_extraidos['destino'] = 'Seúl';
        } elseif (stripos($prompt, 'Roma') !== false || stripos($prompt, 'Rome') !== false) {
            $datos_extraidos['destino'] = 'Roma';
        } elseif (stripos($prompt, 'Buenos Aires') !== false || stripos($prompt, 'Argentina') !== false) {
            $datos_extraidos['destino'] = 'Buenos Aires';
        }
        
        $datos_extraidos['pasajeros'] = $pasajeros_detectados;
        $parametros_json = json_encode($datos_extraidos);
    }

    // 5. Guardamos la consulta en el historial de la base de datos
    $conexion = conectar_db();
    if ($conexion) {
        $sql = "INSERT INTO consultas_ia (usuario_id, prompt_original, parametros_extraidos, respuesta_raw) 
                VALUES (:usuario_id, :prompt, :parametros, :respuesta_raw)";
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':usuario_id', $usuario_id);
        $consulta->bindParam(':prompt', $prompt);
        $consulta->bindParam(':parametros', $parametros_json);
        $consulta->bindParam(':respuesta_raw', $respuesta_historial);
        $consulta->execute();
    }

    return $datos_extraidos;
}

/**
 * Función alternativa para simular la búsqueda con IA si no hay claves API disponibles.
 */
function simular_busqueda_ia($prompt, $usuario_id) {
    $destino = '';
    if (stripos($prompt, 'París') !== false) $destino = 'París';
    elseif (stripos($prompt, 'Madrid') !== false) $destino = 'Madrid';
    
    $datos_simulados = [
        'origen' => 'Lima',
        'destino' => $destino,
        'fecha_salida' => date('Y-m-d', strtotime('+1 day')),
        'fecha_vuelta' => '',
        'tipo_viaje' => 'solo_ida',
        'pasajeros' => 1
    ];

    $parametros_json = json_encode($datos_simulados);
    $respuesta_raw = json_encode(["mensaje" => "Simulación de respuesta Gemini sin API key"]);
    
    $conexion = conectar_db();
    if ($conexion) {
        $sql = "INSERT INTO consultas_ia (usuario_id, prompt_original, parametros_extraidos, respuesta_raw) 
                VALUES (:usuario_id, :prompt, :parametros, :respuesta_raw)";
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':usuario_id', $usuario_id);
        $consulta->bindParam(':prompt', $prompt);
        $consulta->bindParam(':parametros', $parametros_json);
        $consulta->bindParam(':respuesta_raw', $respuesta_raw);
        $consulta->execute();
    }
    
    return $datos_simulados;
}
?>
