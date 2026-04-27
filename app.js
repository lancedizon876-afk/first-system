document.addEventListener("DOMContentLoaded", function () {
  const passwordInput = document.getElementById("password");
  const togglePassword = document.getElementById("togglePassword");
  const rememberEmail = document.getElementById("rememberEmail");
  const loginForm = document.getElementById("loginForm");
  const loginBtn = document.getElementById("loginBtn");
  const emailInput = document.querySelector('input[name="email"]');
  const errorAlert = document.querySelector(".alert.error");

  if (togglePassword && passwordInput) {
    togglePassword.addEventListener("click", function () {
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        togglePassword.textContent = "Hide";
      } else {
        passwordInput.type = "password";
        togglePassword.textContent = "Show";
      }
    });
  }

  if (emailInput) {
    const savedEmail = localStorage.getItem("remembered_email");
    if (savedEmail) {
      emailInput.value = savedEmail;
      if (rememberEmail) rememberEmail.checked = true;
    }
  }

  if (loginForm) {
    loginForm.addEventListener("submit", function () {
      if (emailInput && rememberEmail) {
        if (rememberEmail.checked) {
          localStorage.setItem("remembered_email", emailInput.value);
        } else {
          localStorage.removeItem("remembered_email");
        }
      }

      if (loginBtn) {
        const btnText = loginBtn.querySelector(".btn-text");
        const btnLoading = loginBtn.querySelector(".btn-loading");
        if (btnText) btnText.style.display = "none";
        if (btnLoading) btnLoading.style.display = "inline";
        loginBtn.disabled = true;
      }
    });
  }

  if (errorAlert && errorAlert.animate) {
    errorAlert.animate(
      [
        { transform: "translateX(0)" },
        { transform: "translateX(-10px)" },
        { transform: "translateX(10px)" },
        { transform: "translateX(-6px)" },
        { transform: "translateX(6px)" },
        { transform: "translateX(0)" }
      ],
      { duration: 400, easing: "ease-out" }
    );
  }

  const cards = document.querySelectorAll(".card");
  const stats = document.querySelectorAll(".stat");
  const menuLinks = document.querySelectorAll(".menu a");
  const tableRows = document.querySelectorAll("tbody tr");

  function revealElements(nodeList, delayStep, yStart) {
    nodeList.forEach(function (el, index) {
      el.style.opacity = "0";
      el.style.transform = "translateY(" + yStart + "px)";
      setTimeout(function () {
        el.style.transition = "opacity 0.45s ease, transform 0.45s ease";
        el.style.opacity = "1";
        el.style.transform = "translateY(0)";
      }, index * delayStep);
    });
  }

  revealElements(cards, 70, 18);
  revealElements(stats, 60, 14);
  revealElements(menuLinks, 45, 10);
  revealElements(tableRows, 25, 8);
});