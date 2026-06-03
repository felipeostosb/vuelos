<?php
/**
 * 🌸 ============================================================================================== 🌸
 * CONTROLADOR: FlightController.php (La "Personal Shopper" de Viajes)
 * 🌸 ============================================================================================== 🌸
 * 
 * 💖 CONCEPTO GENERAL:
 * En nuestra arquitectura (MVC), este archivo es el Controlador especialista en vuelos.
 * Piensa en ella como una Personal Shopper experta en viajes.
 * 
 * Cuando la Hostess (index.php) ve que quieres hacer algo con vuelos (buscar, ver ofertas),
 * te manda directo con ella. La Personal Shopper:
 * 1. Te escucha (Lee qué opciones o filtros marcaste en la pantalla).
 * 2. Llama a la bodega (El Modelo) y pide que le traigan las opciones exactas.
 * 3. Se las da al escaparatista (La Vista) para que te las muestre súper bonitas.
 * ==============================================================================================
 */

class FlightController extends BaseController
{
    /**
     * 👗 TAREA 1: Mostrar los Destinos (showDestinos)
     * ------------------------------------------------------------------------------------------
     * ¿Para qué sirve?: Es la función que se enciende cuando entras a ver la galería de destinos.
     * ¿Cómo funciona?: Llama al truco que le enseñó su mamá (renderView) para armar 
     * el "outfit" visual y mostrarte la pantalla que está guardada en la carpeta 'destinos'.
     */
    public function showDestinos(): void
    {
        $this->renderView('flights', 'destinos');
    }

    /**
     * 👗 TAREA 2: Mostrar las Ofertas (showOfertas)
     * ------------------------------------------------------------------------------------------
     * Lo mismo que arriba, pero esta vez te viste con la colección de "ofertas".
     */
    public function showOfertas(): void
    {
        $this->renderView('flights', 'ofertas');
    }

    /**
     * 👗 TAREA 3: Procesar los Filtros y Mostrar Vuelos (showReserva) - ¡El Corazón del Sistema!
     * ------------------------------------------------------------------------------------------
     * ¿Para qué sirve?: Cuando estás en la web y mueves la barrita de "Precio Máximo" o 
     * marcas la casilla "Quiero viajar por LATAM", esta función es la que procesa esa información.
     */
    public function showReserva(): void
    {
        // PASO 1: Conectar con la bodega (FlightModel)
        // El comando "new" significa "contratar". Estamos llamando al encargado de la bodega
        // para tenerlo listo porque le vamos a pedir información pesada.
        $flightModel = new FlightModel();
        
        // PASO 2: Anotar la lista de deseos del cliente ($filtros)
        // Creamos un arreglo vacío [] (como un cuadernito en blanco).
        // ¿Qué es $_GET?: Es la magia invisible. Cuando haces clic en "Filtrar", tus opciones viajan 
        // pegadas a la URL. $_GET nos permite leer esas opciones.
        $filtros = [];
        
        // Preguntamos: isset($_GET['max_price']) (¿El usuario me envió un precio máximo?)
        // Si sí lo envió, lo anotamos en nuestro cuadernito de filtros.
        if (isset($_GET['max_price'])) {
            $filtros['max_price'] = $_GET['max_price'];
        }
        
        // Lo mismo hacemos con las escalas...
        if (isset($_GET['stops'])) {
            $filtros['stops'] = $_GET['stops']; 
        }
        
        // Y lo mismo hacemos con las aerolíneas favoritas...
        if (isset($_GET['airlines'])) {
            $filtros['airlines'] = $_GET['airlines']; 
        }

        // TAREA EXTRA: Filtros de la Búsqueda Clásica (Origen y Destino)
        if (isset($_GET['origen'])) {
            $filtros['origen'] = $_GET['origen'];
        }
        if (isset($_GET['destino'])) {
            $filtros['destino'] = $_GET['destino'];
        }

        // TAREA EXTRA: Ida y Vuelta (parseando flatpickr)
        $tipo_viaje = $_GET['tipo_viaje'] ?? 'solo_ida';
        $rango_fechas = $_GET['rango_fechas'] ?? '';
        $fecha_retorno = '';
        
        if (strpos($rango_fechas, ' to ') !== false) {
            $partes = explode(' to ', $rango_fechas);
            $filtros['fecha'] = $partes[0];
            $fecha_retorno = $partes[1];
        } else {
            $filtros['fecha'] = $rango_fechas;
        }

        // Sobrescribimos el GET para que la vista de reserva reciba la fecha original parseada
        $_GET['fecha'] = $filtros['fecha'];

        // PASO 3: Darle la lista de deseos a la bodega
        // Le pasamos nuestro cuadernito de filtros ($filtros) al encargado de bodega (flightModel).
        // Le decimos "searchFlights" (Búscame los vuelos que cumplan con estos requisitos).
        // Él nos devolverá una cajita solo con los vuelos aprobados, y la guardaremos en "$vuelos".
        $vuelos = $flightModel->searchFlights($filtros);

        // Si es ida y vuelta, multiplicamos el precio por 2 para la demo
        if ($tipo_viaje === 'ida_vuelta') {
            foreach ($vuelos as &$v) {
                $v['price'] *= 2;
            }
        }

        // PASO 4: Mandar todo a la vitrina (La Vista)
        // Usamos nuestro truco 'renderView'. Pero fíjate que al final le estamos enviando 
        // ['vuelos' => $vuelos]. ¡Le estamos dando la cajita de vuelos a la parte visual 
        // para que dibuje un cuadro por cada vuelo encontrado!
        $this->renderView('flights', 'reserva', [
            'vuelos' => $vuelos,
            'tipo_viaje' => $tipo_viaje,
            'fecha_retorno' => $fecha_retorno
        ]);
    }

