<?php
session_start();
require_once __DIR__ . '/../app/controllers/LoginController.php';

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = "Debes ingresar usuario y contraseña.";
    header("Location: ../app/views/login.php");
    exit;
}

$controller = new LoginController();
$controller->login($username, $password);
