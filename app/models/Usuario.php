<?php
// Ruta: app/models/Usuario.php
require_once __DIR__ . '/../Core/DB.php';

class Usuario {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::connect();
    }

    public function buscarPorUsuario($usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
        $stmt->execute([':usuario' => $usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarCambioContraseña($idUsuario, $nuevaPassword) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET contrasena = :contrasena, primer_login = 0 WHERE id_usuario = :id");
        return $stmt->execute([':contrasena' => $nuevaPassword, ':id' => $idUsuario]);
    }
}

