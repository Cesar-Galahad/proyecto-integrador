<?php
require_once __DIR__ . '/../models/Usuario.php';

class LoginController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    public function login($usuario, $password) {
        $user = $this->usuarioModel->buscarPorUsuario($usuario);

        if (!$user) {
            $_SESSION['login_error'] = "Usuario no encontrado.";
            header("Location: ../app/views/login.php");
            exit;
        }

        if ($password !== $user['contrasena']) {
            $_SESSION['login_error'] = "Contraseña incorrecta.";
            header("Location: ../app/views/login.php");
            exit;
        }

        // Guardar datos en sesión
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['usuario']    = $user['usuario'];
        $_SESSION['role']       = $user['rol'];   // consistente con SessionGuard
        $_SESSION['estado']     = $user['estatus'];

        // Flujo de cambio de contraseña (excepto gerentes)
        if ($user['primer_login'] == 1 && $user['rol'] !== 'gerente') {
            header("Location: ../app/views/cambiocontraseña.php");
            exit;
        }

        // Redirigir según rol
        switch ($user['rol']) {
            case 'cliente':
                $_SESSION['id_cliente'] = $user['id_cliente'] ?? null;
                header("Location: ../app/views/dashboard-cliente.php");
                exit;
            case 'agente':
                $_SESSION['id_agente'] = $user['id_agente'] ?? null;
                header("Location: ../app/views/dashboard-agente.php");
                exit;
            case 'gerente':
                $_SESSION['id_gerente'] = $user['id_gerente'] ?? null;
                header("Location: ../app/views/dashboard-gerente.php");
                exit;
        }
    }

    public function cambioContraseña($idUsuario, $nuevaPassword) {
        return $this->usuarioModel->actualizarCambioContraseña($idUsuario, $nuevaPassword);
    }
}