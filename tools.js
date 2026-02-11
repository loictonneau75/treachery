export function clearAllErrors(form){
    form.querySelectorAll("p.error").forEach(err => err.remove());
    form.querySelectorAll("input.error").forEach(input => input.classList.remove("error"));
}

export function setErrors(errorList, form){
    clearAllErrors(form);
    for(const [msg, inputs] of errorList){
        inputs.forEach(input => input.classList.add("error"));
        const input = inputs[inputs.length -1];
        const err = document.createElement("p");
        err.dataset.for = input.id;
        err.classList.add("error");
        err.textContent = msg;
        input.parentElement.insertAdjacentElement("afterend", err);
    };
}