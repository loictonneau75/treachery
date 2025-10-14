const btnLogin = document.getElementById("btnLogin");
const btnRegister = document.getElementById("btnRegister");
const formLogin = document.getElementById("formLogin");
const formRegister = document.getElementById("formRegister");

function validEmail(errorList, form){
    const input = ((form.id === "formLogin") ? formLogin : formRegister).querySelector((form.id === "formLogin") ? "#mail" : "#mail-r");
    const value = input.value.trim();
    if(value === ""){errorList.push(["Veuillez entrer votre adresse mail !", [input]])}
    else if(!(form.id === "formLogin")){
        if(!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)){errorList.push(["Adresse mail invalide !", [input]])}
        else if(value.length > 100){errorList.push(["Votre adresse mail doit faire moins de 100 charactère !", [input]])};
    };
}

function validPseudo(errorList){
    const input = formRegister.querySelector("#pseudo");
    const value = input.value.trim();
    if(value === ""){errorList.push(["Veuillez choisir un pseudo !", [input]])}
    else if(value.length < 3 || value.length > 50){errorList.push(["Votre pseudo doit faire entre 3 et 50 charactère !", [input]])};
}

function validPassword(errorList, form){
    const input = ((form.id === "formLogin") ? formLogin : formRegister).querySelector((form.id === "formLogin") ? "#password" : "#password-r");
    const value = input.value.trim();
    if(value === ""){errorList.push(["Veuillez choisir un mot de passe !", [input]])}
    else if(!(form.id === "formLogin")){
        if(value.length < 5 || value.length > 255 || !/(?=.*[A-Z])(?=.*\d)/.test(value)) errorList.push(["Votre mot de passe doit :\n\t- Faire entre 5 et 255 charactère\n\t- Contenir au minimum une majuscule et un chiffre !", [input]]);
    };
}

function validConfirmPassword(errorList){
    const input = formRegister.querySelector("#confirm");
    const value = input.value.trim();
    if(value === ""){errorList.push(["Veuillez confirmer votre mot de passe !", [input]])}
    else if(formRegister.querySelector("#password-r").value.trim() !== value){errorList.push(["les deux champs mot de passe doivent être identique !", [input]])};
}

function clearAllErrors(form){
    form.querySelectorAll("p.error").forEach(err => err.remove());
    form.querySelectorAll("input.invalid").forEach(input => input.classList.remove("invalid"));
}

function setErrors(errorList, form){
    clearAllErrors(form);
    for(const [msg, inputs] of errorList){
        inputs.forEach(input => input.classList.add("invalid"));
        const input = inputs[inputs.length -1];
        const err = document.createElement("p");
        err.dataset.for = input.id;
        err.classList.add("error");
        err.textContent = msg;
        input.closest(".input-group").insertAdjacentElement("afterend", err);
    };
}

async function handleFormSubmit(errorList, form) {
    const data = await (await fetch(form.action, { method: "POST", body: new FormData(form) })).json();
    if(!data.valid){
        for(const [mess, ids] of data.errors){
            const inputs = (ids || []).map(id => form.querySelector(`#${id}`)).filter(Boolean);
            errorList.push([mess, inputs]);
        }
        setErrors(errorList, form);
    }else{window.location.href = "./index.php"}
}

function toggleForms(showLoginForm) {
    btnLogin.classList.toggle("active", showLoginForm);
    btnRegister.classList.toggle("active", !showLoginForm);
    formLogin.hidden = !showLoginForm;
    formRegister.hidden = showLoginForm;
    for(const form of [formLogin, formRegister]){
        clearAllErrors(form);
        form.querySelectorAll("input").forEach(input => input.value = "");
    };
}

for(const form of [formLogin, formRegister]){
    form.addEventListener("change", (e) => {
        e.target.classList.remove("invalid");
        form.querySelector(`p.error[data-for="${e.target.id}"]`)?.remove();
    });
    form.addEventListener("submit", async function (e) {
        e.preventDefault();
        let errors = [];
        if(form.id === "formRegister"){
            validPseudo(errors);
            validConfirmPassword(errors);
        }
        validEmail(errors, form);
        validPassword(errors, form);
        if(errors.length >0){setErrors(errors, form)}
        else{await handleFormSubmit(errors, form)};
    });
}

btnLogin.addEventListener("click", () => toggleForms(true));
btnRegister.addEventListener("click", () => toggleForms(false));
