<?php
// app/Models/Solicitud.php
require_once __DIR__ . '/../Core/DB.php';

class Solicitud {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::connect();
    }

    public function listarClientesPorAgente($idAgente) {
        $sql = "SELECT 
                    s.folio,
                    c.id_cliente,
                    c.nombre,
                    c.apellidoPaterno,
                    c.apellidoMaterno,
                    c.curp,
                    c.rfc,
                    c.telefono,
                    c.direccion,
                    ts.nombre AS seguro,
                    s.fechaRecepcion,
                    s.cantidadAsegurada,
                    s.estatus AS estatusSolicitud
                FROM solicitud s
                INNER JOIN cliente c ON s.idCliente = c.id_cliente
                INNER JOIN tipo_seguro ts ON s.idTipoSeguro = ts.id_tipo_seguro
                WHERE s.idAgente = :idAgente";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  public function listarPorAgente($idAgente) {
        $sql = "SELECT 
                    s.id_solicitud,
                    s.folio,
                    s.fechaRecepcion,
                    s.cantidadAsegurada,
                    ts.nombre AS tipoSeguro,
                    ts.porcentajeComision,
                    (s.cantidadAsegurada * ts.porcentajeComision / 100) AS comisionGenerada
                FROM solicitud s
                INNER JOIN tipo_seguro ts ON s.idTipoSeguro = ts.id_tipo_seguro
                WHERE s.idAgente = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public function create($data) {
    $stmt = $this->pdo->prepare("INSERT INTO solicitud (
        folio, fecharecepcion, cantidadasegurada,
        idcliente, idsucursal, idagente, idtiposeguro, estatus
    ) VALUES (
        :folio, :fecharecepcion, :cantidadasegurada,
        :idcliente, :idsucursal, :idagente, :idtiposeguro, :estatus
    )");
    $stmt->execute($data);
    return $this->pdo->lastInsertId();
}



  
}