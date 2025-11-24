<?php
session_start();
require_once __DIR__ . '/../Core/SessionGuard.php';
SessionGuard::requireRole('agente');

$pdo = new PDO("mysql:host=localhost;dbname=nexoseguros;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$idAgente   = $_SESSION['id_agente'] ?? null;
$idSucursal = $_SESSION['id_sucursal'] ?? null;

// Si no tenemos id_sucursal en sesión, lo buscamos en BD
if ($idSucursal === null && $idAgente !== null) {
    $stmt = $pdo->prepare("SELECT id_sucursal FROM agente WHERE id_agente = :idAgente");
    $stmt->execute([':idAgente' => $idAgente]);
    $idSucursal = $stmt->fetchColumn();
}

// 1) Buscar cliente por CURP
$stmt = $pdo->prepare("SELECT id_cliente FROM cliente WHERE curp = :curp");
$stmt->execute([':curp' => $_POST['curp']]);
$clienteExistente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($clienteExistente) {
    $idCliente = $clienteExistente['id_cliente'];
} else {
    // Insertar cliente nuevo
    $stmt = $pdo->prepare("INSERT INTO cliente (nombre, apellidoPaterno, apellidoMaterno, direccion, curp, rfc, telefono) 
                           VALUES (:nombre, :apellidoPaterno, :apellidoMaterno, :direccion, :curp, :rfc, :telefono)");
    $stmt->execute([
        ':nombre'          => $_POST['nombre'],
        ':apellidoPaterno' => $_POST['apellidoPaterno'],
        ':apellidoMaterno' => $_POST['apellidoMaterno'],
        ':direccion'       => $_POST['direccion'],
        ':curp'            => $_POST['curp'],
        ':rfc'             => $_POST['rfc'],
        ':telefono'        => $_POST['telefono']
    ]);
    $idCliente = $pdo->lastInsertId();
}

// 2) Insertar usuario solo si no existe
$stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE usuario = :usuario");
$stmt->execute([':usuario' => $_POST['usuario']]);
$usuarioExistente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuarioExistente) {
    $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, contrasena, correo, id_cliente) 
                           VALUES (:usuario, :contrasena, :correo, :id_cliente)");
    $stmt->execute([
        ':usuario'    => $_POST['usuario'],
        ':contrasena' => $_POST['passwordCliente'], // texto plano como pediste
        ':correo'     => $_POST['correo'],
        ':id_cliente' => $idCliente
    ]);
}

// 3) Insertar seguro según tipo
$tipo       = $_POST['idTipoSeguro'];
$idSeguro   = null;
$columnaSeguro = null;

if ($tipo === 'vida') {
    $stmt = $pdo->prepare("INSERT INTO seguro_vida (id_cliente, edad, enfermedades_preexistentes, folio_vida, valor_asegurado, porcentaje_comision) 
                           VALUES (:id_cliente, :edad, :enfermedades, :folio, :valor, :comision)");
    $stmt->execute([
        ':id_cliente'   => $idCliente,
        ':edad'         => $_POST['edad'],
        ':enfermedades' => $_POST['enfermedades_preexistentes'],
        ':folio'        => $_POST['folio_vida'],
        ':valor'        => $_POST['valor_asegurado'],
        ':comision'     => $_POST['porcentaje_comision']
    ]);
    $idSeguro      = $pdo->lastInsertId();
    $columnaSeguro = 'idSeguroVida';

} elseif ($tipo === 'auto') {
    $stmt = $pdo->prepare("INSERT INTO seguro_auto (id_cliente, matricula, modelo, anio, valor_factura, porcentaje_comision) 
                           VALUES (:id_cliente, :matricula, :modelo, :anio, :valor, :comision)");
    $stmt->execute([
        ':id_cliente' => $idCliente,
        ':matricula'  => $_POST['matricula'],
        ':modelo'     => $_POST['modelo'],
        ':anio'       => $_POST['anio'],
        ':valor'      => $_POST['valor_factura'],
        ':comision'   => $_POST['porcentaje_comision']
    ]);
    $idSeguro      = $pdo->lastInsertId();
    $columnaSeguro = 'idSeguroAuto';

} elseif ($tipo === 'robo') {
    $stmt = $pdo->prepare("INSERT INTO seguro_robo (id_cliente, tipo_objeto, medidas_seguridad, valor_articulo, porcentaje_comision) 
                           VALUES (:id_cliente, :objeto, :medidas, :valor, :comision)");
    $stmt->execute([
        ':id_cliente' => $idCliente,
        ':objeto'     => $_POST['tipo_objeto'],
        ':medidas'    => $_POST['medidas_seguridad'],
        ':valor'      => $_POST['valor_articulo'],
        ':comision'   => $_POST['porcentaje_comision']
    ]);
    $idSeguro      = $pdo->lastInsertId();
    $columnaSeguro = 'idSeguroRobo';

} elseif ($tipo === 'incendio') {
    $stmt = $pdo->prepare("INSERT INTO seguro_incendio (id_cliente, valor_vivienda, antiguedad, nivel_incendio, causa_probable, tipo_construccion, porcentaje_comision) 
                           VALUES (:id_cliente, :valor, :antiguedad, :nivel, :causa, :tipo, :comision)");
    $stmt->execute([
        ':id_cliente'  => $idCliente,
        ':valor'       => $_POST['valor_vivienda'],
        ':antiguedad'  => $_POST['antiguedad'],
        ':nivel'       => $_POST['nivel_incendio'],
        ':causa'       => $_POST['causa_probable'],
        ':tipo'        => $_POST['tipo_construccion'],
        ':comision'    => $_POST['porcentaje_comision']
    ]);
    $idSeguro      = $pdo->lastInsertId();
    $columnaSeguro = 'idSeguroIncendio';
}

// 4) Insertar solicitud
$sql = "INSERT INTO solicitud (id_cliente, id_agente, id_sucursal, fecha, estatus, $columnaSeguro) 
        VALUES (:id_cliente, :id_agente, :id_sucursal, :fecha, 'Activo', :idSeguro)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id_cliente' => $idCliente,
    ':id_agente'  => $idAgente,
    ':id_sucursal'=> $idSucursal,
    ':fecha'      => $_POST['fechaRecepcion'],
    ':idSeguro'   => $idSeguro
]);

// Feedback y redirección
$_SESSION['crear_success']   = "Póliza registrada correctamente.";
$_SESSION['active_section']  = 'crear';
header("Location: ../views/dashboard-agente.php#crear");
exit;

exit;