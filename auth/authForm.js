const formLogin = document.querySelector("#formLogin");
const formRegister = document.querySelector("#formRegister");

function validEmail(errorList, form){
    const input = form.querySelector((form === formLogin) ? "#mailLogin" : "#mailRegister");
    const value = input.value.trim();
    const maxLength = 255;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    if (value === "") errorList.push(["Veuillez entrer votre adresse mail !", [input]])
    else if (!emailRegex.test(value)) errorList.push(["Adresse mail invalide !", [input]])
    else if (value.length > maxLength) errorList.push([`Votre email doit faire moins de ${maxLength} caractères !`, [input]]);

}

function validPseudo(errorList, form){
    const input = form.querySelector("#pseudoRegister");
    const value = input.value.trim();
    const minLength = 3;
    const maxLength = 50;
    if (value === "") errorList.push(["Veuillez entrez un pseudo !", [input]])
    else if (value.length < minLength || value.length > maxLength) errorList.push([`Votre pseudo doit faire entre ${minLength} et ${maxLength} charactères !`, [input]]);
}

function validPassword(errorList, form){
    const input = form.querySelector((form === formLogin) ? "#passwordLogin" : "#passwordRegister");
    const value = input.value.trim();
    const minLength = 5;
    const maxLength = 255;
    const passwordRegex = /(?=.*[A-Z])(?=.*\d)/;
    if(value === "") errorList.push(["Veuillez entrez un mot de passe !", [input]])
    else if ((value.length < minLength ||
        value.length > maxLength ||
        !passwordRegex.test(value))){
            errorList.push([`Votre mot de passe doit :\n\t- Faire entre ${minLength} et ${maxLength} charactères\n\t- Contenir au minimum une majuscule et un chiffre !`, [input]]);
        } 
    
}

function validConfirmPassword(errorList, form){
    const input = form.querySelector("#confirmRegister");
    const initInput = form.querySelector("#passwordRegister")
    const value = input.value.trim();
    if(value === "") errorList.push(["Veuillez confirmer votre mot de passe !", [input]])
    else if (initInput.value.trim() !== value) errorList.push(["les deux champs mot de passe doivent être identiques !", [input, initInput]]);
}

export function clearAllErrors(form){
    form.querySelectorAll("p.error").forEach(err => err.remove());
    form.querySelectorAll("input.error").forEach(input => input.classList.remove("error"));
}

function setErrors(errorList, form){
    clearAllErrors(form);
    for(const [msg, inputs] of errorList){
        inputs.forEach(input => input.classList.add("error"));
        const input = inputs[inputs.length -1];
        const err = document.createElement("p");
        err.dataset.for = input.id;
        err.classList.add("error");
        err.textContent = msg;
        input.parentElement.insertAdjacentElement("afterend", err);
    };
}

async function handleFormSubmit(errorList, form) {
    const data = await fetch(form.action, { method: "POST", body: new FormData(form) }).then(res => res.json());
    if(data.valid) {window.location.href = "./index.php"}
    else {
        for(const [mess, ids] of data.errors){
            const inputs = (ids || []).map(id => form.querySelector(`#${id}`)).filter(Boolean);
            errorList.push([mess, inputs]);
        }
        setErrors(errorList, form);
    };
}

async function handleFormSubmitEvent(e, form) {
    e.preventDefault();
    let errors = [];
    if (form === formRegister) {
        validPseudo(errors, form);
        validConfirmPassword(errors, form);
    }
    validEmail(errors, form);
    validPassword(errors, form);
    if (errors.length >0) setErrors(errors, form)
    else await handleFormSubmit(errors, form);
}

function handleFormChangeEvent(e, form) {
    e.target.classList.remove("error");
    const errorElem = form.querySelector(`p.error[data-for="${e.target.id}"]`);
    if (errorElem) errorElem.remove();
}

for(const form of [formLogin, formRegister]){
    form.addEventListener("submit",  (e) => handleFormSubmitEvent(e, form))
    form.addEventListener("input", (e) => handleFormChangeEvent(e, form));
}