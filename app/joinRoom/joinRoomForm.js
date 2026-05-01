import { clearAllErrors, setErrors, handlePostFormSubmit } from "../../tools.js";


async function handleFormSubmitEvent(e, form){
    e.preventDefault()
    let errors = [];
    const codeinput = form.querySelector("#code")
    const codeValue = codeinput.value.trim()
    if (codeValue === "") errors.push(["Veuillez entrer un code de salle !", [codeinput]])
    else if (codeValue.length !== 5) errors.push(["Le code de salle doit être composé de 5 caractères !", [codeinput]])
    if (errors.length > 0) setErrors(errors, form)
    else {
        const result = await handlePostFormSubmit(errors, form, true);
        console.log(result)
        if (result.success) window.location.href = `./rooms/room.php?code=${result.code}`
    }
}

const form = document.querySelector("#joinRoomForm")

form.addEventListener("submit", (e) => {handleFormSubmitEvent(e, form)})
form.addEventListener("input", () => clearAllErrors(form));
