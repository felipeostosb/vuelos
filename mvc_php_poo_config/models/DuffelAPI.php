<?php
/**
 * ==============================================================================================
 * MÓDULO DE INTEGRACIÓN CON DUFFEL API (VERSIÓN PROCEDURAL SIMPLE)
 * ==============================================================================================
 * Este archivo contiene las funciones procedurales para comunicarse con la API de Duffel
 * y buscar ofertas de vuelos en tiempo real.
 * ==============================================================================================
 */

require_once __DIR__ . '/../config/env.php';

/**
 * Busca vuelos reales en la API de Duffel y retorna una lista de ofertas procesadas.
 */
function buscar_vuelos_duffel($origen_iata, $destino_iata, $fecha_salida, $fecha_vuelta = null, $pasajeros_count = 1) {
    $token = $_ENV['DUFFEL_ACCESS_TOKEN'] ?? '';
    if (empty($token)) return [];

    $url = "https://api.duffel.com/air/offer_requests";

    // 1. Armamos la lista de pasajeros (adultos por defecto)
    $pasajeros = [];
    for ($i = 0; $i < $pasajeros_count; $i++) {
        $pasajeros[] = ["type" => "adult"];
    }

    // 2. Definimos los tramos del viaje (ida o ida y vuelta)
    $tramos = [
        [
            "origin" => $origen_iata,
            "destination" => $destino_iata,
            "departure_date" => $fecha_salida
        ]
    ];

    if (!empty($fecha_vuelta)) {
        $tramos[] = [
            "origin" => $destino_iata,
            "destination" => $origen_iata,
            "departure_date" => $fecha_vuelta
        ];
    }

    // 3. Preparamos el cuerpo de la solicitud JSON
    $cuerpo_peticion = [
        "data" => [
            "slices" => $tramos,
            "passengers" => $pasajeros,
            "cabin_class" => "economy",
            "return_offers" => true
        ]
    ];

    // 4. Realizamos la petición HTTP POST vía cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cuerpo_peticion));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Duffel-Version: v2',
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);

    $respuesta = curl_exec($ch);
    $codigo_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $vuelos_formateados = [];

    // 5. Si la respuesta es exitosa (200 o 201), procesamos cada oferta
    if ($codigo_http == 200 || $codigo_http == 201) {
        $datos = json_decode($respuesta, true);
        $ofertas = $datos['data']['offers'] ?? [];

        foreach ($ofertas as $oferta) {
            $vuelo_procesado = procesar_oferta_duffel($oferta);
            if ($vuelo_procesado) {
                $vuelos_formateados[] = $vuelo_procesado;
            }
        }
    }

    return $vuelos_formateados;
}

/**
 * Obtiene los detalles de una oferta específica de Duffel a partir de su ID de oferta.
 */
function obtener_oferta_duffel($offer_id) {
    $token = $_ENV['DUFFEL_ACCESS_TOKEN'] ?? '';
    if (empty($token)) return null;

    $url = "https://api.duffel.com/air/offers/" . $offer_id;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Duffel-Version: v2',
        'Authorization: Bearer ' . $token
    ]);

    $respuesta = curl_exec($ch);
    $codigo_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($codigo_http == 200) {
        $datos = json_decode($respuesta, true);
        $oferta = $datos['data'] ?? null;
        if ($oferta) {
            return procesar_oferta_duffel($oferta);
        }
    }
    return null;
}

/**
 * Crea la orden final de compra en la API de Duffel.
 */
function crear_orden_duffel($offer_id, $passengers_data, $total_amount, $currency) {
    $token = $_ENV['DUFFEL_ACCESS_TOKEN'] ?? '';
    if (empty($token)) return null;

    $url = "https://api.duffel.com/air/orders";

    $cuerpo_peticion = [
        "data" => [
            "type" => "instant",
            "selected_offers" => [$offer_id],
            "passengers" => $passengers_data,
            "payments" => [
                [
                    "type" => "balance",
                    "amount" => (string)$total_amount,
                    "currency" => $currency
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cuerpo_peticion));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Duffel-Version: v2',
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);

    $respuesta = curl_exec($ch);
    $codigo_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($codigo_http == 201) {
        $datos = json_decode($respuesta, true);
        return $datos['data'] ?? null;
    }

    return null;
}

/**
 * Sugiere el código IATA para una ciudad mediante la API de Duffel Places.
 */
function sugerir_iata_duffel($query) {
    $token = $_ENV['DUFFEL_ACCESS_TOKEN'] ?? '';
    if (empty($token) || empty(trim($query))) return null;

    $codigo_iata = realizar_peticion_sugerencia_duffel(trim($query));

    // Si no encuentra nada, probar limpiando la consulta (usar solo la primera palabra)
    if (!$codigo_iata) {
        $palabras = explode(' ', trim($query));
        if (count($palabras) > 1) {
            $codigo_iata = realizar_peticion_sugerencia_duffel($palabras[0]);
        }
    }

    return $codigo_iata;
}

/**
 * Función auxiliar para consultar lugares sugeridos en Duffel.
 */
function realizar_peticion_sugerencia_duffel($query) {
    $token = $_ENV['DUFFEL_ACCESS_TOKEN'] ?? '';
    $url = "https://api.duffel.com/places/suggestions?query=" . urlencode($query);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Duffel-Version: v2',
        'Authorization: Bearer ' . $token
    ]);

    $respuesta = curl_exec($ch);
    $codigo_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($codigo_http == 200) {
        $datos = json_decode($respuesta, true);
        if (!empty($datos['data'])) {
            foreach ($datos['data'] as $lugar) {
                if (isset($lugar['iata_code']) && !empty($lugar['iata_code'])) {
                    return $lugar['iata_code'];
                }
            }
        }
    }
    return null;
}

