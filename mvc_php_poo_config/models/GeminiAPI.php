<?php
require_once __DIR__ . '/../config/database.php';

class GeminiAPI {
    private $apiKey;
    private $conn;

    public function __construct() {
        // Obtenemos la key directamente de las variables de entorno
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
        
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function interpretarBusqueda($prompt, $usuario_id = null) {
        // En caso de no tener API key o si está vacía, no fallamos
        if (empty($this->apiKey)) {
            return $this->simularBusqueda($prompt, $usuario_id);
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=" . $this->apiKey;

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

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // En local a veces falla el certificado

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

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
