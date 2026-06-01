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
    // VISTAS PRINCIPALES DEL PROYECTO VUELA IA
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

    // 👇 NUEVA RUTA AGREGADA PARA LA PÁGINA DE RESERVAS 👇
    public function showReserva(): void
    {
        $this->renderView('reserva');
    }

    public function showCheckin(): void
    {
        $this->renderView('checkin');
    }

    public function showAyuda(): void
    {
        $this->renderView('ayuda');
    }
    
    // =======================================================
    // MÉTODOS DE PROCESAMIENTO Y ACCIONES SECUNDARIAS
    // =======================================================

    // Función que procesará el formulario de búsqueda de la IA
    public function buscar(): void
    {
        // Por ahora cargamos una vista temporal, luego aquí conectarás la IA
        $this->renderView('resultados_busqueda'); 
    }

    // Función que procesará el envío de mensajes de ayuda/soporte
    public function procesarSoporte(): void
    {
        // Lógica futura: Recibir el POST y guardar en BD o enviar email
        // Por ahora redirigimos o mostramos mensaje de éxito
        $this->renderView('ayuda_exito');
    }

    // Funciones base que tenías preparadas en tu index.php
    public function mostrarFormulario(): void
    {
        $this->renderView('formulario_registro');
    }

    public function insertar(): void
    {
        // Lógica para procesar la inserción de datos a la BD
        // header('Location: index.php?action=home');
    }
}