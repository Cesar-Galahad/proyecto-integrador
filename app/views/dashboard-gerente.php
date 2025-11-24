<?php
require_once __DIR__ . '/../Core/SessionGuard.php';
require_once __DIR__ . '/../controllers/GerenteController.php';

SessionGuard::requireRole('gerente');

$controller = new GerenteController();
$perfil = $controller->perfil($_SESSION['id_gerente']);
$usuario = $perfil['usuario'] ?? '';

?>

          
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard Gerente — Aseguradora</title>
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
    <h3 class="mb-4">Perfil</h3>

    <?php if ($perfil): ?>
      <div class="card shadow-sm border-0 perfil-card">
        <div class="card-body d-flex flex-column flex-md-row align-items-center">
          
          <!-- Icono del perfil -->
          <div class="perfil-icon me-lg-5 mb-3 mb-md-0 text-center">
            <img src="../../public/assets/usuario.png" alt="Icono perfil"
                class="rounded-circle border p-2" style="width: 90px; height: 90px;">
            <p class="mt-2 text-muted small">Gerente</p>
          </div>
          <!-- Datos del perfil -->
          <div class="perfil-info w-100">
            <div class="d-flex mb-2">
              <span class="text-muted fw-bold me-2">Nombre:</span>
              <span><?= htmlspecialchars($perfil['nombre']) ?></span>
            </div>
            <div class="d-flex mb-2">
              <span class="text-muted fw-bold me-2">Usuario:</span>
              <span><?= htmlspecialchars($usuario) ?></span>
            </div>
            <div class="d-flex mb-2">
              <span class="text-muted fw-bold me-2">Correo:</span>
              <span><?= htmlspecialchars($perfil['correo']) ?></span>
            </div>
            <div class="d-flex mb-2">
              <span class="text-muted fw-bold me-2">Sucursal:</span>
              <span><?= htmlspecialchars($perfil['ciudad']) ?>, <?= htmlspecialchars($perfil['estado']) ?></span>
            </div>
            <div class="d-flex mb-2">
              <span class="text-muted fw-bold me-2">ID Sucursal:</span>
              <span><?= htmlspecialchars($perfil['id_sucursal']) ?></span>
            </div>
            <div class="d-flex mb-2">
              <span class="text-muted fw-bold me-2">Dirección:</span>
              <span><?= htmlspecialchars($perfil['direccion']) ?></span>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <p class="text-muted">No se encontró información del gerente.</p>
    <?php endif; ?>
  </section>



  <!-- CREAR -->
  <section id="crear" class="section">
    <h3 class="mb-4">Registrar agente</h3>

    <?php if (isset($_SESSION['crear_error'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['crear_error']) ?></div>
      <?php unset($_SESSION['crear_error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['crear_success'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_SESSION['crear_success']) ?></div>
      <?php unset($_SESSION['crear_success']); ?>
    <?php endif; ?>


    <div class="card shadow-sm border-0">
      <div class="card-body">
        <form method="post" action="../../public/crear-agente.php" class="needs-validation" novalidate>

          <!-- Datos del agente -->
          <h5 class="mb-3">Datos del agente</h5>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" required>
              <div class="invalid-feedback">El nombre es obligatorio.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Sueldo base</label>
              <input type="number" name="sueldobase" class="form-control" required min="0">
              <div class="invalid-feedback">El sueldo base es obligatorio.</div>
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-md-6">
              <label class="form-label">Sucursal</label>
              <select name="sucursal" class="form-select" required>
                <option value="">Selecciona una sucursal</option>
                <option value="1">Sucursal Guadalajara</option>
                <option value="2">Sucursal Monterrey</option>
              </select>
              <div class="invalid-feedback">Debes seleccionar una sucursal.</div>
            </div>
          </div>

          <!-- Credenciales de acceso -->
          <h5 class="mb-3">Credenciales de acceso</h5>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Usuario</label>
              <input type="text" name="usuario" class="form-control" required>
              <div class="invalid-feedback">El usuario es obligatorio.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo</label>
              <input type="email" name="correo" class="form-control" required>
              <div class="invalid-feedback">Debes ingresar un correo válido (ejemplo@dominio.com).</div>
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-md-6">
              <label class="form-label">Contraseña</label>
              <input type="password" name="contrasena" class="form-control" required minlength="6">
              <div class="invalid-feedback">La contraseña es obligatoria y debe tener al menos 6 caracteres.</div>
            </div>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-primary px-4">Registrar agente</button>
          </div>
        </form>
      </div>
    </div>
  </section>



    <!-- LEER -->
<section id="leer" class="section">
  <!-- Tabla de Agentes -->
  <h3>Agentes registrados</h3>
  <?php $agentes = $controller->listarAgentes(); ?>
  <div class="table-responsive">
    <table class="table table-hover table-bordered">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>Sueldo</th>
          <th>Código Sucursal</th>
          <th>Usuario</th>
          <th>Correo</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($agentes as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['nombre']) ?></td>
            <td><?= htmlspecialchars($a['sueldoBase']) ?></td>
            <td><?= htmlspecialchars($a['codigoSucursal']) ?></td>
            <td><?= htmlspecialchars($a['usuario']) ?></td>
            <td><?= htmlspecialchars($a['correo']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Seguro de Vida -->
  <h3>Clientes — Seguro de Vida</h3>
  <?php $vida = $controller->listarClientesVida(); ?>
  <div class="table-responsive">
    <table class="table table-hover table-bordered">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>CURP</th>
          <th>RFC</th>
          <th>Teléfono</th>
          <th>Usuario</th>
          <th>Correo</th>
          <th>Edad</th>
          <th>Folio</th>
          <th>Fecha Solicitud</th>
          <th>Valor Asegurado</th>       <!-- NUEVO -->
          <th>% Comisión</th>            <!-- NUEVO -->
          <th>Agente</th>
          <th>Sucursal</th>
          <th>Estado Solicitud</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vida as $v): ?>
          <tr>
            <td><?= htmlspecialchars($v['nombre'].' '.$v['apellidoPaterno'].' '.$v['apellidoMaterno']) ?></td>
            <td><?= htmlspecialchars($v['curp']) ?></td>
            <td><?= htmlspecialchars($v['rfc']) ?></td>
            <td><?= htmlspecialchars($v['telefono']) ?></td>
            <td><?= htmlspecialchars($v['usuario']) ?></td>
            <td><?= htmlspecialchars($v['correo']) ?></td>
            <td><?= htmlspecialchars($v['edad']) ?></td>
            <td><?= htmlspecialchars($v['folio_vida']) ?></td>
            <td><?= htmlspecialchars($v['fecha_solicitud']) ?></td>
            <td><?= htmlspecialchars($v['valor_asegurado']) ?></td> <!-- NUEVO -->
            <td><?= htmlspecialchars($v['porcentaje_comision']) ?>%</td> <!-- NUEVO -->
            <td><?= htmlspecialchars($v['nombre_agente']) ?></td>
            <td><?= htmlspecialchars($v['codigoSucursal']) ?></td>
            <td><?= htmlspecialchars($v['estado_solicitud']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Seguro de Auto -->
  <h3>Clientes — Seguro de Auto</h3>
  <?php $auto = $controller->listarClientesAuto(); ?>
  <div class="table-responsive">
    <table class="table table-hover table-bordered">
      <thead class="table-dark">
      <tr>
        <th>Nombre</th>
        <th>CURP</th>
        <th>RFC</th>
        <th>Teléfono</th>
        <th>Usuario</th>
        <th>Correo</th>
        <th>Matrícula</th>
        <th>Modelo</th>
        <th>Año</th>
        <th>Valor Factura</th>
        <th>% Comisión</th> <!-- NUEVO -->
        <th>Fecha Solicitud</th>
        <th>Agente</th>
        <th>Sucursal</th>
        <th>Estado Solicitud</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($auto as $au): ?>
        <tr>
          <td><?= htmlspecialchars($au['nombre'].' '.$au['apellidoPaterno'].' '.$au['apellidoMaterno']) ?></td>
          <td><?= htmlspecialchars($au['curp']) ?></td>
          <td><?= htmlspecialchars($au['rfc']) ?></td>
          <td><?= htmlspecialchars($au['telefono']) ?></td>
          <td><?= htmlspecialchars($au['usuario']) ?></td>
          <td><?= htmlspecialchars($au['correo']) ?></td>
          <td><?= htmlspecialchars($au['matricula']) ?></td>
          <td><?= htmlspecialchars($au['modelo']) ?></td>
          <td><?= htmlspecialchars($au['anio']) ?></td>
          <td><?= htmlspecialchars($au['valor_factura']) ?></td>
          <td><?= htmlspecialchars($au['porcentaje_comision']) ?>%</td> <!-- NUEVO -->
          <td><?= htmlspecialchars($au['fecha_solicitud']) ?></td>
          <td><?= htmlspecialchars($au['nombre_agente']) ?></td>
          <td><?= htmlspecialchars($au['codigoSucursal']) ?></td>
          <td><?= htmlspecialchars($au['estado_solicitud']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    </table>
  </div>

  <!-- Seguro de Robo -->
  <h3>Clientes — Seguro de Robo</h3>
  <?php $robo = $controller->listarClientesRobo(); ?>
  <div class="table-responsive">
    <table class="table table-hover table-bordered">
    <thead class="table-dark">
      <tr>
        <th>Nombre</th>
        <th>CURP</th>
        <th>RFC</th>
        <th>Teléfono</th>
        <th>Usuario</th>
        <th>Correo</th>
        <th>Objeto</th>
        <th>Medidas Seguridad</th>
        <th>Valor Artículo</th>       <!-- NUEVO -->
        <th>% Comisión</th>           <!-- NUEVO -->
        <th>Fecha Solicitud</th>
        <th>Agente</th>
        <th>Sucursal</th>
        <th>Estado Solicitud</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($robo as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['nombre'].' '.$r['apellidoPaterno'].' '.$r['apellidoMaterno']) ?></td>
          <td><?= htmlspecialchars($r['curp']) ?></td>
          <td><?= htmlspecialchars($r['rfc']) ?></td>
          <td><?= htmlspecialchars($r['telefono']) ?></td>
          <td><?= htmlspecialchars($r['usuario']) ?></td>
          <td><?= htmlspecialchars($r['correo']) ?></td>
          <td><?= htmlspecialchars($r['tipo_objeto']) ?></td>
          <td><?= htmlspecialchars($r['medidas_seguridad']) ?></td>
          <td><?= htmlspecialchars($r['valor_articulo']) ?></td> <!-- NUEVO -->
          <td><?= htmlspecialchars($r['porcentaje_comision']) ?>%</td> <!-- NUEVO -->
          <td><?= htmlspecialchars($r['fecha_solicitud']) ?></td>
          <td><?= htmlspecialchars($r['nombre_agente']) ?></td>
          <td><?= htmlspecialchars($r['codigoSucursal']) ?></td>
          <td><?= htmlspecialchars($r['estado_solicitud']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    </table>
  </div>

  <!-- Seguro de Incendio -->
  <h3>Clientes — Seguro de Incendio</h3>
  <?php $inc = $controller->listarClientesIncendio(); ?>
  <div class="table-responsive">
    <table class="table table-hover table-bordered">
    <thead class="table-dark">
      <tr>
        <th>Nombre</th>
        <th>CURP</th>
        <th>RFC</th>
        <th>Teléfono</th>
        <th>Usuario</th>
        <th>Correo</th>
        <th>Valor Vivienda</th>
        <th>Antigüedad</th>
        <th>Nivel</th>
        <th>Causa Probable</th>
        <th>Tipo Construcción</th>
        <th>% Comisión</th> <!-- NUEVO -->
        <th>Fecha Solicitud</th>
        <th>Agente</th>
        <th>Sucursal</th>
        <th>Estado Solicitud</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($inc as $i): ?>
        <tr>
          <td><?= htmlspecialchars($i['nombre'].' '.$i['apellidoPaterno'].' '.$i['apellidoMaterno']) ?></td>
          <td><?= htmlspecialchars($i['curp']) ?></td>
          <td><?= htmlspecialchars($i['rfc']) ?></td>
          <td><?= htmlspecialchars($i['telefono']) ?></td>
          <td><?= htmlspecialchars($i['usuario']) ?></td>
          <td><?= htmlspecialchars($i['correo']) ?></td>
          <td><?= htmlspecialchars($i['valor_vivienda']) ?></td>
          <td><?= htmlspecialchars($i['antiguedad']) ?></td>
          <td><?= htmlspecialchars($i['nivel_incendio']) ?></td>
          <td><?= htmlspecialchars($i['causa_probable']) ?></td>
          <td><?= htmlspecialchars($i['tipo_construccion']) ?></td>
          <td><?= htmlspecialchars($i['porcentaje_comision']) ?>%</td> <!-- NUEVO -->
          <td><?= htmlspecialchars($i['fecha_solicitud']) ?></td>
          <td><?= htmlspecialchars($i['nombre_agente']) ?></td>
          <td><?= htmlspecialchars($i['codigoSucursal']) ?></td>
          <td><?= htmlspecialchars($i['estado_solicitud']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    </table>
  </div>
</section>


<!-- Actualizar -->
<section id="actualizar" class="section">
  <!-- Agentes -->
  <h3>Actualizar Agentes</h3>
  <?php $agentes = $controller->listarAgentes(); ?>
  <?php if (isset($_SESSION['actualizar_success'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['actualizar_success']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['actualizar_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['actualizar_error'])): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $_SESSION['actualizar_error']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['actualizar_error']); ?>
<?php endif; ?>

  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>Sueldo</th>
          <th>Código Sucursal</th>
          <th>Usuario</th>
          <th>Correo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($agentes as $a): ?>
          <tr>
            <form action="../controllers/ActualizarController.php" method="post">
              <!-- identificadores ocultos -->
              <input type="hidden" name="tipo" value="agente">
              <input type="hidden" name="id" value="<?= $a['id_agente'] ?>">

              <td><input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($a['nombre']) ?>" disabled></td>
              <td><input type="number" class="form-control" name="sueldoBase" value="<?= htmlspecialchars($a['sueldoBase']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="codigoSucursal" value="<?= htmlspecialchars($a['codigoSucursal']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="usuario" value="<?= htmlspecialchars($a['usuario']) ?>" disabled></td>
              <td><input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($a['correo']) ?>" disabled></td>
              <td>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-primary editar">Editar</button>
                  <button type="submit" class="btn btn-sm btn-success actualizar" disabled>Actualizar</button>
                </div>
              </td>
            </form>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Clientes Vida -->
  <h3>Actualizar Clientes — Seguro de Vida</h3>
  <?php $vida = $controller->listarClientesVida(); ?>
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle" data-tipo="vida">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th>
          <th>CURP</th><th>RFC</th><th>Teléfono</th>
          <th>Usuario</th><th>Correo</th><th>Edad</th><th>Folio</th>
          <th>Fecha Solicitud</th><th>Valor Asegurado</th><th>% Comisión</th>
          <th>Agente</th><th>Sucursal</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vida as $v): ?>
          <tr>
            <form action="../controllers/ActualizarController.php" method="post">
              <input type="hidden" name="tipo" value="vida">
              <input type="hidden" name="id" value="<?= $v['id_cliente'] ?>">

              <td><input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($v['nombre']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="apellidoPaterno" value="<?= htmlspecialchars($v['apellidoPaterno']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="apellidoMaterno" value="<?= htmlspecialchars($v['apellidoMaterno']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="curp" value="<?= htmlspecialchars($v['curp']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="rfc" value="<?= htmlspecialchars($v['rfc']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($v['telefono']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="usuario" value="<?= htmlspecialchars($v['usuario']) ?>" disabled></td>
              <td><input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($v['correo']) ?>" disabled></td>
              <td><input type="number" class="form-control" name="edad" value="<?= htmlspecialchars($v['edad']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="folio_vida" value="<?= htmlspecialchars($v['folio_vida']) ?>" disabled></td>
              <td><input type="date" class="form-control" name="fecha_solicitud" value="<?= htmlspecialchars($v['fecha_solicitud']) ?>" disabled></td>
              <td><input type="number" step="0.01" class="form-control" name="valor_asegurado" value="<?= htmlspecialchars($v['valor_asegurado']) ?>" disabled></td> <!-- NUEVO -->
              <td><input type="number" step="0.01" class="form-control" name="porcentaje_comision" value="<?= htmlspecialchars($v['porcentaje_comision']) ?>" disabled></td> <!-- NUEVO -->
              <td><input type="text" class="form-control" name="nombre_agente" value="<?= htmlspecialchars($v['nombre_agente']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="codigoSucursal" value="<?= htmlspecialchars($v['codigoSucursal']) ?>" disabled></td>
              <td>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-primary editar">Editar</button>
                  <button type="submit" class="btn btn-sm btn-success actualizar" disabled>Actualizar</button>
                </div>
              </td>
            </form>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Clientes Auto -->
  <h3>Actualizar Clientes — Seguro de Auto</h3>
  <?php $auto = $controller->listarClientesAuto(); ?>
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle" data-tipo="auto">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th>
          <th>CURP</th><th>RFC</th><th>Teléfono</th>
          <th>Usuario</th><th>Correo</th>
          <th>Matrícula</th><th>Modelo</th><th>Año</th>
          <th>Valor Factura</th><th>% Comisión</th> <!-- NUEVO -->
          <th>Fecha Solicitud</th>
          <th>Agente</th><th>Sucursal</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($auto as $au): ?>
          <tr>
            <form action="../controllers/ActualizarController.php" method="post">
              <input type="hidden" name="tipo" value="auto">
              <input type="hidden" name="id" value="<?= $au['id_cliente'] ?>">

              <td><input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($au['nombre']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="apellidoPaterno" value="<?= htmlspecialchars($au['apellidoPaterno']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="apellidoMaterno" value="<?= htmlspecialchars($au['apellidoMaterno']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="curp" value="<?= htmlspecialchars($au['curp']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="rfc" value="<?= htmlspecialchars($au['rfc']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($au['telefono']) ?>" disabled></td>

              <td><input type="text" class="form-control" name="usuario" value="<?= htmlspecialchars($au['usuario']) ?>" disabled></td>
              <td><input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($au['correo']) ?>" disabled></td>

              <td><input type="text" class="form-control" name="matricula" value="<?= htmlspecialchars($au['matricula']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="modelo" value="<?= htmlspecialchars($au['modelo']) ?>" disabled></td>
              <td><input type="number" class="form-control" name="anio" value="<?= htmlspecialchars($au['anio']) ?>" disabled></td>
              <td><input type="number" class="form-control" step="0.01" name="valor_factura" value="<?= htmlspecialchars($au['valor_factura']) ?>" disabled></td>
              <td><input type="number" class="form-control" step="0.01" name="porcentaje_comision" value="<?= htmlspecialchars($au['porcentaje_comision']) ?>" disabled></td> <!-- NUEVO -->
              <td><input type="date" class="form-control" name="fecha_solicitud" value="<?= htmlspecialchars($au['fecha_solicitud']) ?>" disabled></td>

              <td><input type="text" class="form-control" name="nombre_agente" value="<?= htmlspecialchars($au['nombre_agente']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="codigoSucursal" value="<?= htmlspecialchars($au['codigoSucursal']) ?>" disabled></td>

              <td>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-primary editar">Editar</button>
                  <button type="submit" class="btn btn-sm btn-success actualizar" disabled>Actualizar</button>
                </div>
              </td>
            </form>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Clientes Robo -->
 <h3>Actualizar Clientes — Seguro de Robo</h3>
  <?php $robo = $controller->listarClientesRobo(); ?>
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle" data-tipo="robo">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th>
          <th>CURP</th><th>RFC</th><th>Teléfono</th>
          <th>Usuario</th><th>Correo</th>
          <th>Objeto</th><th>Medidas Seguridad</th>
          <th>Valor Artículo</th><th>% Comisión</th> <!-- NUEVOS -->
          <th>Fecha Solicitud</th>
          <th>Agente</th><th>Sucursal</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($robo as $r): ?>
          <tr>
            <form action="../controllers/ActualizarController.php" method="post">
              <input type="hidden" name="tipo" value="robo">
              <input type="hidden" name="id" value="<?= $r['id_cliente'] ?>">

              <td><input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($r['nombre']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="apellidoPaterno" value="<?= htmlspecialchars($r['apellidoPaterno']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="apellidoMaterno" value="<?= htmlspecialchars($r['apellidoMaterno']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="curp" value="<?= htmlspecialchars($r['curp']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="rfc" value="<?= htmlspecialchars($r['rfc']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($r['telefono']) ?>" disabled></td>

              <td><input type="text" class="form-control" name="usuario" value="<?= htmlspecialchars($r['usuario']) ?>" disabled></td>
              <td><input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($r['correo']) ?>" disabled></td>

              <td><input type="text" class="form-control" name="tipo_objeto" value="<?= htmlspecialchars($r['tipo_objeto']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="medidas_seguridad" value="<?= htmlspecialchars($r['medidas_seguridad']) ?>" disabled></td>
              <td><input type="number" step="0.01" class="form-control" name="valor_articulo" value="<?= htmlspecialchars($r['valor_articulo']) ?>" disabled></td> <!-- NUEVO -->
              <td><input type="number" step="0.01" class="form-control" name="porcentaje_comision" value="<?= htmlspecialchars($r['porcentaje_comision']) ?>" disabled></td> <!-- NUEVO -->
              <td><input type="date" class="form-control" name="fecha_solicitud" value="<?= htmlspecialchars($r['fecha_solicitud']) ?>" disabled></td>

              <td><input type="text" class="form-control" name="nombre_agente" value="<?= htmlspecialchars($r['nombre_agente']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="codigoSucursal" value="<?= htmlspecialchars($r['codigoSucursal']) ?>" disabled></td>

              <td>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-primary editar">Editar</button>
                  <button type="submit" class="btn btn-sm btn-success actualizar" disabled>Actualizar</button>
                </div>
              </td>
            </form>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h3>Actualizar Clientes — Seguro de Incendio</h3>
  <?php $inc = $controller->listarClientesIncendio(); ?>
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle" data-tipo="incendio">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th>
          <th>CURP</th><th>RFC</th><th>Teléfono</th>
          <th>Usuario</th><th>Correo</th>
          <th>Valor Vivienda</th><th>Antigüedad</th>
          <th>Nivel</th><th>Causa Probable</th><th>Tipo Construcción</th>
          <th>% Comisión</th> <!-- NUEVO -->
          <th>Fecha Solicitud</th>
          <th>Agente</th><th>Sucursal</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($inc as $i): ?>
          <tr>
            <form action="../controllers/ActualizarController.php" method="post">
              <input type="hidden" name="tipo" value="incendio">
              <input type="hidden" name="id" value="<?= $i['id_cliente'] ?>">

              <td><input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($i['nombre']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="apellidoPaterno" value="<?= htmlspecialchars($i['apellidoPaterno']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="apellidoMaterno" value="<?= htmlspecialchars($i['apellidoMaterno']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="curp" value="<?= htmlspecialchars($i['curp']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="rfc" value="<?= htmlspecialchars($i['rfc']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($i['telefono']) ?>" disabled></td>

              <td><input type="text" class="form-control" name="usuario" value="<?= htmlspecialchars($i['usuario']) ?>" disabled></td>
              <td><input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($i['correo']) ?>" disabled></td>

              <td><input type="number" class="form-control" name="valor_vivienda" value="<?= htmlspecialchars($i['valor_vivienda']) ?>" disabled></td>
              <td><input type="number" class="form-control" name="antiguedad" value="<?= htmlspecialchars($i['antiguedad']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="nivel_incendio" value="<?= htmlspecialchars($i['nivel_incendio']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="causa_probable" value="<?= htmlspecialchars($i['causa_probable']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="tipo_construccion" value="<?= htmlspecialchars($i['tipo_construccion']) ?>" disabled></td>
              <td><input type="number" step="0.01" class="form-control" name="porcentaje_comision" value="<?= htmlspecialchars($i['porcentaje_comision']) ?>" disabled></td> <!-- NUEVO -->
              <td><input type="date" class="form-control" name="fecha_solicitud" value="<?= htmlspecialchars($i['fecha_solicitud']) ?>" disabled></td>

              <td><input type="text" class="form-control" name="nombre_agente" value="<?= htmlspecialchars($i['nombre_agente']) ?>" disabled></td>
              <td><input type="text" class="form-control" name="codigoSucursal" value="<?= htmlspecialchars($i['codigoSucursal']) ?>" disabled></td>

              <td>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-primary editar">Editar</button>
                  <button type="submit" class="btn btn-sm btn-success actualizar" disabled>Actualizar</button>
                </div>
              </td>
            </form>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  </section>        
    <!-- ELIMINAR -->
    <section id="eliminar" class="section">
      <h3>Clientes — Seguro de Vida</h3>
      <?php $vida = $controller->listarClientesVida(); ?>
      <?php if (isset($_SESSION['eliminar_success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['eliminar_success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['eliminar_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['eliminar_error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['eliminar_error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['eliminar_error']); ?>
    <?php endif; ?>

      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead class="table-dark">
            <tr>
              <th>Nombre</th>
              <th>CURP</th>
              <th>RFC</th>
              <th>Teléfono</th>
              <th>Usuario</th>
              <th>Correo</th>
              <th>Edad</th>
              <th>Folio</th>
              <th>Valor Asegurado</th>
              <th>% Comisión</th>
              <th>Fecha Solicitud</th>
              <th>Agente</th>
              <th>Sucursal</th>
              <th>Estado Solicitud</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($vida as $v): ?>
              <tr>
                <td><?= htmlspecialchars($v['nombre'].' '.$v['apellidoPaterno'].' '.$v['apellidoMaterno']) ?></td>
                <td><?= htmlspecialchars($v['curp']) ?></td>
                <td><?= htmlspecialchars($v['rfc']) ?></td>
                <td><?= htmlspecialchars($v['telefono']) ?></td>
                <td><?= htmlspecialchars($v['usuario']) ?></td>
                <td><?= htmlspecialchars($v['correo']) ?></td>
                <td><?= htmlspecialchars($v['edad']) ?></td>
                <td><?= htmlspecialchars($v['folio_vida']) ?></td>
                <td><?= htmlspecialchars($v['valor_asegurado']) ?></td>
                <td><?= htmlspecialchars($v['porcentaje_comision']) ?>%</td>
                <td><?= htmlspecialchars($v['fecha_solicitud']) ?></td>
                <td><?= htmlspecialchars($v['nombre_agente']) ?></td>
                <td><?= htmlspecialchars($v['codigoSucursal']) ?></td>
                <td><?= htmlspecialchars($v['estado_solicitud']) ?></td>
                <td>
                  <form action="../controllers/ActualizarController.php" method="post" class="d-inline">
                    <input type="hidden" name="accion" value="cambiar_estatus">
                    <input type="hidden" name="id_solicitud" value="<?= $v['id_solicitud'] ?>">
                    <button type="submit" name="nuevo_estatus" value="Activo" class="btn btn-sm btn-success">Activar</button>
                    <button type="submit" name="nuevo_estatus" value="Inactivo" class="btn btn-sm btn-danger">Desactivar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <h3>Clientes — Seguro de Auto</h3>
    <?php $auto = $controller->listarClientesAuto(); ?>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Nombre</th>
            <th>CURP</th>
            <th>RFC</th>
            <th>Teléfono</th>
            <th>Usuario</th>
            <th>Correo</th>
            <th>Matrícula</th>
            <th>Modelo</th>
            <th>Año</th>
            <th>Valor Factura</th>
            <th>% Comisión</th>
            <th>Fecha Solicitud</th>
            <th>Agente</th>
            <th>Sucursal</th>
            <th>Estado Solicitud</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($auto as $au): ?>
            <tr>
              <td><?= htmlspecialchars($au['nombre'].' '.$au['apellidoPaterno'].' '.$au['apellidoMaterno']) ?></td>
              <td><?= htmlspecialchars($au['curp']) ?></td>
              <td><?= htmlspecialchars($au['rfc']) ?></td>
              <td><?= htmlspecialchars($au['telefono']) ?></td>
              <td><?= htmlspecialchars($au['usuario']) ?></td>
              <td><?= htmlspecialchars($au['correo']) ?></td>
              <td><?= htmlspecialchars($au['matricula']) ?></td>
              <td><?= htmlspecialchars($au['modelo']) ?></td>
              <td><?= htmlspecialchars($au['anio']) ?></td>
              <td><?= htmlspecialchars($au['porcentaje_comision']) ?>%</td>
              <td><?= htmlspecialchars($au['valor_factura']) ?></td>
              <td><?= htmlspecialchars($au['fecha_solicitud']) ?></td>
              <td><?= htmlspecialchars($au['nombre_agente']) ?></td>
              <td><?= htmlspecialchars($au['codigoSucursal']) ?></td>
              <td><?= htmlspecialchars($au['estado_solicitud']) ?></td>
              <td>
                <form action="../controllers/ActualizarController.php" method="post" class="d-inline">
                  <input type="hidden" name="accion" value="cambiar_estatus">
                  <input type="hidden" name="id_solicitud" value="<?= $au['id_solicitud'] ?>">
                  <button type="submit" name="nuevo_estatus" value="Activo" class="btn btn-sm btn-success">Activar</button>
                  <button type="submit" name="nuevo_estatus" value="Inactivo" class="btn btn-sm btn-danger">Desactivar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <h3>Clientes — Seguro de Robo</h3>
    <?php $robo = $controller->listarClientesRobo(); ?>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Nombre</th><th>CURP</th><th>RFC</th><th>Teléfono</th>
            <th>Usuario</th><th>Correo</th><th>Objeto</th><th>Medidas Seguridad</th><th>Valor Artículo</th>
            <th>% Comisión</th>
            <th>Fecha Solicitud</th><th>Agente</th><th>Sucursal</th>
            <th>Estado Solicitud</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($robo as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['nombre'].' '.$r['apellidoPaterno'].' '.$r['apellidoMaterno']) ?></td>
              <td><?= htmlspecialchars($r['curp']) ?></td>
              <td><?= htmlspecialchars($r['rfc']) ?></td>
              <td><?= htmlspecialchars($r['telefono']) ?></td>
              <td><?= htmlspecialchars($r['usuario']) ?></td>
              <td><?= htmlspecialchars($r['correo']) ?></td>
              <td><?= htmlspecialchars($r['tipo_objeto']) ?></td>
              <td><?= htmlspecialchars($r['medidas_seguridad']) ?></td>
              <td><?= htmlspecialchars($r['valor_articulo']) ?></td>
              <td><?= htmlspecialchars($r['porcentaje_comision']) ?>%</td>
              <td><?= htmlspecialchars($r['fecha_solicitud']) ?></td>
              <td><?= htmlspecialchars($r['nombre_agente']) ?></td>
              <td><?= htmlspecialchars($r['codigoSucursal']) ?></td>
              <td><?= htmlspecialchars($r['estado_solicitud']) ?></td>
              <td>
                <form action="../controllers/ActualizarController.php" method="post" class="d-inline">
                  <input type="hidden" name="accion" value="cambiar_estatus">
                  <input type="hidden" name="id_solicitud" value="<?= $r['id_solicitud'] ?>">
                  <button type="submit" name="nuevo_estatus" value="Activo" class="btn btn-sm btn-success">Activar</button>
                  <button type="submit" name="nuevo_estatus" value="Inactivo" class="btn btn-sm btn-danger">Desactivar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>      
    <h3>Clientes — Seguro de Incendio</h3>
    <?php $inc = $controller->listarClientesIncendio(); ?>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Nombre</th><th>CURP</th><th>RFC</th><th>Teléfono</th>
            <th>Usuario</th><th>Correo</th><th>Valor Vivienda</th><th>Antigüedad</th>
            <th>Nivel</th><th>Causa Probable</th><th>Tipo Construcción</th>
            <th>Fecha Solicitud</th><th>% Comisión</th><th>Agente</th><th>Sucursal</th>
            <th>Estado Solicitud</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($inc as $i): ?>
            <tr>
              <td><?= htmlspecialchars($i['nombre'].' '.$i['apellidoPaterno'].' '.$i['apellidoMaterno']) ?></td>
              <td><?= htmlspecialchars($i['curp']) ?></td>
              <td><?= htmlspecialchars($i['rfc']) ?></td>
              <td><?= htmlspecialchars($i['telefono']) ?></td>
              <td><?= htmlspecialchars($i['usuario']) ?></td>
              <td><?= htmlspecialchars($i['correo']) ?></td>
              <td><?= htmlspecialchars($i['valor_vivienda']) ?></td>
              <td><?= htmlspecialchars($i['antiguedad']) ?></td>
              <td><?= htmlspecialchars($i['nivel_incendio']) ?></td>
              <td><?= htmlspecialchars($i['causa_probable']) ?></td>
              <td><?= htmlspecialchars($i['tipo_construccion']) ?></td>
              <td><?= htmlspecialchars($i['fecha_solicitud']) ?></td>
              <td><?= htmlspecialchars($i['porcentaje_comision']) ?>%</td>
              <td><?= htmlspecialchars($i['nombre_agente']) ?></td>
              <td><?= htmlspecialchars($i['codigoSucursal']) ?></td>
              <td><?= htmlspecialchars($i['estado_solicitud']) ?></td>
              <td>
                <form action="../controllers/ActualizarController.php" method="post" class="d-inline">
                  <input type="hidden" name="accion" value="cambiar_estatus">
                  <input type="hidden" name="id_solicitud" value="<?= $i['id_solicitud'] ?>">
                  <button type="submit" name="nuevo_estatus" value="Activo" class="btn btn-sm btn-success">Activar</button>
                  <button type="submit" name="nuevo_estatus" value="Inactivo" class="btn btn-sm btn-danger">Desactivar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    
    </section>

  </main>

  <script>
      document.addEventListener("DOMContentLoaded", function() {
      const hash = window.location.hash || "#perfil"; // por defecto perfil
      document.querySelectorAll(".section").forEach(sec => sec.classList.remove("active"));
      const target = document.querySelector(hash);
      if (target) {
        target.classList.add("active");
      }
    });

    // Activar validaciones Bootstrap
    (function () {
      'use strict';
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();

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

//js para actualizar
document.querySelectorAll(".editar").forEach(btn => {
  btn.addEventListener("click", e => {
    const row = e.target.closest("tr");
    row.querySelectorAll("input").forEach(inp => inp.removeAttribute("disabled"));
    row.querySelector(".actualizar").removeAttribute("disabled");
  });
});


  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>   
              