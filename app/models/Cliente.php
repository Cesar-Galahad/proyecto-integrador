<?php
// app/Models/Cliente.php
require_once __DIR__ . '/../Core/DB.php';

class Cliente {
private $pdo;

    public function __construct() {
        $this->pdo = DB::connect();
    }

    public function crear($data) {
        $stmt = $this->pdo->prepare("INSERT INTO cliente (nombre, apellidoPaterno, apellidoMaterno, curp, rfc, telefono, direccion)
                                     VALUES (:nombre, :apellidoPaterno, :apellidoMaterno, :curp, :rfc, :telefono, :direccion)");
        $stmt->execute($data);
        return $this->pdo->lastInsertId();
    }


}