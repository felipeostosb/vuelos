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
        // Aquí se realizará la petición cURL a la API de Gemini
        // Simularemos la respuesta por ahora para la etapa inicial
        
        // Simulación: extraemos palabras clave básicas (ej. "París")
        $destino = '';
        if (stripos($prompt, 'París') !== false) {
            $destino = 'París';
        } elseif (stripos($prompt, 'Madrid') !== false) {
            $destino = 'Madrid';
        }
        
        // Guardar en la base de datos (Historial de consultas IA)
        $parametros = json_encode(['destino' => $destino]);
        $respuesta_raw = "Simulación de respuesta Gemini";
        
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
