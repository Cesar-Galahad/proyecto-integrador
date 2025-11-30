<?php
// app/Models/Cliente.php
require_once __DIR__ . '/../Core/DB.php';

class Cliente {
private $pdo;

    public function __construct() {
        $this->pdo = DB::connect();
    }
    public function obtenerPerfilCliente($idCliente) {
        $sql = "SELECT 
                    c.id_cliente,
                    c.nombre,
                    c.apellidoPaterno,
                    c.apellidoMaterno,
                    c.curp,
                    c.rfc,
                    c.telefono,
                    c.direccion,
                    u.usuario,
                    u.correo
                FROM cliente c
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                WHERE c.id_cliente = :idCliente
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idCliente' => $idCliente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}