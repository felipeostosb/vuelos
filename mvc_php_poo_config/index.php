<?php
session_start();

/**
 * 👨‍🍳 ============================================================================================== 👨‍🍳
 * EL MOSTRADOR PRINCIPAL: index.php
 * 👨‍🍳 ============================================================================================== 👨‍🍳
 * 
 * 💖 BIENVENIDO A LA ENTRADA DE TU RESTAURANTE:
 * Imagina que este archivo es el mostrador de entrada o el "Host" de tu restaurante.
 * ABSOLUTAMENTE TODOS los clientes (las páginas que alguien quiere ver) pasan por aquí primero.
 * 
 * 1. Lo primero que hace el host es ir a la cocina y traer la "Pizarra del Menú" (datos.php).
 * 2. Luego, le pregunta al cliente: "¿A qué mesa quieres ir?" (Leyendo $_GET['action']).
 * 3. Dependiendo de lo que diga el cliente, el host (usando un condicional SWITCH)
 *    lo acompaña a la zona correcta, mostrándole el archivo visual correspondiente.
 * 
 * Todo es directo y en orden. Sin magia, sin "clases", sin complicaciones.
 * ==============================================================================================
 */

// 📋 PASO 1: TRAER LA PIZARRA DEL MENÚ
// 'require_once' le dice a PHP: "Ve y trae todo lo que está escrito en datos.php, lo necesitamos".
require_once 'datos.php';

// 🗣️ PASO 2: ESCUCHAR LO QUE QUIERE EL CLIENTE
// Si el cliente en la URL pone "?action=destinos", $peticion guardará la palabra "destinos".
// Si el cliente entra sin pedir nada, por defecto lo mandamos a "home" (la puerta de inicio).
if (isset($_GET['action'])) {
    $peticion = $_GET['action'];
} else {
    $peticion = 'home'; // Valor por defecto
}

