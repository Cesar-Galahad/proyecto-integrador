<?php
session_start();

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db   = "nexoseguros";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Recibir datos del formulario
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = "Debes ingresar usuario y contraseña.";
    header("Location: ../app/views/login.php");
    exit;
}

// Buscar usuario en tabla usuarios
$sql = "SELECT * FROM usuarios WHERE nusuario = ? AND estatus = 'Activo'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Comparar contraseñas directamente (texto plano)
    if ($password === $user['contrasena']) {
        // Guardar datos en sesión
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['usuario']    = $user['nusuario'];   
        $_SESSION['role']       = $user['rol'];        
        $_SESSION['estado']     = $user['estatus'];    
        $_SESSION['id_cliente'] = $user['id_cliente'];
        $_SESSION['id_agente']  = $user['id_agente'];
        $_SESSION['id_gerente'] = $user['id_gerente'];


        // Redirigir según rol
        switch ($user['rol']) {
            case 'cliente':
                header("Location: ../app/views/dashboard-cliente.php");
                exit;
            case 'agente':
                header("Location: ../app/views/dashboard-agente.php");
                exit;
            case 'gerente':
                header("Location: ../app/views/dashboard-gerente.php");
                exit;
        }
    } else {
        $_SESSION['login_error'] = "Contraseña incorrecta.";
        header("Location: ../app/views/login.php");
        exit;
    }
} else {
    $_SESSION['login_error'] = "Usuario no encontrado o inactivo.";
    header("Location: ../app/views/login.php");
    exit;
}