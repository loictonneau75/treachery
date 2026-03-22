import { clearAllErrors, setErrors, handlePostFormSubmit } from "../../tools.js";
import {resetSelect} from "../customSelect/customSelect.js"

async function handleFormSubmitEvent(e, form){
    e.preventDefault();
    let errors = [];
    if (form.querySelector("#cardRole").value.trim() === "") errors.push(["Veuillez choisir un role !", [form.querySelector("#cardRoleInput > div")]]);
    if (form.querySelector("#cardRarity").value.trim() === "") errors.push(["Veuillez choisir une rareté !", [form.querySelector("#cardRarityInput > div")]]);
    if (form.querySelector("#cardImg").files.length === 0) errors.push(["Veuillez choisir une image !", [form.querySelector("#cardImgInput > label")]]);
    if (errors.length >0) setErrors(errors, form);
    else await handlePostFormSubmit(errors, form);
}

function resetAddCardForm(form) {
    form.reset();
    form.querySelectorAll('.custom-select').forEach(select => {
        resetSelect(
            document.querySelector("#" + select.dataset.dropdown),
            optionsWrapper.querySelectorAll("li"),
            select.querySelector(":scope > div span:nth-of-type(1)"),
            select.querySelector("input")
        )
    })
    form.querySelector(".custom-input-file span").innerText = "Aucun fichier";
}


const form = document.querySelector("#addCardForm");
form.addEventListener("submit", (e) => handleFormSubmitEvent(e, form))
form.addEventListener("input", () => clearAllErrors(form));
form.querySelector('button[type="button"]').addEventListener('click', () => resetAddCardForm(form));