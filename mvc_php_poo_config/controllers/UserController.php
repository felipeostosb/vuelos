<?php

// COMENTADO: Aún no usamos base de datos, así que no llamamos al modelo
// require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    /* =======================================================
       COMENTADO: Código preparado para cuando usemos la BD
    =======================================================
    private UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }
    */

    // =======================================================
    // NUEVA MEJORA: Función unificadora (Principio DRY)
    // En lugar de escribir el llamado al header y footer en 
    // cada vista, creamos una función que lo haga por nosotros.
    // =======================================================
    private function renderView(string $viewName): void
    {
        require_once __DIR__ . '/../views/includes/header.html';
        require_once __DIR__ . '/../views/user/' . $viewName . '.html';
        require_once __DIR__ . '/../views/includes/footer.html';
    }

    // =======================================================
    // VISTAS DEL PROYECTO VUELA IA
    // =======================================================
    
    public function showHome(): void
    {
        $this->renderView('home');
    }

    public function showDestinos(): void
    {
        $this->renderView('destinos');
    }

    public function showOfertas(): void
    {
        $this->renderView('ofertas');
    }

    public function showCheckin(): void
    {
        $this->renderView('checkin');
    }

    public function showAyuda(): void
    {
        $this->renderView('ayuda');
    }
}