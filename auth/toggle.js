import { clearAllErrors } from "./auth.js";

const btnLogin = document.getElementById("btnShowLoginForm");
const btnRegister = document.getElementById("btnShowRegisterForm");

function toggleForms(showLoginForm) {
    btnLogin.classList.toggle("active", showLoginForm);
    btnRegister.classList.toggle("active", !showLoginForm);
    formLogin.hidden = !showLoginForm;
    formRegister.hidden = showLoginForm;
    for(const form of [formLogin, formRegister]){
        clearAllErrors(form);
        form.querySelectorAll("input").forEach(input => {
            if (input.name === "csrf_token") return;
            input.value = "";
        });

    };
}

btnLogin.addEventListener("click", () => toggleForms(true));
btnRegister.addEventListener("click", () => toggleForms(false));