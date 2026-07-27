<?php
require 'config/env.php';
require 'models/Vuelo.php';
require 'models/DuffelAPI.php';

$vueloModel = new Vuelo();
$origen = $vueloModel->obtenerIataPorCiudad('Estados Unidos');
$destino = $vueloModel->obtenerIataPorCiudad('Corea');

echo "Origen extraido: " . $origen . "\n";
echo "Destino extraido: " . $destino . "\n";

if ($origen !== 'LIM' || $destino !== 'LIM') {
    $api = new DuffelAPI();
    $flights = $api->buscarVuelosEnTiempoReal($origen, $destino, date('Y-m-d', strtotime('+1 day')), null, 1);
    echo "Encontrados " . count($flights) . " vuelos de $origen a $destino.\n";
} else {
    echo "Error: Fallback a LIM detectado en ambos.\n";
}
