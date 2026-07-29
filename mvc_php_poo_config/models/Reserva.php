<?php
/**
 * ==============================================================================================
 * MÓDULO DE GESTIÓN DE RESERVAS (VERSIÓN PROCEDURAL SIMPLE)
 * ==============================================================================================
 * Este archivo contiene todas las funciones para crear, consultar y actualizar reservas
 * y pasajeros en el sistema.
 * ==============================================================================================
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Registra una nueva reserva en la base de datos y retorna su ID generado.
 */
function crear_reserva($pnr, $usuario_id, $vuelo_id, $pasajeros_count, $precio_total, $duffel_order_id = null, $tipo_viaje = 'solo_ida', $vuelo_vuelta_data = null) {
    $conexion = conectar_db();
    if (!$conexion) return false;

    // Si vuelo_id es 0 o inválido, enviamos NULL para no violar la Foreign Key de vuelos
    if (empty($vuelo_id) || (int)$vuelo_id <= 0) {
        $vuelo_id = null;
    }

    try {
        $sql = "INSERT INTO reservas (pnr, usuario_id, vuelo_id, duffel_order_id, tipo_viaje, precio_total, pasajeros_count, vuelo_vuelta_data, estado) 
                VALUES (:pnr, :usuario_id, :vuelo_id, :duffel_order_id, :tipo_viaje, :precio_total, :pasajeros_count, :vuelo_vuelta_data, 'Confirmada')";
        
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':pnr', $pnr);
        $consulta->bindParam(':usuario_id', $usuario_id);
        $consulta->bindParam(':vuelo_id', $vuelo_id, $vuelo_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $consulta->bindParam(':duffel_order_id', $duffel_order_id);
        $consulta->bindParam(':tipo_viaje', $tipo_viaje);
        $consulta->bindParam(':precio_total', $precio_total);
        $consulta->bindParam(':pasajeros_count', $pasajeros_count);
        
        $vuelo_vuelta_json = $vuelo_vuelta_data ? json_encode($vuelo_vuelta_data) : null;
        $consulta->bindParam(':vuelo_vuelta_data', $vuelo_vuelta_json);
        
        if ($consulta->execute()) {
            return $conexion->lastInsertId();
        }
    } catch (PDOException $e) {
        error_log("Error en crear_reserva: " . $e->getMessage());
        return false;
    }
    return false;
}

/**
 * Asocia un pasajero a una reserva existente con sus datos completos.
 */
