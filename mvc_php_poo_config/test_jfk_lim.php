<?php
require 'config/env.php';
require 'models/DuffelAPI.php';
$api = new DuffelAPI();
$flights = $api->buscarVuelosEnTiempoReal('JFK', 'LIM', date('Y-m-d', strtotime('+1 day')), null, 1);
echo "Flights found: " . count($flights) . "\n";
