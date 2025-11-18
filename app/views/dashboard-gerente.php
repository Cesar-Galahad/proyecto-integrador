<?php
require_once __DIR__ . '/../Core/SessionGuard.php';
SessionGuard::requireRole('gerente');

require_once __DIR__ . '/../models/Agente.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Gerente.php';

$agenteModel  = new Agente();
$usuarioModel = new Usuario();
$gerenteModel = new Gerente();


$usuario = $_SESSION['usuario'] ?? '';
$role    = $_SESSION['role'] ?? '';
$estado  = $_SESSION['estado'] ?? 'Activo';


$flash = null;



/* ACCIONES */

// Crear agente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'crearAgente') {
    $nombre     = trim($_POST['nombre'] ?? '');
    $correo     = trim($_POST['correo'] ?? '');
    $sueldoBase = trim($_POST['sueldoBase'] ?? '');
    $idSucursal = (int)($_POST['id_sucursal'] ?? 0);
    $usuario    = trim($_POST['usuario'] ?? '');
    $password   = trim($_POST['password'] ?? '');

    if ($nombre === '' || $correo === '' || $sueldoBase === '' || $idSucursal === 0 || $usuario === '' || $password === '') {
        $flash = "Todos los campos son obligatorios.";
        $flashType = 'warning';
    } else {
        // Paso 1: crear agente
        $idAgente = $agenteModel->crear($nombre, $correo, $sueldoBase, $idSucursal);

        if ($idAgente === null) {
            // Correo duplicado → no intentes usar $idAgente
            $flash = "El correo ya está registrado para otro agente.";
            $flashType = 'warning';
        } else {
            // Paso 2: crear usuario asociado
            $ok = $usuarioModel->crearAgenteUsuario($usuario, $password, $idAgente);

            if ($ok) {
                $flash = 'Agente registrado con acceso correctamente.';
                $flashType = 'success';
            } else {
                $flash = 'Error al registrar usuario del agente.';
                $flashType = 'danger';
            }
        }
    }
    
}
//$hash = password_hash($password, PASSWORD_DEFAULT); aqui lo puedo cambiar por hash
// Activar/Inactivar
if (isset($_GET['accion']) && $_GET['accion'] === 'toggle' && isset($_GET['tipo'], $_GET['id'])) {
  $tipo = $_GET['tipo'];
  $id   = (int)$_GET['id'];
  if ($tipo === 'agente') {
    $flash = $agenteModel->toggle($id) ? "Estado del agente actualizado." : "Error al actualizar estado del agente.";
  } elseif ($tipo === 'cliente') {
    $flash = $gerenteModel->toggleCliente($id) ? "Estado del cliente actualizado." : "Error al actualizar estado del cliente.";
  }
}

// Buscar con select
$busqueda = null;
$resultAgentes = [];
$resultClientes = [];
if (isset($_GET['accion']) && $_GET['accion'] === 'buscar') {
  $campo = $_GET['campo'] ?? '';
  $valor = trim($_GET['valor'] ?? '');
  $busqueda = [$campo, $valor];
  if ($campo && $valor) {
    $resultAgentes  = $agenteModel->buscarPorCampo($campo, $valor);
    $resultClientes = $gerenteModel->buscarClientesPorCampo($campo, $valor);
  }
}

/* LECTURA */
$idGerente  = $_SESSION['id_gerente'] ?? null;
$perfil     = $idGerente ? $gerenteModel->obtenerPerfil($idGerente) : null;
$agentes    = $agenteModel->listar();
$clientes   = $gerenteModel->listarClientes();
$sucursales = $gerenteModel->listarSucursales();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard Gerente — Aseguradora</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="/prueba/proyecto-integrador/public/css/shared.css">
  <link rel="stylesheet" href="/prueba/proyecto-integrador/public/css/dashboard.css">
</head>
<body>
  <nav class="sidebar">
    <div class="brand">
      <img src="../../public/assets/logo2.png" alt="Logo" class="logo" onerror="this.style.display='none'">
      <hr class="divider">
    </div>

    <ul>
      <li>
        <button type="button" onclick="showSection('perfil')">
          <span class="icon">
            <img src="../../public/assets/usuario.png" alt="Perfil" class="icon-img">
          </span>
          Perfil
        </button>
      </li>
      <li>
        <button type="button" onclick="showSection('crear')">
          <span class="icon">
            <img src="../../public/assets/crear.png" alt="Crear" class="icon-img">
          </span>
          Crear
        </button>
      </li>
      <li>
        <button type="button" onclick="showSection('leer')">
          <span class="icon">
            <img src="../../public/assets/leer.png" alt="Leer" class="icon-img">
          </span>
          Leer
        </button>
      </li>
      <li>
        <button type="button" onclick="showSection('actualizar')">
          <span class="icon">
            <img src="../../public/assets/actualizar.png" alt="Actualizar" class="icon-img">
          </span>
          Actualizar
        </button>
      </li>
      <li>
        <button type="button" onclick="showSection('eliminar')">
          <span class="icon">
            <img src="../../public/assets/eliminar.png" alt="Eliminar" class="icon-img">
          </span>
          Eliminar
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
    <h1>Panel Gerente</h1>
    <div class="user-info"><span><?= htmlspecialchars($usuario) ?></span></div>

    </header>
    <!-- PERFIL -->
