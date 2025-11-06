// public/js/validate.js
// Exponer funciones al scope global (window) para compatibilidad

window.AppValidate = (function () {
  function showAlert(container, message, type = 'danger') {
    container.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
  }

  function clearAlert(container) {
    container.innerHTML = '';
  }

  function validateUsername(value) {
    return typeof value === 'string' && value.trim().length >= 3;
  }

  function validateMinLength(value, min) {
    return typeof value === 'string' && value.trim().length >= min;
  }

  return {
    showAlert,
    clearAlert,
    validateUsername,
    validateMinLength
  };
})();