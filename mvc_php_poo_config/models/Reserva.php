<?php
require_once __DIR__ . '/../config/database.php';

class Reserva {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function crearReserva($pnr, $usuario_id, $vuelo_id, $pasajeros_count, $precio_total, $duffel_order_id = null, $tipo_viaje = 'solo_ida', $vuelo_vuelta_data = null) {
        $query = "INSERT INTO reservas (pnr, usuario_id, vuelo_id, duffel_order_id, tipo_viaje, precio_total, pasajeros_count, vuelo_vuelta_data, estado) 
                  VALUES (:pnr, :usuario_id, :vuelo_id, :duffel_order_id, :tipo_viaje, :precio_total, :pasajeros_count, :vuelo_vuelta_data, 'Confirmada')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pnr', $pnr);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':vuelo_id', $vuelo_id);
        $stmt->bindParam(':duffel_order_id', $duffel_order_id);
        $stmt->bindParam(':tipo_viaje', $tipo_viaje);
        $stmt->bindParam(':precio_total', $precio_total);
        $stmt->bindParam(':pasajeros_count', $pasajeros_count);
        $vuelo_vuelta_json = $vuelo_vuelta_data ? json_encode($vuelo_vuelta_data) : null;
        $stmt->bindParam(':vuelo_vuelta_data', $vuelo_vuelta_json);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function agregarPasajero($reserva_id, $nombre, $apellido = '') {
        $query = "INSERT INTO pasajeros (reserva_id, nombre, apellido) VALUES (:reserva_id, :nombre, :apellido)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reserva_id', $reserva_id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        
        return $stmt->execute();
    }

    public function obtenerPasajeros($reserva_id) {
        $query = "SELECT * FROM pasajeros WHERE reserva_id = :reserva_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reserva_id', $reserva_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerReservaPorPnr($pnr) {
        $query = "SELECT r.*, v.numero_vuelo, al.nombre as aerolinea_nombre, 
                         a_origen.codigo_iata as origen_iata, a_origen.ciudad as origen_ciudad, a_origen.nombre as origen_nombre,
                         a_destino.codigo_iata as destino_iata, a_destino.ciudad as destino_ciudad, a_destino.nombre as destino_nombre,
                         v.hora_salida, v.hora_llegada, v.duracion, v.escalas, v.precio as precio_unitario_vuelo
                  FROM reservas r
                  LEFT JOIN vuelos v ON r.vuelo_id = v.id
                  LEFT JOIN aerolineas al ON v.aerolinea_id = al.id
                  LEFT JOIN aeropuertos a_origen ON v.origen_aeropuerto_id = a_origen.id
                  LEFT JOIN aeropuertos a_destino ON v.destino_aeropuerto_id = a_destino.id
                  WHERE r.pnr = :pnr LIMIT 1";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pnr', $pnr);
        $stmt->execute();
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pasajeros = $this->obtenerPasajeros($row['id']);
            $pasajero_principal = !empty($pasajeros) ? trim($pasajeros[0]['nombre'] . ' ' . $pasajeros[0]['apellido']) : 'Pasajero';

            return [
                'id' => $row['id'],
                'pnr' => $row['pnr'],
                'estado' => $row['estado'],
                'fecha_reserva' => $row['fecha_reserva'],
                'precio_total' => (float)$row['precio_total'],
                'pasajeros_count' => (int)$row['pasajeros_count'],
                'tipo_viaje' => $row['tipo_viaje'] ?? 'solo_ida',
                'duffel_order_id' => $row['duffel_order_id'],
                'pasajeros' => $pasajeros,
                'pasajero_nombre' => $pasajero_principal,
                'vuelo' => [
                    'flight_number' => $row['numero_vuelo'] ?? 'N/A',
                    'airline' => $row['aerolinea_nombre'] ?? 'Aerolínea',
                    'departure_airport' => $row['origen_iata'] ?? 'LIM',
                    'departure_city' => $row['origen_ciudad'] ?? 'Lima',
                    'arrival_airport' => $row['destino_iata'] ?? $row['destino_ciudad'] ?? 'DEST',
                    'arrival_city' => $row['destino_ciudad'] ?? 'Destino',
                    'departure_time' => !empty($row['hora_salida']) ? date('H:i', strtotime($row['hora_salida'])) : '08:00',
                    'arrival_time' => !empty($row['hora_llegada']) ? date('H:i', strtotime($row['hora_llegada'])) : '12:00',
                    'duration' => $row['duracion'] ?? '2h 00m',
                    'stops' => $row['escalas'] ?? 0,
                    'date' => date('d M Y', strtotime($row['fecha_reserva']))
                ]
            ];
        }
        return null;
    }

