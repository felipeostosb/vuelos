<?php
require_once __DIR__ . '/../config/database.php';

class GeminiAPI {
    private $apiKeys = [];
    private $conn;

    public function __construct() {
        // Obtenemos la key directamente de las variables de entorno
        $keys = $_ENV['GEMINI_API_KEY'] ?? '';
        // Permitir múltiples llaves separadas por coma
        $this->apiKeys = array_filter(array_map('trim', explode(',', $keys)));
        
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function interpretarBusqueda($prompt, $usuario_id = null) {
        // En caso de no tener API key o si está vacía, no fallamos
        if (empty($this->apiKeys)) {
            return $this->simularBusqueda($prompt, $usuario_id);
        }

        $fecha_actual = date('Y-m-d');
        $system_instruction = "Eres un asistente experto de reservas de la aerolínea NovAirlines. Tu tarea es extraer la intención de búsqueda de vuelos del usuario y devolver un JSON puro (sin formato markdown ni bloques de código).
Hoy es $fecha_actual.
Extrae los siguientes campos:
- origen (ciudad, por defecto 'Lima' si no se menciona)
- destino (ciudad)
- fecha_salida (en formato YYYY-MM-DD, asume una fecha futura razonable si usa palabras como 'mañana', 'próximo mes', o usa mañana si no dice nada)
- fecha_vuelta (en formato YYYY-MM-DD, déjalo vacío si es solo ida)
- tipo_viaje (debe ser estrictamente 'solo_ida' o 'ida_vuelta')
- pasajeros (número entero, por defecto 1)

Ejemplo de salida:
{\"origen\": \"Lima\", \"destino\": \"Miami\", \"fecha_salida\": \"2026-08-10\", \"fecha_vuelta\": \"2026-08-20\", \"tipo_viaje\": \"ida_vuelta\", \"pasajeros\": 2}";

        $data = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => $system_instruction . "\nFrase del usuario: " . $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.1,
                "responseMimeType" => "application/json"
            ]
        ];

        $http_code = 0;
        $response = null;

        // Intentar con cada llave hasta que una funcione
        foreach ($this->apiKeys as $key) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=" . $key;
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // En local a veces falla el certificado
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Si la llamada es exitosa, salimos del bucle para no usar las otras llaves
            if ($http_code == 200 && $response) {
                break;
            }
        }

        $datos_extraidos = [
            'origen' => 'Lima',
            'destino' => '',
            'fecha_salida' => date('Y-m-d', strtotime('+1 day')),
            'fecha_vuelta' => '',
            'tipo_viaje' => 'solo_ida',
            'pasajeros' => 1
        ];
        
        $parametros = json_encode($datos_extraidos);
        $respuesta_raw = json_encode(['error' => 'No response']);

        if ($http_code == 200 && $response) {
            $json_response = json_decode($response, true);
            $texto_ia = $json_response['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            // Convertimos el JSON de Gemini a array PHP
            $datos_ia = json_decode(trim($texto_ia), true);
            if (is_array($datos_ia)) {
                $datos_extraidos = array_merge($datos_extraidos, array_filter($datos_ia));
                $parametros = json_encode($datos_extraidos);
            }
            $respuesta_raw = $response; // Guardamos todo el JSON original de Google
        } else {
            $respuesta_raw = json_encode(['error' => 'API falló', 'code' => $http_code, 'raw' => $response]);
            
            // Fallback manual de emergencia por si la API falla (ej. error 429 por falta de créditos)
            // Extraer posible origen si dice "desde X" o "de X"
            if (preg_match('/(?:desde|de)\s+([a-zA-Z\s]+?)\s+(?:a|hacia|para)/i', $prompt, $matches)) {
                $datos_extraidos['origen'] = trim($matches[1]);
            } elseif (stripos($prompt, 'new york') !== false || stripos($prompt, 'nueva york') !== false) {
                // Si menciona new york pero no capturó el regex, asumimos origen o destino según contexto (por defecto origen si no hay otro)
                if (stripos($prompt, 'a new york') !== false || stripos($prompt, 'para new york') !== false) {
                    $datos_extraidos['destino'] = 'New York';
                } else {
                    $datos_extraidos['origen'] = 'New York';
                }
            }

            // Extraer posible destino
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
            
            // Si encontró algo en el fallback, actualizamos los parámetros que se guardarán
            if (!empty($datos_extraidos['destino']) || $datos_extraidos['origen'] !== 'Lima') {
                $parametros = json_encode($datos_extraidos);
            }
        }

        // Guardar el historial en la BD
        $query = "INSERT INTO consultas_ia (usuario_id, prompt_original, parametros_extraidos, respuesta_raw) 
                  VALUES (:usuario_id, :prompt, :parametros, :respuesta_raw)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':prompt', $prompt);
        $stmt->bindParam(':parametros', $parametros);
        $stmt->bindParam(':respuesta_raw', $respuesta_raw);
        $stmt->execute();

        return $datos_extraidos;
    }

    private function simularBusqueda($prompt, $usuario_id) {
        $destino = '';
        if (stripos($prompt, 'París') !== false) $destino = 'París';
        elseif (stripos($prompt, 'Madrid') !== false) $destino = 'Madrid';
        
        $parametros = json_encode(['destino' => $destino]);
        $respuesta_raw = json_encode(["mensaje" => "Simulación de respuesta Gemini sin API key"]);
        
        $query = "INSERT INTO consultas_ia (usuario_id, prompt_original, parametros_extraidos, respuesta_raw) 
                  VALUES (:usuario_id, :prompt, :parametros, :respuesta_raw)";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':prompt', $prompt);
        $stmt->bindParam(':parametros', $parametros);
        $stmt->bindParam(':respuesta_raw', $respuesta_raw);
        $stmt->execute();
        
        return $destino;
    }
}
?>
