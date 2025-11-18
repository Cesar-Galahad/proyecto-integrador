<?php
// app/Core/DB.php

class DB {
  private static $pdo = null;

  public static function connect() {
    if (self::$pdo === null) {
      $host = 'localhost';
      $dbname = 'nexoseguros';
      $user = 'root';
      $pass = '';
      $charset = 'utf8mb4';

      $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

      try {
        self::$pdo = new PDO($dsn, $user, $pass, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false
        ]);
      } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
      }
    }

    return self::$pdo;
  }
}