<?php
require_once __DIR__ . '/../config/database.php';

class Vuelo {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerTodos() {
        $query = "SELECT v.*, 
                         a_origen.codigo_iata as origen_iata, a_origen.nombre as origen_nombre,
                         a_destino.codigo_iata as destino_iata, a_destino.nombre as destino_nombre, a_destino.ciudad as destino_ciudad,
                         al.nombre as aerolinea_nombre
                  FROM vuelos v
                  JOIN aeropuertos a_origen ON v.origen_aeropuerto_id = a_origen.id
                  JOIN aeropuertos a_destino ON v.destino_aeropuerto_id = a_destino.id
                  JOIN aerolineas al ON v.aerolinea_id = al.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarTradicional($destino_ciudad = '') {
        $query = "SELECT v.*, 
                         a_origen.codigo_iata as origen_iata, a_origen.nombre as origen_nombre,
                         a_destino.codigo_iata as destino_iata, a_destino.nombre as destino_nombre, a_destino.ciudad as destino_ciudad,
                         al.nombre as aerolinea_nombre
                  FROM vuelos v
                  JOIN aeropuertos a_origen ON v.origen_aeropuerto_id = a_origen.id
                  JOIN aeropuertos a_destino ON v.destino_aeropuerto_id = a_destino.id
                  JOIN aerolineas al ON v.aerolinea_id = al.id";
                  
        if (!empty($destino_ciudad)) {
            $query .= " WHERE a_destino.ciudad LIKE :destino OR a_destino.codigo_iata LIKE :destino";
        }

        $stmt = $this->conn->prepare($query);
        
        if (!empty($destino_ciudad)) {
            $searchTerm = "%" . $destino_ciudad . "%";
            $stmt->bindParam(':destino', $searchTerm);
        }
        
        $stmt->execute();
        
        // Convertimos al formato que usaba el frontend (para no romper las vistas)
        $resultados = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = [
                'id' => $row['id'],
                'airline' => $row['aerolinea_nombre'],
                'flight_number' => $row['numero_vuelo'],
                'departure_time' => date('H:i', strtotime($row['hora_salida'])),
                'departure_airport' => $row['origen_iata'],
                'arrival_time' => date('H:i', strtotime($row['hora_llegada'])),
                'arrival_next_day' => (bool)$row['llegada_dia_siguiente'],
                'arrival_airport' => $row['destino_ciudad'] == 'Madrid' ? 'MAD' : $row['destino_ciudad'], // Adaptación rápida al formato anterior
                'duration' => $row['duracion'],
                'stops' => $row['escalas'],
                'price' => $row['precio'],
                'best_price' => (bool)$row['es_mejor_precio']
            ];
        }
        return $resultados;
    }

