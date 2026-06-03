<?php
declare(strict_types=1);
session_start();
/**
 * 🌸 ============================================================================================== 🌸
 * ARCHIVO PRINCIPAL: index.php (La "Hostess" o Anfitriona VIP de nuestra web)
 * 🌸 ============================================================================================== 🌸
 * 
 * 💖 CONCEPTO GENERAL (Para entenderlo súper fácil):
 * Imagina que nuestra página web es una boutique de moda o un club VIP súper exclusivo.
 * Cuando una persona hace clic en un link (por ejemplo, "Ver Destinos"), es como si acabara de 
 * entrar por la puerta principal. 
 * 
 * Este archivo (index.php) es la chica de la entrada, la "Hostess". NUNCA dejamos que los clientes
 * pasen solos a la bodega o a las oficinas. Siempre, siempre, pasan por ella primero.
 * 
 * 💖 ¿CÓMO FUNCIONA ESTE ARCHIVO EN NUESTRO SISTEMA?
 * 1. Prende las luces de emergencia (muestra errores si la página falla).
 * 2. Llama a la asistente mágica (Autoloader) para tener las carpetas listas.
 * 3. Revisa la lista de invitados/destinos (Router) para saber a dónde quiere ir la persona.
 * 4. Acompaña a la persona a la zona correcta (Dispatcher).
 * ==============================================================================================
 */

// 💡 1. PRENDIENDO LAS LUCES (Manejo de errores)
// ¿Para qué sirve? Si algo se rompe en nuestro código, PHP nos mostrará un mensaje de error exacto.
// Si no pusiéramos esto, la pantalla se quedaría totalmente blanca y no sabríamos qué pasó.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/**
 * ----------------------------------------------------------------------------------------------
 * 👗 2. EL AUTOLOADER (La Asistente Personal)
 * ----------------------------------------------------------------------------------------------
 * ¿QUÉ ES EL COMANDO "spl_autoload_register"?: 
 * Es una función mágica de PHP que carga archivos de código automáticamente sin que tengamos que 
 * llamarlos uno por uno.
 * 
 * ¿QUÉ HACE EN EL SISTEMA?:
 * Imagina que tienes 50 empleados (archivos) trabajando. Normalmente tendrías que llamarlos uno
 * a uno (usando un comando llamado 'require'). Esta asistente (autoloader) está atenta; en cuanto
 * tú mencionas el nombre de un empleado (ej. "FlightModel"), ella va corriendo a la carpeta 
 * correcta, agarra el archivo y te lo trae. ¡Ahorra muchísimo tiempo!
 */
spl_autoload_register(function ($class_name) {
    // Preguntamos: ¿El nombre del archivo que buscas tiene la palabra "Controller"?
    if (strpos($class_name, 'Controller') !== false) {
        $file = __DIR__ . '/controllers/' . $class_name . '.php'; // Busca en la oficina de controladores
        if (file_exists($file)) {
            require_once $file; // Lo trae y lo integra al proyecto
        }
    } 
    // Preguntamos: ¿El nombre del archivo que buscas tiene la palabra "Model"?
    elseif (strpos($class_name, 'Model') !== false) {
        $file = __DIR__ . '/models/' . $class_name . '.php'; // Busca en la bodega de modelos
        if (file_exists($file)) {
            require_once $file; // Lo trae y lo integra
        }
    }
});

/**
 * ----------------------------------------------------------------------------------------------
 * 🗺️ 3. EL ENRUTADOR (El Mapa de la Hostess)
 * ----------------------------------------------------------------------------------------------
 * ¿QUÉ ES EL COMANDO "$_GET['action']"?: 
 * Es la forma en que leemos la URL. Si la web dice "misitio.com/index.php?action=destinos", 
 * este comando atrapa la palabra "destinos".
 * 
 * ¿QUÉ HACE EN EL SISTEMA?:
 * Si el usuario no dice a dónde va, lo mandamos al inicio ('home').
 */
