<?php
/**
 * =========================================================================
 * CONTROLADOR: USUARIO (Departamento de Clientes)
 * =========================================================================
 * Este "gerente" se especializa en gestionar las cuentas de las personas.
 * Aquí se controla el Check-In, el registro de nuevas cuentas (Sign Up) 
 * y el inicio de sesión (Login).
 */

class UserController extends BaseController
{
    // =======================================================
    // ZONA COMENTADA: Conexión con Base de Datos
    // Actualmente desactivada hasta que implementemos MySQL.
    // =======================================================
    // private UserModel $model;
    // public function __construct() { $this->model = new UserModel(); }

    /**
     * Muestra la pantalla para que el pasajero haga Check-In online.
     */
    public function showCheckin(): void
    {
        $this->renderView('user', 'checkin');
    }

    /**
     * Muestra el formulario para crear una nueva cuenta.
     */
    public function mostrarFormulario(): void
    {
        $this->renderView('user', 'create');
    }

    /**
     * Recibe los datos del formulario y los guarda en la base de datos.
     * (Actualmente en pausa).
     */
    public function insertar(): void
    {
        // Cuando activemos la base de datos, el código irá aquí.
        // Después de guardar, usualmente redirigimos a otra pantalla:
        // header('Location: index.php?action=home');
    }
}