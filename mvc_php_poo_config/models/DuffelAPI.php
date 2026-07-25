<?php
require_once __DIR__ . '/../config/env.php';

class DuffelAPI {
    private $token;

    public function __construct() {
        $this->token = $_ENV['DUFFEL_ACCESS_TOKEN'] ?? '';
    }

    public function buscarVuelosEnTiempoReal($origen, $destino, $fecha) {
        // En esta etapa dejamos la estructura lista.
        // Aquí se realizará la petición cURL a https://api.duffel.com/air/offer_requests
        
        // Simulación temporal para evitar romper la web si el token no es válido aún
        return [];
    }
}
?>
