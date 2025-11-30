<?php
require_once dirname(__DIR__) . '/models/Agente.php';
require_once dirname(__DIR__) . '/models/ListaOrdenada.php';

class AgenteController {
    private $agenteModel;

    public function __construct() {
        $pdo = new PDO("mysql:host=localhost;dbname=nexoseguros;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->agenteModel = new Agente($pdo);
    }

    public function perfil($idAgente) {
        return $this->agenteModel->obtenerPerfil($idAgente);
    }

    public function listarClientesVidaActivos($idAgente) {
        return $this->agenteModel->listarClientesVidaActivos($idAgente);
    }
    public function listarClientesAutoActivos($idAgente) {
        return $this->agenteModel->listarClientesAutoActivos($idAgente);
    }
    public function listarClientesRoboActivos($idAgente) {
        return $this->agenteModel->listarClientesRoboActivos($idAgente);
    }
    public function listarClientesIncendioActivos($idAgente) {
        return $this->agenteModel->listarClientesIncendioActivos($idAgente);
    }
    public function listarSolicitudesVendidas($idAgente) {
        return $this->agenteModel->listarSolicitudesVendidas($idAgente);
    }

    public function ordenarClientes($tabla, $campoOrden, &$vida, &$auto, &$robo, &$incendio, $ascendente = true) {
        if ($tabla === 'vida') {
            $lista = new ListaOrdenada($campoOrden, $ascendente);
            foreach ($vida as $v) $lista->insertar($v);
            $vida = $lista->obtenerTodos();
        }
        if ($tabla === 'auto') {
            $lista = new ListaOrdenada($campoOrden, $ascendente);
            foreach ($auto as $a) $lista->insertar($a);
            $auto = $lista->obtenerTodos();
        }
        if ($tabla === 'robo') {
            $lista = new ListaOrdenada($campoOrden, $ascendente);
            foreach ($robo as $r) $lista->insertar($r);
            $robo = $lista->obtenerTodos();
        }
        if ($tabla === 'incendio') {
            $lista = new ListaOrdenada($campoOrden, $ascendente);
            foreach ($incendio as $i) $lista->insertar($i);
            $incendio = $lista->obtenerTodos();
        }

        $_SESSION['active_section'] = 'leer';
    }
}

