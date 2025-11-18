<?php
// Usuario.php
require_once __DIR__ . '/../Core/DB.php';

class Usuario {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::connect(); // igual que Agente y Gerente
    }

    public function crearAgenteUsuario($usuario, $passwordHash, $idAgente) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO usuarios (nusuario, contrasena, rol, id_agente, estatus)
             VALUES (:usuario, :contrasena, 'agente', :idAgente, 'Activo')"
        );
        return $stmt->execute([
            ':usuario'    => $usuario,
            ':contrasena' => $passwordHash,
            ':idAgente'   => $idAgente
        ]);
    }

    public function buscarPorUsuario($usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE nusuario = :usuario");
        $stmt->execute([':usuario' => $usuario]);
        return $stmt->fetch();
    }
public function crear($data) {
    $stmt = $this->pdo->prepare("INSERT INTO usuarios (
        nusuario, contrasena, rol, id_cliente, estatus
    ) VALUES (
        :usuario, :password, :rol, :id_cliente, 'Activo'
    )");

    $stmt->execute([
        ':usuario' => $data['usuario'],
        ':password' => $data['password'],
        ':rol' => $data['rol'],
        ':id_cliente' => $data['id_cliente']
    ]);

    return $this->pdo->lastInsertId();
}


}
