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

    // Seguro de Vida (solo uno permitido)
    public function obtenerSegurosVida($idCliente) {
        $sql = "SELECT v.*
                FROM seguro_vida v
                INNER JOIN solicitud s ON s.idSeguroVida = v.id_vida
                WHERE v.id_cliente = :idCliente
                  AND s.estatus = 'Activo'
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idCliente' => $idCliente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Seguros de Auto (varios posibles)
    public function obtenerSegurosAuto($idCliente) {
        $sql = "SELECT a.*
                FROM seguro_auto a
                INNER JOIN solicitud s ON s.idSeguroAuto = a.id_auto
                WHERE a.id_cliente = :idCliente
                  AND s.estatus = 'Activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idCliente' => $idCliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Seguros de Robo (varios posibles)
    public function obtenerSegurosRobo($idCliente) {
        $sql = "SELECT r.*
                FROM seguro_robo r
                INNER JOIN solicitud s ON s.idSeguroRobo = r.id_robo
                WHERE r.id_cliente = :idCliente
                  AND s.estatus = 'Activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idCliente' => $idCliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Seguros de Incendio (varios posibles)
    public function obtenerSegurosIncendio($idCliente) {
        $sql = "SELECT i.*
                FROM seguro_incendio i
                INNER JOIN solicitud s ON s.idSeguroIncendio = i.id_incendio
                WHERE i.id_cliente = :idCliente
                  AND s.estatus = 'Activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idCliente' => $idCliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}