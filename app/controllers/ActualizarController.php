<?php
session_start();
require_once __DIR__ . '/../models/Gerente.php';

$tipo    = $_POST['tipo'] ?? null;
$id      = $_POST['id'] ?? null;
$valores = $_POST;

$gerente = new Gerente();
$ok = false;

$accion = $_POST['accion'] ?? null;

if ($accion === 'cambiar_estatus') {

    $idSolicitud = $_POST['id_solicitud'];
    $nuevoEstatus = $_POST['nuevo_estatus'];
    $ok = $gerente->cambiarEstatusSolicitud($idSolicitud, $nuevoEstatus);

    $_SESSION[$ok ? 'eliminar_success' : 'eliminar_error'] = $ok
      ? "El estado de la solicitud se actualizó correctamente."
      : "Error al cambiar el estado de la solicitud.";

    header("Location: ../views/dashboard-gerente.php#eliminar");
    exit;

} else {

    if ($tipo === 'agente') {
        $ok = $gerente->updateAgente($id, $valores);
    } elseif ($tipo === 'vida') {
        $ok = $gerente->updateClienteVida($id, $valores);
    } elseif ($tipo === 'auto') {
        $ok = $gerente->updateClienteAuto($id, $valores);
    } elseif ($tipo === 'robo') {
        $ok = $gerente->updateClienteRobo($id, $valores);
    } elseif ($tipo === 'incendio') {
        $ok = $gerente->updateClienteIncendio($id, $valores);
    }

    $_SESSION[$ok ? 'actualizar_success' : 'actualizar_error'] = $ok
      ? "Registro actualizado correctamente."
      : "Error al actualizar el registro.";

    header("Location: ../views/dashboard-gerente.php#actualizar");
    exit;
}


