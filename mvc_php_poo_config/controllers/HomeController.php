<?php
/**
 * =========================================================================
 * CONTROLADOR: HOME (Departamento de Inicio)
 * =========================================================================
 * Este "gerente" se encarga exclusivamente de las páginas estáticas
 * informativas de la empresa, es decir, las pantallas que no necesitan 
 * procesar vuelos ni hablar con la base de datos de usuarios.
 */

class HomeController extends BaseController
{
    /**
     * Muestra la página principal (Home)
     * Es la primera pantalla que ve el usuario al entrar a la web.
     */
    public function showHome(): void
    {
        // Instrucción: "Ve a la carpeta 'home' y muestra el archivo 'home.php'"
        $this->renderView('home', 'home');
    }

    /**
     * Muestra el Centro de Ayuda y Preguntas Frecuentes.
     */
    public function showAyuda(): void
    {
        $this->renderView('home', 'ayuda');
    }

    /**
     * Recibe los datos del formulario de contacto y los procesa.
     * En el futuro, aquí se programará el envío de correos electrónicos.
     */
    public function procesarSoporte(): void
    {
        // Todo: Programar lógica para enviar correo electrónico a soporte
        $this->renderView('home', 'ayuda_exito'); // Asume que se mostrará una página de "Éxito"
    }
}
