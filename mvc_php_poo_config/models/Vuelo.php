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
}
?>
