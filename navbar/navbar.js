nav = document.querySelector("nav")
burger = nav.querySelector(".burger")
dropdown = nav.querySelector("#dropdown")

burger.addEventListener("click", (e) => {
    e.stopPropagation()
    if(dropdown.hidden){
        dropdown.hidden = false
    }else{
        dropdown.hidden = true
    }
})

document.addEventListener("click", (e) =>{
    if(!dropdown.hidden && !burger.contains(e.target) && !dropdown.contains(e.target)){
        dropdown.hidden = true
    }
})
