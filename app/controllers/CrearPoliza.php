<?php
session_start();
require_once __DIR__ . '/../Core/SessionGuard.php';
SessionGuard::requireRole('agente');

require_once __DIR__ . '/../models/Agente.php';

try {
    $agenteModel = new Agente();
    $idAgente   = $_SESSION['id_agente'] ?? null;
    $idSucursal = $_SESSION['id_sucursal'] ?? null;

    // Si no tenemos id_sucursal en sesión, lo buscamos en BD
    if ($idSucursal === null && $idAgente !== null) {
        $perfil = $agenteModel->obtenerPerfil($idAgente);
        $idSucursal = $perfil['id_sucursal'];
    }

    $tipoCliente = $_POST['tipoCliente'] ?? 'nuevo';
    $idCliente   = null;

    if ($tipoCliente === 'existente') {
        // Buscar cliente por CURP
        $cliente = $agenteModel->buscarClientePorCurp($_POST['curpBusqueda']);
        if (!$cliente) {
            $_SESSION['crear_error'] = "No se encontró cliente con ese CURP.";
            header("Location: ../views/dashboard-agente.php#crear");
            exit;
        }
        $idCliente = $cliente['id_cliente'];

        // Validar asignación
        if (!$agenteModel->validarAsignacionCliente($idCliente, $idAgente)) {
            $_SESSION['crear_error'] = "Este cliente está asignado con otro agente.";
            header("Location: ../views/dashboard-agente.php#crear");
            exit;
        }

    } else {
        // Crear cliente nuevo
        $idCliente = $agenteModel->crearCliente([
            ':nombre'          => $_POST['nombre'],
            ':apellidoPaterno' => $_POST['apellidoPaterno'],
            ':apellidoMaterno' => $_POST['apellidoMaterno'],
            ':direccion'       => $_POST['direccion'],
            ':curp'            => $_POST['curp'],
            ':rfc'             => $_POST['rfc'],
            ':telefono'        => $_POST['telefono']
        ]);

        // Crear usuario para cliente nuevo
        $agenteModel->crearUsuarioCliente([
            ':usuario'    => $_POST['usuario'],
            ':contrasena' => $_POST['passwordCliente'],
            ':correo'     => $_POST['correo'],
            ':id_cliente' => $idCliente
        ]);
    }

    // Crear seguro según tipo
    $tipoSeguro = $_POST['idTipoSeguro'];
    $idSeguro   = null;
    $columnaSeguro = null;

    switch ($tipoSeguro) {
        case 'vida':
            if ($agenteModel->tieneSeguroVida($idCliente)) {
                $_SESSION['crear_error'] = "Este cliente ya tiene un seguro de vida registrado.";
                header("Location: ../views/dashboard-agente.php#crear");
                exit;
            }

            $idSeguro = $agenteModel->crearSeguroVida([
                ':id_cliente'   => $idCliente,
                ':edad'         => $_POST['edad'],
                ':enfermedades' => $_POST['enfermedades_preexistentes'],
                ':folio'        => $_POST['folio_vida'],
                ':valor'        => $_POST['valor_asegurado'],
                ':comision'     => $_POST['porcentaje_comision']
            ]);
            $columnaSeguro = 'idSeguroVida';
            break;
        case 'auto':
            $idSeguro = $agenteModel->crearSeguroAuto([
                ':id_cliente' => $idCliente,
                ':matricula'  => $_POST['matricula'],
                ':modelo'     => $_POST['modelo'],
                ':anio'       => $_POST['anio'],
                ':valor'      => $_POST['valor_factura'],
                ':comision'   => $_POST['porcentaje_comision']
            ]);
            $columnaSeguro = 'idSeguroAuto';
            break;

        case 'robo':
            $idSeguro = $agenteModel->crearSeguroRobo([
                ':id_cliente' => $idCliente,
                ':objeto'     => $_POST['tipo_objeto'],
                ':medidas'    => $_POST['medidas_seguridad'],
                ':valor'      => $_POST['valor_articulo'],
                ':comision'   => $_POST['porcentaje_comision']
            ]);
            $columnaSeguro = 'idSeguroRobo';
            break;

        case 'incendio':
            $idSeguro = $agenteModel->crearSeguroIncendio([
                ':id_cliente'  => $idCliente,
                ':valor'       => $_POST['valor_vivienda'],
                ':antiguedad'  => $_POST['antiguedad'],
                ':nivel'       => $_POST['nivel_incendio'],
                ':causa'       => $_POST['causa_probable'],
                ':tipo'        => $_POST['tipo_construccion'],
                ':comision'    => $_POST['porcentaje_comision']
            ]);
            $columnaSeguro = 'idSeguroIncendio';
            break;
    }

    // Crear solicitud
    $agenteModel->crearSolicitud([
        ':id_cliente' => $idCliente,
        ':id_agente'  => $idAgente,
        ':id_sucursal'=> $idSucursal,
        ':fecha'      => $_POST['fechaRecepcion'],
        ':idSeguro'   => $idSeguro
    ], $columnaSeguro);

    $_SESSION['crear_success']   = "Póliza registrada correctamente.";
    $_SESSION['active_section']  = 'crear';
    header("Location: ../views/dashboard-agente.php#crear");
    exit;

} catch (Exception $e) {
    $_SESSION['crear_error'] = "Error al registrar póliza: " . $e->getMessage();
    $_SESSION['active_section'] = 'crear';
    header("Location: ../views/dashboard-agente.php#crear");
    exit;
}
