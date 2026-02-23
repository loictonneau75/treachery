async function sendValue(radio, csrf, container) {
    try {
        const data = await postData(radio, csrf);
        container.innerHTML = "";
        renderGroups(data.groups, radio.value, container);
    } catch (error) {
        console.error("Erreur :", error);
    }
}

async function postData(radio, csrf) {
    const response = await fetch(radio.dataset.action, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({groupBy: radio.value,csrf_token: csrf.value})
    });
    if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
    return await response.json();
}

function renderGroups(groups, groupType, container) {
    Object.values(groups).forEach(group => {
        const groupElement = createGroupElement(group, groupType);
        container.appendChild(groupElement);
    });
}

function createGroupElement(group, groupType) {
    const div = document.createElement("div");
    const title = createTitle(group.info.name);
    const groupImg = createGroupImage(group.info.url, groupType);
    const cardsWrapper = createCardsWrapper(group.cards);
    div.appendChild(groupImg);
    div.appendChild(title);
    div.appendChild(cardsWrapper);
    return div;
}

function createTitle(text) {
    const title = document.createElement("h2");
    title.textContent = text;
    return title;
}

function createGroupImage(url, groupType) {
    const img = document.createElement("img");
    img.src = `assets/img/${groupType}/${url}`;
    img.classList.add("group-img");
    return img;
}

function createCardsWrapper(cards) {
    const wrapper = document.createElement("div");
    cards.forEach(card => {
        const cardImg = createCardImage(card.path);
        wrapper.appendChild(cardImg);
    });
    return wrapper;
}

function createCardImage(path) {
    const img = document.createElement("img");
    img.src = `assets/img/cards/${path}`;
    img.classList.add("card-img");
    return img;
}

const showCard = document.querySelector("#showCard");
const radios = showCard.querySelectorAll("input[name='groupBy']");
const container = showCard.querySelector(":scope > div:last-of-type");
const csrfToken = showCard.querySelector("input[name='csrfToken']");
const checkedRadio = document.querySelector('input[name="groupBy"]:checked');
if (checkedRadio) sendValue(checkedRadio, csrfToken, container);
radios.forEach(radio => {
    radio.addEventListener("change", async () => {
        await sendValue(radio, csrfToken, container);
    });
});