$action = $_GET['action'] ?? $_POST['action'] ?? 'home';

/*
 * Esta es nuestra LISTA VIP (Arreglo o Array). 
 * La Hostess lee a dónde quieres ir (ej. 'ofertas') y mira en la lista quién es el
 * 'Personal Shopper' (Controlador) encargado de atenderte y qué tarea (Método) hará.
 */
$routes = [
    // Peticiones básicas van con el HomeController (Atención al cliente general)
    'home'             => ['controller' => 'HomeController', 'method' => 'showHome'],
    'ayuda'            => ['controller' => 'HomeController', 'method' => 'showAyuda'],
    'procesar_soporte' => ['controller' => 'HomeController', 'method' => 'procesarSoporte'],
    
    // Todo lo relacionado a buscar y reservar vuelos va con el FlightController (El experto en viajes)
    'destinos'         => ['controller' => 'FlightController', 'method' => 'showDestinos'],
    'ofertas'          => ['controller' => 'FlightController', 'method' => 'showOfertas'],
    'reserva'          => ['controller' => 'FlightController', 'method' => 'showReserva'],
    'buscar'           => ['controller' => 'FlightController', 'method' => 'buscar'],
    'checkout'         => ['controller' => 'FlightController', 'method' => 'checkout'],
    'confirmarReserva' => ['controller' => 'FlightController', 'method' => 'confirmarReserva'],
    
    // Perfiles y registros van con el UserController (El de los registros)
    'checkin'          => ['controller' => 'UserController', 'method' => 'showCheckin'],
    'procesarCheckin'  => ['controller' => 'UserController', 'method' => 'procesarCheckin'],
    'formulario'       => ['controller' => 'UserController', 'method' => 'mostrarFormulario'],
    'insertar'         => ['controller' => 'UserController', 'method' => 'insertar'],
    'panel'            => ['controller' => 'UserController', 'method' => 'panel'],
    
    // Autenticación de Usuario (Login / Logout)
    'login'            => ['controller' => 'UserController', 'method' => 'procesarLogin'],
    'logout'           => ['controller' => 'UserController', 'method' => 'procesarLogout'],
];

/**
 * ----------------------------------------------------------------------------------------------
 * 🚕 4. EL DESPACHADOR (Llevando al cliente a su destino)
 * ----------------------------------------------------------------------------------------------
 * ¿CÓMO FUNCIONA ESTE BLOQUE?:
 * Usamos una condición "if" (que significa "SI ocurre esto...").
 * array_key_exists pregunta: "Hostess, ¿la palabra que pidió el cliente (ej. 'ofertas') 
 * existe en tu lista VIP ($routes)?"
 */
if (array_key_exists($action, $routes)) {
    // Leemos quién es el encargado (ej. FlightController) y qué función debe hacer (ej. showOfertas)
    $controllerName = $routes[$action]['controller']; 
    $methodName = $routes[$action]['method'];         

    // class_exists: Verificamos si ese empleado realmente vino a trabajar hoy
    if (class_exists($controllerName)) {
        
        // $controller = new ... : Es como ponerle el uniforme al empleado y ponerlo a trabajar.
        $controller = new $controllerName();
        
        // method_exists: Verificamos si el empleado sí sabe hacer la tarea que le pedimos
        if (method_exists($controller, $methodName)) {
            // ¡Todo perfecto! Le decimos al empleado que ejecute su tarea de inmediato.
            $controller->$methodName();
        } else {
            // Si el empleado no sabe hacerla, por seguridad llevamos a la persona a la pantalla de Inicio
            (new HomeController())->showHome();
        }
    } else {
        // Si el empleado no vino, lo mandamos a Inicio
        (new HomeController())->showHome();
    }
} else {
    // Si la persona inventó un destino que no existe en nuestra lista VIP, lo mandamos a Inicio
    (new HomeController())->showHome();
}