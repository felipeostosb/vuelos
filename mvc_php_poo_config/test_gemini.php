<?php
require 'config/env.php';
require 'models/GeminiAPI.php';
$api = new GeminiAPI();
$res = $api->interpretarBusqueda("origen de new york a corea");
print_r($res);
