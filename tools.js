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

export async function handlePostFormSubmit(errorList, form) {
    const data = await fetch(form.action, { method: "POST", body: new FormData(form) }).then(res => res.json());
    if(data.valid) {window.location.href = "./index.php"}
    else {
        for(const [mess, ids] of data.errors){
            const inputs = (ids || []).map(id => form.querySelector(`#${id}`)).filter(Boolean);
            errorList.push([mess, inputs]);
        }
        setErrors(errorList, form);
    };
}