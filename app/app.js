const row = document.querySelector('.app .row:has(> button.show-toggle:last-child)');
const showToggleButton = row.querySelector(".show-toggle");
const form = row.querySelector("form");
const removeAddCardButton = row.querySelector("#removeAddCardButton");

const addCardForm = document.getElementById("formAddCard");


function inOptions(select){ 
    optionValues = Array.from(select.options).map(o=>o.value)
    return optionValues.includes(select.value); 
}

function setErrors(errorList, form){
    clearAllErrors(form);
    for(const [msg, inputs] of errorList){
        inputs.forEach(input => input.classList.add("invalid"));
        const input = inputs[inputs.length -1];
        const err = document.createElement("p");
        err.dataset.for = input.id;
        err.classList.add("error");
        err.textContent = msg;
        input.closest("div").insertAdjacentElement("afterend", err);
    };
}

function clearAllErrors(form){
    form.querySelectorAll("p.error").forEach(err => err.remove());
    form.querySelectorAll("select.invalid").forEach(input => input.classList.remove("invalid"));
}

async function handleFormSubmit(errorList, form) {
    const data = await (await fetch(form.action, { method: "POST", body: new FormData(form) })).json();
    if(!data.valid){
        for(const [mess, ids] of data.errors){
            const inputs = (ids || []).map(id => form.querySelector(`#${id}`)).filter(Boolean);
            errorList.push([mess, inputs]);
        }
        setErrors(errorList, form);
    }else{window.location.href = "./index.php"}
}

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

for(const form of [addCardForm]){
    form.addEventListener("input", (e) => {
        e.target.classList.remove("invalid");
        form.querySelector(`p.error[data-for="${e.target.id}"]`)?.remove();
    });
    form.addEventListener("submit", async function (e) {
        e.preventDefault();
        let errors = [];
        if(form.id === "formAddCard"){
            const type = form.getElementById("card-type")
            if(type.value.trim() === ""){errors.push(["Veuillez remplir ce champ !", [type]])}
            if(!inOptions(type)){errors.push(["Choisissez un type valide !", [type]])}

            const rarity = form.getElementById("card-rarity")
            if(rarity.value.trim() === ""){errors.push(["Veuillez remplir ce champ !", [rarity]])}
            if(!inOptions(rarity)){errors.push(["Choisissez une rareté valide !", [rarity]])}

            const img = form.getElementById("card-img")
            if(img.value.trim() === ""){errors.push(["veuillez remplir ce champ !", [img]])}
            const fchk = fileOk(img);
            if(!fchk.ok){errors.push([fchk.msg, [img]])}

            if(errors.length >0){setErrors(errors, form)}
            else{await handleFormSubmit(errors, form)};
        }
    })
}
