<?php
// Ruta: public/recuperacionCorreo.php
require_once __DIR__ . '/../app/Core/DB.php';

// Variables para mensajes
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = "Debes ingresar un correo.";
    } else {
        try {
            $pdo = DB::connect();

            // Buscar en agentes
            $stmtAgente = $pdo->prepare("SELECT id_agente FROM agentes WHERE correo = :correo LIMIT 1");
            $stmtAgente->execute([':correo' => $email]);
            $agente = $stmtAgente->fetch(PDO::FETCH_ASSOC);

            // Buscar en clientes
            $stmtCliente = $pdo->prepare("SELECT id_cliente FROM clientes WHERE correo = :correo LIMIT 1");
            $stmtCliente->execute([':correo' => $email]);
            $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

            if ($agente || $cliente) {
                // Aquí más adelante integras PHPMailer
                // Ejemplo:
                /*
                use PHPMailer\PHPMailer\PHPMailer;
                use PHPMailer\PHPMailer\SMTP;
                use PHPMailer\PHPMailer\Exception;
                require __DIR__ . '/../app/libs/PHPMailer/src/Exception.php';
                require __DIR__ . '/../app/libs/PHPMailer/src/PHPMailer.php';
                require __DIR__ . '/../app/libs/PHPMailer/src/SMTP.php';

                $mail = new PHPMailer(true);
                // Configuración SMTP...
                $mail->addAddress($email);
                $mail->Subject = 'Recupera tu contraseña';
                $mail->Body    = 'Haz clic en el enlace para restablecer tu contraseña...';
                $mail->send();
                */

                $success = "Correo enviado. Revisa tu bandeja.";
            } else {
                $error = "El correo no está registrado.";
            }
        } catch (Exception $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Recuperación de contraseña</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5" style="max-width: 400px;">
    <?php if ($error): ?>
      <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" class="needs-validation" novalidate>
      <div class="mb-3">
        <label class="form-label" for="email">Correo electrónico</label>
        <input type="email" class="form-control" id="email" name="email" required>
        <div class="invalid-feedback">Ingresa un correo válido.</div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Recuperar contraseña</button>
    </form>
  </div>

  <script>
    (function () {
      'use strict';
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();
  </script>
</body>
</html>