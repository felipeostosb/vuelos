-- ==============================================================================================
-- BASE DE DATOS MEJORADA: novairlines_db
-- Sistema de Reserva de Vuelos con Duffel API & Gemini 3.5 Flash
-- Motor: MySQL 8.0+ / MariaDB 10.4+
-- ==============================================================================================

CREATE DATABASE IF NOT EXISTS novairlines_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE novairlines_db;

-- Desactivar temporalmente la revisión de llaves foráneas para reinicio limpio
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS pasajeros;
DROP TABLE IF EXISTS consultas_ia;
DROP TABLE IF EXISTS reservas;
DROP TABLE IF EXISTS vuelos;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS aerolineas;
DROP TABLE IF EXISTS aeropuertos;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------------------------
-- 1. TABLA DE AEROPUERTOS
-- ----------------------------------------------------------------------------------------------
CREATE TABLE aeropuertos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    codigo_iata CHAR(3) NOT NULL UNIQUE COMMENT 'Código IATA único de 3 letras (ej: LIM, MAD, CDG)',
    nombre VARCHAR(150) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,
    pais VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------------------------
-- 2. TABLA DE AEROLÍNEAS
-- ----------------------------------------------------------------------------------------------
CREATE TABLE aerolineas (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    codigo_iata VARCHAR(3) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    logo_url VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------------------------
-- 3. TABLA DE USUARIOS
-- ----------------------------------------------------------------------------------------------
CREATE TABLE usuarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hash seguro generado por password_hash() en PHP',
    rol ENUM('cliente', 'admin') DEFAULT 'cliente',
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------------------------
-- 4. TABLA DE VUELOS
-- ----------------------------------------------------------------------------------------------
CREATE TABLE vuelos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    aerolinea_id INT(11) NOT NULL,
    numero_vuelo VARCHAR(20) NOT NULL,
    origen_aeropuerto_id INT(11) NOT NULL,
    destino_aeropuerto_id INT(11) NOT NULL,
    hora_salida TIME NOT NULL,
    hora_llegada TIME NOT NULL,
    llegada_dia_siguiente TINYINT(1) DEFAULT 0,
    duracion VARCHAR(20) NOT NULL,
    escalas INT(11) DEFAULT 0,
    precio DECIMAL(10,2) NOT NULL,
    es_mejor_precio TINYINT(1) DEFAULT 0,
    duffel_offer_id VARCHAR(255) DEFAULT NULL COMMENT 'ID de oferta devuelto por Duffel API (off_xxx)',
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vuelos_aerolinea FOREIGN KEY (aerolinea_id) REFERENCES aerolineas(id) ON DELETE CASCADE,
    CONSTRAINT fk_vuelos_origen FOREIGN KEY (origen_aeropuerto_id) REFERENCES aeropuertos(id) ON DELETE CASCADE,
    CONSTRAINT fk_vuelos_destino FOREIGN KEY (destino_aeropuerto_id) REFERENCES aeropuertos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------------------------
-- 5. TABLA DE CONSULTAS IA (GEMINI 3.5 FLASH)
-- ----------------------------------------------------------------------------------------------
CREATE TABLE consultas_ia (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT(11) DEFAULT NULL COMMENT 'NULL para búsquedas de usuarios no autenticados',
    prompt_original TEXT NOT NULL COMMENT 'Frase ingresada por el usuario',
    parametros_extraidos JSON DEFAULT NULL COMMENT 'JSON generado por Gemini (origen, destino, fecha, pasajeros)',
    respuesta_raw JSON DEFAULT NULL COMMENT 'Respuesta devuelta por la API o resultados',
    fecha_consulta DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_consultas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------------------------
-- 6. TABLA DE RESERVAS
-- ----------------------------------------------------------------------------------------------
CREATE TABLE reservas (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    pnr CHAR(6) NOT NULL UNIQUE COMMENT 'Código PNR local de 6 caracteres',
    usuario_id INT(11) DEFAULT NULL COMMENT 'NULL si el comprador es un usuario invitado',
    vuelo_id INT(11) DEFAULT NULL COMMENT 'NULL si la reserva se gestiona 100% vía Duffel API',
    duffel_order_id VARCHAR(255) DEFAULT NULL COMMENT 'ID de orden en Duffel API (ord_xxx)',
    tipo_viaje ENUM('solo_ida', 'ida_vuelta') DEFAULT 'solo_ida',
    estado ENUM('Pendiente', 'Confirmada', 'Checked-in', 'Cancelada') DEFAULT 'Pendiente',
    precio_total DECIMAL(10,2) NOT NULL,
    pasajeros_count INT(11) NOT NULL DEFAULT 1,
    fecha_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reservas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    CONSTRAINT fk_reservas_vuelo FOREIGN KEY (vuelo_id) REFERENCES vuelos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------------------------
-- 7. TABLA DE PASAJEROS
-- ----------------------------------------------------------------------------------------------
CREATE TABLE pasajeros (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT(11) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    tipo_documento VARCHAR(20) DEFAULT 'DNI',
    numero_documento VARCHAR(50) DEFAULT NULL,
    asiento VARCHAR(10) DEFAULT NULL,
    tipo_pasajero ENUM('adulto', 'nino', 'infante') DEFAULT 'adulto',
    duffel_passenger_id VARCHAR(255) DEFAULT NULL COMMENT 'ID del pasajero asignado por Duffel (pas_xxx)',
    CONSTRAINT fk_pasajeros_reserva FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================================
-- INSERCIÓN DE DATOS INICIALES (MIGRACIÓN Y DATOS SEMILLA)
-- ==============================================================================================

-- 1. Insertar Usuarios VIP (Contraseñas seguras con Hash BCRYPT)
INSERT INTO usuarios (id, nombre, email, password, rol) VALUES
(1, 'Administrador', 'admin@novairlines.com', '$2y$10$e8wFh.z9gY.d49.27P8Bquk3F1aGf59L.YfS6J3eMvA0k6b6S7f2S', 'admin'),
(2, 'Juan Pérez', 'juan@ejemplo.com', '$2y$10$w6x9jY.s8K2Z5V5L7P8Bquk3F1aGf59L.YfS6J3eMvA0k6b6S7f2S', 'cliente');

-- 2. Insertar Aeropuertos
INSERT INTO aeropuertos (id, codigo_iata, nombre, ciudad, pais) VALUES
(1, 'LIM', 'Aeropuerto Internacional Jorge Chávez', 'Lima', 'Perú'),
(2, 'MAD', 'Aeropuerto Adolfo Suárez Madrid-Barajas', 'Madrid', 'España'),
(3, 'CDG', 'Aeropuerto de París-Charles de Gaulle', 'París', 'Francia'),
(4, 'BOG', 'Aeropuerto Internacional El Dorado', 'Bogotá', 'Colombia'),
(5, 'CUZ', 'Aeropuerto Internacional Alejandro Velasco Astete', 'Cusco', 'Perú'),
(6, 'MIA', 'Aeropuerto Internacional de Miami', 'Miami', 'Estados Unidos');

-- 3. Insertar Aerolíneas
INSERT INTO aerolineas (id, codigo_iata, nombre, logo_url) VALUES
(1, 'CM', 'Copa Airlines', 'assets/img/airlines/copa.png'),
(2, 'IB', 'Iberia', 'assets/img/airlines/iberia.png'),
(3, 'LA', 'LATAM Airlines', 'assets/img/airlines/latam.png'),
(4, 'AF', 'Air France', 'assets/img/airlines/airfrance.png'),
(5, 'AV', 'Avianca', 'assets/img/airlines/avianca.png'),
(6, 'H2', 'Sky Airline', 'assets/img/airlines/sky.png'),
(7, 'AA', 'American Airlines', 'assets/img/airlines/american.png');

-- 4. Insertar Vuelos
INSERT INTO vuelos (id, aerolinea_id, numero_vuelo, origen_aeropuerto_id, destino_aeropuerto_id, hora_salida, hora_llegada, llegada_dia_siguiente, duracion, escalas, precio, es_mejor_precio) VALUES
-- MADRID (Destino ID 2)
(1, 1, 'CM 331', 1, 2, '22:00:00', '13:55:00', 1, '15h 55m', 1, 1490.00, 0),
(2, 2, 'IB 6650', 1, 2, '19:40:00', '14:25:00', 1, '11h 45m', 0, 2200.00, 0),
(3, 3, 'LA 2451', 1, 2, '08:15:00', '22:40:00', 0, '14h 25m', 0, 1850.00, 1),
-- PARÍS (Destino ID 3)
(4, 4, 'AF 480', 1, 3, '18:15:00', '13:40:00', 1, '12h 25m', 0, 2400.00, 1),
(5, 2, 'IB 341', 1, 3, '19:40:00', '17:25:00', 1, '14h 45m', 1, 1800.00, 0),
-- BOGOTÁ (Destino ID 4)
(6, 5, 'AV 204', 1, 4, '10:30:00', '14:15:00', 0, '3h 45m', 0, 620.00, 1),
(7, 3, 'LA 2390', 1, 4, '15:00:00', '18:20:00', 0, '3h 20m', 0, 750.00, 0),
-- CUSCO (Destino ID 5)
(8, 3, 'LA 2011', 1, 5, '05:30:00', '06:50:00', 0, '1h 20m', 0, 150.00, 1),
(9, 6, 'H2 5013', 1, 5, '14:00:00', '15:20:00', 0, '1h 20m', 0, 95.00, 1),
-- MIAMI (Destino ID 6)
(10, 7, 'AA 918', 1, 6, '08:00:00', '14:50:00', 0, '5h 50m', 0, 1200.00, 1);

-- 5. Insertar Reserva de Prueba
INSERT INTO reservas (id, pnr, usuario_id, vuelo_id, tipo_viaje, estado, precio_total, pasajeros_count) VALUES
(1, 'ABC123', 2, 4, 'solo_ida', 'Confirmada', 2400.00, 1);

-- 6. Insertar Pasajero de Prueba
INSERT INTO pasajeros (id, reserva_id, nombre, apellido, email, tipo_documento, numero_documento, tipo_pasajero) VALUES
(1, 1, 'Juan', 'Pérez', 'juan@ejemplo.com', 'DNI', '77889900', 'adulto');

-- 7. Insertar Consulta IA de Prueba (Gemini 3.5 Flash)
INSERT INTO consultas_ia (usuario_id, prompt_original, parametros_extraidos) VALUES
(2, 'Deseo viajar a París desde Lima, con mi esposa el 25 de julio', '{"origen": "LIM", "destino": "CDG", "fecha_salida": "2026-07-25", "pasajeros": 2}');
