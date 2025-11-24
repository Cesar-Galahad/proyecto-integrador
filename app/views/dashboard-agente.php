<?php
  require_once __DIR__ . '/../Core/SessionGuard.php';
  require_once __DIR__ . '/../controllers/AgenteController.php';

  SessionGuard::requireRole('agente');

  $controller = new AgenteController();
  $idAgente = $_SESSION['id_agente'];

  $perfil   = $controller->perfil($idAgente);
  $usuario  = $perfil['usuario'] ?? '';
  $vida     = $controller->listarClientesVidaActivos($idAgente);
  $auto     = $controller->listarClientesAutoActivos($idAgente);
  $robo     = $controller->listarClientesRoboActivos($idAgente);
  $incendio = $controller->listarClientesIncendioActivos($idAgente);

  // Ordenamiento si viene POST
  $tabla = $_POST['tabla'] ?? null;
  $campoOrden = $_POST['ordenar_por'] ?? null;
  if ($tabla && $campoOrden) {
      $controller->ordenarClientes($tabla, $campoOrden, $vida, $auto, $robo, $incendio);
  }

  $activeSection = $_SESSION['active_section'] ?? 'perfil';
  unset($_SESSION['active_section']);
?>



<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard Agente — Aseguradora</title>
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
      <h1>Panel Agente</h1>
      <div class="user-info">
        <span><?= htmlspecialchars($usuario) ?></span>
      </div>
    </header>

    <section id="perfil" class="section <?php echo $activeSection === 'perfil' ? 'active' : ''; ?>">
      <h3 class="mb-4">Perfil</h3>

      <?php if ($perfil): ?>
        <div class="card shadow-sm border-0 perfil-card">
          <div class="card-body d-flex flex-column flex-md-row align-items-center">
            
            <!-- Icono del perfil -->
            <div class="perfil-icon me-lg-5 mb-3 mb-md-0 text-center">
              <img src="../../public/assets/usuario.png" alt="Icono perfil"
                  class="rounded-circle border p-2" style="width: 90px; height: 90px;">
              <p class="mt-2 text-muted small">Agente</p>
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
              <div class="d-flex mb-2">
                <span class="text-muted fw-bold me-2">Sueldo Base:</span>
                <span>$<?= htmlspecialchars($perfil['sueldoBase']) ?></span>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-4">
  <button class="btn btn-primary" onclick="mostrarTabla()">Solicitudes Vendidas</button>
  </div>

  <div id="tablaSeguros" style="display:none;" class="mt-4">
    <h4>Solicitudes Vendidas</h4>
    <table id="tablaSolicitudes" class="table table-hover table-bordered">
      <thead class="table-dark">
        <tr>
          <th>ID Solicitud</th>
          <th>Cliente</th>
          <th>Cantidad</th>
          <th>Fecha</th>
          <th>% Comisión</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($solicitudesVendidas)): ?>
        <?php foreach ($solicitudesVendidas as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['id_solicitud']) ?></td>
            <td><?= htmlspecialchars($s['cliente']) ?></td>
            <td><?= htmlspecialchars($s['cantidad']) ?></td>
            <td><?= htmlspecialchars($s['fecha']) ?></td>
            <td><?= htmlspecialchars($s['porcentaje']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="text-center text-muted">No hay solicitudes activas vendidas.</td>
        </tr>
      <?php endif; ?>

      </tbody>
    </table>

    <button class="btn btn-success mt-3" onclick="calcularTotalRecursivo()">Calcular Comisión</button>
    <p id="resultado" class="mt-2 fw-bold text-success"></p>
  </div>
      <?php else: ?>
        <p class="text-muted">No se encontró información del agente.</p>
      <?php endif; ?>
    </section>

  <section id="crear" class="section <?php echo $activeSection === 'crear' ? 'active' : ''; ?>">
  <h3>Crear nueva póliza</h3>
        <?php if (isset($_SESSION['crear_success'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['crear_success']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
  <?php unset($_SESSION['crear_success']); ?>
<?php endif; ?>

    <form method="POST" action="../controllers/CrearPoliza.php" 
          class="row g-3 needs-validation" novalidate>

      <!-- Datos del cliente -->
      <h5>Datos del cliente</h5>
      <div class="col-md-4">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
        <div class="invalid-feedback">El nombre es obligatorio.</div>
      </div>
      <div class="col-md-4">
        <label>Apellido paterno</label>
        <input type="text" name="apellidoPaterno" class="form-control" required>
        <div class="invalid-feedback">El apellido paterno es obligatorio.</div>
      </div>
      <div class="col-md-4">
        <label>Apellido materno</label>
        <input type="text" name="apellidoMaterno" class="form-control" required>
        <div class="invalid-feedback">El apellido materno es obligatorio.</div>
      </div>
      <div class="col-md-4">
        <label>CURP</label>
        <input type="text" name="curp" class="form-control" required>
        <div class="invalid-feedback">La CURP es obligatoria.</div>
      </div>
      <div class="col-md-4">
        <label>RFC</label>
        <input type="text" name="rfc" class="form-control" required>
        <div class="invalid-feedback">El RFC es obligatorio.</div>
      </div>
      <div class="col-md-4">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control" required>
        <div class="invalid-feedback">El teléfono es obligatorio.</div>
      </div>
      <div class="col-12">
        <label>Dirección</label>
        <input type="text" name="direccion" class="form-control" required>
        <div class="invalid-feedback">La dirección es obligatoria.</div>
      </div>


      <!-- Datos de la solicitud -->
      <h5 class="mt-4">Datos de la solicitud</h5>
      <div class="col-md-4">
        <label>Tipo de seguro</label>
        <select name="idTipoSeguro" id="tipoSeguro" class="form-select" required>
          <option value="">Selecciona...</option>
          <option value="vida">Vida</option>
          <option value="auto">Auto</option>
          <option value="incendio">Incendio</option>
          <option value="robo">Robo</option>
        </select>
        <div class="invalid-feedback">Debes seleccionar un tipo de seguro.</div>
      </div>
      <div class="col-md-4">
        <label>Fecha de recepción</label>
        <input type="date" name="fechaRecepcion" class="form-control" required>
        <div class="invalid-feedback">La fecha de recepción es obligatoria.</div>
      </div>

      <!-- Inputs dinámicos -->
      <div id="inputsSeguro" class="row g-3 mt-3"></div>

      <!-- Credenciales del cliente -->
      <h5 class="mt-4">Credenciales de acceso</h5>
      <div class="col-md-6">
        <label>Nombre de usuario</label>
        <input type="text" name="usuario" class="form-control" required>
        <div class="invalid-feedback">El usuario es obligatorio.</div>
      </div>
      <div class="col-md-6">
        <label>Contraseña</label>
        <input type="password" name="passwordCliente" class="form-control" required>
        <div class="invalid-feedback">La contraseña es obligatoria.</div>
      </div>
      <div class="col-md-6">
        <label>Correo</label>
        <input type="email" name="correo" class="form-control" required>
        <div class="invalid-feedback">El correo es obligatorio.</div>
      </div>

      <!-- Botón -->
      <div class="col-12 mt-4">
        <button type="submit" class="btn btn-primary">Registrar póliza</button>
      </div>
    </form>
  </section>


    <section id="leer" class="section <?php echo $activeSection === 'leer' ? 'active' : ''; ?>">
    <h3>Clientes — Seguro de Vida</h3>
    <form method="POST" class="mb-3 d-flex gap-2">
      <input type="hidden" name="tabla" value="vida">
      <select name="ordenar_por" class="form-select w-auto">
        <option value="">Ordenar por...</option>
        <option value="nombre">Nombre</option>
        <option value="curp">CURP</option>
        <option value="fecha_solicitud">Fecha Solicitud</option>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Ordenar</button>
    </form>

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
            <th>Valor Asegurado</th>
            <th>% Comisión</th>
            <th>Sucursal</th>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($vida) && is_array($vida) && count($vida) > 0): ?>
            <?php foreach ($vida as $v): ?>
              <tr>
                <td><?= htmlspecialchars(($v['nombre'] ?? '').' '.($v['apellidoPaterno'] ?? '').' '.($v['apellidoMaterno'] ?? '')) ?></td>
                <td><?= htmlspecialchars($v['curp'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['rfc'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['telefono'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['usuario'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['correo'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['edad'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['folio_vida'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['fecha_solicitud'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['valor_asegurado'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['porcentaje_comision'] ?? '') ?>%</td>
                <td><?= htmlspecialchars($v['codigoSucursal'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="12" class="text-center text-muted">No hay pólizas de Vida activas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <h3>Clientes — Seguro de Auto</h3>

    <form method="POST" class="mb-3 d-flex gap-2">
      <input type="hidden" name="tabla" value="auto">
      <select name="ordenar_por" class="form-select w-auto">
        <option value="">Ordenar por...</option>
        <option value="nombre">Nombre</option>
        <option value="curp">CURP</option>
        <option value="fecha_solicitud">Fecha Solicitud</option>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Ordenar</button>
    </form>

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
            <th>Fecha Solicitud</th>
            <th>Valor Factura</th>
            <th>% Comisión</th>
            <th>Sucursal</th>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($auto) && is_array($auto) && count($auto) > 0): ?>
            <?php foreach ($auto as $a): ?>
              <tr>
                <td><?= htmlspecialchars(($a['nombre'] ?? '').' '.($a['apellidoPaterno'] ?? '').' '.($a['apellidoMaterno'] ?? '')) ?></td>
                <td><?= htmlspecialchars($a['curp'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['rfc'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['telefono'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['usuario'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['correo'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['matricula'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['modelo'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['anio'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['fecha_solicitud'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['valor_factura'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['porcentaje_comision'] ?? '') ?>%</td>
                <td><?= htmlspecialchars($a['codigoSucursal'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="13" class="text-center text-muted">No hay pólizas de Auto activas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>  

    <h3>Clientes — Seguro de Robo</h3>

    <form method="POST" class="mb-3 d-flex gap-2">
      <input type="hidden" name="tabla" value="robo">
      <select name="ordenar_por" class="form-select w-auto">
        <option value="">Ordenar por...</option>
        <option value="nombre">Nombre</option>
        <option value="curp">CURP</option>
        <option value="fecha_solicitud">Fecha Solicitud</option>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Ordenar</button>
    </form>

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
            <th>Tipo Objeto</th>
            <th>Medidas Seguridad</th>
            <th>Fecha Solicitud</th>
            <th>Valor Artículo</th>
            <th>% Comisión</th>
            <th>Sucursal</th>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($robo) && is_array($robo) && count($robo) > 0): ?>
            <?php foreach ($robo as $r): ?>
              <tr>
                <td><?= htmlspecialchars(($r['nombre'] ?? '').' '.($r['apellidoPaterno'] ?? '').' '.($r['apellidoMaterno'] ?? '')) ?></td>
                <td><?= htmlspecialchars($r['curp'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['rfc'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['telefono'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['usuario'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['correo'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['tipo_objeto'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['medidas_seguridad'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['fecha_solicitud'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['valor_articulo'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['porcentaje_comision'] ?? '') ?>%</td>
                <td><?= htmlspecialchars($r['codigoSucursal'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="12" class="text-center text-muted">No hay pólizas de Robo activas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <h3>Clientes — Seguro de Incendio</h3>

    <form method="POST" class="mb-3 d-flex gap-2">
      <input type="hidden" name="tabla" value="incendio">
      <select name="ordenar_por" class="form-select w-auto">
        <option value="">Ordenar por...</option>
        <option value="nombre">Nombre</option>
        <option value="curp">CURP</option>
        <option value="fecha_solicitud">Fecha Solicitud</option>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Ordenar</button>
    </form>

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
            <th>Nivel Incendio</th>
            <th>Causa Probable</th>
            <th>Tipo Construcción</th>
            <th>Fecha Solicitud</th>
            <th>% Comisión</th>
            <th>Sucursal</th>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($incendio) && is_array($incendio) && count($incendio) > 0): ?>
            <?php foreach ($incendio as $i): ?>
              <tr>
                <td><?= htmlspecialchars(($i['nombre'] ?? '').' '.($i['apellidoPaterno'] ?? '').' '.($i['apellidoMaterno'] ?? '')) ?></td>
                <td><?= htmlspecialchars($i['curp'] ?? '') ?></td>
                <td><?= htmlspecialchars($i['rfc'] ?? '') ?></td>
                <td><?= htmlspecialchars($i['telefono'] ?? '') ?></td>
                <td><?= htmlspecialchars($i['usuario'] ?? '') ?></td>
                <td><?= htmlspecialchars($i['correo'] ?? '') ?></td>
                <td><?= htmlspecialchars($i['valor_vivienda'] ?? '') ?></td>
                <td><?= htmlspecialchars($i['antiguedad'] ?? '') ?></td>
                <td><?= htmlspecialchars($i['nivel_incendio'] ?? '') ?></td>
                <td><?= htmlspecialchars($i['causa_probable'] ?? '') ?></td>
                <td><?= htmlspecialchars($i['tipo_construccion'] ?? '') ?></td>
                <td><?= htmlspecialchars($i['fecha_solicitud'] ?? '') ?></td>
                <td><?= htmlspecialchars($i['porcentaje_comision'] ?? '') ?>%</td>
                <td><?= htmlspecialchars($i['codigoSucursal'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="14" class="text-center text-muted">No hay pólizas de Incendio activas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>        

    </section>

    <section id="actualizar" class="section <?php echo $activeSection === 'actualizar' ? 'active' : ''; ?>">
      <h3>Actualizar Clientes — Seguro de Vida</h3>
      <?php $vida = $controller->listarClientesVidaActivos($idAgente); ?>
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle" data-tipo="vida">
          <thead class="table-dark">
            <tr>
              <th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th>
              <th>CURP</th><th>RFC</th><th>Teléfono</th>
              <th>Usuario</th><th>Correo</th><th>Edad</th><th>Folio</th>
              <th>Fecha Solicitud</th><th>Valor Asegurado</th><th>% Comisión</th>
              <th>Sucursal</th><th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($vida as $v): ?>
              <tr>
                <form action="../controllers/ActualizarAController.php" method="post">
                  <input type="hidden" name="tipo" value="vida">
                  <input type="hidden" name="id" value="<?= $row['id_cliente'] ?>">



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
                  <td><input type="number" step="0.01" class="form-control" name="valor_asegurado" value="<?= htmlspecialchars($v['valor_asegurado']) ?>" disabled></td>
                  <td><input type="number" step="0.01" class="form-control" name="porcentaje_comision" value="<?= htmlspecialchars($v['porcentaje_comision']) ?>" disabled></td>
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


     <h3>Actualizar Clientes — Seguro de Auto</h3>
      <?php $auto = $controller->listarClientesAutoActivos($idAgente); ?>
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle" data-tipo="auto">
          <thead class="table-dark">
            <tr>
              <th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th>
              <th>CURP</th><th>RFC</th><th>Teléfono</th>
              <th>Usuario</th><th>Correo</th>
              <th>Matrícula</th><th>Modelo</th><th>Año</th>
              <th>Valor Factura</th><th>% Comisión</th>
              <th>Fecha Solicitud</th>
              <th>Sucursal</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($auto as $au): ?>
              <tr>
                <form action="../controllers/ActualizarAController.php" method="post">
                  <input type="hidden" name="tipo" value="auto">
                  <input type="hidden" name="id" value="<?= $row['id_cliente'] ?>">


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
                  <td><input type="number" class="form-control" step="0.01" name="porcentaje_comision" value="<?= htmlspecialchars($au['porcentaje_comision']) ?>" disabled></td>
                  <td><input type="date" class="form-control" name="fecha_solicitud" value="<?= htmlspecialchars($au['fecha_solicitud']) ?>" disabled></td>

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
<h3>Actualizar Clientes — Seguro de Robo</h3>
<?php $robo = $controller->listarClientesRoboActivos($idAgente); ?>
<div class="table-responsive">
  <table class="table table-hover table-bordered align-middle" data-tipo="robo">
    <thead class="table-dark">
      <tr>
        <th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th>
        <th>CURP</th><th>RFC</th><th>Teléfono</th>
        <th>Usuario</th><th>Correo</th>
        <th>Objeto</th><th>Medidas Seguridad</th>
        <th>Valor Artículo</th><th>% Comisión</th>
        <th>Fecha Solicitud</th>
        <th>Sucursal</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($robo as $r): ?>
        <tr>
          <form action="../controllers/ActualizarAController.php" method="post">
            <input type="hidden" name="tipo" value="robo">
            <input type="hidden" name="id" value="<?= $row['id_cliente'] ?>">

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
            <td><input type="number" step="0.01" class="form-control" name="valor_articulo" value="<?= htmlspecialchars($r['valor_articulo']) ?>" disabled></td>
            <td><input type="number" step="0.01" class="form-control" name="porcentaje_comision" value="<?= htmlspecialchars($r['porcentaje_comision']) ?>" disabled></td>
            <td><input type="date" class="form-control" name="fecha_solicitud" value="<?= htmlspecialchars($r['fecha_solicitud']) ?>" disabled></td>

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
<?php $incendio = $controller->listarClientesIncendioActivos($idAgente); ?>
<div class="table-responsive">
  <table class="table table-hover table-bordered align-middle" data-tipo="incendio">
    <thead class="table-dark">
      <tr>
        <th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th>
        <th>CURP</th><th>RFC</th><th>Teléfono</th>
        <th>Usuario</th><th>Correo</th>
        <th>Valor Vivienda</th><th>Antigüedad</th>
        <th>Nivel</th><th>Causa Probable</th><th>Tipo Construcción</th>
        <th>% Comisión</th>
        <th>Fecha Solicitud</th>
        <th>Sucursal</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($incendio as $i): ?>
        <tr>
          <form action="../controllers/ActualizarAController.php" method="post">
            <input type="hidden" name="tipo" value="incendio">
            <input type="hidden" name="id" value="<?= $row['id_cliente'] ?>">
  
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
            <td><input type="number" step="0.01" class="form-control" name="porcentaje_comision" value="<?= htmlspecialchars($i['porcentaje_comision']) ?>" disabled></td>
            <td><input type="date" class="form-control" name="fecha_solicitud" value="<?= htmlspecialchars($i['fecha_solicitud']) ?>" disabled></td>

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
    
    <section id="eliminar" class="section <?php echo $activeSection === 'eliminar' ? 'active' : ''; ?>">
      <h3>Eliminar</h3>
      <p>Confirmación para eliminar.</p>
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
    function mostrarTabla() {
      document.getElementById('tablaSeguros').style.display = 'block';
    }

  
    function calcularTotalRecursivo() {
      const filas = document.querySelectorAll('#tablaSolicitudes tbody tr');

      function calcular(filaActual) {
        if (filaActual >= filas.length) return 0;

        const cantidad = parseFloat(filas[filaActual].children[2].textContent) || 0;
        const porcentaje = parseFloat(filas[filaActual].children[4].textContent) || 0;
        const comision = cantidad * porcentaje / 100;

        console.log(`Fila ${filaActual}: cantidad=${cantidad}, porcentaje=${porcentaje}, comisión=${comision}`);

        return comision + calcular(filaActual + 1);
      }

      const total = calcular(0);
      document.getElementById('resultado').textContent =
        "Comisión total generada: $" + total.toFixed(2);
    }

    //habilitar los inputs de crear poliza
    document.getElementById('tipoSeguro').addEventListener('change', function() {
    const tipo = this.value;
    const container = document.getElementById('inputsSeguro');
    container.innerHTML = '';

    if (tipo === 'vida') {
      container.innerHTML = `
        <h5>Seguro de Vida</h5>
        <div class="col-md-4">
          <label>Edad</label>
          <input type="number" name="edad" class="form-control" required>
        </div>
        <div class="col-md-8">
          <label>Enfermedades preexistentes</label>
          <input type="text" name="enfermedades_preexistentes" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Folio</label>
          <input type="text" name="folio_vida" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Valor asegurado</label>
          <input type="number" step="0.01" name="valor_asegurado" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>% Comisión</label>
          <input type="number" step="0.01" name="porcentaje_comision" class="form-control" required>
        </div>
      `;
    } else if (tipo === 'auto') {
      container.innerHTML = `
        <h5>Seguro de Auto</h5>
        <div class="col-md-4">
          <label>Matrícula</label>
          <input type="text" name="matricula" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Modelo</label>
          <input type="text" name="modelo" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Año</label>
          <input type="number" name="anio" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Valor factura</label>
          <input type="number" step="0.01" name="valor_factura" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>% Comisión</label>
          <input type="number" step="0.01" name="porcentaje_comision" class="form-control" required>
        </div>
      `;
    } else if (tipo === 'robo') {
      container.innerHTML = `
        <h5>Seguro de Robo</h5>
        <div class="col-md-6">
          <label>Tipo de objeto</label>
          <input type="text" name="tipo_objeto" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label>Medidas de seguridad</label>
          <input type="text" name="medidas_seguridad" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Valor artículo</label>
          <input type="number" step="0.01" name="valor_articulo" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>% Comisión</label>
          <input type="number" step="0.01" name="porcentaje_comision" class="form-control" required>
        </div>
      `;
    } else if (tipo === 'incendio') {
      container.innerHTML = `
        <h5>Seguro de Incendio</h5>
        <div class="col-md-4">
          <label>Valor vivienda</label>
          <input type="number" step="0.01" name="valor_vivienda" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Antigüedad</label>
          <input type="number" name="antiguedad" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Nivel incendio</label>
          <input type="text" name="nivel_incendio" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Causa probable</label>
          <input type="text" name="causa_probable" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>Tipo construcción</label>
          <input type="text" name="tipo_construccion" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label>% Comisión</label>
          <input type="number" step="0.01" name="porcentaje_comision" class="form-control" required>
        </div>
      `;
    }
  });
  //validacion con bootstrap
  (() => {
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


document.querySelectorAll(".editar").forEach(btn => {
  btn.addEventListener("click", e => {
    const row = e.target.closest("tr");
    row.querySelectorAll("input").forEach(inp => inp.removeAttribute("disabled"));
    row.querySelector(".actualizar").removeAttribute("disabled");
  });
});

  </script>
</body>
</html>