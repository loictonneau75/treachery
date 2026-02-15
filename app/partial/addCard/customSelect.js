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
    optionsWrapper.style.display = "none"
}

function setupOptionSelection(optionsWrapper, currentValue, input, optionsList){
    optionsList.forEach(option => {
        option.addEventListener("click", () => {
            changeSelectedOption(optionsList, option, currentValue, input, optionsWrapper)
        })
    })
}

function setupKeyboardNavigation(fakeSelect, optionsWrapper, currentValue, input, optionsList){
    let focusedIndex = 0
    fakeSelect.tabIndex = 0 
    fakeSelect.addEventListener("keydown", e => {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault()
            const isOpen = optionsWrapper.style.display === "block"
            optionsWrapper.style.display = isOpen ? "none" : "block"
            fakeSelect.classList.toggle("active", !isOpen)
        } else if (e.key === "ArrowDown") {
            e.preventDefault()
            focusedIndex = 0
            optionsList[focusedIndex].focus()
        }
    })
    optionsList.forEach((option, index) => {
        option.tabIndex = 0
        option.addEventListener("keydown", e => {
            if (e.key === "ArrowDown") {
                e.preventDefault()
                focusedIndex = (index + 1) % optionsList.length
                optionsList[focusedIndex].focus()
            } else if (e.key === "ArrowUp") {
                e.preventDefault()
                focusedIndex = (index - 1 + optionsList.length) % optionsList.length
                optionsList[focusedIndex].focus()
            } else if (e.key === "Enter" || e.key === " ") {
                e.preventDefault()
                changeSelectedOption(optionsList, option, currentValue, input, optionsWrapper)
                fakeSelect.classList.remove("active")
                fakeSelect.focus()
            } else if (e.key === "Escape") {
                optionsWrapper.style.display = "none"
                fakeSelect.classList.remove("active")
                fakeSelect.focus()
            }
        })
    })
}


function getSelectElements(customSelect) {
    const fakeSelect = customSelect.querySelector(":scope > div:first-child > div")
    const currentValue = fakeSelect.querySelector("div")
    const optionsWrapper = customSelect.querySelector(":scope > div:nth-child(2)")
    const optionsList = optionsWrapper.querySelectorAll(":scope > div")
    const input = customSelect.querySelector("input")
    return { fakeSelect, currentValue, optionsWrapper, optionsList, input }
}

const customSelects = Array.from(document.querySelectorAll(".custom-select")).map(customSelect => {
    const elements = getSelectElements(customSelect)
    initializeDefaultSelection(elements.optionsWrapper, elements.currentValue, elements.input)
    setupToggleBehavior(elements.fakeSelect, elements.optionsWrapper)
    setupOptionSelection(elements.optionsWrapper, elements.currentValue, elements.input, elements.optionsList)
    setupKeyboardNavigation(elements.fakeSelect, elements.optionsWrapper, elements.currentValue, elements.input, elements.optionsList)
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








