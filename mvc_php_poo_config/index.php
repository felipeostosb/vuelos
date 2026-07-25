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
        
        // Si usaron la Búsqueda Inteligente (IA)
        if (!empty($query_ia)) {
            $geminiAPI = new GeminiAPI();
            // Interpretar y guardar la consulta
            $destino_extraido = $geminiAPI->interpretarBusqueda($query_ia, $_SESSION['user_id'] ?? null);
            if (!empty($destino_extraido)) {
                $destino = $destino_extraido;
            }
        }
        
        // Buscar vuelos tradicionales en nuestra base de datos
        $vuelos_encontrados = $vueloModel->buscarTradicional($destino);
        
        // [FUTURO]: Aquí se integrará la llamada a DuffelAPI para unir los vuelos en tiempo real 
        // a la variable $vuelos_encontrados
        
        include 'views/layout/header.php';
        include 'views/flights/reserva.php';
        include 'views/layout/footer.php';
        break;

    case 'reserva':
        include 'views/layout/header.php';
        include 'views/flights/reserva.php';
        include 'views/layout/footer.php';
        break;

    case 'checkout':
        // Protección de ruta: Solo usuarios logueados pueden comprar
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=home&login=required');
            exit();
        }

        $id_vuelo_seleccionado = $_POST['flight_id'] ?? 1;
        $pasajeros = $_POST['pasajeros'] ?? 1;
        $tipo_viaje = $_POST['tipo_viaje'] ?? 'solo_ida';
        
        $vueloModel = new Vuelo();
        $vuelo = $vueloModel->obtenerPorId($id_vuelo_seleccionado);
        
        include 'views/layout/header.php';
        include 'views/flights/checkout.php';
        include 'views/layout/footer.php';
        break;

    case 'confirmarReserva':
        $id_vuelo = $_POST['flight_id'] ?? 1;
        $nombre_pasajero = $_POST['nombre'] ?? 'Pasajero Anónimo';
        
        // En una app real los calcularíamos, aquí lo simulamos
        $pasajeros_count = $_POST['pasajeros'] ?? 1;
        
        $vueloModel = new Vuelo();
        $vuelo = $vueloModel->obtenerPorId($id_vuelo);
        $precio_total = $vuelo ? ($vuelo['price'] * $pasajeros_count) : 0;
        
        // Creamos un código de reserva aleatorio (PNR)
        $pnr = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);
        
        // Si el usuario era invitado, le damos un ID temporal para la base de datos local
        // Nota: En producción deberías crear un usuario "guest" o forzar registro
        $usuario_id = $_SESSION['user_id'] ?? 2; // Asignamos al usuario ID 2 (Juan Pérez) como fallback
        
        $reservaModel = new Reserva();
        $reserva_id = $reservaModel->crearReserva($pnr, $usuario_id, $id_vuelo, $pasajeros_count, $precio_total);
        
        if ($reserva_id) {
            // Separar nombre y apellido simple
            $partes_nombre = explode(' ', $nombre_pasajero, 2);
            $nombre = $partes_nombre[0];
            $apellido = $partes_nombre[1] ?? '';
            
            $reservaModel->agregarPasajero($reserva_id, $nombre, $apellido);
        }
        
        // Lo mandamos a la pantalla de confirmación exitosa
        header('Location: index.php?action=confirmacion&pnr=' . $pnr);
        exit();
        break;

    case 'confirmacion':
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