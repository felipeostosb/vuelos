<?php
require 'config/env.php';
require 'models/Vuelo.php';
require 'models/DuffelAPI.php';

$api = new DuffelAPI();
echo "Duffel New York: " . $api->sugerirIata('New York') . "\n";
echo "Duffel Roma italia: " . $api->sugerirIata('Roma italia') . "\n";