    /**
     * 👗 TAREA 4: La Inteligencia Artificial Mágica (buscar)
     * ------------------------------------------------------------------------------------------
     * ¿Para qué sirve?: Cuando la clienta escribe texto libre (ej: "Viaje a París mañana"),
     * esta función toma ese texto, lo lee sin mostrar pantallas de carga feas, y la manda 
     * directamente al probador (la vista de reserva) con las opciones listas.
     */
    public function buscar(): void
    {
        $textoLibre = $_GET['query'] ?? '';
        
        // Aquí conectaremos con Gemini. Por ahora, "simulamos" que la IA fue súper rápida
        // y detectó que la clienta quería ir a París.
        $destinoDetectado = 'París'; 
        
        // ¡Magia! La redirigimos directamente a la pantalla de resultados finales (reserva),
        // sin que ella vea pasos intermedios.
        header('Location: index.php?action=reserva&destino=' . urlencode($destinoDetectado) . '&origen=Lima');
        exit;
    }

    /**
     * 🛒 TAREA 5: Ir a la caja (Checkout)
     * ------------------------------------------------------------------------------------------
     * ¿Para qué sirve?: Recibe el vuelo que elegiste y te muestra el resumen antes de pagar.
     * MEJORA DE UX: Si ya iniciaste sesión, se salta este paso y te reserva de inmediato.
     */
    public function checkout(): void
    {
        $flightId = $_POST['flight_id'] ?? null;
        $pasajeros = $_POST['pasajeros'] ?? 1;
        $tipo_viaje = $_POST['tipo_viaje'] ?? 'solo_ida';
        $fecha_retorno = $_POST['fecha_retorno'] ?? '';

        if (!$flightId) {
            header('Location: index.php?action=reserva');
            exit;
        }

        // Si el usuario ya está logueado, compramos con un solo clic (One-Click Booking)
        if (isset($_SESSION['user_id'])) {
            $this->procesarReservaDirecta($flightId, $pasajeros, $_SESSION['user_name'], $_SESSION['user_email'], $tipo_viaje, $fecha_retorno);
            return;
        }

        // Si no está logueado, lo mandamos a la caja registradora para pedirle sus datos
        $flightModel = new FlightModel();
        $todos = $flightModel->searchFlights([]);
        $vueloSeleccionado = null;
        foreach ($todos as $v) {
            if ($v['id'] == $flightId) {
                $vueloSeleccionado = $v;
                break;
            }
        }

        if ($vueloSeleccionado && $tipo_viaje === 'ida_vuelta') {
            $vueloSeleccionado['price'] *= 2;
        }

        $data = [
            'vuelo' => $vueloSeleccionado,
            'pasajeros' => $pasajeros,
            'tipo_viaje' => $tipo_viaje,
            'fecha_retorno' => $fecha_retorno
        ];

        $this->renderView('flights', 'checkout', $data);
    }

