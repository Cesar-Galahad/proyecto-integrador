<?php
session_start();
require_once __DIR__ . '/../models/Agente.php';

$tipo    = $_POST['tipo'] ?? null;
$id      = $_POST['id'] ?? null;
$valores = $_POST;

$agente = new Agente();
$ok = false;

// Actualizaciones según tipo de seguro
if ($tipo === 'vida') {
    $ok = $agente->updateClienteVida($id, $valores);
} elseif ($tipo === 'auto') {
    $ok = $agente->updateClienteAuto($id, $valores);
} elseif ($tipo === 'robo') {
    $ok = $agente->updateClienteRobo($id, $valores);
} elseif ($tipo === 'incendio') {
    $ok = $agente->updateClienteIncendio($id, $valores);
}

// Mensaje de feedback
$_SESSION[$ok ? 'actualizar_success' : 'actualizar_error'] = $ok
  ? "Registro actualizado correctamente."
  : "Error al actualizar el registro.";

// Redirigir al dashboard del agente
header("Location: ../views/dashboard-agente.php#actualizar");
exit;