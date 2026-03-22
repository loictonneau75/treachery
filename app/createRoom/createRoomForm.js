import { clearAllErrors, setErrors, handlePostFormSubmit } from "../../tools.js";


async function handleFormSubmitEvent(e, form) {
    e.preventDefault()
    let errors = [];
    const nbplayersInput = form.querySelector("#nbPlayers")
    const Value = parseInt(nbplayersInput.value.trim())
    const min = parseInt(nbplayersInput.min)
    const max = parseInt(nbplayersInput.max)
    const cardSelected = document.querySelectorAll(".selected")
    if (Number.isNaN(Value)) errors.push(["Veuillez entrer un nombre de joueurs !", [nbplayersInput]])
    else if (Value < min || Value > max) errors.push([`Le nombre de joueurs doit être un entier compris entre ${min} et ${max} !`, [nbplayersInput]])
    if (cardSelected.length === 0) errors.push(["Veuillez sélectionner au moins une carte !\nPour sélectioner uniquement seulement certaine carte vous pouvez cliquer dessus !", [form.querySelector(".custom-checkbox label.toggle")]])
    if (errors.length > 0) setErrors(errors, form);
    //else await handlePostFormSubmit(errors, form);
}

const form = document.querySelector("#createRoomForm")
const toggle = form.querySelector("#selectAllCard")


toggle.addEventListener("change", () => {document.querySelectorAll(".card-container img").forEach(card => {card.classList.toggle("selected", toggle.checked)})})
form.addEventListener("submit", (e) => {handleFormSubmitEvent(e, form)})
form.addEventListener("input", () => clearAllErrors(form));
form.querySelectorAll(".custom-input-number button").forEach(button => {button.addEventListener("click", () => {clearAllErrors(form)})})
