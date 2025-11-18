<?php
require_once __DIR__ . '/../Core/SessionGuard.php';
SessionGuard::requireRole('agente');

$usuario = $_SESSION['usuario'] ?? '';
$role = $_SESSION['role'] ?? '';
$estado = $_SESSION['estado'] ?? 'Activo';
$idAgente = $_SESSION['id_agente'] ?? null;

require_once __DIR__ . '/../models/Agente.php';
$agenteModel = new Agente();
$perfil = $idAgente ? $agenteModel->obtenerPerfil($idAgente) : null;
$agentes = $agenteModel->listar();


require_once __DIR__ . '/../models/Solicitud.php';
$solicitudModel = new Solicitud();

// Solicitudes para cálculo de comisión (ya lo tenías)
$solicitudes = $idAgente ? $solicitudModel->listarPorAgente($idAgente) : [];

// Clientes vinculados al agente logueado
$clientesAgente = $idAgente ? $solicitudModel->listarClientesPorAgente($idAgente) : [];

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

    <section id="perfil" class="section active">
      <h3>Perfil del agente</h3>

      <?php if ($perfil): ?>
        <div class="perfil-card">
          <div class="perfil-item"><span class="label">Nombre:</span><span class="value"><?= htmlspecialchars($perfil['nombre']) ?></span></div>
          <div class="perfil-item"><span class="label">Usuario:</span><span class="value"><?= htmlspecialchars($usuario) ?></span></div>
          <div class="perfil-item"><span class="label">Correo:</span><span class="value"><?= htmlspecialchars($perfil['correo']) ?></span></div>
          <div class="perfil-item"><span class="label">Sueldo base:</span><span class="value">$<?= htmlspecialchars($perfil['sueldoBase']) ?></span></div>
          <div class="perfil-item"><span class="label">Sucursal:</span><span class="value"><?= htmlspecialchars($perfil['ciudad']) ?>, <?= htmlspecialchars($perfil['estado']) ?></span></div>
          <div class="perfil-item"><span class="label">ID de sucursal:</span><span class="value"><?= htmlspecialchars($perfil['id_sucursal']) ?></span></div>
          <div class="perfil-item"><span class="label">Dirección:</span><span class="value"><?= htmlspecialchars($perfil['direccion']) ?></span></div>

        </div>

        <button class="btn btn-info mt-3" onclick="mostrarTabla()">Seguros vendidos</button>

        <div id="tablaSeguros" class="mt-4" style="display:none;">
          <h4>Solicitudes atendidas</h4>
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tablaSolicitudes">
              <thead class="table-dark">
                <tr>
                  <th>Folio</th>
                  <th>Fecha recepción</th>
                  <th>Cantidad asegurada</th>
                  <th>Tipo de seguro</th>
                  <th>% Comisión</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($solicitudes as $s): ?>
                  <tr>
                    <td><?= htmlspecialchars($s['folio']) ?></td>
                    <td><?= htmlspecialchars($s['fechaRecepcion']) ?></td>
                    <td><?= htmlspecialchars($s['cantidadAsegurada']) ?></td>
                    <td><?= htmlspecialchars($s['tipoSeguro']) ?></td>
                    <td><?= htmlspecialchars($s['porcentajeComision']) ?>%</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <button class="btn btn-success mt-3" onclick="calcularTotalRecursivo()">Consultar comisión</button>
          <p id="resultado" class="mt-2 fw-bold"></p>
        </div>
      <?php else: ?>
        <p class="text-muted">No se encontró información del agente.</p>
      <?php endif; ?>
    </section>

    <section id="crear" class="section">
  <h3>Crear nueva póliza</h3>
  <form method="POST" action="../controllers/CrearPoliza.php" class="row g-3">

    <!-- Datos del cliente -->
    <h5>Datos del cliente</h5>
    <div class="col-md-4">
      <label>Nombre</label>
      <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label>Apellido paterno</label>
      <input type="text" name="apellidoPaterno" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label>Apellido materno</label>
      <input type="text" name="apellidoMaterno" class="form-control">
    </div>
    <div class="col-md-4">
      <label>CURP</label>
      <input type="text" name="curp" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label>RFC</label>
      <input type="text" name="rfc" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label>Teléfono</label>
      <input type="text" name="telefono" class="form-control">
    </div>
    <div class="col-12">
      <label>Dirección</label>
      <input type="text" name="direccion" class="form-control">
    </div>

    <!-- Datos de la solicitud -->
    <h5 class="mt-4">Datos de la solicitud</h5>
    <div class="col-md-4">
      <label>Tipo de seguro</label>
      <select name="idTipoSeguro" class="form-select" required>
        <option value="">Selecciona...</option>
        <option value="1">Vida</option>
        <option value="2">Auto</option>
        <option value="3">Casa</option>
        <option value="4">Robo</option>
        <option value="5">Gastos Médicos</option>
        <option value="6">Empresarial</option>
      </select>
    </div>
    <div class="col-md-4">
      <label>Cantidad asegurada</label>
      <input type="number" name="cantidadAsegurada" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label>Fecha de recepción</label>
      <input type="date" name="fechaRecepcion" class="form-control" required>
    </div>

    <!-- Credenciales del cliente -->
    <h5 class="mt-4">Credenciales de acceso</h5>
    <div class="col-md-6">
      <label>Nombre de usuario</label>
      <input type="text" name="usuario" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>Contraseña</label>
      <input type="password" name="passwordCliente" class="form-control" required>
    </div>

    <!-- Botón -->
    <div class="col-12 mt-4">
      <button type="submit" class="btn btn-primary">Registrar póliza</button>
    </div>
  </form>
</section>

    
    <section id="leer" class="section">
      <h3>Clientes registrados por el agente</h3>
      <table class="table table-hover table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Folio</th>
            <th>Nombre</th>
            <th>CURP</th>
            <th>RFC</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Seguro</th>
            <th>Fecha recepción</th>
            <th>Cantidad asegurada</th>
            <th>Estado Solicitud</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($clientesAgente as $c): ?>
            <tr>
              <td><?= htmlspecialchars($c['folio']) ?></td>
              <td><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellidoPaterno'] . ' ' . $c['apellidoMaterno']) ?></td>
              <td><?= htmlspecialchars($c['curp']) ?></td>
              <td><?= htmlspecialchars($c['rfc']) ?></td>
              <td><?= htmlspecialchars($c['telefono']) ?></td>
              <td><?= htmlspecialchars($c['direccion']) ?></td>
              <td><?= htmlspecialchars($c['seguro']) ?></td>
              <td><?= htmlspecialchars($c['fechaRecepcion']) ?></td>
              <td><?= htmlspecialchars($c['cantidadAsegurada']) ?></td>
              <td><?= htmlspecialchars($c['estatusSolicitud']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section id="editar" class="section"><h3>Editar</h3><p>Formulario para editar.</p></section>
    <section id="eliminar" class="section"><h3>Eliminar</h3><p>Confirmación para eliminar.</p></section>
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


  </script>
</body>
</html>