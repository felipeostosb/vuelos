<?php

require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    private UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    // =======================================================
    // TU NUEVA FUNCIÓN (Para mostrar la vista Home unida)
    // =======================================================
    public function showHome(): void
    {
        require_once __DIR__ . '/../views/includes/header.html';
        require_once __DIR__ . '/../views/user/home.html';
        require_once __DIR__ . '/../views/includes/footer.html';
    }

    public function showDestinos(): void
    {
        require_once __DIR__ . '/../views/includes/header.html';
        require_once __DIR__ . '/../views/user/destinos.html';
        require_once __DIR__ . '/../views/includes/footer.html';
    }

    public function showOfertas(): void
    {
        require_once __DIR__ . '/../views/includes/header.html';
        require_once __DIR__ . '/../views/user/ofertas.html';
        require_once __DIR__ . '/../views/includes/footer.html';
    }

    public function showCheckin(): void
    {
        require_once __DIR__ . '/../views/includes/header.html';
        require_once __DIR__ . '/../views/user/checkin.html';
        require_once __DIR__ . '/../views/includes/footer.html';
    }

    public function showAyuda(): void
    {
        require_once __DIR__ . '/../views/includes/header.html';
        require_once __DIR__ . '/../views/user/ayuda.html';
        require_once __DIR__ . '/../views/includes/footer.html';
    }
    // =======================================================
    // CÓDIGO DE TU COMPAÑERO (Mantenido intacto)
    // =======================================================
    public function mostrarFormulario(): void
    {
        $error = null;
        require __DIR__ . '/../views/user/create.php';
    }

    public function insertar(): void
    {
        $nombre   = trim($_POST['nombre']   ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validación básica
        if ($nombre === '' || $email === '' || $password === '') {
            $error = 'Todos los campos son obligatorios.';
            require __DIR__ . '/../views/user/create.php';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El email no tiene un formato válido.';
            require __DIR__ . '/../views/user/create.php';
            return;
        }

        if ($this->model->emailExiste($email)) {
            $error = 'Ya existe un usuario con ese email.';
            require __DIR__ . '/../views/user/create.php';
            return;
        }

        if ($this->model->insert($nombre, $email, $password)) {
            require __DIR__ . '/../views/user/success.php';
        } else {
            $error = 'Error al guardar el usuario. Intenta de nuevo.';
            require __DIR__ . '/../views/user/create.php';
        }
    }
}