    /**
     * 💳 TAREA 6: Pagar y Confirmar (confirmarReserva)
     * ------------------------------------------------------------------------------------------
     * ¿Para qué sirve?: Recibe los datos del formulario de checkout (invitados) y guarda la reserva.
     */
    public function confirmarReserva(): void
    {
        $flightId = $_POST['flight_id'] ?? null;
        $pasajeros = $_POST['pasajeros'] ?? 1;
        $nombre = $_POST['nombre'] ?? 'Invitado';
        $email = $_POST['email'] ?? 'invitado@email.com';
        $tipo_viaje = $_POST['tipo_viaje'] ?? 'solo_ida';
        $fecha_retorno = $_POST['fecha_retorno'] ?? '';

        $this->procesarReservaDirecta($flightId, $pasajeros, $nombre, $email, $tipo_viaje, $fecha_retorno);
    }

    /**
     * 🛠️ Función auxiliar para guardar la reserva en la memoria
     */
    private function procesarReservaDirecta($flightId, $pasajeros, $nombre, $email, $tipo_viaje = 'solo_ida', $fecha_retorno = ''): void
    {
        $flightModel = new FlightModel();
        
        // Aquí simularemos la llamada a Duffel (que actualmente solo devuelve un PNR simulado)
        $resultadoDuffel = $flightModel->bookFlightWithDuffel($flightId, $pasajeros);
        $pnr = $resultadoDuffel['pnr'];

        // Buscamos los detalles del vuelo para guardarlos
        $todos = $flightModel->searchFlights([]);
        $vueloSeleccionado = null;
        foreach ($todos as $v) {
            if ($v['id'] == $flightId) {
                $vueloSeleccionado = $v;
                break;
            }
        }

        if ($vueloSeleccionado && $tipo_viaje === 'ida_vuelta') {
            $vueloSeleccionado['price'] *= 2;
        }

        // Creamos la "caja de zapatos" de reservas si no existe
        if (!isset($_SESSION['reservas'])) {
            $_SESSION['reservas'] = [];
        }

        // Guardamos la reserva en la memoria (como un post-it en la sesión)
        $_SESSION['reservas'][$pnr] = [
            'pnr' => $pnr,
            'vuelo' => $vueloSeleccionado,
            'pasajeros' => $pasajeros,
            'pasajero_nombre' => $nombre,
            'pasajero_email' => $email,
            'estado' => 'Confirmada',
            'tipo_viaje' => $tipo_viaje,
            'fecha_retorno' => $fecha_retorno,
            'fecha_reserva' => date('Y-m-d H:i:s')
        ];

        // Lo mandamos al panel de usuario (o a una pantalla de éxito si es invitado)
        // Por simplicidad en la simulación, iniciaremos sesión al invitado para que vea su panel
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 999; // ID genérico de invitado
            $_SESSION['user_name'] = $nombre;
            $_SESSION['user_email'] = $email;
        }

        header('Location: index.php?action=panel&success=1&pnr=' . $pnr);
        exit;
    }
}