/**
 * Convierte una estructura de oferta cruda de Duffel a un arreglo uniforme para la aplicación.
 */
function procesar_oferta_duffel($oferta) {
    if (empty($oferta['slices'])) return null;

    $tramo_ida = $oferta['slices'][0];
    $segmento_ida = $tramo_ida['segments'][0];
    $ultimo_segmento_ida = $tramo_ida['segments'][count($tramo_ida['segments']) - 1];

    $vuelo = [
        'id' => $oferta['id'],
        'offer_id' => $oferta['id'],
        'price' => $oferta['total_amount'],
        'currency' => $oferta['total_currency'],
        'passengers' => $oferta['passengers'] ?? [],
        'best_price' => false,
        'is_round_trip' => count($oferta['slices']) > 1,
        'outbound' => [
            'slice_id' => $tramo_ida['id'] ?? '',
            'airline' => $segmento_ida['operating_carrier']['name'] ?? $oferta['owner']['name'] ?? 'Aerolínea',
            'flight_number' => ($segmento_ida['operating_carrier']['iata_code'] ?? '') . ' ' . ($segmento_ida['operating_carrier_flight_number'] ?? ''),
            'departure_time' => date('H:i', strtotime($segmento_ida['departing_at'])),
            'departure_date' => date('d/m/Y', strtotime($segmento_ida['departing_at'])),
            
            'departure_airport' => $segmento_ida['origin']['iata_code'],
            'departure_airport_name' => $segmento_ida['origin']['name'] ?? $segmento_ida['origin']['iata_code'],
            'departure_city' => $segmento_ida['origin']['city_name'] ?? $segmento_ida['origin']['iata_code'],
            'departure_country' => $segmento_ida['origin']['iata_country_code'] ?? '',
            
            'arrival_time' => date('H:i', strtotime($ultimo_segmento_ida['arriving_at'])),
            'arrival_date' => date('d/m/Y', strtotime($ultimo_segmento_ida['arriving_at'])),
            'arrival_next_day' => (date('Y-m-d', strtotime($segmento_ida['departing_at'])) != date('Y-m-d', strtotime($ultimo_segmento_ida['arriving_at']))),
            
            'arrival_airport' => $ultimo_segmento_ida['destination']['iata_code'],
            'arrival_airport_name' => $ultimo_segmento_ida['destination']['name'] ?? $ultimo_segmento_ida['destination']['iata_code'],
            'arrival_city' => $ultimo_segmento_ida['destination']['city_name'] ?? $ultimo_segmento_ida['destination']['iata_code'],
            'arrival_country' => $ultimo_segmento_ida['destination']['iata_country_code'] ?? '',
            
            'duration' => $tramo_ida['duration'] ?? 'N/A',
            'stops' => count($tramo_ida['segments']) - 1,
        ]
    ];

    if ($vuelo['is_round_trip']) {
        $tramo_vuelta = $oferta['slices'][1];
        $segmento_vuelta = $tramo_vuelta['segments'][0];
        $ultimo_segmento_vuelta = $tramo_vuelta['segments'][count($tramo_vuelta['segments']) - 1];

        $vuelo['inbound'] = [
            'slice_id' => $tramo_vuelta['id'] ?? '',
            'airline' => $segmento_vuelta['operating_carrier']['name'] ?? $oferta['owner']['name'] ?? 'Aerolínea',
            'flight_number' => ($segmento_vuelta['operating_carrier']['iata_code'] ?? '') . ' ' . ($segmento_vuelta['operating_carrier_flight_number'] ?? ''),
            'departure_time' => date('H:i', strtotime($segmento_vuelta['departing_at'])),
            'departure_date' => date('d/m/Y', strtotime($segmento_vuelta['departing_at'])),
            'departure_airport' => $segmento_vuelta['origin']['iata_code'],
            'arrival_time' => date('H:i', strtotime($ultimo_segmento_vuelta['arriving_at'])),
            'arrival_date' => date('d/m/Y', strtotime($ultimo_segmento_vuelta['arriving_at'])),
            'arrival_next_day' => (date('Y-m-d', strtotime($segmento_vuelta['departing_at'])) != date('Y-m-d', strtotime($ultimo_segmento_vuelta['arriving_at']))),
            'arrival_airport' => $ultimo_segmento_vuelta['destination']['iata_code'],
            'duration' => $tramo_vuelta['duration'] ?? 'N/A',
            'stops' => count($tramo_vuelta['segments']) - 1,
        ];
    }

    $vuelo = array_merge($vuelo, $vuelo['outbound']);
    return $vuelo;
}
?>