    public function obtenerPorId($id) {
        $query = "SELECT v.*, 
                         a_origen.codigo_iata as origen_iata, a_origen.nombre as origen_nombre,
                         a_destino.codigo_iata as destino_iata, a_destino.nombre as destino_nombre, a_destino.ciudad as destino_ciudad,
                         al.nombre as aerolinea_nombre
                  FROM vuelos v
                  JOIN aeropuertos a_origen ON v.origen_aeropuerto_id = a_origen.id
                  JOIN aeropuertos a_destino ON v.destino_aeropuerto_id = a_destino.id
                  JOIN aerolineas al ON v.aerolinea_id = al.id
                  WHERE v.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return [
                'id' => $row['id'],
                'airline' => $row['aerolinea_nombre'],
                'flight_number' => $row['numero_vuelo'],
                'departure_time' => date('H:i', strtotime($row['hora_salida'])),
                'departure_airport' => $row['origen_iata'],
                'arrival_time' => date('H:i', strtotime($row['hora_llegada'])),
                'arrival_next_day' => (bool)$row['llegada_dia_siguiente'],
                'arrival_airport' => $row['destino_ciudad'],
                'duration' => $row['duracion'],
                'stops' => $row['escalas'],
                'price' => $row['precio'],
                'best_price' => (bool)$row['es_mejor_precio']
            ];
        }
        return null;
    }

    public function obtenerFiltrosDestinos() {
        $query = "SELECT DISTINCT ciudad FROM aeropuertos ORDER BY ciudad ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultados = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $row['ciudad'];
        }
        return $resultados;
    }

    public function obtenerFiltrosAerolineas() {
        $query = "SELECT nombre FROM aerolineas ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultados = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $row['nombre'];
        }
        return $resultados;
    }

    public function obtenerIataPorCiudad($ciudad) {
        $query = "SELECT codigo_iata FROM aeropuertos WHERE ciudad = :ciudad LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row['codigo_iata'];
        }
        return 'LIM'; // Default fallback
    }

    public function garantizarAeropuerto($iata, $nombre, $ciudad = '', $pais = '') {
        // Busca si existe, si no lo inserta
        $query = "SELECT id FROM aeropuertos WHERE codigo_iata = :iata LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':iata', $iata);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row['id'];
        }
        // Insert
        $query = "INSERT INTO aeropuertos (codigo_iata, nombre, ciudad, pais) VALUES (:iata, :nombre, :ciudad, :pais)";
        $stmt = $this->conn->prepare($query);
        if(empty($ciudad)) $ciudad = $nombre;
        if(empty($pais)) $pais = 'Desconocido';
        $stmt->bindParam(':iata', $iata);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':pais', $pais);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function garantizarAerolinea($iata, $nombre) {
        $query = "SELECT id FROM aerolineas WHERE codigo_iata = :iata LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':iata', $iata);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row['id'];
        }
        $query = "INSERT INTO aerolineas (codigo_iata, nombre) VALUES (:iata, :nombre)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':iata', $iata);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function guardarVueloDuffel($offer) {
        // Verifica si ya lo guardamos por su duffel_offer_id
        $query = "SELECT id FROM vuelos WHERE duffel_offer_id = :offer_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offer_id', $offer['offer_id']);
        $stmt->execute();
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row['id'];
        }

        $id_origen = $this->garantizarAeropuerto($offer['departure_airport'], $offer['departure_airport']);
        $id_destino = $this->garantizarAeropuerto($offer['arrival_airport'], $offer['arrival_airport']);
        $id_aerolinea = $this->garantizarAerolinea(substr($offer['flight_number'], 0, 2), $offer['airline']);

        $query = "INSERT INTO vuelos (aerolinea_id, numero_vuelo, origen_aeropuerto_id, destino_aeropuerto_id, hora_salida, hora_llegada, llegada_dia_siguiente, duracion, escalas, precio, duffel_offer_id) 
                  VALUES (:aero_id, :num_vuelo, :orig_id, :dest_id, :h_salida, :h_llegada, :dia_sig, :duracion, :escalas, :precio, :offer_id)";
        $stmt = $this->conn->prepare($query);
        
        $h_salida = date('H:i:s', strtotime($offer['departure_time']));
        $h_llegada = date('H:i:s', strtotime($offer['arrival_time']));
        $dia_sig = $offer['arrival_next_day'] ? 1 : 0;
        
        $stmt->bindParam(':aero_id', $id_aerolinea);
        $stmt->bindParam(':num_vuelo', $offer['flight_number']);
        $stmt->bindParam(':orig_id', $id_origen);
        $stmt->bindParam(':dest_id', $id_destino);
        $stmt->bindParam(':h_salida', $h_salida);
        $stmt->bindParam(':h_llegada', $h_llegada);
        $stmt->bindParam(':dia_sig', $dia_sig, PDO::PARAM_INT);
        $stmt->bindParam(':duracion', $offer['duration']);
        $stmt->bindParam(':escalas', $offer['stops']);
        $stmt->bindParam(':precio', $offer['price']);
        $stmt->bindParam(':offer_id', $offer['offer_id']);
        
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
}
?>
