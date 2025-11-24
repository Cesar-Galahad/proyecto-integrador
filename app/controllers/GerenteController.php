<?php
require_once __DIR__ . '/../models/Gerente.php';

class GerenteController {
    private $gerenteModel;

    public function __construct() {
        // Conexión PDO centralizada
        $pdo = new PDO("mysql:host=localhost;dbname=nexoseguros;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->gerenteModel = new Gerente($pdo);
    }

    // Perfil del gerente
    public function perfil($idGerente) {
        return $this->gerenteModel->obtenerPerfil($idGerente);
    }

    // Crear agente
    public function crearAgente($nombre, $sueldobase, $sucursal, $usuario, $correo, $contrasena) {
        if (empty($nombre) || empty($sueldobase) || empty($sucursal) || empty($usuario) || empty($correo) || empty($contrasena)) {
            $_SESSION['crear_error'] = "Todos los campos son obligatorios.";
            header("Location: ../app/views/dashboard-gerente.php#crear");
            exit;
        }

        $resultado = $this->gerenteModel->crearAgente($nombre, $sueldobase, $sucursal, $usuario, $correo, $contrasena);

        if ($resultado) {
            $_SESSION['crear_success'] = "Agente registrado correctamente.";
        } else {
            $_SESSION['crear_error'] = "Error al registrar el agente.";
        }

        header("Location: ../app/views/dashboard-gerente.php#crear");
        exit;
    }

    // Listar agentes
    public function listarAgentes() {
        return $this->gerenteModel->listarAgentes();
    }

    // Listar clientes por seguro
    public function listarClientesVida() {
        return $this->gerenteModel->listarClientesVida();
    }

    public function listarClientesAuto() {
        return $this->gerenteModel->listarClientesAuto();
    }

    public function listarClientesRobo() {
        return $this->gerenteModel->listarClientesRobo();
    }

    public function listarClientesIncendio() {
        return $this->gerenteModel->listarClientesIncendio();
    }
}
    