<?php
/**
 * 🌸 ============================================================================================== 🌸
 * MODELO: FlightModel.php (El "Jefe de la Bodega de Vuelos")
 * 🌸 ============================================================================================== 🌸
 * 
 * 💖 CONCEPTO GENERAL:
 * Siguiendo el concepto MVC (Modelo - Vista - Controlador), este archivo es el MODELO.
 * Un Modelo nunca, jamás, se preocupa por cómo se ven las cosas (colores, botones).
 * Su único trabajo es ser el Jefe absoluto de la bodega donde guardamos la información pura.
 * 
 * Cuando la Personal Shopper (Controlador) necesita datos, no entra a buscar sola, le
 * toca la puerta al Modelo y él hace todo el trabajo pesado.
 * ==============================================================================================
 */

class FlightModel
{
    /**
     * 📦 BODEGA DE INVENTARIO BRUTO (getAllFlights)
     * ------------------------------------------------------------------------------------------
     * ¿Qué es este comando?: Es una función "private" (privada). Significa que nadie de afuera 
     * puede venir a tocar esta lista; solo el Jefe de Bodega tiene la llave.
     * 
     * ¿Qué hace?: En la vida real, esta función se conectaría a internet para pedirle los vuelos 
     * a las aerolíneas. Como aún no hemos conectado eso, lo que hicimos fue escribir una lista a mano 
     * (un Arreglo) que simula tener los vuelos reales. En programación, a esto se le llama "Hardcodear" 
     * o hacer un "Mock" (datos de prueba).
     */
    private function getAllFlights(): array
    {
        // "return" significa: Devuélveme todo lo que está dentro de estos corchetes gigantes [ ... ]
        return [
            // RUTAS A MADRID (MAD)
            [
                'id' => 1, 'airline' => 'Copa Airlines', 'flight_number' => 'CM 331',
                'departure_time' => '22:00', 'departure_airport' => 'LIM',
                'arrival_time' => '13:55', 'arrival_next_day' => true, 'arrival_airport' => 'MAD',
                'duration' => '15h 55m', 'stops' => 1, 'price' => 1490, 'best_price' => false,
            ],
            [
                'id' => 2, 'airline' => 'Iberia', 'flight_number' => 'IB 6650',
                'departure_time' => '19:40', 'departure_airport' => 'LIM',
                'arrival_time' => '14:25', 'arrival_next_day' => true, 'arrival_airport' => 'MAD',
                'duration' => '11h 45m', 'stops' => 0, 'price' => 2200, 'best_price' => false,
            ],
            [
                'id' => 3, 'airline' => 'LATAM Airlines', 'flight_number' => 'LA 2451',
                'departure_time' => '08:15', 'departure_airport' => 'LIM',
                'arrival_time' => '22:40', 'arrival_next_day' => false, 'arrival_airport' => 'MAD',
                'duration' => '14h 25m', 'stops' => 0, 'price' => 1850, 'best_price' => true,
            ],
            [
                'id' => 4, 'airline' => 'Avianca', 'flight_number' => 'AV 210',
                'departure_time' => '12:00', 'departure_airport' => 'BOG',
                'arrival_time' => '06:15', 'arrival_next_day' => true, 'arrival_airport' => 'MAD',
                'duration' => '10h 15m', 'stops' => 0, 'price' => 1200, 'best_price' => true,
            ],

            // RUTAS A PARÍS (CDG)
            [
                'id' => 5, 'airline' => 'Air France', 'flight_number' => 'AF 480',
                'departure_time' => '18:15', 'departure_airport' => 'LIM',
                'arrival_time' => '13:40', 'arrival_next_day' => true, 'arrival_airport' => 'París',
                'duration' => '12h 25m', 'stops' => 0, 'price' => 2400, 'best_price' => true,
            ],
            [
                'id' => 6, 'airline' => 'Iberia', 'flight_number' => 'IB 341',
                'departure_time' => '19:40', 'departure_airport' => 'LIM',
                'arrival_time' => '17:25', 'arrival_next_day' => true, 'arrival_airport' => 'París',
                'duration' => '14h 45m', 'stops' => 1, 'price' => 1800, 'best_price' => false,
            ],
            [
                'id' => 7, 'airline' => 'LATAM Airlines', 'flight_number' => 'LA 801',
                'departure_time' => '21:00', 'departure_airport' => 'BOG',
                'arrival_time' => '14:30', 'arrival_next_day' => true, 'arrival_airport' => 'París',
                'duration' => '11h 30m', 'stops' => 0, 'price' => 2100, 'best_price' => false,
            ],

            // RUTAS A BOGOTÁ (BOG)
            [
                'id' => 8, 'airline' => 'Avianca', 'flight_number' => 'AV 204',
                'departure_time' => '10:30', 'departure_airport' => 'LIM',
                'arrival_time' => '14:15', 'arrival_next_day' => false, 'arrival_airport' => 'Bogotá',
                'duration' => '3h 45m', 'stops' => 0, 'price' => 620, 'best_price' => true,
            ],
            [
                'id' => 9, 'airline' => 'LATAM Airlines', 'flight_number' => 'LA 2390',
                'departure_time' => '15:00', 'departure_airport' => 'LIM',
                'arrival_time' => '18:20', 'arrival_next_day' => false, 'arrival_airport' => 'Bogotá',
                'duration' => '3h 20m', 'stops' => 0, 'price' => 750, 'best_price' => false,
            ],

            // RUTAS A CUSCO (CUZ)
            [
                'id' => 10, 'airline' => 'LATAM Airlines', 'flight_number' => 'LA 2011',
                'departure_time' => '05:30', 'departure_airport' => 'LIM',
                'arrival_time' => '06:50', 'arrival_next_day' => false, 'arrival_airport' => 'Cusco',
                'duration' => '1h 20m', 'stops' => 0, 'price' => 150, 'best_price' => true,
            ],
            [
                'id' => 11, 'airline' => 'Avianca', 'flight_number' => 'AV 105',
                'departure_time' => '08:15', 'departure_airport' => 'LIM',
                'arrival_time' => '09:40', 'arrival_next_day' => false, 'arrival_airport' => 'Cusco',
                'duration' => '1h 25m', 'stops' => 0, 'price' => 180, 'best_price' => false,
            ],
            [
                'id' => 12, 'airline' => 'Sky Airline', 'flight_number' => 'H2 5013',
                'departure_time' => '14:00', 'departure_airport' => 'LIM',
                'arrival_time' => '15:20', 'arrival_next_day' => false, 'arrival_airport' => 'Cusco',
                'duration' => '1h 20m', 'stops' => 0, 'price' => 95, 'best_price' => true,
            ],
            
            // RUTAS A MIAMI (MIA)
            [
                'id' => 13, 'airline' => 'American Airlines', 'flight_number' => 'AA 918',
                'departure_time' => '08:00', 'departure_airport' => 'LIM',
                'arrival_time' => '14:50', 'arrival_next_day' => false, 'arrival_airport' => 'Miami',
                'duration' => '5h 50m', 'stops' => 0, 'price' => 1200, 'best_price' => true,
            ],
            [
                'id' => 14, 'airline' => 'LATAM Airlines', 'flight_number' => 'LA 2500',
                'departure_time' => '23:55', 'departure_airport' => 'LIM',
                'arrival_time' => '06:40', 'arrival_next_day' => true, 'arrival_airport' => 'Miami',
                'duration' => '5h 45m', 'stops' => 0, 'price' => 1350, 'best_price' => false,
            ],
            [
                'id' => 15, 'airline' => 'Copa Airlines', 'flight_number' => 'CM 400',
                'departure_time' => '11:20', 'departure_airport' => 'LIM',
                'arrival_time' => '19:30', 'arrival_next_day' => false, 'arrival_airport' => 'Miami',
                'duration' => '8h 10m', 'stops' => 1, 'price' => 950, 'best_price' => false,
            ],
        ];
    }