    public function obtenerReservasUsuario($usuario_id) {
        $query = "SELECT r.*, v.numero_vuelo, al.nombre as aerolinea_nombre, 
                         a_origen.codigo_iata as origen_iata, a_origen.ciudad as origen_ciudad,
                         a_destino.codigo_iata as destino_iata, a_destino.ciudad as destino_ciudad,
                         v.hora_salida, v.hora_llegada
                  FROM reservas r
                  LEFT JOIN vuelos v ON r.vuelo_id = v.id
                  LEFT JOIN aerolineas al ON v.aerolinea_id = al.id
                  LEFT JOIN aeropuertos a_origen ON v.origen_aeropuerto_id = a_origen.id
                  LEFT JOIN aeropuertos a_destino ON v.destino_aeropuerto_id = a_destino.id
                  WHERE r.usuario_id = :usuario_id
                  ORDER BY r.fecha_reserva DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        $resultados = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pasajeros = $this->obtenerPasajeros($row['id']);
            $pasajero_principal = !empty($pasajeros) ? trim($pasajeros[0]['nombre'] . ' ' . $pasajeros[0]['apellido']) : 'Pasajero';

            // Parsear datos del vuelo de vuelta si existen
            $vuelo_vuelta = null;
            if (!empty($row['vuelo_vuelta_data'])) {
                $vuelo_vuelta = json_decode($row['vuelo_vuelta_data'], true);
            }

            $resultados[] = [
                'id' => $row['id'],
                'pnr' => $row['pnr'],
                'estado' => $row['estado'],
                'fecha_reserva' => $row['fecha_reserva'],
                'precio_total' => (float)$row['precio_total'],
                'pasajeros_count' => (int)$row['pasajeros_count'],
                'tipo_viaje' => $row['tipo_viaje'] ?? 'solo_ida',
                'pasajero_nombre' => $pasajero_principal,
                'pasajeros' => $pasajeros,
                'fecha_retorno' => '',
                'vuelo_vuelta' => $vuelo_vuelta,
                'vuelo' => [
                    'flight_number' => $row['numero_vuelo'] ?? 'N/A',
                    'airline' => $row['aerolinea_nombre'] ?? 'Aerolínea',
                    'departure_airport' => $row['origen_iata'] ?? 'LIM',
                    'arrival_airport' => $row['destino_iata'] ?? $row['destino_ciudad'] ?? 'DEST',
                    'departure_time' => !empty($row['hora_salida']) ? date('H:i', strtotime($row['hora_salida'])) : '08:00',
                    'arrival_time' => !empty($row['hora_llegada']) ? date('H:i', strtotime($row['hora_llegada'])) : '12:00',
                    'date' => date('d M Y', strtotime($row['fecha_reserva']))
                ]
            ];
        }
        return $resultados;
    }

    public function hacerCheckin($pnr, $usuario_id) {
        $query = "UPDATE reservas SET estado = 'Checked-in' WHERE pnr = :pnr AND (usuario_id = :usuario_id OR :usuario_id = 999)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pnr', $pnr);
        $stmt->bindParam(':usuario_id', $usuario_id);
        
        return $stmt->execute() && $stmt->rowCount() > 0;
    }
}
?>
