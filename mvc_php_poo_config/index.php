<?php

declare(strict_types=1);

require_once __DIR__ . '/controllers/UserController.php';

// Capturamos la acción evaluando primero GET (enlaces) y luego POST (formularios)
$action = $_GET['action'] ?? $_POST['action'] ?? 'home';

$controller = new UserController();

// El 'match' decide qué función ejecutar según la petición recibida
match ($action) {
    'insertar'         => $controller->insertar(),
    'formulario'       => $controller->mostrarFormulario(),
    'home'             => $controller->showHome(),
    'destinos'         => $controller->showDestinos(),
    'ofertas'          => $controller->showOfertas(),
    'checkin'          => $controller->showCheckin(),
    'ayuda'            => $controller->showAyuda(),
    
    // Nuevas rutas conectadas desde las vistas HTML
    'buscar'           => $controller->buscar(),
    'procesar_soporte' => $controller->procesarSoporte(),
    
    // Ruta por defecto si la acción no coincide o está vacía
    default            => $controller->showHome(),
};