    /**
     * 🔍 EL FILTRO MÁGICO DE INSTAGRAM (searchFlights)
     * ------------------------------------------------------------------------------------------
     * ¿Qué es este comando?: Es una función pública. Significa que el Controlador puede llamarla.
     * 
     * ¿Cómo funciona?: Imagina que estás comprando zapatos en Shein o Zara y pones el filtro:
     * "Solo quiero zapatos rosados, talla 38". 
     * Esta función toma TODA la ropa de la tienda, la pasa por un embudo de reglas estrictas 
     * y solo deja caer los zapatos que tú pediste.
     */
    public function searchFlights(array $filters): array
    {
        // 1. Agarramos absolutamente TODO el inventario de la bodega y lo ponemos sobre la mesa
        $allFlights = $this->getAllFlights();

        /*
         * 2. PREPARACIÓN DE LAS REGLAS DE LA CLIENTA
         * El comando `isset` pregunta: "¿La clienta me puso una regla para esto?"
         * Por ejemplo: ¿La clienta puso un "precio máximo"? Si sí, lo uso. Si no, le pongo 
         * un límite súper alto por defecto (4000) para no descartar nada.
         */
        $precioMaximo = isset($filters['max_price']) ? (float)$filters['max_price'] : 4000;
        $escalasPermitidas = isset($filters['stops']) ? (array)$filters['stops'] : ['0', '1'];
        $aerolineasPermitidas = isset($filters['airlines']) ? (array)$filters['airlines'] : ['Copa Airlines', 'Avianca', 'LATAM Airlines', 'Iberia'];

        /*
         * 3. EL EMBUDO (La función mágica "array_filter")
         * array_filter es súper genial. Imagina que es un portero exigente.
         * Toma vuelo por vuelo de la mesa (eso es `$vuelo`) y le hace tres preguntas.
         * Si falla aunque sea UNA pregunta, el portero dice "return false;" y bota el vuelo.
         * Si pasa todas las preguntas de estilo, el portero dice "return true;" y lo aprueba.
         */
        $vuelosFiltrados = array_filter($allFlights, function($vuelo) use ($precioMaximo, $escalasPermitidas, $aerolineasPermitidas, $filters) {
            
            // FILTRO 0: Origen y Destino (de la Búsqueda Clásica)
            if (isset($filters['origen']) && $filters['origen'] !== '') {
                // Comparamos el origen (tolerante a mayúsculas/minúsculas o códigos)
                if (stripos($vuelo['departure_airport'], $filters['origen']) === false && stripos($filters['origen'], $vuelo['departure_airport']) === false) {
                    return false;
                }
            }
            if (isset($filters['destino']) && $filters['destino'] !== '') {
                if (stripos($vuelo['arrival_airport'], $filters['destino']) === false && stripos($filters['destino'], $vuelo['arrival_airport']) === false) {
                    return false;
                }
            }

            // FILTRO 1: El Presupuesto
            // Pregunta: Señor vuelo, ¿tú cuestas ($vuelo['price']) más de lo que quiero pagar ($precioMaximo)?
            if ($vuelo['price'] > $precioMaximo) {
                return false; // ¡Eres muy caro, descartado!
            }

            // FILTRO 2: Las Escalas
            // Pregunta: Señor vuelo, ¿tus escalas NO ESTÁN (!in_array) en mi lista de permitidas?
            if (!in_array((string)$vuelo['stops'], $escalasPermitidas)) {
                return false; // ¡No quiero tantas escalas, descartado!
            }

            // FILTRO 3: Las Aerolíneas
            // Pregunta: Señor vuelo, ¿tu marca NO ESTÁ (!in_array) en mi lista de marcas favoritas?
            if (!in_array($vuelo['airline'], $aerolineasPermitidas)) {
                return false; // ¡No me gusta tu aerolínea, descartado!
            }

            // ✨ ¡SOBREVIVISTE! ✨
            // Si el código no chocó con ningún "return false", significa que este vuelo cumple 
            // con todo lo que la clienta pidió. ¡Aprobado y guardado en la nueva caja!
            return true; 
        });

        // 4. ENTREGAMOS EL PAQUETE LISTO
        // Le mandamos a la Personal Shopper (FlightController) la cajita fina que contiene
        // ÚNICAMENTE los vuelos que la clienta va a amar.
        return $vuelosFiltrados;
    }

