<?php
require_once __DIR__ . '/../config/env.php';

class DuffelAPI {
    private $token;

    public function __construct() {
        $this->token = $_ENV['DUFFEL_ACCESS_TOKEN'] ?? '';
    }

    public function buscarVuelosEnTiempoReal($origen_iata, $destino_iata, $fecha_salida, $fecha_vuelta = null, $pasajeros_count = 1) {
        if (empty($this->token)) return [];
        
        $url = "https://api.duffel.com/air/offer_requests";
        
        $passengers = [];
        for($i=0; $i<$pasajeros_count; $i++) {
            $passengers[] = ["type" => "adult"];
        }

        $slices = [
            [
                "origin" => $origen_iata,
                "destination" => $destino_iata,
                "departure_date" => $fecha_salida
            ]
        ];
        
        if (!empty($fecha_vuelta)) {
            $slices[] = [
                "origin" => $destino_iata,
                "destination" => $origen_iata,
                "departure_date" => $fecha_vuelta
            ];
        }

        $data = [
            "data" => [
                "slices" => $slices,
                "passengers" => $passengers,
                "cabin_class" => "economy",
                "return_offers" => true
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Duffel-Version: v2',
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $vuelos_formateados = [];

        if ($http_code == 200 || $http_code == 201) {
            $json = json_decode($response, true);
            $offers = $json['data']['offers'] ?? [];
            
            foreach ($offers as $offer) {
                $vuelo = $this->parseOffer($offer);
                if ($vuelo) {
                    $vuelos_formateados[] = $vuelo;
                }
            }
        }
        
        return $vuelos_formateados;
    }

    public function obtenerOfertaPorId($offer_id) {
        if (empty($this->token)) return null;
        
        $url = "https://api.duffel.com/air/offers/" . $offer_id;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Duffel-Version: v2',
            'Authorization: Bearer ' . $this->token
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            $json = json_decode($response, true);
            $offer = $json['data'] ?? null;
            if ($offer) {
                return $this->parseOffer($offer);
            }
        }
        return null;
    }
    
    public function crearOrdenDuffel($offer_id, $passengers_data, $total_amount, $currency) {
        if (empty($this->token)) return null;
        
        $url = "https://api.duffel.com/air/orders";
        
        $data = [
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Duffel-Version: v2',
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 201) {
            $json = json_decode($response, true);
            return $json['data'] ?? null;
        }
        
        return null;
    }

    public function sugerirIata($query) {
        if (empty($this->token) || empty(trim($query))) return null;
        
        $iata = $this->realizarPeticionSugerencia(trim($query));
        
        // Si no encuentra nada, intentar limpiar el query (ej: "Roma italia" -> "Roma")
        if (!$iata) {
            $palabras = explode(' ', trim($query));
            if (count($palabras) > 1) {
                // Intentamos solo con la primera palabra, que suele ser la ciudad
                $iata = $this->realizarPeticionSugerencia($palabras[0]);
            }
        }
        
        return $iata;
    }
    
    private function realizarPeticionSugerencia($query) {
        $url = "https://api.duffel.com/places/suggestions?query=" . urlencode($query);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Duffel-Version: v2',
            'Authorization: Bearer ' . $this->token
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            $json = json_decode($response, true);
            if (!empty($json['data']) && isset($json['data'][0]['iata_code'])) {
                foreach ($json['data'] as $place) {
                    if (isset($place['iata_code']) && !empty($place['iata_code'])) {
                        return $place['iata_code'];
                    }
                }
            }
        }
        return null;
    }
    
