<?php
// app/Models/Sucursal.php
require_once __DIR__ . '/../Core/DB.php';

class Sucursal {
  public static function getById($id) {
    $pdo = DB::connect();
    $stmt = $pdo->prepare("SELECT * FROM sucursal WHERE id_sucursal = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
  }

  public static function getAgentes($id_sucursal) {
    $pdo = DB::connect();
    $stmt = $pdo->prepare("SELECT * FROM agente WHERE id_sucursal = :id");
    $stmt->execute([':id' => $id_sucursal]);
    return $stmt->fetchAll();
  }
}