    /**
     * 🌐 INTEGRACIÓN CON DUFFEL API (Preparación)
     * ------------------------------------------------------------------------------------------
     * ¿Para qué sirve?: Este es el conducto directo a la API real de Duffel para emitir boletos.
     * Actualmente está simulando una respuesta exitosa, pero la estructura está lista.
     */
    public function bookFlightWithDuffel($flightId, $passengers): array
    {
        /*
        // EJEMPLO DE CÓDIGO REAL PARA DUFFEL:
        $duffelApiKey = DUFFEL_API_KEY; // Variable de entorno o config/api.php
        $client = new \GuzzleHttp\Client();
        
        $response = $client->request('POST', 'https://api.duffel.com/air/orders', [
            'headers' => [
                'Authorization' => 'Bearer ' . $duffelApiKey,
                'Duffel-Version' => 'beta',
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'data' => [
                    'selected_offers' => [$flightId],
                    'passengers' => [
                        // Aquí mapearíamos los datos de los pasajeros...
                    ]
                ]
            ]
        ]);
        $data = json_decode($response->getBody(), true);
        return $data['data']['booking_reference']; // El PNR real
        */

        // SIMULACIÓN: Generamos un PNR aleatorio de 6 caracteres (ej: XY8P2Q)
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $pnrSimulado = '';
        for ($i = 0; $i < 6; $i++) {
            $pnrSimulado .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        return [
            'status' => 'success',
            'pnr' => $pnrSimulado,
            'message' => 'Vuelo reservado exitosamente (Simulado)'
        ];
    }
}
