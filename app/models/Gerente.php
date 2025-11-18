<?php
require_once __DIR__ . '/../Core/DB.php';

class Gerente {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::connect();
    }

    public function obtenerPerfil($idGerente) {
        $stmt = $this->pdo->prepare(
            "SELECT 
                g.nombre,
                g.correo,
                s.id_sucursal,
                s.ciudad,
                s.estado,
                s.direccion
            FROM gerente g
            LEFT JOIN sucursal s ON g.id_sucursal = s.id_sucursal
            WHERE g.id_gerente = :id"
        );
        $stmt->execute([':id' => $idGerente]);
        return $stmt->fetch();
    }


    public function listarClientes() {
        $stmt = $this->pdo->query(
            "SELECT 
                c.*, 
                ts.nombre AS seguro, 
                sol.estatus AS estatusSolicitud,
                sol.fechaRecepcion,
                sol.cantidadAsegurada,
                a.nombre AS nombreAgente
            FROM cliente c
            LEFT JOIN solicitud sol ON c.id_cliente = sol.idCliente
            LEFT JOIN tipo_seguro ts ON sol.idTipoSeguro = ts.id_tipo_seguro
            LEFT JOIN agente a ON sol.idAgente = a.id_agente"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
//modificar
    public function buscarClientesPorCampo($campo, $valor) {
    $allowed = ['nombre','curp','rfc','seguro'];
    if (!in_array($campo, $allowed)) return [];
    $sql = "SELECT c.*, ts.nombre AS seguro, sol.estatus, c.estatus AS estatusCliente
            FROM cliente c
            LEFT JOIN solicitud sol ON c.id_cliente = sol.idCliente
            LEFT JOIN tipo_seguro ts ON sol.idTipoSeguro = ts.id_tipo_seguro
            WHERE $campo LIKE :val";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':val' => "%$valor%"]);
    return $stmt->fetchAll();
}
    public function listarSucursales() {
    $stmt = $this->pdo->query("SELECT id_sucursal, codigoSucursal, ciudad FROM sucursal");
    return $stmt->fetchAll();
}

}