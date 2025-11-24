<?php
require_once __DIR__ . '/../models/Agente.php';

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
    public function ordenarClientes($tabla, $campoOrden, &$vida, &$auto, &$robo, &$incendio) {
        $cmp = function($a, $b) use ($campoOrden) {
            if ($campoOrden === 'fecha_solicitud') {
                return strtotime($a[$campoOrden] ?? '') <=> strtotime($b[$campoOrden] ?? '');
            }
            return strcmp(($a[$campoOrden] ?? ''), ($b[$campoOrden] ?? ''));
        };

        if ($tabla === 'vida') usort($vida, $cmp);
        if ($tabla === 'auto') usort($auto, $cmp);
        if ($tabla === 'robo') usort($robo, $cmp);
        if ($tabla === 'incendio') usort($incendio, $cmp);

        // Guardar sección activa
        $_SESSION['active_section'] = 'leer';
    }

}