<section id="perfil" class="section active">
  <h3>Perfil</h3>

  <?php if ($perfil): ?>
    <div class="perfil-card">
      <div class="perfil-item">
        <span class="label">Nombre:</span>
        <span class="value"><?= htmlspecialchars($perfil['nombre'] ?? '') ?></span>
      </div>
      <div class="perfil-item">
        <span class="label">Usuario:</span>
        <span class="value"><?= htmlspecialchars($usuario) ?></span>
      </div>
      <div class="perfil-item">
        <span class="label">Correo:</span>
        <span class="value"><?= htmlspecialchars($perfil['correo'] ?? '') ?></span>
      </div>
      <div class="perfil-item">
        <span class="label">Sucursal:</span>
        <span class="value">
          <?= htmlspecialchars($perfil['ciudad'] ?? '') ?>,
          <?= htmlspecialchars($perfil['estado'] ?? '') ?>
        </span>
      </div>
      <div class="perfil-item">
        <span class="label">ID de sucursal:</span>
        <span class="value"><?= htmlspecialchars($perfil['id_sucursal'] ?? '') ?></span>
      </div>
      <div class="perfil-item">
        <span class="label">Dirección:</span>
        <span class="value"><?= htmlspecialchars($perfil['direccion'] ?? '') ?></span>
      </div>
    </div>
  <?php else: ?>
    <p class="text-muted">No se encontró información del gerente.</p>
  <?php endif; ?>
