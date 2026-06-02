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

    /**
     * Procesa el formulario del Modal de Login.
     */
    public function procesarLogin(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new UserModel();
        $userData = $userModel->verificarLogin($email, $password);

        if ($userData) {
            // Guardamos al usuario en la sesión para que el sistema lo recuerde
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_name'] = $userData['nombre'];
            $_SESSION['user_email'] = $userData['email'];
            
            // Redirigimos al home con un mensaje de éxito
            header('Location: index.php?action=home&login=success');
        } else {
            // Redirigimos al home con un mensaje de error
            header('Location: index.php?action=home&login=error');
        }
        exit;
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function procesarLogout(): void
    {
        // Destruimos la sesión
        session_unset();
        session_destroy();
        
        // Redirigimos al inicio
        header('Location: index.php?action=home');
        exit;
    }
}