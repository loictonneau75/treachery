const form = document.querySelector("#createRoomForm")
const toggle = form.querySelector("#selectAllCard")

toggle.addEventListener("change", () => {
    const cards = document.querySelectorAll(".card-container img")
    cards.forEach(card => {
        card.classList.toggle("selected", toggle.checked)
    })
})