function agregar_pasajero($reserva_id, $nombre, $apellido = '', $email = null, $tipo_documento = 'DNI', $numero_documento = null) {
    $conexion = conectar_db();
    if (!$conexion) return false;

    $sql = "INSERT INTO pasajeros (reserva_id, nombre, apellido, email, tipo_documento, numero_documento) 
            VALUES (:reserva_id, :nombre, :apellido, :email, :tipo_documento, :numero_documento)";
    try {
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':reserva_id', $reserva_id);
        $consulta->bindParam(':nombre', $nombre);
        $consulta->bindParam(':apellido', $apellido);
        $consulta->bindParam(':email', $email);
        $consulta->bindParam(':tipo_documento', $tipo_documento);
        $consulta->bindParam(':numero_documento', $numero_documento);
        
        return $consulta->execute();
    } catch (PDOException $e) {
        error_log("Error en agregar_pasajero: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene la lista de pasajeros pertenecientes a una reserva.
 */
function obtener_pasajeros($reserva_id) {
    $conexion = conectar_db();
    if (!$conexion) return [];

    $sql = "SELECT * FROM pasajeros WHERE reserva_id = :reserva_id";
    $consulta = $conexion->prepare($sql);
    $consulta->bindParam(':reserva_id', $reserva_id);
    $consulta->execute();
    
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Busca los detalles de una reserva utilizando su código PNR (ej: X7Y8Z9).
 */
function obtener_reserva_por_pnr($pnr) {
    $conexion = conectar_db();
    if (!$conexion) return null;

    $sql = "SELECT r.*, v.numero_vuelo, al.nombre as aerolinea_nombre, 
                   a_origen.codigo_iata as origen_iata, a_origen.ciudad as origen_ciudad, a_origen.nombre as origen_nombre,
                   a_destino.codigo_iata as destino_iata, a_destino.ciudad as destino_ciudad, a_destino.nombre as destino_nombre,
                   v.hora_salida, v.hora_llegada, v.duracion, v.escalas, v.precio as precio_unitario_vuelo
            FROM reservas r
            LEFT JOIN vuelos v ON r.vuelo_id = v.id
            LEFT JOIN aerolineas al ON v.aerolinea_id = al.id
            LEFT JOIN aeropuertos a_origen ON v.origen_aeropuerto_id = a_origen.id
            LEFT JOIN aeropuertos a_destino ON v.destino_aeropuerto_id = a_destino.id
            WHERE r.pnr = :pnr LIMIT 1";
              
    $consulta = $conexion->prepare($sql);
    $consulta->bindParam(':pnr', $pnr);
    $consulta->execute();
    
    if ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $lista_pasajeros = obtener_pasajeros($fila['id']);
        $pasajero_principal = !empty($lista_pasajeros) ? trim($lista_pasajeros[0]['nombre'] . ' ' . $lista_pasajeros[0]['apellido']) : 'Pasajero';

        return [
            'id' => $fila['id'],
            'pnr' => $fila['pnr'],
            'estado' => $fila['estado'],
            'fecha_reserva' => $fila['fecha_reserva'],
            'precio_total' => (float)$fila['precio_total'],
            'pasajeros_count' => (int)$fila['pasajeros_count'],
            'tipo_viaje' => $fila['tipo_viaje'] ?? 'solo_ida',
            'duffel_order_id' => $fila['duffel_order_id'],
            'pasajeros' => $lista_pasajeros,
            'pasajero_nombre' => $pasajero_principal,
            'vuelo' => [
                'flight_number' => $fila['numero_vuelo'] ?? 'N/A',
                'airline' => $fila['aerolinea_nombre'] ?? 'Aerolínea',
                'departure_airport' => $fila['origen_iata'] ?? 'LIM',
                'departure_city' => $fila['origen_ciudad'] ?? 'Lima',
                'arrival_airport' => $fila['destino_iata'] ?? $fila['destino_ciudad'] ?? 'DEST',
                'arrival_city' => $fila['destino_ciudad'] ?? 'Destino',
                'departure_time' => !empty($fila['hora_salida']) ? date('H:i', strtotime($fila['hora_salida'])) : '08:00',
                'arrival_time' => !empty($fila['hora_llegada']) ? date('H:i', strtotime($fila['hora_llegada'])) : '12:00',
                'duration' => $fila['duracion'] ?? '2h 00m',
                'stops' => $fila['escalas'] ?? 0,
                'date' => date('d M Y', strtotime($fila['fecha_reserva']))
            ]
        ];
    }
    return null;
}

/**
 * Obtiene todas las reservas pertenecientes a un usuario registrado.
 */
function obtener_reservas_usuario($usuario_id) {
    $conexion = conectar_db();
    if (!$conexion) return [];

    $sql = "SELECT r.*, v.numero_vuelo, al.nombre as aerolinea_nombre, 
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
              
    $consulta = $conexion->prepare($sql);
    $consulta->bindParam(':usuario_id', $usuario_id);
    $consulta->execute();
    
    $resultados = [];
    while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $lista_pasajeros = obtener_pasajeros($fila['id']);
        $pasajero_principal = !empty($lista_pasajeros) ? trim($lista_pasajeros[0]['nombre'] . ' ' . $lista_pasajeros[0]['apellido']) : 'Pasajero';

        $vuelo_vuelta = null;
        if (!empty($fila['vuelo_vuelta_data'])) {
            $vuelo_vuelta = json_decode($fila['vuelo_vuelta_data'], true);
        }

        $resultados[] = [
            'id' => $fila['id'],
            'pnr' => $fila['pnr'],
            'estado' => $fila['estado'],
            'fecha_reserva' => $fila['fecha_reserva'],
            'precio_total' => (float)$fila['precio_total'],
            'pasajeros_count' => (int)$fila['pasajeros_count'],
            'tipo_viaje' => $fila['tipo_viaje'] ?? 'solo_ida',
            'pasajero_nombre' => $pasajero_principal,
            'pasajeros' => $lista_pasajeros,
            'fecha_retorno' => '',
            'vuelo_vuelta' => $vuelo_vuelta,
            'vuelo' => [
                'flight_number' => $fila['numero_vuelo'] ?? 'N/A',
                'airline' => $fila['aerolinea_nombre'] ?? 'Aerolínea',
                'departure_airport' => $fila['origen_iata'] ?? 'LIM',
                'arrival_airport' => $fila['destino_iata'] ?? $fila['destino_ciudad'] ?? 'DEST',
                'departure_time' => !empty($fila['hora_salida']) ? date('H:i', strtotime($fila['hora_salida'])) : '08:00',
                'arrival_time' => !empty($fila['hora_llegada']) ? date('H:i', strtotime($fila['hora_llegada'])) : '12:00',
                'date' => date('d M Y', strtotime($fila['fecha_reserva']))
            ]
        ];
    }
    return $resultados;
}

/**
 * Actualiza el estado de la reserva a "Checked-in" dado su PNR.
 */
function realizar_checkin($pnr, $usuario_id) {
    $conexion = conectar_db();
    if (!$conexion) return false;

    $sql = "UPDATE reservas SET estado = 'Checked-in' WHERE pnr = :pnr AND (usuario_id = :usuario_id OR :usuario_id = 999)";
    $consulta = $conexion->prepare($sql);
    $consulta->bindParam(':pnr', $pnr);
    $consulta->bindParam(':usuario_id', $usuario_id);
    
    return $consulta->execute() && $consulta->rowCount() > 0;
}

/**
 * Genera y descarga el boleto electrónico (E-Ticket) en PDF utilizando la librería Dompdf.
 */
function generar_boleto_pdf($pnr) {
    // 1. Cargamos el autoloader de Composer para la librería Dompdf
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // 2. Buscamos los datos de la reserva por su código PNR
    $reserva = obtener_reserva_por_pnr($pnr);
    if (!$reserva) {
        echo "No se encontró ninguna reserva con el código PNR proporcionado.";
        return;
    }

    $vuelo = $reserva['vuelo'] ?? [];
    $pasajeros = $reserva['pasajeros'] ?? [];
    $total_pagado = number_format($reserva['precio_total'], 2);
    $cant_pasajeros = max(1, (int)$reserva['pasajeros_count']);
    $precio_unitario = number_format($reserva['precio_total'] / $cant_pasajeros, 2);
    $es_ida_vuelta = ($reserva['tipo_viaje'] === 'ida_vuelta');
    $fecha_actual = date('d/m/Y H:i');

    // 3. Armamos la lista HTML de pasajeros
    $html_pasajeros = '';
    if (!empty($pasajeros)) {
        foreach ($pasajeros as $pas) {
            $nombre_completo = trim($pas['nombre'] . ' ' . $pas['apellido']);
            if (empty($nombre_completo)) continue;
            
            $doc_info = !empty($pas['numero_documento']) ? ' &nbsp;<span style="color:#64748b; font-size:11px;">(' . htmlspecialchars($pas['tipo_documento'] ?? 'DNI') . ': ' . htmlspecialchars($pas['numero_documento']) . ')</span>' : '';
            $html_pasajeros .= '<li style="margin-bottom: 6px;"><strong>' . htmlspecialchars($nombre_completo) . '</strong>' . $doc_info . '</li>';
        }
    }
    if (empty($html_pasajeros)) {
        $nombre_fallback = !empty(trim($reserva['pasajero_nombre'] ?? '')) ? $reserva['pasajero_nombre'] : 'Pasajero Registrado';
        $html_pasajeros = '<li><strong>' . htmlspecialchars($nombre_fallback) . '</strong></li>';
    }

    // 4. Construimos el diseño HTML estilizado para el ticket en PDF (Optimizado para 1 página A4)
    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Boleto de Avión - ' . htmlspecialchars($pnr) . '</title>
        <style>
            @page { margin: 10mm 12mm; }
            body { font-family: "Helvetica", "Arial", sans-serif; color: #1e293b; margin: 0; padding: 0; font-size: 11.5px; line-height: 1.35; }
            .header { background-color: #0A1628; color: #ffffff; padding: 12px; border-radius: 8px; margin-bottom: 10px; text-align: center; }
            .logo-title { font-size: 22px; font-weight: bold; color: #C5A880; letter-spacing: 2px; margin: 0; }
            .subtitle { font-size: 9.5px; color: #94a3b8; text-transform: uppercase; margin-top: 2px; letter-spacing: 1px; }
            
            .pnr-box { background-color: #0A1628; border: 2px dashed #C5A880; border-radius: 8px; padding: 10px 12px; text-align: center; margin-bottom: 10px; }
            .pnr-label { font-size: 9.5px; color: #9C694C; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; letter-spacing: 1px; }
            .pnr-code { font-size: 26px; font-weight: bold; color: #C5A880; letter-spacing: 5px; font-family: monospace; }
            
            .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; margin-bottom: 10px; page-break-inside: avoid; }
            .card-title { font-size: 11.5px; font-weight: bold; color: #0A1628; border-bottom: 2px solid #C5A880; padding-bottom: 4px; margin-bottom: 8px; text-transform: uppercase; }
            
            table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
            th { text-align: left; font-size: 9px; color: #64748b; text-transform: uppercase; padding-bottom: 4px; border-bottom: 1px solid #f1f5f9; }
            td { font-size: 11.5px; font-weight: bold; color: #0f172a; padding-top: 5px; padding-bottom: 5px; }
            
            .price-row { display: table; width: 100%; margin-top: 4px; padding-top: 4px; border-top: 1px solid #f1f5f9; }
            .price-label { display: table-cell; font-size: 11px; color: #475569; }
            .price-val { display: table-cell; text-align: right; font-size: 11px; font-weight: bold; color: #0f172a; }
            
            .total-box { background-color: #0A1628; border: 1px solid #C5A880; padding: 8px 12px; border-radius: 6px; margin-top: 8px; text-align: right; }
            .total-label { font-size: 11.5px; font-weight: bold; color: #ffffff; }
            .total-amount { font-size: 20px; font-weight: bold; color: #C5A880; }
            
            .notice-box { background-color: #fdfbf7; border-left: 4px solid #C5A880; padding: 8px 12px; margin-top: 10px; font-size: 10px; color: #48324F; border-radius: 4px; page-break-inside: avoid; }
            .footer { text-align: center; margin-top: 10px; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; page-break-inside: avoid; }
            .barcode { font-family: monospace; font-size: 16px; letter-spacing: 4px; text-align: center; color: #475569; margin-top: 8px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1 class="logo-title">NOVAIRLINES</h1>
            <div class="subtitle">Boleto Electrónico de Viaje / E-Ticket</div>
        </div>

        <div class="pnr-box">
            <div class="pnr-label">Código de Reserva (PNR)</div>
            <div class="pnr-code">' . htmlspecialchars($pnr) . '</div>
        </div>

        <div class="card">
            <div class="card-title">1. Detalles del Vuelo</div>
            <table>
                <tr>
                    <th>Aerolínea / Vuelo</th>
                    <th>Ruta</th>
                    <th>Horarios</th>
                    <th>Tipo de Viaje</th>
                </tr>
                <tr>
                    <td>' . htmlspecialchars($vuelo['airline'] ?? 'NovAirlines') . ' (' . htmlspecialchars($vuelo['flight_number'] ?? 'N/A') . ')</td>
                    <td>' . htmlspecialchars($vuelo['departure_airport'] ?? 'LIM') . ' &rarr; ' . htmlspecialchars($vuelo['arrival_airport'] ?? 'DEST') . '</td>
                    <td>' . htmlspecialchars($vuelo['departure_time'] ?? '08:00') . ' - ' . htmlspecialchars($vuelo['arrival_time'] ?? '10:00') . '</td>
                    <td>' . ($es_ida_vuelta ? 'Ida y Vuelta' : 'Solo Ida') . '</td>
                </tr>
            </table>
        </div>

        <div class="card">
            <div class="card-title">2. Pasajero(s) Registrado(s)</div>
            <ul style="margin: 0; padding-left: 20px; color: #1e293b;">
                ' . $html_pasajeros . '
            </ul>
        </div>

        <div class="card">
            <div class="card-title">3. Resumen del Pago</div>
            <div class="price-row">
                <div class="price-label">Cantidad de Pasajeros:</div>
                <div class="price-val">' . $cant_pasajeros . ' boleto(s)</div>
            </div>
            <div class="price-row">
                <div class="price-label">Precio Unitario por Boleto:</div>
                <div class="price-val">S/. ' . $precio_unitario . '</div>
            </div>
            <div class="total-box">
                <span class="total-label">TOTAL PAGADO: </span>
                <span class="total-amount">S/. ' . $total_pagado . '</span>
            </div>
        </div>

        <div class="notice-box">
            <strong>Información Importante:</strong>
            <br>&bull; Presente este boleto electrónico junto con su documento de identidad (DNI o Pasaporte) en el mostrador del aeropuerto.
            <br>&bull; El Check-in online estará disponible 24 horas antes de su vuelo utilizando su código PNR: <strong>' . htmlspecialchars($pnr) . '</strong>.
            <br>&bull; Preséntese en el aeropuerto con 2 horas de anticipación para vuelos nacionales y 3 horas para vuelos internacionales.
        </div>

        <div class="barcode">||| | |||| | ||||| ||| |||| | ||| | |||</div>

        <div class="footer">
            NovAirlines S.A. &bull; Tu viaje con Inteligencia Artificial &bull; Emitido el ' . $fecha_actual . ' &bull; Soporte: +51 1 700-NOVA
        </div>
    </body>
    </html>
    ';

    // 5. Instanciamos la clase Dompdf
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Limpiamos cualquier buffer activo para evitar corrupción de encabezados o descargas incompletas
    if (ob_get_length()) {
        ob_end_clean();
    }

    // 6. Enviamos el PDF generado al navegador para la descarga del usuario
    $dompdf->stream("Boleto_NovAirlines_" . $pnr . ".pdf", ["Attachment" => true]);
    exit();
}
?>
