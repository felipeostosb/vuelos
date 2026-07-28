<?php
/**
 * ==============================================================================================
 * ENRUTADOR PRINCIPAL DEL PROYECTO NOVAIRLINES (VERSIÓN PROCEDURAL ELEMENTAL)
 * ==============================================================================================
 * Este archivo actúa como el controlador central. Recibe las peticiones del usuario
 * mediante el parámetro 'action' en la URL o formulario, ejecuta la lógica necesaria
 * invocando funciones procedurales sencillas y carga las vistas correspondientes.
 * ==============================================================================================
 */

// Iniciamos o reanudamos la sesión PHP para manejar usuarios e intenciones de búsqueda
session_start();

// 1. Cargamos las librerías de funciones procedurales del proyecto
require_once 'models/Usuario.php';
require_once 'models/Vuelo.php';
require_once 'models/Reserva.php';
require_once 'models/GeminiAPI.php';
require_once 'models/DuffelAPI.php';
require_once 'models/Admin.php';

// 2. Leemos la acción solicitada por el usuario (GET o POST)
if (isset($_GET['action'])) {
    $accion = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $accion = $_POST['action'];
} else {
    $accion = 'home'; // Acción por defecto si no se especifica ninguna
}

// 3. Evaluamos la acción mediante una estructura switch limpia y directa
switch ($accion) {

    // ------------------------------------------------------------------------------------------
    // SECCIÓN 1: VISTAS PÚBLICAS Y NAVEGACIÓN
    // ------------------------------------------------------------------------------------------
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

    // ------------------------------------------------------------------------------------------
    // SECCIÓN 2: AUTENTICACIÓN Y REGISTRO DE USUARIOS
    // ------------------------------------------------------------------------------------------
    case 'procesarRegistro':
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Llamada a la función procedural de registro
        $registro_exitoso = registrar_usuario($nombre, $email, $password);

        if ($registro_exitoso) {
            // Iniciar sesión automáticamente al registrarse exitosamente
            $datos_usuario = login_usuario($email, $password);
            $_SESSION['user_id'] = $datos_usuario['id'] ?? null;
            $_SESSION['user_name'] = $nombre;
            $_SESSION['user_email'] = $email;
            
            header('Location: index.php?action=home&registro=success');
        } else {
            header('Location: index.php?action=registro&registro=error');
        }
        exit();
        break;

    case 'login':
        $email_ingresado = $_POST['email'] ?? '';
        $password_ingresada = $_POST['password'] ?? '';
        
        // Llamada a la función procedural de login
        $cliente = login_usuario($email_ingresado, $password_ingresada);
        
        if ($cliente) {
            // Guardamos los datos principales en la sesión
            $_SESSION['user_id'] = $cliente['id'];
            $_SESSION['user_name'] = $cliente['nombre'];
            $_SESSION['user_email'] = $cliente['email'];
            $_SESSION['usuario'] = $cliente;

            if (($cliente['rol'] ?? '') === 'admin') {
                header('Location: index.php?action=admin');
            } else {
                header('Location: index.php?action=home&login=success');
            }
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

    // ------------------------------------------------------------------------------------------
    // SECCIÓN 3: BÚSQUEDA Y SELECCIÓN DE VUELOS (CON IA Y FILTROS)
    // ------------------------------------------------------------------------------------------
    case 'buscar':
        $origen_ciudad = $_GET['origen'] ?? '';
        $destino_ciudad = $_GET['destino'] ?? '';
        $query_ia = $_GET['query'] ?? '';
        $tipo_busqueda = $_GET['tipo_busqueda'] ?? 'normal';
        
        // Si el usuario usó el buscador inteligente con Inteligencia Artificial
        if ($tipo_busqueda === 'ia' || !empty($query_ia)) {
            $usuario_actual_id = $_SESSION['user_id'] ?? null;
            
            // Función procedural que procesa la frase con Gemini AI
            $datos_extraidos = interpretar_busqueda_ia($query_ia, $usuario_actual_id);
            
            $origen_ciudad = $datos_extraidos['origen'] ?? 'Lima';
            $destino_ciudad = $datos_extraidos['destino'] ?? '';
            
            $origen = obtener_iata_por_ciudad($origen_ciudad);
            $destino = obtener_iata_por_ciudad($destino_ciudad);
            
            $fecha_salida = $datos_extraidos['fecha_salida'] ?? date('Y-m-d', strtotime('+1 day'));
            $fecha_vuelta = $datos_extraidos['fecha_vuelta'] ?? '';
            $tipo_viaje = $datos_extraidos['tipo_viaje'] ?? 'solo_ida';
            $pasajeros = $datos_extraidos['pasajeros'] ?? 1;
            
            $usar_cache = false; // Búsquedas IA siempre fuerzan recarga

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
            // Búsqueda Clásica (Formulario Manual)
            $origen = obtener_iata_por_ciudad($origen_ciudad);
            $destino = obtener_iata_por_ciudad($destino_ciudad);
            
            $rango_fechas = $_GET['rango_fechas'] ?? date('Y-m-d');
            $partes_fecha = explode(' to ', $rango_fechas);
            if (count($partes_fecha) == 1) {
                $partes_fecha = explode(' a ', $rango_fechas);
            }
            $fecha_salida = $partes_fecha[0];
            $fecha_vuelta = $partes_fecha[1] ?? '';
            
            $pasajeros = $_GET['pasajeros'] ?? 1;
            $tipo_viaje = $_GET['tipo_viaje'] ?? 'solo_ida';
            
            // Comprobamos si podemos reutilizar ofertas en caché de sesión
            $usar_cache = false;
            if (isset($_SESSION['datos_busqueda']) && isset($_SESSION['ofertas_actuales'])) {
                $sesion_actual = $_SESSION['datos_busqueda'];
                if ($sesion_actual['origen'] === $origen && 
                    $sesion_actual['destino'] === $destino && 
                    $sesion_actual['fecha_salida'] === $fecha_salida && 
                    $sesion_actual['fecha_vuelta'] === $fecha_vuelta && 
                    $sesion_actual['pasajeros'] == $pasajeros) { 
                    $usar_cache = true;
                }
            }

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

        // Obtener vuelos reales mediante la función de Duffel API
        if ($usar_cache) {
            $ofertas_completas = $_SESSION['ofertas_actuales'];
        } else {
            $ofertas_completas = buscar_vuelos_duffel($origen, $destino, $fecha_salida, $fecha_vuelta, $pasajeros);
            $_SESSION['ofertas_actuales'] = $ofertas_completas;
        }

        // Aplicar filtros de precio, escalas y aerolíneas
        $precio_maximo = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 999999;
        $filtro_escalas = isset($_GET['stops']) ? (array)$_GET['stops'] : null;
        $filtro_aerolineas = isset($_GET['airlines']) ? (array)$_GET['airlines'] : null;

        $firmas_idas_unicas = [];
        $vuelos_encontrados = [];
        
        foreach ($ofertas_completas as $vuelo_item) {
            if ((float)$vuelo_item['price'] > $precio_maximo) {
                continue;
            }
            if ($filtro_escalas !== null && !in_array((string)$vuelo_item['outbound']['stops'], $filtro_escalas)) {
                continue;
            }
            if ($filtro_aerolineas !== null && !in_array($vuelo_item['airline'], $filtro_aerolineas)) {
                continue;
            }

            $firma_vuelo = $vuelo_item['outbound']['flight_number'] . '_' . $vuelo_item['outbound']['departure_time'];
            if (!in_array($firma_vuelo, $firmas_idas_unicas)) {
                $vuelo_item['outbound']['signature'] = $firma_vuelo;
                $firmas_idas_unicas[] = $firma_vuelo;
                $vuelos_encontrados[] = $vuelo_item;
            }
        }
        
        // Ordenar vuelos del menor al mayor precio
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

        $precio_maximo = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 999999;
        $filtro_escalas = isset($_GET['stops']) ? (array)$_GET['stops'] : null;
        $filtro_aerolineas = isset($_GET['airlines']) ? (array)$_GET['airlines'] : null;
        
        foreach ($ofertas as $oferta) {
            $sig_ida = $oferta['outbound']['flight_number'] . '_' . $oferta['outbound']['departure_time'];
            
            if ($sig_ida === $outbound_signature) {
                if (!$vuelo_ida_seleccionado) {
                    $vuelo_ida_seleccionado = $oferta['outbound'];
                }
                
                if (isset($oferta['inbound'])) {
                    if ((float)$oferta['price'] > $precio_maximo) {
                        continue;
                    }
                    if ($filtro_escalas !== null && !in_array((string)$oferta['inbound']['stops'], $filtro_escalas)) {
                        continue;
                    }
                    if ($filtro_aerolineas !== null && !in_array($oferta['airline'], $filtro_aerolineas)) {
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

    // ------------------------------------------------------------------------------------------
    // SECCIÓN 4: PROCESO DE CHECKOUT Y CONFIRMACIÓN DE RESERVAS
    // ------------------------------------------------------------------------------------------
    case 'checkout':
        $offer_id = $_POST['flight_id'] ?? '';
        $pasajeros = max(1, (int)($_POST['pasajeros'] ?? 1));
        $tipo_viaje = $_POST['tipo_viaje'] ?? 'solo_ida';
        
        // Obtener datos del vuelo en Duffel o en la BD local
        $vuelo = obtener_oferta_duffel($offer_id);
        
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
            // Fallback a base de datos local
            $vuelo = obtener_vuelo_por_id($offer_id);
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
        
        $offer_data = obtener_oferta_duffel($offer_id);
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
            
            $orden = crear_orden_duffel($offer_id, $passengers_payload, $precio_total, $offer_data['currency']);
            
            if ($orden && isset($orden['id'])) {
                $duffel_order_id = $orden['id'];
            }

            $id_vuelo = guardar_vuelo_duffel($offer_data);
        } else {
            // Vuelo proveniente de BD local
            $vuelo_db = obtener_vuelo_por_id($offer_id);
            if ($vuelo_db) {
                $id_vuelo = $vuelo_db['id'];
                $precio_unitario = (float)$vuelo_db['price'];
                $precio_total = $precio_unitario * $pasajeros_count;
            } else {
                $id_vuelo = null;
                $precio_total = 0;
            }
        }
        
        // Generar código PNR aleatorio único de 6 caracteres
        $pnr = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);
        $usuario_id = $_SESSION['user_id'] ?? null;
        
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
        
        // Crear el registro de la reserva mediante la función procedural
        $reserva_id = crear_reserva($pnr, $usuario_id, $id_vuelo, $pasajeros_count, $precio_total, $duffel_order_id, $tipo_viaje, $vuelo_vuelta_data);
        
        if ($reserva_id) {
            for ($i = 0; $i < $pasajeros_count; $i++) {
                $p_nombre = trim($_POST['pasajero_nombre_' . $i] ?? '');
                $p_apellido = trim($_POST['pasajero_apellido_' . $i] ?? '');
                $p_tipo_doc = $_POST['pasajero_tipo_doc_' . $i] ?? 'DNI';
                $p_doc = trim($_POST['pasajero_doc_' . $i] ?? '');
                $p_email = ($i === 0) ? $email_pasajero : null;

                // Fallback por si vinieron campos combinados o vacíos
                if (empty($p_nombre) && empty($p_apellido)) {
                    $fallback = trim($_POST['nombre'] ?? '');
                    if (!empty($fallback)) {
                        $partes = explode(' ', $fallback, 2);
                        $p_nombre = $partes[0];
                        $p_apellido = $partes[1] ?? '';
                    } else {
                        $p_nombre = "Pasajero";
                        $p_apellido = (string)($i + 1);
                    }
                }

                agregar_pasajero($reserva_id, $p_nombre, $p_apellido, $p_email, $p_tipo_doc, $p_doc);
            }
        }
        
        header('Location: index.php?action=confirmacion&pnr=' . $pnr);
        exit();
        break;

    case 'confirmacion':
        $pnr = $_GET['pnr'] ?? '';
        $reserva = obtener_reserva_por_pnr($pnr);
        
        include 'views/layout/header.php';
        include 'views/flights/confirmacion.php';
        include 'views/layout/footer.php';
        break;

    case 'generarBoleto':
        $pnr_descarga = $_GET['pnr'] ?? '';
        if (!empty($pnr_descarga)) {
            generar_boleto_pdf($pnr_descarga);
        } else {
            header('Location: index.php?action=home');
        }
        exit();
        break;

    // ------------------------------------------------------------------------------------------
    // SECCIÓN 5: ZONA PRIVADA Y CHECK-IN
    // ------------------------------------------------------------------------------------------
    case 'panel':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=home');
            exit();
        }
        
        $misReservas = obtener_reservas_usuario($_SESSION['user_id']);
        
        include 'views/layout/header.php';
        include 'views/user/panel.php';
        include 'views/layout/footer.php';
        break;

    case 'procesarCheckin':
        $pnr_buscado = $_POST['pnr'] ?? '';
        $usuario_id = $_SESSION['user_id'] ?? 999; 
        
        $resultado = realizar_checkin($pnr_buscado, $usuario_id);
        
        if (isset($_SESSION['user_id'])) {
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

    // ------------------------------------------------------------------------------------------
    // SECCIÓN 6: PANEL DE ADMINISTRACIÓN Y CONTROL DE GESTIÓN (SOLO ROL ADMIN)
    // ------------------------------------------------------------------------------------------
    case 'admin':
        // Verificación de seguridad: Solo usuarios autenticados con rol 'admin'
        $rol_actual = $_SESSION['usuario']['rol'] ?? '';
        if ($rol_actual !== 'admin') {
            header('Location: index.php?action=home&error=Acceso+denegado+Solo+administradores');
            exit();
        }

        $stats = obtener_estadisticas_admin();
        $reservas = obtener_todas_reservas_admin();
        $usuarios = obtener_todos_usuarios_admin();
        $consultas_ia = obtener_consultas_ia_admin();

        include 'views/admin/panel.php';
        break;

    case 'admin_cambiar_estado':
        $rol_actual = $_SESSION['usuario']['rol'] ?? '';
        if ($rol_actual !== 'admin') {
            header('Location: index.php?action=home');
            exit();
        }

        $pnr = $_POST['pnr'] ?? '';
        $estado = $_POST['estado'] ?? '';
        
        if (!empty($pnr) && !empty($estado)) {
            actualizar_estado_reserva_admin($pnr, $estado);
            header('Location: index.php?action=admin&mensaje=Estado+de+reserva+' . urlencode($pnr) . '+actualizado+a+' . urlencode($estado));
        } else {
            header('Location: index.php?action=admin&error=Datos+incompletos');
        }
        exit();
        break;

    case 'admin_cambiar_rol':
        $rol_actual = $_SESSION['usuario']['rol'] ?? '';
        if ($rol_actual !== 'admin') {
            header('Location: index.php?action=home');
            exit();
        }

        $usuario_id = $_POST['usuario_id'] ?? '';
        $rol = $_POST['rol'] ?? '';

        if (!empty($usuario_id) && !empty($rol)) {
            actualizar_rol_usuario_admin($usuario_id, $rol);
            header('Location: index.php?action=admin&mensaje=Rol+de+usuario+actualizado+exitosamente');
        } else {
            header('Location: index.php?action=admin&error=Error+al+cambiar+rol');
        }
        exit();
        break;

    case 'admin_eliminar_usuario':
        $rol_actual = $_SESSION['usuario']['rol'] ?? '';
        $admin_id = $_SESSION['usuario']['id'] ?? 0;
        if ($rol_actual !== 'admin') {
            header('Location: index.php?action=home');
            exit();
        }

        $usuario_id_del = $_GET['id'] ?? '';
        if (!empty($usuario_id_del)) {
            $exito = eliminar_usuario_seguro_admin($usuario_id_del, $admin_id);
            if ($exito) {
                header('Location: index.php?action=admin&mensaje=Usuario+eliminado+de+forma+segura');
            } else {
                header('Location: index.php?action=admin&error=No+se+pudo+eliminar+el+usuario+o+intento+de+autoeliminacion');
            }
        } else {
            header('Location: index.php?action=admin&error=ID+de+usuario+invalido');
        }
        exit();
        break;

    case 'admin_crear_usuario':
        $rol_actual = $_SESSION['usuario']['rol'] ?? '';
        if ($rol_actual !== 'admin') {
            header('Location: index.php?action=home');
            exit();
        }

        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $rol = $_POST['rol'] ?? 'cliente';

        if (!empty($nombre) && !empty($email) && !empty($password)) {
            $creado = crear_usuario_admin($nombre, $email, $password, $rol);
            if ($creado) {
                header('Location: index.php?action=admin&mensaje=Nuevo+usuario+creado+exitosamente');
            } else {
                header('Location: index.php?action=admin&error=El+correo+ya+se+encuentra+registrado');
            }
        } else {
            header('Location: index.php?action=admin&error=Complete+todos+los+campos');
        }
        exit();
        break;

    case 'admin_guardar_modo_ofertas':
        $rol_actual = $_SESSION['usuario']['rol'] ?? '';
        if ($rol_actual !== 'admin') {
            header('Location: index.php?action=home');
            exit();
        }

        $modo = $_POST['modo_ofertas'] ?? 'peru_destacadas';
        if (actualizar_modo_ofertas($modo)) {
            $nombre_modo = ($modo === 'peru_destacadas') ? 'Ofertas Destacadas de Perú (Tarapoto, Cusco, Arequipa)' : 'Ofertas en Tiempo Real de Duffel API';
            header('Location: index.php?action=admin&mensaje=Modo+de+ofertas+actualizado+exitosamente+a:+' . urlencode($nombre_modo));
        } else {
            header('Location: index.php?action=admin&error=Error+al+actualizar+modo+de+ofertas');
        }
        exit();
        break;

    case 'admin_subir_imagen_oferta':
        $rol_actual = $_SESSION['usuario']['rol'] ?? '';
        if ($rol_actual !== 'admin') {
            header('Location: index.php?action=home');
            exit();
        }

        $ciudad_slug = $_POST['destino_slug'] ?? '';
        $archivo = $_FILES['imagen_destino'] ?? null;

        if (!empty($ciudad_slug) && $archivo) {
            $exito = subir_imagen_destino_admin($ciudad_slug, $archivo);
            if ($exito) {
                header('Location: index.php?action=admin&mensaje=Fotografía+de+' . urlencode($ciudad_slug) . '+actualizada+exitosamente');
            } else {
                header('Location: index.php?action=admin&error=Error+al+subir+la+imagen.+Asegúrese+de+usar+formato+JPG,+PNG+o+WEBP');
            }
        } else {
            header('Location: index.php?action=admin&error=Seleccione+un+destino+y+un+archivo+de+imagen');
        }
        exit();
        break;

    // ------------------------------------------------------------------------------------------
    // ACCIÓN POR DEFECTO SI LA RUTA NO EXISTE
    // ------------------------------------------------------------------------------------------
    default:
        header('Location: index.php?action=home');
        exit();
        break;
}
?>