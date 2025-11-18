<?php
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Solicitud.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../Core/SessionGuard.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

SessionGuard::requireRole('agente');

$idAgente = $_SESSION['id_agente'] ?? null;
$idSucursal = $_SESSION['id_sucursal'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteModel = new Cliente();
    $usuarioModel = new Usuario();
    $solicitudModel = new Solicitud();

    // 1. Crear cliente
    $idCliente = $clienteModel->crear([
        'nombre' => $_POST['nombre'],
        'apellidoPaterno' => $_POST['apellidoPaterno'],
        'apellidoMaterno' => $_POST['apellidoMaterno'],
        'curp' => $_POST['curp'],
        'rfc' => $_POST['rfc'],
        'telefono' => $_POST['telefono'],
        'direccion' => $_POST['direccion']
    ]);

    // 2. Crear solicitud
    $folio = strtoupper(uniqid('FOLIO-'));
    $solicitudModel->create([
        'folio' => $folio,
        'fecharecepcion' => $_POST['fechaRecepcion'],
        'cantidadasegurada' => $_POST['cantidadAsegurada'],
        'idcliente' => $idCliente,
        'idsucursal' => $idSucursal,
        'idagente' => $idAgente,
        'idtiposeguro' => $_POST['idTipoSeguro'],
        'estatus' => 'Activo'
    ]);

    // 3. Crear usuario para el cliente
    $usuarioModel->crear([
        'usuario' => $_POST['usuario'],
        'password' => $_POST['passwordCliente'],
        'rol' => 'cliente',
        'id_cliente' => $idCliente
    ]);

    header('Location: ../views/dashboard-agente.php?creado=1');

    exit;
}