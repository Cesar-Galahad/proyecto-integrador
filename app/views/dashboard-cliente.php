<?php
require_once __DIR__ . '/../Core/SessionGuard.php';
require_once __DIR__ . '/../controllers/ClienteController.php';

SessionGuard::requireRole('cliente');

$controller = new ClienteController();
$perfilCliente = $controller->perfil($_SESSION['id_cliente']);
$usuario = $perfilCliente['usuario'] ?? '';
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard Cliente — Aseguradora</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../proyecto-integrador/public/css/shared.css">
  <link rel="stylesheet" href="../../public/css/dashboard.css">
</head>
<body>
  <nav class="sidebar">
  <div class="brand">
    <img src="../../public/assets/logo2.png" alt="Logo" class="logo" onerror="this.style.display='none'">
    <hr class="divider">
  </div>
  <ul>
    <li>
      <button type="button" onclick="showSection('inicio')">
        <span class="icon">
          <img src="../../public/assets/casa.png" alt="inicio" class="icon-img">
        </span>
        Inicio
      </button>
    </li>
    <li>
      <button type="button" onclick="showSection('perfil')">
        <span class="icon">
          <img src="../../public/assets/usuario.png" alt="Perfil" class="icon-img">
        </span>
        Perfil
      </button>
    </li>
    <li>
      <button type="button" onclick="showSection('seguro')">
        <span class="icon">
          <img src="../../public/assets/seguro.png" alt="Seguro" class="icon-img">
        </span>
        Seguro contratado
      </button>
    </li>
    <li>
      <button type="button" onclick="logout()" class="danger">
        <span class="icon">
          <img src="../../public/assets/salir.png" alt="Cerrar sesión" class="icon-img">
        </span>
        Cerrar sesión
      </button>
    </li>
  </ul>

  <div class="sidebar-footer">

  </div>
</nav>


  <main class="content">
    <header class="content-header">
      <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
      <h1>Panel Cliente</h1>
      <div class="user-info">
        <span><?= htmlspecialchars($usuario) ?></span>
      </div>
    </header>

  <section id="inicio" class="section active">

    <!-- Banner horizontal -->
    <div class="banner mb-4">
      <img src="../../public/assets/baner.png" class="img-fluid w-100" alt="Banner clientes">
    </div>

    <!-- Cards con imágenes y descripción -->
    <div class="container">
      <div class="row g-4">
        
        <!-- Card 1 -->
        <div class="col-md-3">
          <div class="card h-100 shadow-sm">
            <img src="../../public/assets/vida.png" class="card-img-top" alt="Seguro de Vida">
            <div class="card-body">
              <h5 class="card-title">Seguro de Vida</h5>
              <p class="card-text">Brinda respaldo económico a tus seres queridos con planes flexibles y accesibles.</p>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="col-md-3">
          <div class="card h-100 shadow-sm">
            <img src="../../public/assets/auto.png" class="card-img-top" alt="Seguro de Auto">
            <div class="card-body">
              <h5 class="card-title">Seguro de Auto</h5>
              <p class="card-text">Protege tu vehículo ante accidentes, daños o robo. Asistencia 24/7 en toda la republica.</p>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="col-md-3">
          <div class="card h-100 shadow-sm">
            <img src="../../public/assets/robo.png" class="card-img-top" alt="Seguro contra Robo">
            <div class="card-body">
              <h5 class="card-title">Seguro contra Robo</h5>
              <p class="card-text">Cubre perdidas materiales ante robo, personal, en casa o negocio.</p>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card h-100 shadow-sm">
            <img src="../../public/assets/incendio.png" class="card-img-top" alt="Seguro contra Robo">
            <div class="card-body">
              <h5 class="card-title">Seguro contra Incendio</h5>
              <p class="card-text">Protege tu hogar o empresa frente a incendios, explosiones y desastres naturales.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>

  <section id="perfil" class="section">
    <h3 class="mb-4">Perfil</h3>

    <?php if ($perfilCliente): ?>
      <div class="card shadow-sm border-0 perfil-card">
        <div class="card-body d-flex flex-column flex-md-row align-items-center">
          
          <!-- Icono del perfil -->
          <div class="perfil-icon me-lg-5 mb-3 mb-md-0 text-center">
            <img src="../../public/assets/usuario.png" alt="Icono perfil"
                class="rounded-circle border p-2" style="width: 90px; height: 90px;">
            <p class="mt-2 text-muted small">Cliente</p>
          </div>

          <!-- Datos del perfil -->
          <div class="perfil-info w-100">
            <div class="d-flex mb-2 align-items-center">
              <span class="icon me-2">
                <!-- <img src="../../public/assets/nombre.png" alt="Nombre" class="icon-img" style="width:20px;height:20px;">-->
              </span>
              <span class="text-muted fw-bold me-2">Nombre:</span>
              <span><?= htmlspecialchars($perfilCliente['nombre']) ?></span>
            </div>

            <div class="d-flex mb-2 align-items-center">
              <span class="icon me-2">
                <img src="../../public/assets/usuario.png" alt="Usuario" class="icon-img" style="width:20px;height:20px;">
              </span>
              <span class="text-muted fw-bold me-2">Usuario:</span>
              <span><?= htmlspecialchars($usuario) ?></span>
            </div>

            <div class="d-flex mb-2 align-items-center">
              <span class="icon me-2">
                <img src="../../public/assets/email.png" alt="Correo" class="icon-img" style="width:20px;height:20px;">
              </span>
              <span class="text-muted fw-bold me-2">Correo:</span>
              <span><?= htmlspecialchars($perfilCliente['correo']) ?></span>
            </div>

            <div class="d-flex mb-2 align-items-center">
              <span class="icon me-2">
                <img src="../../public/assets/telefono.png" alt="Teléfono" class="icon-img" style="width:20px;height:20px;">
              </span>
              <span class="text-muted fw-bold me-2">Teléfono:</span>
              <span><?= htmlspecialchars($perfilCliente['telefono']) ?></span>
            </div>

            <div class="d-flex mb-2 align-items-center">
              <span class="icon me-2">
                <img src="../../public/assets/direccion.png" alt="Dirección" class="icon-img" style="width:20px;height:20px;">
              </span>
              <span class="text-muted fw-bold me-2">Dirección:</span>
              <span><?= htmlspecialchars($perfilCliente['direccion']) ?></span>
            </div>

            <div class="d-flex mb-2 align-items-center">
              <span class="icon me-2">
                <img src="../../public/assets/curp.png" alt="CURP" class="icon-img" style="width:20px;height:20px;">
              </span>
              <span class="text-muted fw-bold me-2">CURP:</span>
              <span><?= htmlspecialchars($perfilCliente['curp']) ?></span>
            </div>

            <div class="d-flex mb-2 align-items-center">
              <span class="icon me-2">
                <img src="../../public/assets/rfc.png" alt="RFC" class="icon-img" style="width:20px;height:20px;">
              </span>
              <span class="text-muted fw-bold me-2">RFC:</span>
              <span><?= htmlspecialchars($perfilCliente['rfc']) ?></span>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <p class="text-muted">No se encontró información del cliente.</p>
    <?php endif; ?>
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
        function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('open');
    }

    // Cierra el sidebar si haces clic fuera de él
    document.addEventListener('click', function(e) {
      const sidebar = document.querySelector('.sidebar');
      const toggleBtn = document.querySelector('.sidebar-toggle');
      if (sidebar.classList.contains('open') &&
          !sidebar.contains(e.target) &&
          !toggleBtn.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  </script>
</body>
</html>