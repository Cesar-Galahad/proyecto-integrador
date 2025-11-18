<?php
require_once __DIR__ . '/../Core/DB.php';

class Agente {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::connect();
    }

public function crear($nombre, $correo, $sueldoBase, $idSucursal) {
    // Verificar si ya existe el correo
    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM agente WHERE correo = :correo");
    $stmt->execute([':correo' => $correo]);
    if ($stmt->fetchColumn() > 0) {
        // No lanzar excepción, devolver null
        return null;
    }

    // Insertar si no existe
    $stmt = $this->pdo->prepare(
        "INSERT INTO agente (nombre, correo, sueldoBase, id_sucursal)
         VALUES (:nombre, :correo, :sueldoBase, :idSucursal)"
    );
    $stmt->execute([
        ':nombre'     => $nombre,
        ':correo'     => $correo,
        ':sueldoBase' => $sueldoBase,
        ':idSucursal' => $idSucursal
    ]);

    return $this->pdo->lastInsertId();
    }
    public function obtenerPerfil($idAgente) {
        $sql = "SELECT a.*, s.ciudad, s.estado, s.direccion
                FROM agente a
                LEFT JOIN sucursal s ON a.id_sucursal = s.id_sucursal
                WHERE a.id_agente = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idAgente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

public function listar() {
    $sql = "SELECT 
                a.id_agente,
                a.nombre,
                a.correo,
                a.sueldoBase,
                s.codigoSucursal,
                s.ciudad,
                s.estado,
                s.direccion
            FROM agente a
            INNER JOIN sucursal s ON a.id_sucursal = s.id_sucursal";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}





}