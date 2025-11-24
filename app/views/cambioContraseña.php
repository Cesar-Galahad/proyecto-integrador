<?php
// Ruta: app/views/cambiocontraseña.php
session_start();
$loginError = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Cambio de contraseña — Aseguradora</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Estilos propios -->
  <link rel="stylesheet" href="../../public/css/shared.css">
  <link rel="stylesheet" href="../../public/css/login.css?v=2">
</head>
<body class="bg-light login-page">

  <div class="container d-flex align-items-center justify-content-center min-vh-100 flex-column">

    <!-- Logo -->
    <div class="text-center mb-2">
      <a href="../../index.php">
        <img src="../../public/assets/logo2.png" alt="Logo" class="logo" onerror="this.style.display='none'">
      </a>
    </div>

    <!-- Card -->
    <div class="card shadow-sm login-card" style="max-width: 380px; width: 100%;">
      <div class="card-body p-4">
        <div class="text-center">
          <h5 class="mt-1 text-muted">Bienvenido</h5>
          <h5 class="form-label">Restablece tu contraseña para continuar</h5>
          <div id="alertBox"></div>

        </div>
        
        <!-- Mensajes de error -->
        <?php if ($loginError): ?>
          <div class="alert alert-danger text-center"><?= htmlspecialchars($loginError) ?></div>
        <?php endif; ?>

        <!-- Formulario -->
        <form id="changeForm" class="needs-validation" method="post" action="../../public/cambiocontraseña.php" novalidate>
          
          <!-- Nueva contraseña -->
          <div class="mb-3">
            <label class="form-label" for="password">Nueva contraseña</label>
            <div class="input-group">
              <span class="input-group-text">
                <img src="../../public/assets/candado.png" alt="Contraseña" class="icon-input">
              </span>
              <input name="password" type="password" class="form-control" id="password" required minlength="6">
              <button class="btn btn-outline-secondary eye-btn" type="button" id="togglePassword">
                <img src="../../public/assets/invisible.png" alt="Mostrar" class="icon-input">
              </button>
              <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
            </div>
          </div>

          <!-- Confirmar contraseña -->
          <div class="mb-3">
            <label class="form-label" for="confirmPassword">Confirmar contraseña</label>
            <div class="input-group">
              <span class="input-group-text">
                <img src="../../public/assets/candado.png" alt="Confirmar" class="icon-input">
              </span>
              <input name="confirmPassword" type="password" class="form-control" id="confirmPassword" required minlength="6">

              <button class="btn btn-outline-secondary eye-btn" type="button" id="toggleConfirmPassword">
                <img src="../../public/assets/invisible.png" alt="Mostrar" class="icon-input">
              </button>
              <div class="invalid-feedback">Debes confirmar la contraseña.</div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100">Restablecer contraseña</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Mostrar/ocultar contraseña
    document.getElementById('togglePassword').addEventListener('click', function () {
      const pwd = document.getElementById('password');
      pwd.type = pwd.type === 'password' ? 'text' : 'password';
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
      const pwd = document.getElementById('confirmPassword');
      pwd.type = pwd.type === 'password' ? 'text' : 'password';
    });

    // Validación Bootstrap + contraseñas iguales
(function () {
  'use strict';
  const form = document.getElementById('changeForm');
  form.addEventListener('submit', function (event) {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }

    const pwd = document.getElementById('password').value;
    const confirmPwd = document.getElementById('confirmPassword').value;

    if (pwd !== confirmPwd) {
      event.preventDefault();
      document.getElementById('alertBox').innerHTML =
        '<div class="alert alert-danger text-center">Las contraseñas no coinciden.</div>';
    }


    form.classList.add('was-validated');
  }, false);
})();


  </script>
</body>
</html>