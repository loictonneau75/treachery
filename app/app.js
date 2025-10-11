const row = document.querySelector('.app .row:has(> button.show-toggle:last-child)');
const showToggleButton = row.querySelector(".show-toggle")
const form = row.querySelector("form")
const removeAddCardButton = row.querySelector("#removeAddCardButton")

showToggleButton.addEventListener("click", (e) => {
    e.stopPropagation()
    form.hidden = false;
    showToggleButton.hidden = true;
})

removeAddCardButton.addEventListener("click", (e) => {
    e.stopPropagation()
    form.hidden = true;
    showToggleButton.hidden = false;
})
