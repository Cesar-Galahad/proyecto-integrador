<?php
session_start();
require_once __DIR__ . '/../models/Agente.php';

$tipo    = $_POST['tipo'] ?? null;
$id      = $_POST['id'] ?? null;
$valores = $_POST;
$accion  = $_POST['accion'] ?? null;

$agente = new Agente();
$ok = false;

if ($accion === 'cambiar_estatus') {
    $idSolicitud  = $_POST['id_solicitud'];
    $nuevoEstatus = $_POST['nuevo_estatus'];
    $ok = $agente->cambiarEstatusSolicitud($idSolicitud, $nuevoEstatus);

    $_SESSION[$ok ? 'eliminar_success' : 'eliminar_error'] = $ok
      ? "El estado de la solicitud se actualizó correctamente."
      : "Error al cambiar el estado de la solicitud.";

    header("Location: ../views/dashboard-agente.php#eliminar");
    exit;

} else {
    if ($tipo === 'vida') {
        $ok = $agente->updateClienteVida($id, $valores);
    } elseif ($tipo === 'auto') {
        $ok = $agente->updateClienteAuto($id, $valores);
    } elseif ($tipo === 'robo') {
        $ok = $agente->updateClienteRobo($id, $valores);
    } elseif ($tipo === 'incendio') {
        $ok = $agente->updateClienteIncendio($id, $valores);
    }

    $_SESSION[$ok ? 'actualizar_success' : 'actualizar_error'] = $ok
      ? "Registro actualizado correctamente."
      : "Error al actualizar el registro.";

    header("Location: ../views/dashboard-agente.php#actualizar");
    exit;
}