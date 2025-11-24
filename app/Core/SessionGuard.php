<?php
// Ruta: app/Core/SessionGuard.php
class SessionGuard {
  public static function requireRole($role) {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    if (!isset($_SESSION['usuario']) || !isset($_SESSION['role'])) {
      header('Location: ../views/login.php');
      exit;
    }

    if ($_SESSION['role'] !== $role) {
      header('Location: dashboard-' . $_SESSION['role'] . '.php');
      exit;
    }
  }
}
