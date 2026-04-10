function handleNavResize(nav){
    const ro = new ResizeObserver(() => {
        document.body.style.paddingTop = nav.offsetHeight + "px"}
    );
    ro.observe(nav);
}

const nav = document.querySelector("nav")
const burger = nav.querySelector(".burger")
const dropdown = nav.querySelector("#dropdown")

burger.addEventListener("click", (e) => {
    e.stopPropagation()
    dropdown.hidden = !dropdown.hidden;
})

document.addEventListener("click", (e) =>{
    if(!dropdown.hidden && !burger.contains(e.target) && !dropdown.contains(e.target)){
        dropdown.hidden = true
    }
})

document.getElementById('deleteAccountBtn').addEventListener('click', (e) => {
    e.preventDefault();
    if (confirm("Cette action est irréversible. Supprimer votre compte ?")) {
        document.getElementById('deleteAccountForm').submit();
    }
});

handleNavResize(nav)