    private function parseOffer($offer) {
        if (empty($offer['slices'])) return null;
        
        $outbound_slice = $offer['slices'][0];
        $outbound_segment = $outbound_slice['segments'][0];
        
        $vuelo = [
            'id' => $offer['id'], // 'off_xyz...'
            'offer_id' => $offer['id'],
            'price' => $offer['total_amount'],
            'currency' => $offer['total_currency'],
            'passengers' => $offer['passengers'] ?? [],
            'best_price' => false,
            'is_round_trip' => count($offer['slices']) > 1,
            'outbound' => [
                'slice_id' => $outbound_slice['id'] ?? '',
                'airline' => $outbound_segment['operating_carrier']['name'] ?? $offer['owner']['name'] ?? 'Aerolínea',
                'flight_number' => ($outbound_segment['operating_carrier']['iata_code'] ?? '') . ' ' . ($outbound_segment['operating_carrier_flight_number'] ?? ''),
                'departure_time' => date('H:i', strtotime($outbound_segment['departing_at'])),
                'departure_date' => date('d/m/Y', strtotime($outbound_segment['departing_at'])),
                
                'departure_airport' => $outbound_segment['origin']['iata_code'],
                'departure_airport_name' => $outbound_segment['origin']['name'] ?? $outbound_segment['origin']['iata_code'],
                'departure_city' => $outbound_segment['origin']['city_name'] ?? $outbound_segment['origin']['iata_code'],
                'departure_country' => $outbound_segment['origin']['iata_country_code'] ?? '',
                
                'arrival_time' => date('H:i', strtotime($outbound_slice['segments'][count($outbound_slice['segments'])-1]['arriving_at'])),
                'arrival_date' => date('d/m/Y', strtotime($outbound_slice['segments'][count($outbound_slice['segments'])-1]['arriving_at'])),
                'arrival_next_day' => (date('Y-m-d', strtotime($outbound_segment['departing_at'])) != date('Y-m-d', strtotime($outbound_slice['segments'][count($outbound_slice['segments'])-1]['arriving_at']))),
                
                'arrival_airport' => $outbound_slice['segments'][count($outbound_slice['segments'])-1]['destination']['iata_code'],
                'arrival_airport_name' => $outbound_slice['segments'][count($outbound_slice['segments'])-1]['destination']['name'] ?? $outbound_slice['segments'][count($outbound_slice['segments'])-1]['destination']['iata_code'],
                'arrival_city' => $outbound_slice['segments'][count($outbound_slice['segments'])-1]['destination']['city_name'] ?? $outbound_slice['segments'][count($outbound_slice['segments'])-1]['destination']['iata_code'],
                'arrival_country' => $outbound_slice['segments'][count($outbound_slice['segments'])-1]['destination']['iata_country_code'] ?? '',
                
                'duration' => $outbound_slice['duration'] ?? 'N/A',
                'stops' => count($outbound_slice['segments']) - 1,
            ]
        ];
        
        if ($vuelo['is_round_trip']) {
            $inbound_slice = $offer['slices'][1];
            $inbound_segment = $inbound_slice['segments'][0];
            $vuelo['inbound'] = [
                'slice_id' => $inbound_slice['id'] ?? '',
                'airline' => $inbound_segment['operating_carrier']['name'] ?? $offer['owner']['name'] ?? 'Aerolínea',
                'flight_number' => ($inbound_segment['operating_carrier']['iata_code'] ?? '') . ' ' . ($inbound_segment['operating_carrier_flight_number'] ?? ''),
                'departure_time' => date('H:i', strtotime($inbound_segment['departing_at'])),
                'departure_date' => date('d/m/Y', strtotime($inbound_segment['departing_at'])),
                'departure_airport' => $inbound_segment['origin']['iata_code'],
                'arrival_time' => date('H:i', strtotime($inbound_slice['segments'][count($inbound_slice['segments'])-1]['arriving_at'])),
                'arrival_date' => date('d/m/Y', strtotime($inbound_slice['segments'][count($inbound_slice['segments'])-1]['arriving_at'])),
                'arrival_next_day' => (date('Y-m-d', strtotime($inbound_segment['departing_at'])) != date('Y-m-d', strtotime($inbound_slice['segments'][count($inbound_slice['segments'])-1]['arriving_at']))),
                'arrival_airport' => $inbound_slice['segments'][count($inbound_slice['segments'])-1]['destination']['iata_code'],
                'duration' => $inbound_slice['duration'] ?? 'N/A',
                'stops' => count($inbound_slice['segments']) - 1,
            ];
        }
        
        // Compatibilidad hacia atrás:
        $vuelo = array_merge($vuelo, $vuelo['outbound']);
        
        return $vuelo;
    }
}
?>
