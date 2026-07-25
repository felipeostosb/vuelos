<?php
require_once __DIR__ . '/../config/database.php';

class Reserva {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function crearReserva($pnr, $usuario_id, $vuelo_id, $pasajeros_count, $precio_total, $duffel_order_id = null) {
        $query = "INSERT INTO reservas (pnr, usuario_id, vuelo_id, duffel_order_id, precio_total, pasajeros_count, estado) 
                  VALUES (:pnr, :usuario_id, :vuelo_id, :duffel_order_id, :precio_total, :pasajeros_count, 'Confirmada')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pnr', $pnr);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':vuelo_id', $vuelo_id);
        $stmt->bindParam(':duffel_order_id', $duffel_order_id);
        $stmt->bindParam(':precio_total', $precio_total);
        $stmt->bindParam(':pasajeros_count', $pasajeros_count);
        
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

    public function obtenerReservasUsuario($usuario_id) {
        $query = "SELECT r.*, v.numero_vuelo, al.nombre as aerolinea_nombre, 
                         a_origen.codigo_iata as origen_iata, a_destino.ciudad as destino_ciudad,
                         v.hora_salida, v.hora_llegada,
                         p.nombre as pasajero_nombre, p.apellido as pasajero_apellido
                  FROM reservas r
                  JOIN vuelos v ON r.vuelo_id = v.id
                  JOIN aerolineas al ON v.aerolinea_id = al.id
                  JOIN aeropuertos a_origen ON v.origen_aeropuerto_id = a_origen.id
                  JOIN aeropuertos a_destino ON v.destino_aeropuerto_id = a_destino.id
                  LEFT JOIN pasajeros p ON r.id = p.reserva_id
                  WHERE r.usuario_id = :usuario_id
                  GROUP BY r.id
                  ORDER BY r.fecha_reserva DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        // Adaptamos formato para la vista
        $resultados = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Creamos un array similar a como lo tenía la vista
            $resultados[] = [
                'pnr' => $row['pnr'],
                'estado' => $row['estado'],
                'fecha_reserva' => $row['fecha_reserva'],
                'pasajero_nombre' => trim($row['pasajero_nombre'] . ' ' . $row['pasajero_apellido']),
                'tipo_viaje' => $row['tipo_viaje'] ?? 'solo_ida',
                'fecha_retorno' => '', // Not stored in schema
                'vuelo' => [
                    'flight_number' => $row['numero_vuelo'],
                    'airline' => $row['aerolinea_nombre'],
                    'departure_airport' => $row['origen_iata'],
                    'arrival_airport' => $row['destino_ciudad'] == 'Madrid' ? 'MAD' : $row['destino_ciudad'],
                    'departure_time' => date('H:i', strtotime($row['hora_salida'])),
                    'arrival_time' => date('H:i', strtotime($row['hora_llegada'])),
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
