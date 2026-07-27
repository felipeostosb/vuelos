<?php
require 'config/env.php';
require 'models/DuffelAPI.php';
$api = new DuffelAPI();
$flights = $api->buscarVuelosEnTiempoReal('JFK', 'FCO', date('Y-m-d', strtotime('+1 day')), null, 1);
echo "Flights found: " . count($flights) . "\n";
if (count($flights) > 0) {
    echo "First flight destination: " . $flights[0]['arrival_airport'] . " - " . $flights[0]['arrival_city'] . "\n";
}
