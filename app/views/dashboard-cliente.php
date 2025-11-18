<?php

require_once __DIR__ . '/../Core/SessionGuard.php';
SessionGuard::requireRole('cliente');
$usuario = $_SESSION['usuario'] ?? '';
$role = $_SESSION['role'] ?? '';
$estado = $_SESSION['estado'] ?? 'Activo';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard Cliente — Aseguradora</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/prueba/proyecto-integrador/public/css/shared.css">
  <link rel="stylesheet" href="/prueba/proyecto-integrador/public/css/dashboard.css">
</head>
<body>
  <nav class="sidebar">
    <div class="brand">
      <img src="../../public/assets/logo-app.png" alt="Logo" class="logo" onerror="this.style.display='none'">
      <h2>Aseguradora</h2>
      <small class="role-badge"><?= strtoupper($role) ?></small>
    </div>

    <ul>
      <li><button type="button" onclick="showSection('perfil')">Perfil</button></li>
      <li><button type="button" onclick="showSection('mis-seguros')">Seguros contratados</button></li>
      <li><button type="button" onclick="logout()" class="btn btn-outline-danger">Cerrar sesión</button></li>
    </ul>

    <div class="sidebar-footer">
      <small>Estado: <?= htmlspecialchars($estado) ?></small>
    </div>
  </nav>

  <main class="content">
    <header class="content-header">
      <h1>Panel Cliente</h1>
      <div class="user-info">
        <span><?= htmlspecialchars($usuario) ?></span>
      </div>
    </header>

    <section id="perfil" class="section active">
      <h3>Perfil</h3>
      <div>
        <p><strong>Usuario:</strong> <?= htmlspecialchars($usuario) ?></p>
        <p><strong>Rol:</strong> <?= htmlspecialchars($role) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($estado) ?></p>
      </div>
    </section>

    <section id="mis-seguros" class="section">
      <h3>Seguros contratados</h3>
      <div id="segurosList">
        <p class="text-muted">Aquí cargarías las pólizas desde la BD.</p>
      </div>
    </section>
  </main>

  <script>
    function showSection(id) {
      document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
      var el = document.getElementById(id);
      if (el) el.classList.add('active');
    }
    function logout() {
      window.location.href = "../../public/logout.php";
    }
  </script>
</body>
</html>