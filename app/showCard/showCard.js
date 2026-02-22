function sendValue(radio, csrf, container) {
    fetch(radio.dataset.action, {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({groupBy: radio.value, csrf_token: csrf.value})})
    .then(response => response.json())
    .then(data => { 
        container.innerHTML = "";
        Object.values(data.groups).forEach(element => {
            const div = document.createElement("div");
            const title = document.createElement("h2");
            title.textContent = element.info.name;
            const groupImg = document.createElement("img");
            groupImg.src = `assets/img/${radio.value}/${element.info.url}`;
            groupImg.classList.add("group-img");
            const wrapper = document.createElement("div");
            element.cards.forEach(card => {
                const cardImg = document.createElement("img");
                cardImg.src = `assets/img/cards/${card.path}`;
                cardImg.classList.add("card-img");
                wrapper.appendChild(cardImg);
            });
            div.appendChild(groupImg);
            div.appendChild(title);
            div.appendChild(wrapper);
            container.appendChild(div);
        });
    });
}


const showCard = document .querySelector("#showCard")
const radios = showCard.querySelectorAll("input[name='groupBy']");
const container = showCard.querySelector(":scope > div:last-of-type");
const csrfToken = showCard.querySelector("input[name='csrfToken']");
sendValue(document.querySelector('input[name="groupBy"]:checked'), csrfToken, container)
radios.forEach(radio => {radio.addEventListener('change', () => {sendValue(radio, csrfToken, container)})});
