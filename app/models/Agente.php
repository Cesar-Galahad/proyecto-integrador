<?php
require_once __DIR__ . '/../Core/DB.php';

class Agente {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::connect();
    }
    public function obtenerPerfil($idAgente) {
        $sql = "SELECT a.id_agente, a.nombre, a.sueldoBase,
                       s.id_sucursal, s.ciudad, s.estado, s.direccion,
                       u.usuario, u.correo
                FROM agente a
                INNER JOIN sucursal s ON a.id_sucursal = s.id_sucursal
                INNER JOIN usuarios u ON u.id_agente = a.id_agente
                WHERE a.id_agente = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idAgente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function listarSolicitudesVendidas($idAgente) {
        $sql = "SELECT 
                    sol.id_solicitud,
                    c.nombre AS cliente,
                    CASE 
                    WHEN sol.idSeguroVida IS NOT NULL THEN sv.valor_asegurado
                    WHEN sol.idSeguroAuto IS NOT NULL THEN sa.valor_factura
                    WHEN sol.idSeguroRobo IS NOT NULL THEN sr.valor_articulo
                    WHEN sol.idSeguroIncendio IS NOT NULL THEN si.valor_vivienda
                    END AS cantidad,
                    CASE 
                    WHEN sol.idSeguroVida IS NOT NULL THEN sv.porcentaje_comision
                    WHEN sol.idSeguroAuto IS NOT NULL THEN sa.porcentaje_comision
                    WHEN sol.idSeguroRobo IS NOT NULL THEN sr.porcentaje_comision
                    WHEN sol.idSeguroIncendio IS NOT NULL THEN si.porcentaje_comision
                    END AS porcentaje,
                    sol.fecha_solicitud AS fecha   -- usa el nombre real de tu columna
                FROM solicitud sol
                INNER JOIN cliente c ON sol.id_cliente = c.id_cliente
                LEFT JOIN seguro_vida sv ON sol.idSeguroVida = sv.id_vida
                LEFT JOIN seguro_auto sa ON sol.idSeguroAuto = sa.id_auto
                LEFT JOIN seguro_robo sr ON sol.idSeguroRobo = sr.id_robo
                LEFT JOIN seguro_incendio si ON sol.idSeguroIncendio = si.id_incendio
                WHERE sol.id_agente = :idAgente AND sol.estatus = 'Activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarClientesVidaActivos($idAgente) {
        $sql = "SELECT 
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    sv.edad, sv.folio_vida, sv.fecha_solicitud,
                    sv.valor_asegurado, sv.porcentaje_comision,
                    s.codigoSucursal
                FROM solicitud sol
                INNER JOIN cliente c ON sol.id_cliente = c.id_cliente
                INNER JOIN seguro_vida sv ON sol.idSeguroVida = sv.id_vida
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                INNER JOIN sucursal s ON sol.id_sucursal = s.id_sucursal
                WHERE sol.id_agente = :idAgente AND sol.estatus = 'Activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarClientesAutoActivos($idAgente) {
        $sql = "SELECT 
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    sa.matricula, sa.modelo, sa.anio, sa.fecha_solicitud,
                    sa.valor_factura, sa.porcentaje_comision,
                    s.codigoSucursal
                FROM solicitud sol
                INNER JOIN cliente c ON sol.id_cliente = c.id_cliente
                INNER JOIN seguro_auto sa ON sol.idSeguroAuto = sa.id_auto
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                INNER JOIN sucursal s ON sol.id_sucursal = s.id_sucursal
                WHERE sol.id_agente = :idAgente AND sol.estatus = 'Activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarClientesRoboActivos($idAgente) {
        $sql = "SELECT 
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    sr.tipo_objeto, sr.medidas_seguridad, sr.fecha_solicitud,
                    sr.valor_articulo, sr.porcentaje_comision,
                    s.codigoSucursal
                FROM solicitud sol
                INNER JOIN cliente c ON sol.id_cliente = c.id_cliente
                INNER JOIN seguro_robo sr ON sol.idSeguroRobo = sr.id_robo
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                INNER JOIN sucursal s ON sol.id_sucursal = s.id_sucursal
                WHERE sol.id_agente = :idAgente AND sol.estatus = 'Activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarClientesIncendioActivos($idAgente) {
        $sql = "SELECT 
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    si.valor_vivienda, si.antiguedad, si.nivel_incendio,
                    si.causa_probable, si.tipo_construccion, si.fecha_solicitud,
                    si.porcentaje_comision,
                    s.codigoSucursal
                FROM solicitud sol
                INNER JOIN cliente c ON sol.id_cliente = c.id_cliente
                INNER JOIN seguro_incendio si ON sol.idSeguroIncendio = si.id_incendio
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                INNER JOIN sucursal s ON sol.id_sucursal = s.id_sucursal
                WHERE sol.id_agente = :idAgente AND sol.estatus = 'Activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //actualizar
    public function updateClienteVida($idCliente, $valores) {
        $sql = "UPDATE cliente c
                INNER JOIN seguro_vida sv ON c.id_cliente = sv.id_cliente
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                SET c.nombre = :nombre,
                    c.apellidoPaterno = :apellidoPaterno,
                    c.apellidoMaterno = :apellidoMaterno,
                    c.curp = :curp,
                    c.rfc = :rfc,
                    c.telefono = :telefono,
                    u.usuario = :usuario,
                    u.correo = :correo,
                    sv.edad = :edad,
                    sv.folio_vida = :folio_vida,
                    sv.fecha_solicitud = :fecha_solicitud,
                    sv.valor_asegurado = :valor_asegurado,
                    sv.porcentaje_comision = :porcentaje_comision
                WHERE c.id_cliente = :idCliente";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $valores['nombre'],
            ':apellidoPaterno' => $valores['apellidoPaterno'],
            ':apellidoMaterno' => $valores['apellidoMaterno'],
            ':curp' => $valores['curp'],
            ':rfc' => $valores['rfc'],
            ':telefono' => $valores['telefono'],
            ':usuario' => $valores['usuario'],
            ':correo' => $valores['correo'],
            ':edad' => $valores['edad'],
            ':folio_vida' => $valores['folio_vida'],
            ':fecha_solicitud' => $valores['fecha_solicitud'],
            ':valor_asegurado' => $valores['valor_asegurado'],
            ':porcentaje_comision' => $valores['porcentaje_comision'],
            ':idCliente' => $idCliente
        ]);
    }
    public function updateClienteAuto($idCliente, $valores) {
        try {
            // 1. Actualizar datos en cliente
            $stmtCliente = $this->pdo->prepare("
                UPDATE cliente
                SET nombre = :nombre,
                    apellidoPaterno = :apellidoPaterno,
                    apellidoMaterno = :apellidoMaterno,
                    direccion = :direccion,
                    curp = :curp,
                    rfc = :rfc,
                    telefono = :telefono
                WHERE id_cliente = :idCliente
            ");
            $stmtCliente->execute([
                ':nombre' => $valores['nombre'],
                ':apellidoPaterno' => $valores['apellidoPaterno'],
                ':apellidoMaterno' => $valores['apellidoMaterno'],
                ':direccion' => $valores['direccion'],
                ':curp' => $valores['curp'],
                ':rfc' => $valores['rfc'],
                ':telefono' => $valores['telefono'],
                ':idCliente' => $idCliente
            ]);

            // 2. Actualizar datos en usuarios
            $stmtUsuario = $this->pdo->prepare("
                UPDATE usuarios
                SET usuario = :usuario,
                    correo = :correo
                WHERE id_cliente = :idCliente
            ");
            $stmtUsuario->execute([
                ':usuario' => $valores['usuario'],
                ':correo' => $valores['correo'],
                ':idCliente' => $idCliente
            ]);

            // 3. Actualizar datos en seguro_auto
            $stmtAuto = $this->pdo->prepare("
                UPDATE seguro_auto
                SET matricula = :matricula,
                    modelo = :modelo,
                    anio = :anio,
                    valor_factura = :valor_factura,
                    porcentaje_comision = :porcentaje_comision,
                    fecha_solicitud = :fecha_solicitud
                WHERE id_cliente = :idCliente
            ");
            $stmtAuto->execute([
                ':matricula' => $valores['matricula'],
                ':modelo' => $valores['modelo'],
                ':anio' => $valores['anio'],
                ':valor_factura' => $valores['valor_factura'],
                ':porcentaje_comision' => $valores['porcentaje_comision'],
                ':fecha_solicitud' => $valores['fecha_solicitud'],
                ':idCliente' => $idCliente
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error al actualizar cliente auto: " . $e->getMessage());
            return false;
        }
    }
    public function updateClienteRobo($idCliente, $valores) {
        $sql = "UPDATE cliente c
                INNER JOIN seguro_robo sr ON c.id_cliente = sr.id_cliente
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                SET c.nombre = :nombre,
                    c.apellidoPaterno = :apellidoPaterno,
                    c.apellidoMaterno = :apellidoMaterno,
                    c.curp = :curp,
                    c.rfc = :rfc,
                    c.telefono = :telefono,
                    u.usuario = :usuario,
                    u.correo = :correo,
                    sr.tipo_objeto = :tipo_objeto,
                    sr.medidas_seguridad = :medidas_seguridad,
                    sr.valor_articulo = :valor_articulo,
                    sr.porcentaje_comision = :porcentaje_comision,
                    sr.fecha_solicitud = :fecha_solicitud
                WHERE c.id_cliente = :idCliente";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $valores['nombre'],
            ':apellidoPaterno' => $valores['apellidoPaterno'],
            ':apellidoMaterno' => $valores['apellidoMaterno'],
            ':curp' => $valores['curp'],
            ':rfc' => $valores['rfc'],
            ':telefono' => $valores['telefono'],
            ':usuario' => $valores['usuario'],
            ':correo' => $valores['correo'],
            ':tipo_objeto' => $valores['tipo_objeto'],
            ':medidas_seguridad' => $valores['medidas_seguridad'],
            ':valor_articulo' => $valores['valor_articulo'],
            ':porcentaje_comision' => $valores['porcentaje_comision'],
            ':fecha_solicitud' => $valores['fecha_solicitud'],
            ':idCliente' => $idCliente
        ]);
    }
    public function updateClienteIncendio($idCliente, $valores) {
        $sql = "UPDATE cliente c
                INNER JOIN seguro_incendio si ON c.id_cliente = si.id_cliente
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                SET c.nombre = :nombre,
                    c.apellidoPaterno = :apellidoPaterno,
                    c.apellidoMaterno = :apellidoMaterno,
                    c.curp = :curp,
                    c.rfc = :rfc,
                    c.telefono = :telefono,
                    u.usuario = :usuario,
                    u.correo = :correo,
                    si.valor_vivienda = :valor_vivienda,
                    si.antiguedad = :antiguedad,
                    si.nivel_incendio = :nivel_incendio,
                    si.causa_probable = :causa_probable,
                    si.tipo_construccion = :tipo_construccion,
                    si.porcentaje_comision = :porcentaje_comision,
                    si.fecha_solicitud = :fecha_solicitud
                WHERE c.id_cliente = :idCliente";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => $valores['nombre'],
            ':apellidoPaterno' => $valores['apellidoPaterno'],
            ':apellidoMaterno' => $valores['apellidoMaterno'],
            ':curp' => $valores['curp'],
            ':rfc' => $valores['rfc'],
            ':telefono' => $valores['telefono'],
            ':usuario' => $valores['usuario'],
            ':correo' => $valores['correo'],
            ':valor_vivienda' => $valores['valor_vivienda'],
            ':antiguedad' => $valores['antiguedad'],
            ':nivel_incendio' => $valores['nivel_incendio'],
            ':causa_probable' => $valores['causa_probable'],
            ':tipo_construccion' => $valores['tipo_construccion'],
            ':porcentaje_comision' => $valores['porcentaje_comision'],
            ':fecha_solicitud' => $valores['fecha_solicitud'],
            ':idCliente' => $idCliente
        ]);
    }
}
