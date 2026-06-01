<?php
/**
 * 🌸 ============================================================================================== 🌸
 * CLASE PADRE: BaseController.php (La "Directora Creativa")
 * 🌸 ============================================================================================== 🌸
 * 
 * 💖 CONCEPTO GENERAL:
 * En nuestro proyecto usamos algo llamado "MVC" (Modelo - Vista - Controlador).
 * - MODELO: Es la bodega oscura donde están guardados los datos (precios, vuelos).
 * - VISTA: Es el diseño visual hermoso que ve el usuario (el "Feed" de Instagram).
 * - CONTROLADOR: Es la "Organizadora". Pide cosas a la bodega y se las da al diseño visual.
 * 
 * ¿QUÉ HACE ESTE ARCHIVO ESPECÍFICAMENTE?:
 * Imagina que todos nuestros Controladores (FlightController, UserController) son como
 * hermanas menores. Para que no tengan que aprender a hacer la misma tarea difícil una 
 * y otra vez, creamos a la "Directora Creativa" (BaseController) que les enseña un truco 
 * llamado "renderView". ¡Así todas reutilizan el mismo código!
 * ==============================================================================================
 */

class BaseController
{
    /**
     * 👗 FUNCIÓN: renderView() (El Armario Mágico)
     * ------------------------------------------------------------------------------------------
     * ¿PARA QUÉ SIRVE ESTE COMANDO?
     * Construye la página web completa y se la muestra al usuario final.
     * 
     * ¿CÓMO FUNCIONA?
     * Piensa en armar un outfit o un maquillaje:
     * 1. La base siempre es la misma (El Header/Cabecera con el logo de la marca).
     * 2. El toque final siempre es el mismo (El Footer/Pie de página con los links).
     * 3. Lo único que cambiamos es la pieza central (La Vista: por ejemplo, la página de Ofertas).
     * 
     * @param string $folder   La sub-carpeta donde está nuestra ropa (ej. 'flights')
     * @param string $viewName El nombre exacto de la prenda (ej. 'ofertas')
     * @param array $data      (Opcional) Una bolsita de accesorios (Datos de la base de datos que pasamos a la web)
     */
    protected function renderView(string $folder, string $viewName, array $data = []): void
    {
        /*
         * PASO 1: ABRIR LOS ACCESORIOS (El comando "extract")
         * ¿Qué hace?: Si trajimos datos de la bodega, vienen dentro del paquete "$data".
         * La función mágica "extract($data)" abre la bolsa y convierte cada dato en una variable libre
         * lista para usarse. Por ejemplo, si trajimos ['precio' => 500], ahora podemos usar $precio.
         */
        extract($data);

        /*
         * PASO 2: PONERNOS LA BASE (El Header)
         * ¿Qué hace "require_once"?: Es un comando que dice "trae este archivo y pégalo justo aquí".
         * Aquí estamos pegando toda la parte de arriba de nuestra página (El menú bonito y el logo).
         */
        require_once __DIR__ . '/../views/layout/header.php';
        
        /*
         * PASO 3: PONERNOS LA PRENDA PRINCIPAL (La Vista Dinámica)
         * Aquí pegamos el contenido que el usuario pidió ver.
         * Si pidieron ver 'ayuda', el sistema pegará el archivo que está en la ruta:
         * '/views/home/ayuda.php'. ¡Así de dinámico es!
         */
        require_once __DIR__ . '/../views/' . $folder . '/' . $viewName . '.php';
        
        /*
         * PASO 4: EL TOQUE FINAL (El Footer)
         * Pegamos la parte de hasta abajo de la web para cerrarla correctamente con estilo.
         * ¡El outfit (Página Web) está completo y el cliente ya puede verlo!
         */
        require_once __DIR__ . '/../views/layout/footer.php';
    }
}
