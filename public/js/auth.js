// public/js/auth.js

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('loginForm');
  const alertContainer = document.getElementById('alertContainer');

  // Credenciales de ejemplo para pruebas (simulación sin backend)
  const users = [
    { username: 'cliente', password: '123456', role: 'cliente' },
    { username: 'agente', password: 'pass123', role: 'agente' },
    { username: 'gerente', password: 'pass123', role: 'gerente' }
  ];

  function findUser(username, password, role) {
    return users.find(u => u.username === username && u.password === password && u.role === role);
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    AppValidate.clearAlert(alertContainer);

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const role = document.getElementById('roleSelect').value;

    let valid = true;

    if (!AppValidate.validateUsername(username)) {
      const el = document.getElementById('username');
      el.classList.add('is-invalid');
      valid = false;
    } else {
      document.getElementById('username').classList.remove('is-invalid');
      document.getElementById('username').classList.add('is-valid');
    }

    if (!AppValidate.validateMinLength(password, 6)) {
      const el = document.getElementById('password');
      el.classList.add('is-invalid');
      valid = false;
    } else {
      document.getElementById('password').classList.remove('is-invalid');
      document.getElementById('password').classList.add('is-valid');
    }

    if (!role) {
      document.getElementById('roleSelect').classList.add('is-invalid');
      valid = false;
    } else {
      document.getElementById('roleSelect').classList.remove('is-invalid');
      document.getElementById('roleSelect').classList.add('is-valid');
    }

    if (!valid) {
      AppValidate.showAlert(alertContainer, 'Corrige los campos antes de continuar.', 'danger');
      return;
    }

    const user = findUser(username, password, role);
    if (!user) {
      AppValidate.showAlert(alertContainer, 'Credenciales inválidas. Revisa usuario, contraseña o perfil.', 'danger');
      return;
    }

    // Al autenticar correctamente
    localStorage.setItem('usuarioActivo', username);
    localStorage.setItem('usuarioRole', role);
    // Simular estado; por defecto 'Activo', pero podrías setear 'Inactivo' para pruebas
    localStorage.setItem('usuarioEstado', 'Activo');

    // Redirección al dashboard correspondiente
    window.location.href = 'dashboard-' + role + '.html';
  });
});