<?php
require 'config/env.php';
require 'models/Vuelo.php';
$vueloModel = new Vuelo();
$origen = $vueloModel->obtenerIataPorCiudad('new york');
$destino = $vueloModel->obtenerIataPorCiudad('corea');
echo "Origen: $origen\n";
echo "Destino: $destino\n";