// 🚦 PASO 3: EL HOST DIRIGE AL CLIENTE (EL ENRUTADOR)
// El 'switch' es como un semáforo de múltiples vías o un guardia de seguridad.
// Compara la variable $peticion con cada 'case' y cuando encuentra el que coincide, ejecuta ese bloque.
switch ($peticion) {
    // ---------------------------------------------------------
    // ZONA PÚBLICA (El Salón Principal)
    // ---------------------------------------------------------
    case 'home':
        // Si pidieron 'home', mostramos la cabecera, la página de inicio y el pie de página.
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

    // ---------------------------------------------------------
    // ZONA DE RESERVAS (Tomando la Orden)
    // ---------------------------------------------------------
    case 'buscar':
        // 🧑‍🍳 EL MESERO TOMA TU ORDEN (Buscar Vuelo)
        // Guardamos los datos que nos diste en el buscador
        $origen = $_POST['origen'] ?? '';
        $destino = $_POST['destino'] ?? '';
        
        $vuelos_encontrados = [];
        
        // El mesero lee toda la pizarra (todos los vuelos)
        for ($i = 0; $i < count($menu_vuelos); $i++) {
            $vuelo = $menu_vuelos[$i];
            // Si el destino del vuelo es igual al que pidió el cliente...
            // (Para simplificar, por ahora solo filtramos por destino)
            if ($vuelo['arrival_airport'] == $destino || $destino == '') {
                $vuelos_encontrados[] = $vuelo; // Lo anota en la libreta
            }
        }
        
        // Le pasamos la libreta con los resultados a la página de reservas
        include 'views/layout/header.php';
        include 'views/flights/reserva.php';
        include 'views/layout/footer.php';
        break;

    case 'reserva':
        // Aquí mostraremos los resultados de búsqueda de vuelos
        include 'views/layout/header.php';
        include 'views/flights/reserva.php';
        include 'views/layout/footer.php';
        break;

    case 'checkout':
        // 💳 EL CAJERO PREPARA LA FACTURA (Checkout)
        // El cliente seleccionó un plato (vuelo). Buscamos cuál es.
        $id_vuelo_seleccionado = $_POST['flight_id'] ?? 1;
        $pasajeros = $_POST['pasajeros'] ?? 1;
        $tipo_viaje = $_POST['tipo_viaje'] ?? 'solo_ida';
        
        $vuelo = null;
        // Buscamos el vuelo en nuestro menú
        for ($i = 0; $i < count($menu_vuelos); $i++) {
            $v = $menu_vuelos[$i];
            if ($v['id'] == $id_vuelo_seleccionado) {
                $vuelo = $v;
                break;
            }
        }
        
        include 'views/layout/header.php';
        include 'views/flights/checkout.php';
        include 'views/layout/footer.php';
        break;

    case 'confirmarReserva':
        // 🎉 EL CLIENTE PAGA Y GUARDAMOS LA RESERVA
        $id_vuelo = $_POST['flight_id'] ?? 1;
        $nombre_pasajero = $_POST['nombre'] ?? 'Pasajero Anónimo';
        
        // Buscamos el vuelo para los detalles
        $vuelo_reservado = null;
        for ($i = 0; $i < count($menu_vuelos); $i++) {
            $v = $menu_vuelos[$i];
            if ($v['id'] == $id_vuelo) {
                $vuelo_reservado = $v;
                break;
            }
        }
        
        // Creamos un código de reserva aleatorio (PNR)
        $pnr = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);
        
        // Anotamos la reserva en nuestro libro (Sesión)
        // Guardamos los datos exactamente como los espera el panel.
        $_SESSION['reservas'][] = [
            'pnr' => $pnr,
            'vuelo' => $vuelo_reservado,
            'pasajero_nombre' => $nombre_pasajero,
            'estado' => 'Confirmada',
            'fecha_reserva' => date('Y-m-d H:i:s')
        ];
        
        // Si el usuario era invitado, le damos una cuenta temporal para que pueda ver su panel
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 999;
            $_SESSION['user_name'] = $nombre_pasajero;
        }
        
        // Lo mandamos a su panel de viajes con un mensaje de éxito
        header('Location: index.php?action=panel&success=1&pnr=' . $pnr);
        exit();
        break;

    // ---------------------------------------------------------
    // ZONA PRIVADA (Login y Área de Clientes VIP)
    // ---------------------------------------------------------
    case 'login':
        // 🏨 EL RECEPCIONISTA REVISA LA IDENTIFICACIÓN (Login)
        // Tomamos lo que el cliente escribió en el formulario de la puerta.
        $email_ingresado = $_POST['email'] ?? '';
        $password_ingresada = $_POST['password'] ?? '';
        
        $cliente_valido = false; // Asumimos que es un impostor hasta demostrar lo contrario
        
        // El recepcionista lee nuestra pizarra (arreglo $clientes_vip) línea por línea
        for ($i = 0; $i < count($clientes_vip); $i++) {
            $cliente = $clientes_vip[$i];
            if ($cliente['email'] == $email_ingresado && $cliente['password'] == $password_ingresada) {
                // ¡Coincide! Le damos su pulsera VIP (Variables de Sesión)
                $_SESSION['user_id'] = 1; // Un ID falso por ahora
                $_SESSION['user_name'] = $cliente['nombre'];
                $_SESSION['user_email'] = $cliente['email'];
                $cliente_valido = true;
                break; // Ya lo encontramos, dejamos de buscar en la pizarra
            }
        }
        
        if ($cliente_valido) {
            header('Location: index.php?action=home&login=success');
        } else {
            header('Location: index.php?action=home&login=error');
        }
        exit();
        break;

    case 'logout':
        // Aquí el cliente se va del restaurante. Destruimos su "sesión" (su ticket de visita).
        session_destroy();
        header('Location: index.php?action=home'); // Lo regresamos a la calle (inicio)
        exit();
        break;

    case 'panel':
        // Solo los que tienen sesión iniciada (ticket VIP) pueden ver esto
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=home');
            exit();
        }
        // El anfitrión saca la lista de reservas del cliente
        $misReservas = $_SESSION['reservas'] ?? [];
        
        include 'views/layout/header.php';
        include 'views/user/panel.php';
        include 'views/layout/footer.php';
        break;

    case 'procesarCheckin':
        // 🎫 EL CLIENTE PIDE SU PASE DE ABORDAR
        $pnr_buscado = $_POST['pnr'] ?? '';
        
        // Buscamos su reserva en la sesión y le cambiamos el estado
        if (isset($_SESSION['reservas'])) {
            for ($i = 0; $i < count($_SESSION['reservas']); $i++) {
                $reserva = $_SESSION['reservas'][$i];
                if ($reserva['pnr'] == $pnr_buscado) {
                    $_SESSION['reservas'][$i]['estado'] = 'Checked-in';
                    break;
                }
            }
        }
        
        // Lo mandamos de vuelta a su panel
        header('Location: index.php?action=panel');
        exit();
        break;

    // ---------------------------------------------------------
    // RUTA POR DEFECTO (Si piden algo que no existe)
    // ---------------------------------------------------------
    default:
        // Si el cliente pide ir al baño de oro (algo que no existe), lo mandamos al inicio.
        header('Location: index.php?action=home');
        exit();
        break;
}
?>