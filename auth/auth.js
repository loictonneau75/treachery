const btnLogin = document.getElementById("btnLogin");
const btnRegister = document.getElementById("btnRegister");
const formLogin = document.getElementById("formLogin");
const formRegister = document.getElementById("formRegister");

function toggleForms(showLogin) {
    btnLogin.classList.toggle("active", showLogin);
    btnRegister.classList.toggle("active", !showLogin);
    formLogin.hidden = !showLogin;
    formRegister.hidden = showLogin;
}

btnLogin.addEventListener("click", () => toggleForms(true));
btnRegister.addEventListener("click", () => toggleForms(false));
