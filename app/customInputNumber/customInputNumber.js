const customSelects = document.querySelectorAll(".custom-input-number");

customSelects.forEach(customSelect => {
    const input = customSelect.querySelector("input");
    const buttons = customSelect.querySelectorAll("button");

    buttons[0].addEventListener("click", () => input.stepUp());
    buttons[1].addEventListener("click", () => input.stepDown());
});