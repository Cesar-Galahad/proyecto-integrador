<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../Core/DB.php';
require_once __DIR__ . '/../models/Agente.php';
session_start();

$idAgente = $_SESSION['id_agente'] ?? null;
$criterio = trim($_POST['criterio'] ?? '');

if (!$idAgente) {
    echo json_encode(['success' => false, 'message' => 'Agente no autenticado']);
    exit;
}

//Validación de campo vacío
if ($criterio === '') {
    echo json_encode(['success' => false, 'message' => 'Debes ingresar un criterio de búsqueda']);
    exit;
}

$pdo = DB::connect();
$agente = new Agente($pdo);
$clientes = $agente->listarClientesActivos($idAgente);

//Buscar todos los que coincidan
$resultados = [];
foreach ($clientes as $c) {
    if (stripos($c['nombre'], $criterio) !== false || stripos($c['curp'], $criterio) !== false) {
        //Armar array de seguros
        $seguros = [];
        if (!empty($c['idSeguroVida']))     $seguros[] = "Vida ({$c['valor_asegurado']})";
        if (!empty($c['idSeguroAuto']))     $seguros[] = "Auto ({$c['valor_factura']})";
        if (!empty($c['idSeguroRobo']))     $seguros[] = "Robo ({$c['valor_articulo']})";
        if (!empty($c['idSeguroIncendio'])) $seguros[] = "Incendio ({$c['valor_vivienda']})";

        $c['seguros'] = $seguros;
        $resultados[] = $c;
    }
}

if (!empty($resultados)) {
    echo json_encode(['success' => true, 'clientes' => $resultados]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontró ningún cliente']);
}