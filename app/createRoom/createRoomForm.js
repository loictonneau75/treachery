import { clearAllErrors, setErrors, handlePostFormSubmit } from "../../tools.js";


const form = document.querySelector("#createRoomForm")
const toggle = form.querySelector("#selectAllCard")


async function handleFormSubmitEvent(e, form) {
    e.preventDefault()
    let errors = [];
    const nbplayersInput = form.querySelector("#nbPlayers")
    nbplayersInput.addEventListener("invalid", (e) => {e.preventDefault()})
    if (nbplayersInput.value.trim() === "") errors.push(["Veuillez entrer un nombre de joueurs", [form.querySelector("#nbPlayers")]])
    else if (parseInt(nbplayersInput.value) < parseInt(nbplayersInput.min) || parseInt(nbplayersInput.value) > parseInt(nbplayersInput.max)) errors.push([`Le nombre de joueurs doit être compris entre ${nbplayersInput.min} et ${nbplayersInput.max}`, [form.querySelector("#nbPlayers")]])
    if (document.querySelectorAll(".selected").length === 0) errors.push(["Veuillez sélectionner au moins une carte", [form.querySelector("#selectAllCard")]])
    if (errors.length > 0) setErrors(errors, form);
    //else await handlePostFormSubmit(errors, form);
}

toggle.addEventListener("change", () => {
    document.querySelectorAll(".card-container img").forEach(card => {
        card.classList.toggle("selected", toggle.checked)
    })
})

form.addEventListener("submit", (e) => {handleFormSubmitEvent(e, form)})
