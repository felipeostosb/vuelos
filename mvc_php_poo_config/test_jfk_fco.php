<?php
require 'config/env.php';
require 'models/DuffelAPI.php';
$api = new DuffelAPI();
$flights = $api->buscarVuelosEnTiempoReal('JFK', 'FCO', date('Y-m-d', strtotime('+1 day')), null, 1);
echo "Flight 0 Arrival: " . $flights[0]['arrival_airport'] . " - " . $flights[0]['arrival_city'] . "\n";
foreach ($flights as $i => $flight) {
    if (strpos($flight['arrival_city'], 'Seoul') !== false || strpos($flight['arrival_airport'], 'ICN') !== false) {
        echo "Found Seoul at index $i!\n";
    }
}
