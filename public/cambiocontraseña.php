<?php
session_start();
require_once __DIR__ . '/../app/Core/DB.php';
require_once __DIR__ . '/../app/controllers/LoginController.php';

$idUsuario = $_SESSION['id_usuario'] ?? null;
$password = trim($_POST['password'] ?? '');
$confirm  = trim($_POST['confirmPassword'] ?? '');

if ($password === '' || $confirm === '' || $password !== $confirm) {
    $_SESSION['login_error'] = "Las contraseñas no coinciden.";
    header("Location: ../app/views/cambiocontraseña.php");
    exit;
}

$controller = new LoginController();
if ($controller->cambioContraseña($idUsuario, $password)) {
    $_SESSION['login_error'] = null;
    header("Location: ../app/views/login.php");
    exit;
} else {
    $_SESSION['login_error'] = "Error al actualizar la contraseña.";
    header("Location: ../app/views/cambiocontraseña.php");
    exit;
}
