import { clearAllErrors, setErrors, handlePostFormSubmit } from "../../tools.js";
import {resetSelect} from "../customSelect/customSelect.js"

async function handleFormSubmitEvent(e, form){
    e.preventDefault();
    let errors = [];
    const role = form.querySelector("#card-role");
    const rarity = form.querySelector("#card-rarity");
    const img = form.querySelector("#card-img");
    if (role.value.trim() === "") errors.push(["Veuillez choisir un role", [role]]);
    if (rarity.value.trim() === "") errors.push(["Veuillez choisir une rareté", [rarity]]);
    if (img.files.length === 0) errors.push(["Veuillez choisir une image", [img]]);
    if (errors.length >0) setErrors(errors, form);
    else await handlePostFormSubmit(errors, form);
}

function resetAddCardForm(form) {
    form.reset();
    addCardForm.querySelectorAll('.custom-select').forEach(select => {
        const optionsWrapper = select.querySelector("ul")
        const optionsList = optionsWrapper.querySelectorAll("li")
        const currentValue = select.querySelector(":scope > div span:nth-of-type(1)")
        const input = select.querySelector("input")
        resetSelect(optionsWrapper, optionsList, currentValue, input)
    })
}


const addCardForm = document.querySelector("#addCardForm");
addCardForm.addEventListener("submit", (e) => handleFormSubmitEvent(e, addCardForm))
addCardForm.addEventListener("input", () => clearAllErrors(addCardForm));
addCardForm.querySelector('button[type="button"]').addEventListener('click', () => resetAddCardForm(addCardForm));