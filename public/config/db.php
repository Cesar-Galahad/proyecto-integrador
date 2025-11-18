<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "nexoseguros2";

$conexion = new mysqli($host, $user, $password, $db);

if ($conexion->connect_errno) {
    echo "Fallo la conexion a ala base de datos " . $conexion->connect_error;
}
?>