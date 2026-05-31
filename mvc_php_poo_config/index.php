<?php

declare(strict_types=1);

require_once __DIR__ . '/controllers/UserController.php';

// Cambiamos el valor por defecto a 'home'
$action = $_GET['action'] ?? 'home';

$controller = new UserController();

// El 'match' decide qué función ejecutar según la URL
match ($action) {
    'insertar'   => $controller->insertar(),
    'formulario' => $controller->mostrarFormulario(),
    'home'       => $controller->showHome(),
    'destinos'   => $controller->showDestinos(),
    'ofertas'    => $controller->showOfertas(),
    'checkin'    => $controller->showCheckin(),
    'ayuda'      => $controller->showAyuda(),
    default      => $controller->showHome(),
};