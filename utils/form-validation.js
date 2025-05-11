document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", function (event) {
      let isValid = true;

      const nombre = document.getElementById("nombre");
      if (nombre.value.trim() === "") {
        showError(nombre, "El nombre es obligatorio");
        isValid = false;
      } else if (nombre.value.length < 2 || nombre.value.length > 50) {
        showError(nombre, "El nombre debe tener entre 2 y 50 caracteres");
        isValid = false;
      } else {
        removeError(nombre);
      }

      const email = document.getElementById("email");
      if (email.value.trim() === "") {
        showError(email, "El email es obligatorio");
        isValid = false;
      } else if (!isValidEmail(email.value)) {
        showError(email, "El formato del email no es válido");
        isValid = false;
      } else {
        removeError(email);
      }

      const password = document.getElementById("password");
      if (password.value === "") {
        showError(password, "La contraseña es obligatoria");
        isValid = false;
      } else if (password.value.length < 6) {
        showError(password, "La contraseña debe tener al menos 6 caracteres");
        isValid = false;
      } else if (!/\d/.test(password.value)) {
        showError(password, "La contraseña debe contener al menos un número");
        isValid = false;
      } else {
        removeError(password);
      }

      const confirmPassword = document.getElementById("confirm_password");
      if (confirmPassword.value === "") {
        showError(confirmPassword, "Debes confirmar la contraseña");
        isValid = false;
      } else if (confirmPassword.value !== password.value) {
        showError(confirmPassword, "Las contraseñas no coinciden");
        isValid = false;
      } else {
        removeError(confirmPassword);
      }

      const terms = document.getElementById("terms");
      if (terms && !terms.checked) {
        showError(terms, "Debes aceptar los términos y condiciones");
        isValid = false;
      } else if (terms) {
        removeError(terms);
      }

      if (!isValid) {
        event.preventDefault();
      }
    });
  }

  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (event) {
      let isValid = true;

      const email = document.getElementById("email");
      if (email.value.trim() === "") {
        showError(email, "El email es obligatorio");
        isValid = false;
      } else if (!isValidEmail(email.value)) {
        showError(email, "El formato del email no es válido");
        isValid = false;
      } else {
        removeError(email);
      }

      const password = document.getElementById("password");
      if (password.value === "") {
        showError(password, "La contraseña es obligatoria");
        isValid = false;
      } else {
        removeError(password);
      }

      if (!isValid) {
        event.preventDefault();
      }
    });
  }
});

function showError(input, message) {
  input.classList.add("is-invalid");

  let feedback = input.nextElementSibling;
  if (!feedback || !feedback.classList.contains("invalid-feedback")) {
    feedback = document.createElement("div");
    feedback.classList.add("invalid-feedback");
    input.parentNode.insertBefore(feedback, input.nextSibling);
  }

  feedback.textContent = message;
}

function removeError(input) {
  input.classList.remove("is-invalid");

  const feedback = input.nextElementSibling;
  if (feedback && feedback.classList.contains("invalid-feedback")) {
    feedback.textContent = "";
  }
}

function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}
