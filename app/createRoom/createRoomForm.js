import { clearAllErrors, setErrors, handlePostFormSubmit } from "../../tools.js";

function setSelectedCardsInput(form, selectedCards) {
    const selectedCardInput = document.createElement("input")
    selectedCardInput.type = "hidden"
    selectedCardInput.name = "cardIds"
    selectedCardInput.value = JSON.stringify([...selectedCards].map(card => parseInt(card.dataset.cardId)));
    form.appendChild(selectedCardInput)
}

function setMinMaxInput(form, values) {
    const minMaxInput = document.createElement("input")
    minMaxInput.type = "hidden"
    minMaxInput.name = "minMax"
    minMaxInput.value = JSON.stringify(values);
    form.appendChild(minMaxInput)
}

async function handleFormSubmitEvent(e, form) {
    e.preventDefault()
    let errors = [];
    const nbplayersInput = form.querySelector("#nbPlayers")
    const selectedCards = document.querySelectorAll(".selected")
    const value = parseInt(nbplayersInput.value.trim())
    const min = parseInt(nbplayersInput.min)
    const max = parseInt(nbplayersInput.max)
    if (Number.isNaN(value)) errors.push(["Veuillez entrer un nombre de joueurs !", [nbplayersInput]])
    else if (value < min || value > max) errors.push([`Le nombre de joueurs doit être un entier compris entre ${min} et ${max} !`, [nbplayersInput]])
    if (selectedCards.length < value) errors.push(["Veuillez sélectionner au moins autant de carte que de joueur !\nPour sélectioner uniquement seulement certaine carte vous pouvez cliquer dessus !", [form.querySelector("#fakeCheckboxSelectAllCard")]])
    if (errors.length > 0) setErrors(errors, form)
    else {
        setSelectedCardsInput(form, selectedCards)
        setMinMaxInput(form, [min, max])
        if (await handlePostFormSubmit(errors, form)) {
            window.location.href = "./index.php";
        }
    }
}

const form = document.querySelector("#createRoomForm")
const toggle = form.querySelector("#selectAllCard")


toggle.addEventListener("change", () => {document.querySelectorAll(".card-container img").forEach(card => {card.classList.toggle("selected", toggle.checked)})})
form.addEventListener("submit", (e) => {handleFormSubmitEvent(e, form)})
form.addEventListener("input", () => clearAllErrors(form));
form.querySelectorAll(".custom-input-number button").forEach(button => {button.addEventListener("click", () => {clearAllErrors(form)})})
