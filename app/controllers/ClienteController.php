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
    public function segurosVida($idCliente) {
    return $this->model->obtenerSegurosVida($idCliente);
    }

    public function segurosAuto($idCliente) {
        return $this->model->obtenerSegurosAuto($idCliente);
    }

    public function segurosRobo($idCliente) {
        return $this->model->obtenerSegurosRobo($idCliente);
    }

    public function segurosIncendio($idCliente) {
        return $this->model->obtenerSegurosIncendio($idCliente);
    }

}