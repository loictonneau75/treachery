import { clearAllErrors, setErrors, handlePostFormSubmit } from "../../../tools.js";

async function handleFormSubmitEvent(e, form){
    e.preventDefault();
    let errors = [];
    const role = form.querySelector("#card-role");
    const rarity = form.querySelector("#card-rarity");
    const img = form.querySelector("#card-img");
    if (role.value.trim() === "") errors.push(["Veuillez choisir un role", [role]])
    if (rarity.value.trim() === "") errors.push(["Veuillez choisir une rareté", [rarity]])
    if (img.files.length === 0) errors.push(["Veuillez choisir une image", [img]])
    if (errors.length >0) setErrors(errors, form)
    else await handlePostFormSubmit(errors, form);
}


const addCardForm = document.querySelector("#addCardForm")
addCardForm.addEventListener("submit", (e) => handleFormSubmitEvent(e, addCardForm))
addCardForm.addEventListener("input", () => clearAllErrors(addCardForm));
addCardForm.querySelector('button[type="button"]').addEventListener('click', () => addCardForm.reset());