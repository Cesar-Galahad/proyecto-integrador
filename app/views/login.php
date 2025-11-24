<?php
// Ruta: app/views/login.php
session_start();
$loginError = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login — Aseguradora</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../public/css/shared.css">
  <link rel="stylesheet" href="../../public/css/login.css?v=1">

</head>
<body class="bg-light login-page">

  <div class="container d-flex align-items-center justify-content-center min-vh-100 flex-column">

    <!-- Logo y título fuera de la card -->
    <div class="text-center mb-2">
      <a href="../../index.php">
        <img src="../../public/assets/logo2.png" alt="Logo" class="logo" onerror="this.style.display='none'">
      </a>
    </div>

    <!-- Card -->
    <div class="card shadow-sm login-card" style="max-width: 360px; width: 100%;">
      <div class="card-body p-4">
        <div class="text-center">
          <h5 class="mt-1 text-muted">Bienvenido</h5>
          <h5 class="form-label">Inicia sesión para continuar</h5>
        </div>
        
        <!-- Mensajes de error -->
        <?php if ($loginError): ?>
          <div class="alert alert-danger text-center"><?= htmlspecialchars($loginError) ?></div>
        <?php endif; ?>

        <!-- Formulario -->
        <form id="loginForm" class="needs-validation" method="post" action="../../public/auth.php" novalidate>
          
          <!-- Usuario -->
          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <div class="input-group">
              <span class="input-group-text">
                <img src="../../public/assets/usuario.png" alt="Usuario" class="icon-input">
              </span>
              <input name="username" type="text" class="form-control" id="username" required>
              <div class="invalid-feedback">Ingresa tu usuario.</div>
            </div>
          </div>

          <!-- Contraseña -->
          <div class="mb-3">
            <label class="form-label">Contraseña</label>
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

          <button type="submit" class="btn btn-primary w-100">Inicia sesión</button>
        </form>

        <hr class="my-3">
        <a class="recuperacion d-block text-center text-decoration-none mt-2" href="recuperar.php">¿Olvidaste tu contraseña?</a>
      </div>
    </div>
  </div>

  <script src="../../public/js/login.js"></script>
</body>
</html>