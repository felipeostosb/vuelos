<?php
session_start();

/**
 * 👨‍🍳 ============================================================================================== 👨‍🍳
 * EL MOSTRADOR PRINCIPAL: index.php (VERSIÓN BASE DE DATOS)
 * 👨‍🍳 ============================================================================================== 👨‍🍳
 * 
 * Ahora nuestro restaurante tiene un sistema de inventario real (Base de Datos) 
 * y usa "Modelos" (POO) para acceder a los datos.
 * ==============================================================================================
 */

// 📋 Cargamos nuestros Modelos
require_once 'models/Usuario.php';
require_once 'models/Vuelo.php';
require_once 'models/Reserva.php';
require_once 'models/GeminiAPI.php';
require_once 'models/DuffelAPI.php';

// 🗣️ ESCUCHAR LO QUE QUIERE EL CLIENTE
if (isset($_GET['action'])) {
    $peticion = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $peticion = $_POST['action'];
} else {
    $peticion = 'home'; // Valor por defecto
}

// 🚦 EL HOST DIRIGE AL CLIENTE (EL ENRUTADOR)
switch ($peticion) {
    // ---------------------------------------------------------
    // ZONA PÚBLICA
    // ---------------------------------------------------------
    case 'home':
        include 'views/layout/header.php';
        include 'views/home/home.php';
        include 'views/layout/footer.php';
        break;

    case 'destinos':
        include 'views/layout/header.php';
        include 'views/flights/destinos.php';
        include 'views/layout/footer.php';
        break;

    case 'ofertas':
        include 'views/layout/header.php';
        include 'views/flights/ofertas.php';
        include 'views/layout/footer.php';
        break;

    case 'ayuda':
        include 'views/layout/header.php';
        include 'views/home/ayuda.php';
        include 'views/layout/footer.php';
        break;

    case 'checkin':
        include 'views/layout/header.php';
        include 'views/user/checkin.php';
        include 'views/layout/footer.php';
        break;
        
    case 'registro':
        include 'views/layout/header.php';
        include 'views/user/registro.php';
        include 'views/layout/footer.php';
        break;

    case 'procesarRegistro':
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $usuarioModel = new Usuario();
        if ($usuarioModel->registrar($nombre, $email, $password)) {
            // Auto login after registration
            $_SESSION['user_id'] = $usuarioModel->login($email, $password)['id'] ?? null;
            $_SESSION['user_name'] = $nombre;
            $_SESSION['user_email'] = $email;
            header('Location: index.php?action=home&registro=success');
        } else {
            header('Location: index.php?action=registro&registro=error');
        }
        exit();
        break;

    // ---------------------------------------------------------
    // ZONA DE RESERVAS
    // ---------------------------------------------------------
    case 'buscar':
        $origen = $_GET['origen'] ?? '';
        $destino = $_GET['destino'] ?? '';
        $query_ia = $_GET['query'] ?? '';
        
        $vueloModel = new Vuelo();
        $duffelAPI = new DuffelAPI();
        
        // Traducimos ciudad a IATA para Duffel
        $origen_iata = $vueloModel->obtenerIataPorCiudad($origen);
        $destino_iata = $vueloModel->obtenerIataPorCiudad($destino);
        
        $rango_fechas = $_GET['rango_fechas'] ?? '';
        $fecha_partes = explode(' to ', $rango_fechas);
        if(count($fecha_partes) == 1) {
            $fecha_partes = explode(' a ', $rango_fechas); // flatpickr es locale
        }
        
        $tipo_busqueda = $_GET['tipo_busqueda'] ?? 'normal';
        
        if ($tipo_busqueda === 'ia' || !empty($query_ia)) {
            $prompt = $query_ia;
            
            $geminiAPI = new GeminiAPI();
            $datos_extraidos = $geminiAPI->interpretarBusqueda($prompt, $_SESSION['user_id'] ?? null);
            
            $origen_ciudad = $datos_extraidos['origen'] ?? 'Lima';
            $destino_ciudad = $datos_extraidos['destino'] ?? '';
            
            $vueloModel = new Vuelo();
            $origen = $vueloModel->obtenerIataPorCiudad($origen_ciudad);
            $destino = $vueloModel->obtenerIataPorCiudad($destino_ciudad);
            
            $fecha_salida = $datos_extraidos['fecha_salida'] ?? date('Y-m-d', strtotime('+1 day'));
            $fecha_vuelta = $datos_extraidos['fecha_vuelta'] ?? '';
            $tipo_viaje = $datos_extraidos['tipo_viaje'] ?? 'solo_ida';
            $pasajeros = $datos_extraidos['pasajeros'] ?? 1;
            
            $use_cache = false; // Las búsquedas IA siempre fuerzan recarga por seguridad y precisión

            // Guardamos en sesión los parámetros interpretados para mostrarlos
            $_SESSION['datos_busqueda'] = [
                'origen' => $origen,
                'destino' => $destino,
                'fecha_salida' => $fecha_salida,
                'fecha_vuelta' => $fecha_vuelta,
                'pasajeros' => $pasajeros,
                'tipo_viaje' => $tipo_viaje,
                'origen_ciudad' => $origen_ciudad,
                'destino_ciudad' => $destino_ciudad
            ];
            
        } else {
            $origen_ciudad = $_GET['origen'] ?? '';
            $destino_ciudad = $_GET['destino'] ?? '';
            
            $vueloModel = new Vuelo();
            $origen = $vueloModel->obtenerIataPorCiudad($origen_ciudad);
            $destino = $vueloModel->obtenerIataPorCiudad($destino_ciudad);
            
            $rango_fechas = $_GET['rango_fechas'] ?? date('Y-m-d');
            $partes_fecha = explode(' to ', $rango_fechas);
            if (count($partes_fecha) == 1) {
                $partes_fecha = explode(' a ', $rango_fechas);
            }
            $fecha_salida = $partes_fecha[0];
            $fecha_vuelta = $partes_fecha[1] ?? '';
            
            $pasajeros = $_GET['pasajeros'] ?? 1;
            $tipo_viaje = $_GET['tipo_viaje'] ?? 'solo_ida';
            
            // Verificamos si podemos usar la caché ANTES de sobreescribir la sesión
            $use_cache = false;
            if (isset($_SESSION['datos_busqueda']) && isset($_SESSION['ofertas_actuales'])) {
                $sess = $_SESSION['datos_busqueda'];
                if ($sess['origen'] === $origen && 
                    $sess['destino'] === $destino && 
                    $sess['fecha_salida'] === $fecha_salida && 
                    $sess['fecha_vuelta'] === $fecha_vuelta && 
                    $sess['pasajeros'] == $pasajeros) { 
                    $use_cache = true;
                }
            }

            // AHORA sí podemos sobreescribir la sesión con los nuevos parámetros
            $_SESSION['datos_busqueda'] = [
                'origen' => $origen,
                'destino' => $destino,
                'fecha_salida' => $fecha_salida,
                'fecha_vuelta' => $fecha_vuelta,
                'pasajeros' => $pasajeros,
                'tipo_viaje' => $tipo_viaje,
                'origen_ciudad' => $origen_ciudad,
                'destino_ciudad' => $destino_ciudad
            ];
        }

        if ($use_cache) {
            $ofertas_completas = $_SESSION['ofertas_actuales'];
        } else {
            // Buscar vuelos SOLO en Duffel
            $ofertas_completas = $duffelAPI->buscarVuelosEnTiempoReal($origen, $destino, $fecha_salida, $fecha_vuelta, $pasajeros);
            $_SESSION['ofertas_actuales'] = $ofertas_completas;
        }

        // Filtros adicionales (precio, escalas, aerolíneas)
        $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 999999;
        $stops_filter = isset($_GET['stops']) ? (array)$_GET['stops'] : null;
        $airlines_filter = isset($_GET['airlines']) ? (array)$_GET['airlines'] : null;

        // Agrupar por el trayecto de ida para mostrar únicas (por aerolínea y horario)
        $idas_unicas = [];
        $vuelos_encontrados = [];
        
        foreach ($ofertas_completas as $vuelo) {
            // Aplicar filtros
            if ((float)$vuelo['price'] > $max_price) {
                continue;
            }
            if ($stops_filter !== null && !in_array((string)$vuelo['outbound']['stops'], $stops_filter)) {
                continue;
            }
            if ($airlines_filter !== null && !in_array($vuelo['airline'], $airlines_filter)) {
                continue;
            }

            $signature = $vuelo['outbound']['flight_number'] . '_' . $vuelo['outbound']['departure_time'];
            if (!in_array($signature, $idas_unicas)) {
                // Guardamos la firma en el array original para usarla en el HTML
                $vuelo['outbound']['signature'] = $signature;
                $idas_unicas[] = $signature;
                $vuelos_encontrados[] = $vuelo;
            }
        }
        
        // Ordenar las idas por precio (de más barato a más caro)
        usort($vuelos_encontrados, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });
        
        include 'views/layout/header.php';
        include 'views/flights/reserva.php';
        include 'views/layout/footer.php';
        break;

    case 'seleccionar_vuelta':
        $outbound_signature = $_GET['outbound_id'] ?? '';
        $ofertas = $_SESSION['ofertas_actuales'] ?? [];
        $datos = $_SESSION['datos_busqueda'] ?? [];
        
        $origen = $datos['origen'] ?? '';
        $destino = $datos['destino'] ?? '';
        $pasajeros = $datos['pasajeros'] ?? 1;
        
        $vuelo_ida_seleccionado = null;
        $vuelos_encontrados = [];
        $vueltas_unicas = [];

        // Filtros adicionales (precio, escalas, aerolíneas)
        $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 999999;
        $stops_filter = isset($_GET['stops']) ? (array)$_GET['stops'] : null;
        $airlines_filter = isset($_GET['airlines']) ? (array)$_GET['airlines'] : null;
        
        foreach ($ofertas as $oferta) {
            $sig_ida = $oferta['outbound']['flight_number'] . '_' . $oferta['outbound']['departure_time'];
            
            if ($sig_ida === $outbound_signature) {
                if (!$vuelo_ida_seleccionado) {
                    $vuelo_ida_seleccionado = $oferta['outbound'];
                }
                
                if (isset($oferta['inbound'])) {
                    // Aplicar filtros
                    if ((float)$oferta['price'] > $max_price) {
                        continue;
                    }
                    if ($stops_filter !== null && !in_array((string)$oferta['inbound']['stops'], $stops_filter)) {
                        continue;
                    }
                    if ($airlines_filter !== null && !in_array($oferta['airline'], $airlines_filter)) {
                        continue;
                    }

                    $sig_vuelta = $oferta['inbound']['flight_number'] . '_' . $oferta['inbound']['departure_time'] . '_' . $oferta['price'];
                    if (!in_array($sig_vuelta, $vueltas_unicas)) {
                        $vueltas_unicas[] = $sig_vuelta;
                        $vuelos_encontrados[] = $oferta;
                    }
                }
            }
        }

        // Ordenar los regresos por precio (de más barato a más caro)
        usort($vuelos_encontrados, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });
        
        include 'views/layout/header.php';
        include 'views/flights/reserva_vuelta.php';
        include 'views/layout/footer.php';
        break;

    case 'reserva':
        include 'views/layout/header.php';
        include 'views/flights/reserva.php';
        include 'views/layout/footer.php';
        break;

    case 'checkout':
        $offer_id = $_POST['flight_id'] ?? '';
        $pasajeros = max(1, (int)($_POST['pasajeros'] ?? 1));
        $tipo_viaje = $_POST['tipo_viaje'] ?? 'solo_ida';
        
        $duffelAPI = new DuffelAPI();
        $vueloModel = new Vuelo();
        
        $vuelo = $duffelAPI->obtenerOfertaPorId($offer_id);
        
        if ($vuelo) {
            $vuelo['unit_price'] = (float)$vuelo['price'] / $pasajeros;
            $vuelo['origen_iata'] = $vuelo['departure_airport'];
            $vuelo['destino_iata'] = $vuelo['arrival_airport'];
            $vuelo['destino_ciudad'] = $vuelo['arrival_airport'];
            $vuelo['aerolinea_nombre'] = $vuelo['airline'];
            $vuelo['numero_vuelo'] = $vuelo['flight_number'];
            $vuelo['hora_salida'] = date('H:i:s', strtotime($vuelo['departure_time']));
            $vuelo['hora_llegada'] = date('H:i:s', strtotime($vuelo['arrival_time']));
        } else {
            // Fallback a vuelo en base de datos local
            $vuelo = $vueloModel->obtenerPorId($offer_id);
            if ($vuelo) {
                $vuelo['currency'] = 'S/.';
                $vuelo['unit_price'] = (float)$vuelo['price'];
                $vuelo['price'] = $vuelo['unit_price'] * $pasajeros;
                $vuelo['origen_iata'] = $vuelo['departure_airport'];
                $vuelo['destino_iata'] = $vuelo['arrival_airport'];
                $vuelo['destino_ciudad'] = $vuelo['arrival_city'];
                $vuelo['aerolinea_nombre'] = $vuelo['airline'];
                $vuelo['numero_vuelo'] = $vuelo['flight_number'];
                $vuelo['hora_salida'] = date('H:i:s', strtotime($vuelo['departure_time']));
                $vuelo['hora_llegada'] = date('H:i:s', strtotime($vuelo['arrival_time']));
            }
        }
        
        include 'views/layout/header.php';
        include 'views/flights/checkout.php';
        include 'views/layout/footer.php';
        break;

    case 'confirmarReserva':
        $offer_id = $_POST['flight_id'] ?? '';
        $nombre_pasajero = $_POST['nombre'] ?? 'Pasajero Anónimo';
        $email_pasajero = $_POST['email'] ?? 'test@example.com';
        $pasajeros_count = max(1, (int)($_POST['pasajeros'] ?? 1));
        $tipo_viaje = $_POST['tipo_viaje'] ?? 'solo_ida';
        
        $duffelAPI = new DuffelAPI();
        $vueloModel = new Vuelo();
        
        $offer_data = $duffelAPI->obtenerOfertaPorId($offer_id);
        $duffel_order_id = null;
        
        if ($offer_data) {
            $precio_total = (float)$offer_data['price'];
            
            $passengers_payload = [];
            $partes_nombre = explode(' ', $nombre_pasajero, 2);
            $nombre_primero = $partes_nombre[0];
            $apellido_primero = $partes_nombre[1] ?? $nombre_primero;
            
            if (!empty($offer_data['passengers'])) {
                foreach ($offer_data['passengers'] as $idx => $p_data) {
                    $p_nombre = $_POST['pasajero_nombre_' . $idx] ?? ($idx === 0 ? $nombre_pasajero : "Pasajero " . ($idx + 1));
                    $p_partes = explode(' ', trim($p_nombre), 2);
                    $passengers_payload[] = [
                        'id' => $p_data['id'],
                        'phone_number' => '+1234567890',
                        'email' => $email_pasajero,
                        'title' => 'mr',
                        'gender' => 'm',
                        'family_name' => $p_partes[1] ?? $apellido_primero,
                        'given_name' => $p_partes[0] ?? $nombre_primero,
                        'born_on' => '1990-01-01'
                    ];
                }
            }
            
            $orden = $duffelAPI->crearOrdenDuffel($offer_id, $passengers_payload, $precio_total, $offer_data['currency']);
            
            if ($orden && isset($orden['id'])) {
                $duffel_order_id = $orden['id'];
            }

            $id_vuelo = $vueloModel->guardarVueloDuffel($offer_data);
        } else {
            // Vuelo local de BD
            $vuelo_db = $vueloModel->obtenerPorId($offer_id);
            if ($vuelo_db) {
                $id_vuelo = $vuelo_db['id'];
                $precio_unitario = (float)$vuelo_db['price'];
                $precio_total = $precio_unitario * $pasajeros_count;
            } else {
                $id_vuelo = null;
                $precio_total = 0;
            }
        }
        
        $pnr = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);
        $usuario_id = $_SESSION['user_id'] ?? null;
        
        // Preparar datos del trayecto de vuelta si es ida y vuelta
        $vuelo_vuelta_data = null;
        if ($tipo_viaje === 'ida_vuelta' && isset($offer_data['inbound']) && !empty($offer_data['inbound'])) {
            $inbound = $offer_data['inbound'];
            $vuelo_vuelta_data = [
                'airline'           => $inbound['airline'] ?? ($offer_data['airline'] ?? 'Aerolínea'),
                'flight_number'     => $inbound['flight_number'] ?? 'N/A',
                'departure_airport' => $inbound['departure_airport'] ?? $offer_data['arrival_airport'] ?? '',
                'departure_city'    => $inbound['departure_city'] ?? $offer_data['arrival_city'] ?? '',
                'arrival_airport'   => $inbound['arrival_airport'] ?? $offer_data['departure_airport'] ?? '',
                'arrival_city'      => $inbound['arrival_city'] ?? $offer_data['departure_city'] ?? '',
                'departure_time'    => $inbound['departure_time'] ?? '',
                'departure_date'    => $inbound['departure_date'] ?? '',
                'arrival_time'      => $inbound['arrival_time'] ?? '',
                'arrival_date'      => $inbound['arrival_date'] ?? '',
                'duration'          => $inbound['duration'] ?? 'N/A',
                'stops'             => $inbound['stops'] ?? 0,
                'price'             => $offer_data['inbound_price'] ?? round((float)$offer_data['price'] * 0.5, 2),
                'currency'          => $offer_data['currency'] ?? 'USD',
            ];
        }
        
        $reservaModel = new Reserva();
        $reserva_id = $reservaModel->crearReserva($pnr, $usuario_id, $id_vuelo, $pasajeros_count, $precio_total, $duffel_order_id, $tipo_viaje, $vuelo_vuelta_data);
        
        if ($reserva_id) {
            for ($i = 0; $i < $pasajeros_count; $i++) {
                $p_nombre_input = $_POST['pasajero_nombre_' . $i] ?? ($i === 0 ? $nombre_pasajero : "Pasajero " . ($i + 1));
                $partes = explode(' ', trim($p_nombre_input), 2);
                $reservaModel->agregarPasajero($reserva_id, $partes[0], $partes[1] ?? '');
            }
        }
        
        header('Location: index.php?action=confirmacion&pnr=' . $pnr);
        exit();
        break;

    case 'confirmacion':
        $pnr = $_GET['pnr'] ?? '';
        $reservaModel = new Reserva();
        $reserva = $reservaModel->obtenerReservaPorPnr($pnr);
        
        include 'views/layout/header.php';
        include 'views/flights/confirmacion.php';
        include 'views/layout/footer.php';
        break;

    // ---------------------------------------------------------
    // ZONA PRIVADA
    // ---------------------------------------------------------
    case 'login':
        $email_ingresado = $_POST['email'] ?? '';
        $password_ingresada = $_POST['password'] ?? '';
        
        $usuarioModel = new Usuario();
        $cliente = $usuarioModel->login($email_ingresado, $password_ingresada);
        
        if ($cliente) {
            // ¡Coincide! Le damos su pulsera VIP
            $_SESSION['user_id'] = $cliente['id'];
            $_SESSION['user_name'] = $cliente['nombre'];
            $_SESSION['user_email'] = $cliente['email'];
            header('Location: index.php?action=home&login=success');
        } else {
            header('Location: index.php?action=home&login=error');
        }
        exit();
        break;

    case 'logout':
        session_destroy();
        header('Location: index.php?action=home');
        exit();
        break;

    case 'panel':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=home');
            exit();
        }
        
        $reservaModel = new Reserva();
        $misReservas = $reservaModel->obtenerReservasUsuario($_SESSION['user_id']);
        
        include 'views/layout/header.php';
        include 'views/user/panel.php';
        include 'views/layout/footer.php';
        break;

    case 'procesarCheckin':
        $pnr_buscado = $_POST['pnr'] ?? '';
        
        $reservaModel = new Reserva();
        // Allow checkin without user_id if they have the PNR (common in airlines)
        // We pass 999 or handle it differently. The model currently accepts 999.
        $usuario_id = $_SESSION['user_id'] ?? 999; 
        
        $resultado = $reservaModel->hacerCheckin($pnr_buscado, $usuario_id);
        
        if (isset($_SESSION['user_id'])) {
            // If they are logged in, usually they check in from panel
            header('Location: index.php?action=panel');
        } else {
            if ($resultado) {
                 header('Location: index.php?action=checkin&success=1&pnr=' . urlencode($pnr_buscado));
            } else {
                 header('Location: index.php?action=checkin&error=1');
            }
        }
        exit();
        break;

    // ---------------------------------------------------------
    // RUTA POR DEFECTO
    // ---------------------------------------------------------
    default:
        header('Location: index.php?action=home');
        exit();
        break;
}
?>