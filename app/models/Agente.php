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
//crear
    public function tieneSeguroVida($idCliente) {
        $sql = "SELECT id_vida FROM seguro_vida WHERE id_cliente = :idCliente";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idCliente' => $idCliente]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

// Buscar cliente por CURP
    public function buscarClientePorCurp($curp) {
        $sql = "SELECT * FROM cliente WHERE curp = :curp";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':curp' => $curp]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear cliente nuevo
    public function crearCliente($datos) {
        $sql = "INSERT INTO cliente (nombre, apellidoPaterno, apellidoMaterno, direccion, curp, rfc, telefono)
                VALUES (:nombre, :apellidoPaterno, :apellidoMaterno, :direccion, :curp, :rfc, :telefono)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datos);
        return $this->pdo->lastInsertId();
    }

    // Crear usuario para cliente nuevo
    public function crearUsuarioCliente($datos) {
        $sql = "INSERT INTO usuarios (usuario, contrasena, correo, rol, id_cliente)
                VALUES (:usuario, :contrasena, :correo, 'cliente', :id_cliente)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datos);
        return $this->pdo->lastInsertId();
    }

    // Validar si cliente ya está asignado a otro agente
    public function validarAsignacionCliente($idCliente, $idAgente) {
        $sql = "SELECT id_agente FROM solicitud WHERE id_cliente = :idCliente AND estatus = 'Activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idCliente' => $idCliente]);
        $asignacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($asignacion && $asignacion['id_agente'] != $idAgente) {
            return false; // asignado a otro agente
        }
        return true; // libre o mismo agente
    }

    // Crear seguro de vida
    public function crearSeguroVida($datos) {
        $sql = "INSERT INTO seguro_vida (id_cliente, edad, enfermedades_preexistentes, folio_vida, valor_asegurado, porcentaje_comision)
                VALUES (:id_cliente, :edad, :enfermedades, :folio, :valor, :comision)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datos);
        return $this->pdo->lastInsertId();
    }

    // Crear seguro de auto
    public function crearSeguroAuto($datos) {
        $sql = "INSERT INTO seguro_auto (id_cliente, matricula, modelo, anio, valor_factura, porcentaje_comision)
                VALUES (:id_cliente, :matricula, :modelo, :anio, :valor, :comision)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datos);
        return $this->pdo->lastInsertId();
    }

    // Crear seguro de robo
    public function crearSeguroRobo($datos) {
        $sql = "INSERT INTO seguro_robo (id_cliente, tipo_objeto, medidas_seguridad, valor_articulo, porcentaje_comision)
                VALUES (:id_cliente, :objeto, :medidas, :valor, :comision)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datos);
        return $this->pdo->lastInsertId();
    }

    // Crear seguro de incendio
    public function crearSeguroIncendio($datos) {
        $sql = "INSERT INTO seguro_incendio (id_cliente, valor_vivienda, antiguedad, nivel_incendio, causa_probable, tipo_construccion, porcentaje_comision)
                VALUES (:id_cliente, :valor, :antiguedad, :nivel, :causa, :tipo, :comision)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datos);
        return $this->pdo->lastInsertId();
    }

    // Crear solicitud
    public function crearSolicitud($datos, $columnaSeguro) {
        $sql = "INSERT INTO solicitud (id_cliente, id_agente, id_sucursal, fecha, estatus, $columnaSeguro)
                VALUES (:id_cliente, :id_agente, :id_sucursal, :fecha, 'Activo', :idSeguro)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datos);
        return $this->pdo->lastInsertId();
    }

    public function listarClientesVidaActivos($idAgente) {
        $sql = "SELECT
                    c.id_cliente,
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    sv.edad,
                    sv.enfermedades_preexistentes,
                    sv.folio_vida,
                    sv.fecha_solicitud,
                    sv.valor_asegurado,
                    sv.porcentaje_comision,
                    s.codigoSucursal,
                    sol.id_solicitud,
                    sol.estatus AS estado_solicitud
                FROM solicitud sol
                INNER JOIN cliente c      ON sol.id_cliente   = c.id_cliente
                INNER JOIN seguro_vida sv ON sol.idSeguroVida = sv.id_vida
                INNER JOIN usuarios u     ON u.id_cliente     = c.id_cliente
                INNER JOIN sucursal s     ON sol.id_sucursal  = s.id_sucursal
                WHERE sol.id_agente = :idAgente
                AND sol.estatus = 'Activo'
                AND sol.idSeguroVida IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarClientesAutoActivos($idAgente) {
        $sql = "SELECT
                    c.id_cliente,
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    sa.matricula, sa.modelo, sa.anio,
                    sa.valor_factura, sa.porcentaje_comision,
                    sa.fecha_solicitud,              -- usa la fecha de la tabla seguro_auto
                    s.codigoSucursal,
                    sol.id_solicitud,
                    sol.estatus AS estado_solicitud
                FROM solicitud sol
                INNER JOIN cliente c      ON sol.id_cliente   = c.id_cliente
                INNER JOIN seguro_auto sa ON sol.idSeguroAuto = sa.id_auto
                INNER JOIN usuarios u     ON u.id_cliente     = c.id_cliente
                INNER JOIN sucursal s     ON sol.id_sucursal  = s.id_sucursal
                WHERE sol.id_agente = :idAgente
                AND sol.estatus = 'Activo'
                AND sol.idSeguroAuto IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarClientesRoboActivos($idAgente) {
        $sql = "SELECT
                    c.id_cliente,
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    sr.tipo_objeto, sr.medidas_seguridad,
                    sr.valor_articulo, sr.porcentaje_comision,
                    sr.fecha_solicitud,
                    s.codigoSucursal,
                    sol.id_solicitud,
                    sol.estatus AS estado_solicitud
                FROM solicitud sol
                INNER JOIN cliente c      ON sol.id_cliente   = c.id_cliente
                INNER JOIN seguro_robo sr ON sol.idSeguroRobo = sr.id_robo
                INNER JOIN usuarios u     ON u.id_cliente     = c.id_cliente
                INNER JOIN sucursal s     ON sol.id_sucursal  = s.id_sucursal
                WHERE sol.id_agente = :idAgente
                AND sol.estatus = 'Activo'
                AND sol.idSeguroRobo IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarClientesIncendioActivos($idAgente) {
        $sql = "SELECT
                    c.id_cliente,
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    si.valor_vivienda, si.antiguedad,
                    si.nivel_incendio, si.causa_probable,
                    si.tipo_construccion, si.porcentaje_comision,
                    si.fecha_solicitud,
                    s.codigoSucursal,
                    sol.id_solicitud,
                    sol.estatus AS estado_solicitud
                FROM solicitud sol
                INNER JOIN cliente c          ON sol.id_cliente       = c.id_cliente
                INNER JOIN seguro_incendio si ON sol.idSeguroIncendio = si.id_incendio
                INNER JOIN usuarios u         ON u.id_cliente         = c.id_cliente
                INNER JOIN sucursal s         ON sol.id_sucursal      = s.id_sucursal
                WHERE sol.id_agente = :idAgente
                AND sol.estatus = 'Activo'
                AND sol.idSeguroIncendio IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarSolicitudesVendidas($idAgente) {
        $sql = "SELECT 
                    sol.id_solicitud,
                    CONCAT(c.nombre, ' ', c.apellidoPaterno, ' ', IFNULL(c.apellidoMaterno,'')) AS cliente,
                    CASE 
                        WHEN sol.idSeguroVida IS NOT NULL THEN 'Vida'
                        WHEN sol.idSeguroAuto IS NOT NULL THEN 'Auto'
                        WHEN sol.idSeguroRobo IS NOT NULL THEN 'Robo'
                        WHEN sol.idSeguroIncendio IS NOT NULL THEN 'Incendio'
                    END AS tipo_seguro,
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
                    sol.fecha AS fecha
                FROM solicitud sol
                INNER JOIN cliente c ON sol.id_cliente = c.id_cliente
                LEFT JOIN seguro_vida     sv ON sol.idSeguroVida     = sv.id_vida
                LEFT JOIN seguro_auto     sa ON sol.idSeguroAuto     = sa.id_auto
                LEFT JOIN seguro_robo     sr ON sol.idSeguroRobo     = sr.id_robo
                LEFT JOIN seguro_incendio si ON sol.idSeguroIncendio = si.id_incendio
                WHERE sol.id_agente = :idAgente
                AND sol.estatus = 'Activo'
                AND (
                    sol.idSeguroVida IS NOT NULL OR
                    sol.idSeguroAuto IS NOT NULL OR
                    sol.idSeguroRobo IS NOT NULL OR
                    sol.idSeguroIncendio IS NOT NULL
                )
                ORDER BY sol.fecha DESC, sol.id_solicitud DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    //buscar

    public function listarClientesActivos($idAgente) {
        $sql = "SELECT DISTINCT
                    c.id_cliente,
                    c.nombre,
                    c.apellidoPaterno,
                    c.apellidoMaterno,
                    c.curp,
                    c.rfc,
                    c.telefono,
                    u.usuario,
                    u.correo,
                    sol.id_solicitud,
                    sol.estatus AS estado_solicitud,
                    s.codigoSucursal,
                    sol.idSeguroVida,
                    sol.idSeguroAuto,
                    sol.idSeguroRobo,
                    sol.idSeguroIncendio,
                    sv.valor_asegurado,
                    sa.valor_factura,
                    sr.valor_articulo,
                    si.valor_vivienda
                FROM solicitud sol
                INNER JOIN cliente c ON sol.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                INNER JOIN sucursal s ON sol.id_sucursal = s.id_sucursal
                LEFT JOIN seguro_vida sv ON sol.idSeguroVida = sv.id_vida
                LEFT JOIN seguro_auto sa ON sol.idSeguroAuto = sa.id_auto
                LEFT JOIN seguro_robo sr ON sol.idSeguroRobo = sr.id_robo
                LEFT JOIN seguro_incendio si ON sol.idSeguroIncendio = si.id_incendio
                WHERE sol.id_agente = :idAgente
                AND sol.estatus = 'Activo'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idAgente' => $idAgente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    //actualizar
    public function updateClienteVida($idCliente, $valores) {
        try {
            // 1. Sucursal
            $stmtSucursal = $this->pdo->prepare("SELECT id_sucursal FROM sucursal WHERE codigoSucursal = :codigo");
            $stmtSucursal->execute([':codigo' => $valores['codigoSucursal']]);
            $idSucursal = $stmtSucursal->fetchColumn();
            if (!$idSucursal) throw new Exception("Sucursal no encontrada para código: " . ($valores['codigoSucursal'] ?? ''));

            // 2. Cliente
            $stmtCliente = $this->pdo->prepare("
                UPDATE cliente
                SET nombre = :nombre,
                    apellidoPaterno = :apellidoPaterno,
                    apellidoMaterno = :apellidoMaterno,
                    curp = :curp,
                    rfc = :rfc,
                    telefono = :telefono
                WHERE id_cliente = :id
            ");
            $stmtCliente->execute([
                ':nombre'          => $valores['nombre'],
                ':apellidoPaterno' => $valores['apellidoPaterno'],
                ':apellidoMaterno' => $valores['apellidoMaterno'],
                ':curp'            => $valores['curp'],
                ':rfc'             => $valores['rfc'],
                ':telefono'        => $valores['telefono'],
                ':id'              => $idCliente
            ]);

            // 3. Seguro_vida
            $stmtVida = $this->pdo->prepare("
                UPDATE seguro_vida
                SET edad = :edad,
                    enfermedades_preexistentes = :enfermedades_preexistentes,
                    folio_vida = :folio_vida,
                    valor_asegurado = :valor_asegurado,
                    porcentaje_comision = :porcentaje_comision,
                    fecha_solicitud = :fecha_solicitud
                WHERE id_cliente = :id
            ");
            $stmtVida->execute([
                ':edad'                        => $valores['edad'],
                ':enfermedades_preexistentes'  => $valores['enfermedades_preexistentes'] ?? null,
                ':folio_vida'                  => $valores['folio_vida'],
                ':valor_asegurado'             => $valores['valor_asegurado'],
                ':porcentaje_comision'         => $valores['porcentaje_comision'],
                ':fecha_solicitud'             => $valores['fecha_solicitud'],
                ':id'                          => $idCliente
            ]);

            // 4. Usuarios
            $stmtUsuario = $this->pdo->prepare("
                UPDATE usuarios
                SET usuario = :usuario, correo = :correo
                WHERE id_cliente = :id
            ");
            $stmtUsuario->execute([
                ':usuario' => $valores['usuario'],
                ':correo'  => $valores['correo'],
                ':id'      => $idCliente
            ]);

            // 5. Solicitud (sucursal)
            $stmtSolicitud = $this->pdo->prepare("
                UPDATE solicitud
                SET id_sucursal = :id_sucursal
                WHERE id_cliente = :id AND idSeguroVida IS NOT NULL
            ");
            $stmtSolicitud->execute([
                ':id_sucursal' => $idSucursal,
                ':id'          => $idCliente
            ]);

            return true;
        } catch (Exception $e) {
            error_log("updateClienteVida (Agente): " . $e->getMessage());
            return false;
        }
    }


    /* ===================== CLIENTES AUTO ===================== */
    public function updateClienteAuto($id, $valores) {
        try {
            // 1) Traducir codigoSucursal -> id_sucursal
            $stmtSucursal = $this->pdo->prepare("SELECT id_sucursal FROM sucursal WHERE codigoSucursal = :codigo");
            $stmtSucursal->execute([':codigo' => $valores['codigoSucursal']]);
            $idSucursal = $stmtSucursal->fetchColumn();
            if (!$idSucursal) {
                throw new Exception("Sucursal no encontrada para código: " . $valores['codigoSucursal']);
            }

            // 2) Actualizar cliente
            $stmtCliente = $this->pdo->prepare("
                UPDATE cliente 
                SET nombre = :nombre,
                    apellidoPaterno = :apellidoPaterno,
                    apellidoMaterno = :apellidoMaterno,
                    curp = :curp,
                    rfc = :rfc,
                    telefono = :telefono
                WHERE id_cliente = :id
            ");
            $stmtCliente->execute([
                ':nombre'          => $valores['nombre'],
                ':apellidoPaterno' => $valores['apellidoPaterno'],
                ':apellidoMaterno' => $valores['apellidoMaterno'],
                ':curp'            => $valores['curp'],
                ':rfc'             => $valores['rfc'],
                ':telefono'        => $valores['telefono'],
                ':id'              => $id
            ]);

            // 3) Actualizar seguro_auto
            $stmtAuto = $this->pdo->prepare("
                UPDATE seguro_auto 
                SET matricula = :matricula,
                    modelo = :modelo,
                    anio = :anio,
                    valor_factura = :valor_factura,
                    porcentaje_comision = :porcentaje_comision,   -- NUEVO
                    fecha_solicitud = :fecha_solicitud
                WHERE id_cliente = :id
            ");
            $stmtAuto->execute([
                ':matricula'          => $valores['matricula'],
                ':modelo'             => $valores['modelo'],
                ':anio'               => $valores['anio'],
                ':valor_factura'      => $valores['valor_factura'],
                ':porcentaje_comision'=> $valores['porcentaje_comision'], // NUEVO
                ':fecha_solicitud'    => $valores['fecha_solicitud'],
                ':id'                 => $id
            ]);


            // 4) Actualizar usuarios
            $stmtUsuario = $this->pdo->prepare("
                UPDATE usuarios 
                SET usuario = :usuario, correo = :correo
                WHERE id_cliente = :id
            ");
            $stmtUsuario->execute([
                ':usuario' => $valores['usuario'],
                ':correo'  => $valores['correo'],
                ':id'      => $id
            ]);

            // 5) Actualizar sucursal en solicitud (solo si es seguro_auto)
            $stmtSolicitud = $this->pdo->prepare("
                UPDATE solicitud 
                SET id_sucursal = :id_sucursal
                WHERE id_cliente = :id AND idSeguroAuto IS NOT NULL
            ");
            $stmtSolicitud->execute([
                ':id_sucursal' => $idSucursal,
                ':id'          => $id
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error al actualizar cliente auto: " . $e->getMessage());
            return false;
        }
    }

    /* ===================== CLIENTES ROBO ===================== */
    public function updateClienteRobo($idCliente, $valores) {
        try {
            // 1. Traducir codigoSucursal a id_sucursal
            $stmtSucursal = $this->pdo->prepare("SELECT id_sucursal FROM sucursal WHERE codigoSucursal = :codigo");
            $stmtSucursal->execute([':codigo' => $valores['codigoSucursal']]);
            $idSucursal = $stmtSucursal->fetchColumn();
            if (!$idSucursal) {
                throw new Exception("Sucursal no encontrada para código: " . ($valores['codigoSucursal'] ?? ''));
            }

            // 2. Actualizar cliente
            $stmtCliente = $this->pdo->prepare("
                UPDATE cliente
                SET nombre = :nombre,
                    apellidoPaterno = :apellidoPaterno,
                    apellidoMaterno = :apellidoMaterno,
                    curp = :curp,
                    rfc = :rfc,
                    telefono = :telefono
                WHERE id_cliente = :id
            ");
            $stmtCliente->execute([
                ':nombre'          => $valores['nombre'],
                ':apellidoPaterno' => $valores['apellidoPaterno'],
                ':apellidoMaterno' => $valores['apellidoMaterno'],
                ':curp'            => $valores['curp'],
                ':rfc'             => $valores['rfc'],
                ':telefono'        => $valores['telefono'],
                ':id'              => $idCliente
            ]);

            // 3. Actualizar seguro_robo
            $stmtRobo = $this->pdo->prepare("
                UPDATE seguro_robo
                SET tipo_objeto = :tipo_objeto,
                    medidas_seguridad = :medidas_seguridad,
                    valor_articulo = :valor_articulo,
                    porcentaje_comision = :porcentaje_comision,
                    fecha_solicitud = :fecha_solicitud
                WHERE id_cliente = :id
            ");
            $stmtRobo->execute([
                ':tipo_objeto'        => $valores['tipo_objeto'],
                ':medidas_seguridad'  => $valores['medidas_seguridad'],
                ':valor_articulo'     => $valores['valor_articulo'],
                ':porcentaje_comision'=> $valores['porcentaje_comision'],
                ':fecha_solicitud'    => $valores['fecha_solicitud'],
                ':id'                 => $idCliente
            ]);

            // 4. Actualizar usuarios
            $stmtUsuario = $this->pdo->prepare("
                UPDATE usuarios
                SET usuario = :usuario, correo = :correo
                WHERE id_cliente = :id
            ");
            $stmtUsuario->execute([
                ':usuario' => $valores['usuario'],
                ':correo'  => $valores['correo'],
                ':id'      => $idCliente
            ]);

            // 5. Actualizar sucursal en solicitud
            $stmtSolicitud = $this->pdo->prepare("
                UPDATE solicitud
                SET id_sucursal = :id_sucursal
                WHERE id_cliente = :id AND idSeguroRobo IS NOT NULL
            ");
            $stmtSolicitud->execute([
                ':id_sucursal' => $idSucursal,
                ':id'          => $idCliente
            ]);

            return true;
        } catch (Exception $e) {
            error_log("updateClienteRobo (Agente): " . $e->getMessage());
            return false;
        }
    }

    /* ===================== CLIENTES INCENDIO ===================== */
    public function updateClienteIncendio($idCliente, $valores) {
        try {
            // 1. Traducir codigoSucursal a id_sucursal
            $stmtSucursal = $this->pdo->prepare("SELECT id_sucursal FROM sucursal WHERE codigoSucursal = :codigo");
            $stmtSucursal->execute([':codigo' => $valores['codigoSucursal']]);
            $idSucursal = $stmtSucursal->fetchColumn();
            if (!$idSucursal) {
                throw new Exception("Sucursal no encontrada para código: " . ($valores['codigoSucursal'] ?? ''));
            }

            // 2. Actualizar cliente
            $stmtCliente = $this->pdo->prepare("
                UPDATE cliente
                SET nombre = :nombre,
                    apellidoPaterno = :apellidoPaterno,
                    apellidoMaterno = :apellidoMaterno,
                    curp = :curp,
                    rfc = :rfc,
                    telefono = :telefono
                WHERE id_cliente = :id
            ");
            $stmtCliente->execute([
                ':nombre'          => $valores['nombre'],
                ':apellidoPaterno' => $valores['apellidoPaterno'],
                ':apellidoMaterno' => $valores['apellidoMaterno'],
                ':curp'            => $valores['curp'],
                ':rfc'             => $valores['rfc'],
                ':telefono'        => $valores['telefono'],
                ':id'              => $idCliente
            ]);

            // 3. Actualizar seguro_incendio
            $stmtIncendio = $this->pdo->prepare("
                UPDATE seguro_incendio
                SET valor_vivienda = :valor_vivienda,
                    antiguedad = :antiguedad,
                    nivel_incendio = :nivel_incendio,
                    causa_probable = :causa_probable,
                    tipo_construccion = :tipo_construccion,
                    porcentaje_comision = :porcentaje_comision,
                    fecha_solicitud = :fecha_solicitud
                WHERE id_cliente = :id
            ");
            $stmtIncendio->execute([
                ':valor_vivienda'     => $valores['valor_vivienda'],
                ':antiguedad'         => $valores['antiguedad'],
                ':nivel_incendio'     => $valores['nivel_incendio'],
                ':causa_probable'     => $valores['causa_probable'],
                ':tipo_construccion'  => $valores['tipo_construccion'],
                ':porcentaje_comision'=> $valores['porcentaje_comision'],
                ':fecha_solicitud'    => $valores['fecha_solicitud'],
                ':id'                 => $idCliente
            ]);

            // 4. Actualizar usuarios
            $stmtUsuario = $this->pdo->prepare("
                UPDATE usuarios
                SET usuario = :usuario, correo = :correo
                WHERE id_cliente = :id
            ");
            $stmtUsuario->execute([
                ':usuario' => $valores['usuario'],
                ':correo'  => $valores['correo'],
                ':id'      => $idCliente
            ]);

            // 5. Actualizar sucursal en solicitud
            $stmtSolicitud = $this->pdo->prepare("
                UPDATE solicitud
                SET id_sucursal = :id_sucursal
                WHERE id_cliente = :id AND idSeguroIncendio IS NOT NULL
            ");
            $stmtSolicitud->execute([
                ':id_sucursal' => $idSucursal,
                ':id'          => $idCliente
            ]);

            return true;
        } catch (Exception $e) {
            error_log("updateClienteIncendio (Agente): " . $e->getMessage());
            return false;
        }
    }
    public function cambiarEstatusSolicitud($idSolicitud, $nuevoEstatus) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE solicitud 
                SET estatus = :estatus 
                WHERE id_solicitud = :id
            ");
            $stmt->execute([
                ':estatus' => $nuevoEstatus,
                ':id'      => $idSolicitud
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Error al cambiar estatus de solicitud: " . $e->getMessage());
            return false;
        }
    }

}
