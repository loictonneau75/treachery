function initializeDefaultSelection(optionsWrapper, currentValue, input){
    const firstOption = optionsWrapper.firstElementChild
    currentValue.innerText = firstOption.querySelector("span").innerText
    input.value = firstOption.dataset.value
    firstOption.querySelector(".caret").textContent = "✓"
}

function setupToggleBehavior(fakeSelect, optionsWrapper){
    fakeSelect.addEventListener("click", () => {
        optionsWrapper.style.display = optionsWrapper.style.display === 'block' ? 'none' : 'block';
        fakeSelect.classList.toggle("active")
    })
}

function changeCaretPosition(optionsList, option){
    optionsList.forEach(opt => opt.querySelector(".caret").innerText = "")
    option.querySelector(".caret").textContent = "✓"
}

function changeSelectedOption(optionsList, option, currentValue, input, optionsWrapper){
    changeCaretPosition(optionsList, option)
    currentValue.innerText = option.querySelector("span").innerText
    input.value = option.dataset.value
    input.dispatchEvent(new Event("input", { bubbles: true }));
    optionsWrapper.style.display = "none"
}

function setupOptionSelection(fakeSelect, optionsWrapper, currentValue, input, optionsList){
    optionsList.forEach(option => {
        option.addEventListener("click", () => {
            changeSelectedOption(optionsList, option, currentValue, input, optionsWrapper)
            fakeSelect.classList.remove("active")
        })
    })
}

function getSelectElements(customSelect) {
    const fakeSelect = customSelect.querySelector(":scope > div")
    const currentValue = fakeSelect.querySelector(":scope div")
    const optionsWrapper = customSelect.querySelector(":scope > div:nth-of-type(2)")
    const optionsList = optionsWrapper.querySelectorAll(":scope > div")
    const input = customSelect.querySelector(":scope input")
    return { fakeSelect, currentValue, optionsWrapper, optionsList, input }
}

export function resetSelect(optionsWrapper, optionsList, currentValue, input){
    initializeDefaultSelection(optionsWrapper, currentValue, input);
    changeCaretPosition(optionsList, optionsWrapper.firstElementChild)
}

const customSelects = Array.from(document.querySelectorAll(".custom-select")).map(customSelect => {
    const elements = getSelectElements(customSelect)
    initializeDefaultSelection(elements.optionsWrapper, elements.currentValue, elements.input)
    setupToggleBehavior(elements.fakeSelect, elements.optionsWrapper)
    setupOptionSelection(elements.fakeSelect, elements.optionsWrapper, elements.currentValue, elements.input, elements.optionsList)
    return { customSelect, ...elements }
})

document.addEventListener("click", (e) => {
    customSelects.forEach(({ customSelect, fakeSelect, optionsWrapper }) => {
        if (!customSelect.contains(e.target)) {
            optionsWrapper.style.display = "none"
            fakeSelect.classList.remove("active")
        }
    })
})








