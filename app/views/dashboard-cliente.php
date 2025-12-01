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
  <link rel="stylesheet" href="../../public/css/shared.css">
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

<section id="seguro" class="section">
  <h3>Seguros contratados</h3>
  <div class="row text-center mb-4">
    <div class="col-md-3">
      <img src="../../public/assets/vida.png" alt="Seguro de Vida" class="img-fluid seguro-icon" data-target="#vidaTable">
    </div>
    <div class="col-md-3">
      <img src="../../public/assets/auto.png" alt="Seguro de Auto" class="img-fluid seguro-icon" data-target="#autoTable">
    </div>
    <div class="col-md-3">
      <img src="../../public/assets/robo.png" alt="Seguro de Robo" class="img-fluid seguro-icon" data-target="#roboTable">
    </div>
    <div class="col-md-3">
      <img src="../../public/assets/incendio.png" alt="Seguro de Incendio" class="img-fluid seguro-icon" data-target="#incendioTable">
    </div>
  </div>

  <!-- Tablas ocultas -->
  <div id="vidaTable" class="collapse seguro-table mt-3">
    <?php if ($vida = $controller->segurosVida($_SESSION['id_cliente'])): ?>
      <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th>Folio</th><th>Edad</th><th>Enfermedades</th><th>Valor asegurado</th><th>Comisión (%)</th><th>Fecha solicitud</th><th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?= htmlspecialchars($vida['folio_vida']) ?></td>
              <td><?= htmlspecialchars($vida['edad']) ?></td>
              <td><?= htmlspecialchars($vida['enfermedades_preexistentes']) ?></td>
              <td>$<?= number_format($vida['valor_asegurado'], 2) ?></td>
              <td><?= htmlspecialchars($vida['porcentaje_comision']) ?>%</td>
              <td><?= htmlspecialchars($vida['fecha_solicitud']) ?></td>
              <td>
                <button class="btn btn-sm btn-primary" onclick="descargarPoliza('vida', <?= $vida['id_vida'] ?>)">PDF</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p>No tiene seguro de vida contratado.</p>
    <?php endif; ?>
  </div>


  <div id="autoTable" class="collapse seguro-table mt-3">
    <?php $autos = $controller->segurosAuto($_SESSION['id_cliente']); ?>
    <?php if (!empty($autos)): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>Matrícula</th><th>Modelo</th><th>Año</th><th>Valor factura</th><th>Comisión (%)</th><th>Fecha solicitud</th><th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($autos as $auto): ?>
              <tr>
                <td><?= htmlspecialchars($auto['matricula']) ?></td>
                <td><?= htmlspecialchars($auto['modelo']) ?></td>
                <td><?= htmlspecialchars($auto['anio']) ?></td>
                <td>$<?= number_format($auto['valor_factura'], 2) ?></td>
                <td><?= htmlspecialchars($auto['porcentaje_comision']) ?>%</td>
                <td><?= htmlspecialchars($auto['fecha_solicitud']) ?></td>
                <td>
                  <button class="btn btn-sm btn-primary" onclick="descargarPoliza('auto', <?= $auto['id_auto'] ?>)">PDF</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p>No tiene seguros de auto contratados.</p>
    <?php endif; ?>
  </div>

  <div id="roboTable" class="collapse seguro-table mt-3">
    <?php $robos = $controller->segurosRobo($_SESSION['id_cliente']); ?>
    <?php if (!empty($robos)): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>Objeto</th><th>Medidas seguridad</th><th>Valor artículo</th><th>Comisión (%)</th><th>Fecha solicitud</th><th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($robos as $robo): ?>
              <tr>
                <td><?= htmlspecialchars($robo['tipo_objeto']) ?></td>
                <td><?= htmlspecialchars($robo['medidas_seguridad']) ?></td>
                <td>$<?= number_format($robo['valor_articulo'], 2) ?></td>
                <td><?= htmlspecialchars($robo['porcentaje_comision']) ?>%</td>
                <td><?= htmlspecialchars($robo['fecha_solicitud']) ?></td>
                <td>
                  <button class="btn btn-sm btn-primary" onclick="descargarPoliza('robo', <?= $robo['id_robo'] ?>)">PDF</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p>No tiene seguros de robo contratados.</p>
    <?php endif; ?>
  </div>

  <div id="incendioTable" class="collapse seguro-table mt-3">
  <?php $incendios = $controller->segurosIncendio($_SESSION['id_cliente']); ?>
  <?php if (!empty($incendios)): ?>
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>Valor vivienda</th><th>Antigüedad</th><th>Nivel</th><th>Causa probable</th><th>Construcción</th><th>Comisión (%)</th><th>Fecha solicitud</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($incendios as $incendio): ?>
            <tr>
              <td>$<?= number_format($incendio['valor_vivienda'], 2) ?></td>
              <td><?= htmlspecialchars($incendio['antiguedad']) ?> años</td>
              <td><?= htmlspecialchars($incendio['nivel_incendio']) ?></td>
              <td><?= htmlspecialchars($incendio['causa_probable']) ?></td>
              <td><?= htmlspecialchars($incendio['tipo_construccion']) ?></td>
              <td><?= htmlspecialchars($incendio['porcentaje_comision']) ?>%</td>
              <td><?= htmlspecialchars($incendio['fecha_solicitud']) ?></td>
              <td>
                <button class="btn btn-sm btn-primary" onclick="descargarPoliza('incendio', <?= $incendio['id_incendio'] ?>)">PDF</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p>No tiene seguros de incendio contratados.</p>
  <?php endif; ?>
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

    // Listener único para las imágenes de seguros
    document.querySelectorAll('.seguro-icon').forEach(icon => {
      icon.addEventListener('click', function() {
        // Ocultar todas las tablas
        document.querySelectorAll('.seguro-table').forEach(tbl => tbl.classList.remove('show'));

        // Quitar efecto de selección en todas las imágenes
        document.querySelectorAll('.seguro-icon').forEach(img => img.classList.remove('active-icon'));

        // Mostrar solo la tabla seleccionada
        const target = document.querySelector(this.dataset.target);
        if (target) target.classList.add('show');

        // Marcar la imagen seleccionada con transición
        this.classList.add('active-icon');
      });
    });

    // Descargar póliza en PDF
    function descargarPoliza(tipo, id) {
      window.location.href = "../../public/pdf.php?tipo=" + tipo + "&id=" + id;
    }



  </script>
</body>
</html>