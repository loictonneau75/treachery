function sendValue(radio, csrf, container) {
    fetch(radio.dataset.action, {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({groupBy: radio.value, csrf_token: csrf.value})})
    .then(response => response.json())
    .then(data => { 
        container.innerText = "Radio sélectionné : " + data.group["name"];
        console.log(data.group)
    });
}


const showCard = document .querySelector("#showCard")
const radios = showCard.querySelectorAll("input[name='groupBy']");
const container = showCard.querySelector(":scope > div:last-of-type");
const csrfToken = showCard.querySelector("input[name='csrfToken']");
sendValue(document.querySelector('input[name="groupBy"]:checked'), csrfToken, container)
radios.forEach(radio => {radio.addEventListener('change', () => {sendValue(radio, csrfToken, container)})});
