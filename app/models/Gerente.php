<?php
require_once __DIR__ . '/../Core/DB.php';

class Gerente {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::connect();
    }

    public function obtenerPerfil($idGerente) {
        $sql = "SELECT g.id_gerente, g.nombre,
                       s.id_sucursal, s.ciudad, s.estado, s.direccion,
                       u.usuario, u.correo
                FROM gerente g
                INNER JOIN sucursal s ON g.id_sucursal = s.id_sucursal
                INNER JOIN usuarios u ON u.id_gerente = g.id_gerente
                WHERE g.id_gerente = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idGerente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function crearAgente($nombre, $sueldobase, $id_sucursal, $usuario, $correo, $contrasena) {
        try {
            // 1. Insertar en agente
            $stmt = $this->pdo->prepare("
                INSERT INTO agente (nombre, sueldoBase, id_sucursal)
                VALUES (:nombre, :sueldobase, :id_sucursal)
            ");
            $stmt->execute([
                ':nombre'     => $nombre,
                ':sueldobase' => $sueldobase,
                ':id_sucursal'=> $id_sucursal
            ]);

            // 2. Obtener id_agente recién creado
            $idAgente = $this->pdo->lastInsertId();

            // 3. Insertar en usuarios
            $stmt2 = $this->pdo->prepare("
                INSERT INTO usuarios (usuario, contrasena, correo, rol, id_agente, primer_login)
                VALUES (:usuario, :contrasena, :correo, 'agente', :id_agente, 1)
            ");
            return $stmt2->execute([
                ':usuario'    => $usuario,
                ':contrasena' => $contrasena,
                ':correo'     => $correo,
                ':id_agente'  => $idAgente
            ]);

        } catch (PDOException $e) {
            echo "Error al crear agente: " . $e->getMessage();
            return false;
        }
    }
    //LEER
    // Agentes
    public function listarAgentes() {
        $sql = "SELECT a.id_agente, a.nombre, a.sueldoBase,
                    s.id_sucursal, s.codigoSucursal, s.ciudad, s.estado,
                    u.usuario, u.correo
                FROM agente a
                INNER JOIN sucursal s ON a.id_sucursal = s.id_sucursal
                INNER JOIN usuarios u ON u.id_agente = a.id_agente";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Clientes con seguro de vida
    public function listarClientesVida() {
        $sql = "SELECT 
                    c.id_cliente,
                    sol.id_solicitud,              
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    sv.edad, sv.folio_vida, sv.fecha_solicitud,
                    sv.valor_asegurado,                 -- NUEVO
                    sv.porcentaje_comision,             -- NUEVO
                    a.nombre AS nombre_agente,
                    s.codigoSucursal,
                    sol.estatus AS estado_solicitud
                FROM seguro_vida sv
                INNER JOIN cliente c ON sv.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                INNER JOIN solicitud sol ON sol.idSeguroVida = sv.id_vida
                INNER JOIN agente a ON sol.id_agente = a.id_agente
                INNER JOIN sucursal s ON sol.id_sucursal = s.id_sucursal";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    // Clientes con seguro de auto
    public function listarClientesAuto() {
        $sql = "SELECT 
                    c.id_cliente,
                    sol.id_solicitud,              
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    sa.matricula, sa.modelo, sa.anio, sa.valor_factura, sa.fecha_solicitud,
                    sa.porcentaje_comision,              -- NUEVO
                    a.nombre AS nombre_agente,
                    s.codigoSucursal,
                    sol.estatus AS estado_solicitud
                FROM seguro_auto sa
                INNER JOIN cliente c ON sa.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                INNER JOIN solicitud sol ON sol.idSeguroAuto = sa.id_auto
                INNER JOIN agente a ON sol.id_agente = a.id_agente
                INNER JOIN sucursal s ON sol.id_sucursal = s.id_sucursal";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    // Clientes con seguro de robo
    public function listarClientesRobo() {
        $sql = "SELECT 
                    c.id_cliente,
                    sol.id_solicitud,             
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    sr.tipo_objeto, sr.medidas_seguridad, sr.fecha_solicitud,
                    sr.valor_articulo,                 -- NUEVO
                    sr.porcentaje_comision,            -- NUEVO
                    a.nombre AS nombre_agente,
                    s.codigoSucursal,
                    sol.estatus AS estado_solicitud
                FROM seguro_robo sr
                INNER JOIN cliente c ON sr.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                INNER JOIN solicitud sol ON sol.idSeguroRobo = sr.id_robo
                INNER JOIN agente a ON sol.id_agente = a.id_agente
                INNER JOIN sucursal s ON sol.id_sucursal = s.id_sucursal";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Clientes con seguro de incendio
    public function listarClientesIncendio() {
        $sql = "SELECT 
                    c.id_cliente,
                    sol.id_solicitud,        
                    c.nombre, c.apellidoPaterno, c.apellidoMaterno,
                    c.curp, c.rfc, c.telefono,
                    u.usuario, u.correo,
                    si.valor_vivienda, si.antiguedad, si.nivel_incendio,
                    si.causa_probable, si.tipo_construccion, si.fecha_solicitud,
                    si.porcentaje_comision,              -- NUEVO
                    a.nombre AS nombre_agente,
                    s.codigoSucursal,
                    sol.estatus AS estado_solicitud
                FROM seguro_incendio si
                INNER JOIN cliente c ON si.id_cliente = c.id_cliente
                INNER JOIN usuarios u ON u.id_cliente = c.id_cliente
                INNER JOIN solicitud sol ON sol.idSeguroIncendio = si.id_incendio
                INNER JOIN agente a ON sol.id_agente = a.id_agente
                INNER JOIN sucursal s ON sol.id_sucursal = s.id_sucursal";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    //Metodos para actualizar!!!!!!!!!
        /* ===================== AGENTES ===================== */
    public function updateAgente($id, $valores) {
        try {
            // 1. Traducir codigoSucursal a id_sucursal
            $stmtSucursal = $this->pdo->prepare("SELECT id_sucursal FROM sucursal WHERE codigoSucursal = :codigo");
            $stmtSucursal->execute([':codigo' => $valores['codigoSucursal']]);
            $idSucursal = $stmtSucursal->fetchColumn();

            if (!$idSucursal) {
                throw new Exception("Sucursal no encontrada para código: " . $valores['codigoSucursal']);
            }

            // 2. Actualizar datos en la tabla agente
            $stmt = $this->pdo->prepare("
                UPDATE agente 
                SET nombre = :nombre, sueldoBase = :sueldoBase, id_sucursal = :id_sucursal
                WHERE id_agente = :id
            ");
            $stmt->execute([
                ':nombre'      => $valores['nombre'],
                ':sueldoBase'  => $valores['sueldoBase'],
                ':id_sucursal' => $idSucursal,
                ':id'          => $id
            ]);

            // 3. Actualizar usuario y correo en la tabla usuarios
            $stmt2 = $this->pdo->prepare("
                UPDATE usuarios 
                SET usuario = :usuario, correo = :correo
                WHERE id_agente = :id
            ");
            $stmt2->execute([
                ':usuario' => $valores['usuario'],
                ':correo'  => $valores['correo'],
                ':id'      => $id
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error al actualizar agente: " . $e->getMessage());
            return false;
        }
    }



    /* ===================== CLIENTES VIDA ===================== */
    public function updateClienteVida($id, $valores) {
        try {
            // 1. Traducir codigoSucursal a id_sucursal
            $stmtSucursal = $this->pdo->prepare("SELECT id_sucursal FROM sucursal WHERE codigoSucursal = :codigo");
            $stmtSucursal->execute([':codigo' => $valores['codigoSucursal']]);
            $idSucursal = $stmtSucursal->fetchColumn();

            if (!$idSucursal) {
                throw new Exception("Sucursal no encontrada para código: " . $valores['codigoSucursal']);
            }

            // 2. Actualizar datos en cliente
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

            // 3. Actualizar datos en seguro_vida (incluyendo nuevos campos)
            $stmtVida = $this->pdo->prepare("
                UPDATE seguro_vida 
                SET edad = :edad,
                    folio_vida = :folio_vida,
                    fecha_solicitud = :fecha_solicitud,
                    valor_asegurado = :valor_asegurado,          -- NUEVO
                    porcentaje_comision = :porcentaje_comision   -- NUEVO
                WHERE id_cliente = :id
            ");
            $stmtVida->execute([
                ':edad'               => $valores['edad'],
                ':folio_vida'         => $valores['folio_vida'],
                ':fecha_solicitud'    => $valores['fecha_solicitud'],
                ':valor_asegurado'    => $valores['valor_asegurado'],
                ':porcentaje_comision'=> $valores['porcentaje_comision'],
                ':id'                 => $id
            ]);

            // 4. Actualizar usuario y correo en usuarios
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

            // 5. Actualizar sucursal en solicitud
            $stmtSolicitud = $this->pdo->prepare("
                UPDATE solicitud 
                SET id_sucursal = :id_sucursal
                WHERE id_cliente = :id AND idSeguroVida IS NOT NULL
            ");
            $stmtSolicitud->execute([
                ':id_sucursal' => $idSucursal,
                ':id'          => $id
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error al actualizar cliente vida: " . $e->getMessage());
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
    public function updateClienteRobo($id, $valores) {
        try {
            // 1. Traducir codigoSucursal a id_sucursal
            $stmtSucursal = $this->pdo->prepare("SELECT id_sucursal FROM sucursal WHERE codigoSucursal = :codigo");
            $stmtSucursal->execute([':codigo' => $valores['codigoSucursal']]);
            $idSucursal = $stmtSucursal->fetchColumn();
            if (!$idSucursal) {
                throw new Exception("Sucursal no encontrada para código: " . $valores['codigoSucursal']);
            }

            // 2. Actualizar datos en cliente
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

            // 3. Actualizar datos en seguro_robo
            $stmtRobo = $this->pdo->prepare("
                UPDATE seguro_robo 
                SET tipo_objeto = :tipo_objeto,
                    medidas_seguridad = :medidas_seguridad,
                    valor_articulo = :valor_articulo,          -- NUEVO
                    porcentaje_comision = :porcentaje_comision,-- NUEVO
                    fecha_solicitud = :fecha_solicitud
                WHERE id_cliente = :id
            ");
            $stmtRobo->execute([
                ':tipo_objeto'        => $valores['tipo_objeto'],
                ':medidas_seguridad'  => $valores['medidas_seguridad'],
                ':valor_articulo'     => $valores['valor_articulo'],
                ':porcentaje_comision'=> $valores['porcentaje_comision'],
                ':fecha_solicitud'    => $valores['fecha_solicitud'],
                ':id'                 => $id
            ]);

            // 4. Actualizar usuario y correo en usuarios
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

            // 5. Actualizar sucursal en solicitud
            $stmtSolicitud = $this->pdo->prepare("
                UPDATE solicitud 
                SET id_sucursal = :id_sucursal
                WHERE id_cliente = :id AND idSeguroRobo IS NOT NULL
            ");
            $stmtSolicitud->execute([
                ':id_sucursal' => $idSucursal,
                ':id'          => $id
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error al actualizar cliente robo: " . $e->getMessage());
            return false;
        }
    }

    /* ===================== CLIENTES INCENDIO ===================== */
    public function updateClienteIncendio($id, $valores) {
        try {
            // 1. Traducir codigoSucursal a id_sucursal
            $stmtSucursal = $this->pdo->prepare("SELECT id_sucursal FROM sucursal WHERE codigoSucursal = :codigo");
            $stmtSucursal->execute([':codigo' => $valores['codigoSucursal']]);
            $idSucursal = $stmtSucursal->fetchColumn();
            if (!$idSucursal) {
                throw new Exception("Sucursal no encontrada para código: " . $valores['codigoSucursal']);
            }

            // 2. Actualizar datos en cliente
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

            // 3. Actualizar datos en seguro_incendio (incluyendo % comisión)
            $stmtInc = $this->pdo->prepare("
                UPDATE seguro_incendio 
                SET valor_vivienda = :valor_vivienda,
                    antiguedad = :antiguedad,
                    nivel_incendio = :nivel_incendio,
                    causa_probable = :causa_probable,
                    tipo_construccion = :tipo_construccion,
                    fecha_solicitud = :fecha_solicitud,
                    porcentaje_comision = :porcentaje_comision   -- NUEVO
                WHERE id_cliente = :id
            ");
            $stmtInc->execute([
                ':valor_vivienda'    => $valores['valor_vivienda'],
                ':antiguedad'        => $valores['antiguedad'],
                ':nivel_incendio'    => $valores['nivel_incendio'],
                ':causa_probable'    => $valores['causa_probable'],
                ':tipo_construccion' => $valores['tipo_construccion'],
                ':fecha_solicitud'   => $valores['fecha_solicitud'],
                ':porcentaje_comision'=> $valores['porcentaje_comision'],
                ':id'                => $id
            ]);

            // 4. Actualizar usuario y correo en usuarios
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

            // 5. Actualizar sucursal en solicitud
            $stmtSolicitud = $this->pdo->prepare("
                UPDATE solicitud 
                SET id_sucursal = :id_sucursal
                WHERE id_cliente = :id AND idSeguroIncendio IS NOT NULL
            ");
            $stmtSolicitud->execute([
                ':id_sucursal' => $idSucursal,
                ':id'          => $id
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error al actualizar cliente incendio: " . $e->getMessage());
            return false;
        }
    }
    /* seccion eliminar  */
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
