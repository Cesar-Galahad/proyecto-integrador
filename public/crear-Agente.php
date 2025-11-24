<?php
session_start();
require_once __DIR__ . '/../app/controllers/GerenteController.php';

$nombre     = trim($_POST['nombre'] ?? '');
$sueldobase = trim($_POST['sueldobase'] ?? '');
$sucursal   = trim($_POST['sucursal'] ?? ''); // aquí debe ser el id_sucursal
$usuario    = trim($_POST['usuario'] ?? '');
$correo     = trim($_POST['correo'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');

$controller = new GerenteController();
$controller->crearAgente($nombre, $sueldobase, $sucursal, $usuario, $correo, $contrasena);