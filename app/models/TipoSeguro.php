<?php
// app/Models/TipoSeguro.php
require_once __DIR__ . '/../Core/DB.php';

class TipoSeguro {
  public static function getAllActivos() {
    $pdo = DB::connect();
    $stmt = $pdo->query("SELECT * FROM tipo_seguro WHERE estatus = 'activo'");
    return $stmt->fetchAll();
  }

  public static function getById($id) {
    $pdo = DB::connect();
    $stmt = $pdo->prepare("SELECT * FROM tipo_seguro WHERE id_tipo_seguro = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
  }
}