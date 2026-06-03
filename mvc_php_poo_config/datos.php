<?php
/**
 * 👨‍🍳 ============================================================================================== 👨‍🍳
 * LA PIZARRA DEL MENÚ: datos.php
 * 👨‍🍳 ============================================================================================== 👨‍🍳
 * 
 * 💖 BIENVENIDO A TU PRIMER RESTAURANTE:
 * Imagina que este archivo es la pizarra gigante que cuelga en la cocina de tu restaurante.
 * No hay bases de datos complejas, ni internet, ni "Magia de Sistemas". 
 * Solo tenemos listas simples (Arreglos o Arrays) escritas "a mano" en nuestra pizarra.
 * 
 * Aquí guardamos tres cosas importantísimas:
 * 1. La lista de Clientes VIP (Usuarios registrados que pueden entrar al club privado / Login).
 * 2. El Menú del Día (La lista de todos los Vuelos disponibles).
 * 
 * En PHP, usamos el símbolo del dólar ($) para crear un espacio en la pizarra (variable).
 * Y usamos los corchetes [ ] para decir "esto es una lista de varias cosas".
 * ==============================================================================================
 */

// ----------------------------------------------------------------------------------------------
// 📋 1. LISTA DE CLIENTES VIP (Usuarios del Sistema)
// ----------------------------------------------------------------------------------------------
// El recepcionista mirará esta lista cuando alguien intente entrar (Login).
// Cada cliente es un "sub-arreglo" con su email, contraseña y nombre.
$clientes_vip = [
    [
        'email' => 'admin@novairlines.com',
        'password' => '123456', 
        'nombre' => 'Administrador'
    ],
    [
        'email' => 'juan@ejemplo.com',
        'password' => 'secreta123',
        'nombre' => 'Juan Pérez'
    ]
];

// ----------------------------------------------------------------------------------------------
// 🍲 2. EL MENÚ DEL DÍA (Los Vuelos Disponibles)
// ----------------------------------------------------------------------------------------------
// Esta es nuestra carta de platos. Cuando un cliente diga "Quiero ir a Madrid", 
// el mesero buscará en esta lista qué platos (vuelos) tienen como destino "MAD" (Madrid).
$menu_vuelos = [
    // --- PLATOS HACIA MADRID (MAD) ---
    [
        'id' => 1, 
        'airline' => 'Copa Airlines', 
        'flight_number' => 'CM 331',
        'departure_time' => '22:00', 
        'departure_airport' => 'LIM',
        'arrival_time' => '13:55', 
        'arrival_next_day' => true, 
        'arrival_airport' => 'MAD',
        'duration' => '15h 55m', 
        'stops' => 1, 
        'price' => 1490, 
        'best_price' => false,
    ],
    [
        'id' => 2, 
        'airline' => 'Iberia', 
        'flight_number' => 'IB 6650',
        'departure_time' => '19:40', 
        'departure_airport' => 'LIM',
        'arrival_time' => '14:25', 
        'arrival_next_day' => true, 
        'arrival_airport' => 'MAD',
        'duration' => '11h 45m', 
        'stops' => 0, 
        'price' => 2200, 
        'best_price' => false,
    ],
    [
        'id' => 3, 
        'airline' => 'LATAM Airlines', 
        'flight_number' => 'LA 2451',
        'departure_time' => '08:15', 
        'departure_airport' => 'LIM',
        'arrival_time' => '22:40', 
        'arrival_next_day' => false, 
        'arrival_airport' => 'MAD',
        'duration' => '14h 25m', 
        'stops' => 0, 
        'price' => 1850, 
        'best_price' => true,
    ],

    // --- PLATOS HACIA PARÍS (CDG) ---
    [
        'id' => 5, 
        'airline' => 'Air France', 
        'flight_number' => 'AF 480',
        'departure_time' => '18:15', 
        'departure_airport' => 'LIM',
        'arrival_time' => '13:40', 
        'arrival_next_day' => true, 
        'arrival_airport' => 'París',
        'duration' => '12h 25m', 
        'stops' => 0, 
        'price' => 2400, 
        'best_price' => true,
    ],
    [
        'id' => 6, 
        'airline' => 'Iberia', 
        'flight_number' => 'IB 341',
        'departure_time' => '19:40', 
        'departure_airport' => 'LIM',
        'arrival_time' => '17:25', 
        'arrival_next_day' => true, 
        'arrival_airport' => 'París',
        'duration' => '14h 45m', 
        'stops' => 1, 
        'price' => 1800, 
        'best_price' => false,
    ],

    // --- PLATOS HACIA BOGOTÁ (BOG) ---
    [
        'id' => 8, 
        'airline' => 'Avianca', 
        'flight_number' => 'AV 204',
        'departure_time' => '10:30', 
        'departure_airport' => 'LIM',
        'arrival_time' => '14:15', 
        'arrival_next_day' => false, 
        'arrival_airport' => 'Bogotá',
        'duration' => '3h 45m', 
        'stops' => 0, 
        'price' => 620, 
        'best_price' => true,
    ],
    [
        'id' => 9, 
        'airline' => 'LATAM Airlines', 
        'flight_number' => 'LA 2390',
        'departure_time' => '15:00', 
        'departure_airport' => 'LIM',
        'arrival_time' => '18:20', 
        'arrival_next_day' => false, 
        'arrival_airport' => 'Bogotá',
        'duration' => '3h 20m', 
        'stops' => 0, 
        'price' => 750, 
        'best_price' => false,
    ],

    // --- PLATOS HACIA CUSCO (CUZ) ---
    [
        'id' => 10, 
        'airline' => 'LATAM Airlines', 
        'flight_number' => 'LA 2011',
        'departure_time' => '05:30', 
        'departure_airport' => 'LIM',
        'arrival_time' => '06:50', 
        'arrival_next_day' => false, 
        'arrival_airport' => 'Cusco',
        'duration' => '1h 20m', 
        'stops' => 0, 
        'price' => 150, 
        'best_price' => true,
    ],
    [
        'id' => 12, 
        'airline' => 'Sky Airline', 
        'flight_number' => 'H2 5013',
        'departure_time' => '14:00', 
        'departure_airport' => 'LIM',
        'arrival_time' => '15:20', 
        'arrival_next_day' => false, 
        'arrival_airport' => 'Cusco',
        'duration' => '1h 20m', 
        'stops' => 0, 
        'price' => 95, 
        'best_price' => true,
    ],
    
    // --- PLATOS HACIA MIAMI (MIA) ---
    [
        'id' => 13, 
        'airline' => 'American Airlines', 
        'flight_number' => 'AA 918',
        'departure_time' => '08:00', 
        'departure_airport' => 'LIM',
        'arrival_time' => '14:50', 
        'arrival_next_day' => false, 
        'arrival_airport' => 'Miami',
        'duration' => '5h 50m', 
        'stops' => 0, 
        'price' => 1200, 
        'best_price' => true,
    ]
];

// ----------------------------------------------------------------------------------------------
// 📖 3. LIBRO DE RESERVAS (Simulación)
// ----------------------------------------------------------------------------------------------
// Aquí guardaremos las reservaciones que los clientes vayan haciendo.
// Como no usamos base de datos, inicia vacío.
if (!isset($_SESSION['reservas'])) {
    $_SESSION['reservas'] = []; 
}
// Nota del chef: Usamos $_SESSION para que el servidor recuerde las reservas 
// mientras el cliente no cierre el navegador.
?>
