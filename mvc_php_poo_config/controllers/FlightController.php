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

        // PASO 3: Darle la lista de deseos a la bodega
        // Le pasamos nuestro cuadernito de filtros ($filtros) al encargado de bodega (flightModel).
        // Le decimos "searchFlights" (Búscame los vuelos que cumplan con estos requisitos).
        // Él nos devolverá una cajita solo con los vuelos aprobados, y la guardaremos en "$vuelos".
        $vuelos = $flightModel->searchFlights($filtros);

        // PASO 4: Mandar todo a la vitrina (La Vista)
        // Usamos nuestro truco 'renderView'. Pero fíjate que al final le estamos enviando 
        // ['vuelos' => $vuelos]. ¡Le estamos dando la cajita de vuelos a la parte visual 
        // para que dibuje un cuadro por cada vuelo encontrado!
        $this->renderView('flights', 'reserva', ['vuelos' => $vuelos]);
    }

    /**
     * 👗 TAREA 4: La Inteligencia Artificial (buscar)
     * ------------------------------------------------------------------------------------------
     * ¿Para qué sirve?: Es la pantalla mágica donde Gemini lee tu texto libre.
     */
    public function buscar(): void
    {
        // En el futuro, aquí conectaremos con el cerebro de Google Gemini.
        // Por ahora, solo armamos la pantalla visual de "resultados_busqueda".
        $this->renderView('flights', 'resultados_busqueda'); 
    }
}
