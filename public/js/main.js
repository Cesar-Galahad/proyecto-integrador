// public/js/main.js

// Configuración: asigna el rol de la página (cliente | agente | gerente)
// Si usas archivos separados, ajusta este valor por archivo, por ejemplo:
// var PAGE_ROLE = 'agente';
var PAGE_ROLE = (function () {
  // intento heurístico: tomar role desde filename (dashboard-agente.html)
  var m = location.pathname.match(/dashboard-([^/.]+)\.html$/);
  return m ? m[1] : null;
})();

function showSection(id) {
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  var el = document.getElementById(id);
  if (el) el.classList.add('active');
}

// Logout simple: limpiar localStorage y volver a login
function logout() {
  localStorage.removeItem('usuarioActivo');
  localStorage.removeItem('usuarioRole');
  localStorage.removeItem('usuarioEstado');
  // opcion: conservar otros datos si se requieren
  location.href = 'login.html';
}

// Valida sesión y estado de cuenta al cargar la página
function checkSession() {
  var usuario = localStorage.getItem('usuarioActivo');
  var role = localStorage.getItem('usuarioRole');
  var estado = localStorage.getItem('usuarioEstado'); // 'Activo' o 'Inactivo'

  // Si no hay sesión, redirigir a login
  if (!usuario || !role) {
    location.href = 'login.html';
    return;
  }

  // Si la cuenta está inactiva, forzar logout y mostrar mensaje
  if (estado && estado !== 'Activo') {
    alert('Tu cuenta está inactiva. Contacta a tu gerente.');
    logout();
    return;
  }

  // Si la página requiere rol específico, validarlo
  if (PAGE_ROLE && role !== PAGE_ROLE) {
    // Si el usuario está en un dashboard que no le corresponde, redirigir al dashboard correcto
    location.href = 'dashboard-' + role + '.html';
    return;
  }

  // Poblado de UI: nombre, rol y estado
  document.getElementById('userName').textContent = usuario;
  document.getElementById('roleBadge').textContent = role ? role.toUpperCase() : '';
  document.getElementById('welcomeTitle').textContent = 'Panel ' + (role ? role.charAt(0).toUpperCase() + role.slice(1) : '');
  var accStateEl = document.getElementById('accountState');
  if (accStateEl) accStateEl.textContent = 'Estado: ' + (estado || 'Activo');

  // Cargar contenido del perfil (ejemplo)
  var perfilContent = document.getElementById('perfilContent');
  if (perfilContent) {
    perfilContent.innerHTML = `
      <p><strong>Usuario:</strong> ${usuario}</p>
      <p><strong>Rol:</strong> ${role}</p>
      <p><strong>Estado:</strong> ${estado || 'Activo'}</p>
    `;
  }
}

// On load
document.addEventListener('DOMContentLoaded', function () {
  checkSession();
});