</section>


    <!-- CREAR -->
    <section id="crear" class="section">
      <h3>Registrar agente</h3>
      <?php if (!empty($flash)): ?>
        <div class="alert alert-<?= htmlspecialchars($flashType ?? 'info') ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($flash) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>


      <form method="post" action="">
        <input type="hidden" name="accion" value="crearAgente">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
        <label>Correo</label>
        <input type="email" name="correo" class="form-control" required>
        <label>Sueldo base</label>
        <input type="number" step="0.01" name="sueldoBase" class="form-control" required>
        <label>Sucursal</label>
        <select name="id_sucursal" class="form-select" required>
          <option value="">Selecciona una sucursal</option>
          <?php foreach ($sucursales as $s): ?>
            <option value="<?= (int)$s['id_sucursal'] ?>">
              <?= htmlspecialchars(($s['codigoSucursal'] ?? '') . ' - ' . ($s['ciudad'] ?? '')) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <label>Usuario</label>
        <input type="text" name="usuario" class="form-control" required>

        <label>Contraseña</label>
        <input type="password" name="password" class="form-control" required>
        <button type="submit" class="btn btn-primary">Registrar</button>
      </form>
    </section>

    <!-- LEER -->
    <section id="leer" class="section">
      <h3>Agentes</h3>
      <table class="table table-hover table-bordered">
        <thead class="table-dark"><tr><th>Nombre</th><th>Correo</th><th>Sueldo</th><th>Sucursal</th><th>Estado</th></tr></thead>
        <tbody>
          <?php foreach ($agentes as $a): ?>
            <tr>
              <td><?= htmlspecialchars($a['nombre']) ?></td>
              <td><?= htmlspecialchars($a['correo']) ?></td>
              <td><?= htmlspecialchars($a['sueldoBase']) ?></td>
              <td><?= htmlspecialchars($a['ciudad'] ?? '') ?></td>
              <td><?= htmlspecialchars($a['estatus'] ?? 'Activo') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <h3>Clientes</h3>
      <table class="table table-hover table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Nombre</th>
            <th>CURP</th>
            <th>RFC</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Seguro</th>
            <th>Fecha recepción</th>
            <th>Cantidad asegurada</th>
            <th>Agente</th>
            <th>Estado Solicitud</th>
            <th>Estado Cliente</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($clientes as $c): ?>
            <tr>
              <td><?= htmlspecialchars(($c['nombre'] ?? '') . ' ' . ($c['apellidoPaterno'] ?? '')) ?></td>
              <td><?= htmlspecialchars($c['curp'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['rfc'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['telefono'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['direccion'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['seguro'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['fechaRecepcion'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['cantidadAsegurada'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['nombreAgente'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['estatusSolicitud'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['estatusCliente'] ?? 'Activo') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <!-- BUSCAR -->
    <section id="buscar" class="section">
      <h3>Buscar cliente/agente</h3>
      <form method="get" class="form-inline">
        <input type="hidden" name="accion" value="buscar">
        <label for="campo">Buscar por:</label>
        <select name="campo" id="campo" class="form-select" required>
          <option value="">Selecciona campo</option>
          <option value="nombre">Nombre</option>
          <option value="correo">Correo</option>
          <option value="curp">CURP</option>
          <option value="rfc">RFC</option>
          <option value="seguro">Seguro</option>
        </select>
        <input type="text" name="valor" class="form-control" placeholder="Valor a buscar" required>
        <button type="submit" class="btn btn-secondary">Buscar</button>
      </form>

      <?php if ($busqueda !== null): ?>
        <h4>Resultados en agentes</h4>
        <table class="table table-hover table-bordered">
          <thead class="table-dark"><tr><th>Nombre</th><th>Correo</th><th>Sucursal</th><th>Estado</th></tr></thead>
          <tbody>
            <?php foreach ($resultAgentes as $ra): ?>
              <tr>
                <td><?= htmlspecialchars($ra['nombre'] ?? '') ?></td>
                <td><?= htmlspecialchars($ra['correo'] ?? '') ?></td>
                <td><?= htmlspecialchars($ra['ciudad'] ?? '') ?></td>
                <td><?= htmlspecialchars($ra['estatus'] ?? 'Activo') ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($resultAgentes)): ?>
              <tr><td colspan="4" class="text-muted">Sin coincidencias en agentes.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>

        <h4>Resultados en clientes</h4>
        <table class="table table-hover table-bordered">
          <thead class="table-dark"><tr><th>Nombre</th><th>CURP</th><th>RFC</th><th>Seguro</th><th>Estado Solicitud</th><th>Estado Cliente</th></tr></thead>
          <tbody>
            <?php foreach ($resultClientes as $rc): ?>
              <tr>
                <td><?= htmlspecialchars(($rc['nombre'] ?? '') . ' ' . ($rc['apellidoPaterno'] ?? '')) ?></td>
                <td><?= htmlspecialchars($rc['curp'] ?? '') ?></td>
                <td><?= htmlspecialchars($rc['rfc'] ?? '') ?></td>
                <td><?= htmlspecialchars($rc['seguro'] ?? '') ?></td>
                <td><?= htmlspecialchars($rc['estatus'] ?? '') ?></td>
                <td><?= htmlspecialchars($rc['estatusCliente'] ?? 'Activo') ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($resultClientes)): ?>
              <tr><td colspan="6" class="text-muted">Sin coincidencias en clientes.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>

    <!-- ELIMINAR -->
    <section id="eliminar" class="section">
      <h3>Activar/Inactivar</h3>
      <p class="text-muted">Aquí puedes cambiar el estado de agentes y clientes.</p>

      <h4>Agentes</h4>
      <table class="table table-hover table-bordered">
        <thead class="table-dark"><tr><th>Nombre</th><th>Correo</th><th>Estado</th><th>Acción</th></tr></thead>
        <tbody>
          <?php foreach ($agentes as $a): ?>
            <tr>
              <td><?= htmlspecialchars($a['nombre']) ?></td>
              <td><?= htmlspecialchars($a['correo']) ?></td>
              <td><?= htmlspecialchars($a['estatus'] ?? 'Activo') ?></td>
              <td>
                <a href="?accion=toggle&tipo=agente&id=<?= (int)$a['id_agente'] ?>" class="btn btn-warning">
                  <?= ($a['estatus'] ?? 'Activo') === 'Activo' ? 'Inactivar' : 'Activar' ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <h4>Clientes</h4>
      <table class="table table-hover table-bordered">
        <thead class="table-dark"><tr><th>Nombre</th><th>CURP</th><th>Estado</th><th>Acción</th></tr></thead>
        <tbody>
          <?php foreach ($clientes as $c): ?>
            <tr>
              <td><?= htmlspecialchars($c['nombre'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['curp'] ?? '') ?></td>
              <td><?= htmlspecialchars($c['estatusCliente'] ?? 'Activo') ?></td>
              <td>
                <a href="?accion=toggle&tipo=cliente&id=<?= (int)$c['id_cliente'] ?>" class="btn btn-warning">
                  <?= ($c['estatusCliente'] ?? 'Activo') === 'Activo' ? 'Inactivar' : 'Activar' ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
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
    function filtrarTabla(tablaId, filtro) {
      filtro = (filtro || '').toLowerCase();
      document.querySelectorAll(`#${tablaId} tbody tr`).forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(filtro) ? '' : 'none';
      });
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>   
              