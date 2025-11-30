<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $model;

    public function __construct() {
        $this->model = new Cliente();
    }

    public function perfil($idCliente) {
        return $this->model->obtenerPerfilCliente($idCliente);
    }
}