function createCardImage(card) {
    const img = document.createElement("img");
    img.src = `assets/img/cards/${card.path}`;
    img.classList.add("preview-target");
    return img;
}

function createGroupImage(url, groupType) {
    const img = document.createElement("img");
    img.src = `assets/img/${groupType}/${url}`;
    return img;
}

function createTitle(text) {
    const title = document.createElement("h2");
    title.textContent = text;
    return title;
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

function createCardsWrapper(cards, data) {
    const wrapper = document.createElement("div");
    cards.forEach(card => {
        const cardContainer = document.createElement("div");
        const cardImg = createCardImage(card);
        cardContainer.appendChild(cardImg);
        if (card.added_by == data.id || data.admin) {
            const deleteBtn = document.createElement("button");
            deleteBtn.innerText = "X"
            //btn.addEventListener("click", () => deleteCard(card.id));
            cardContainer.appendChild(deleteBtn);
        }
        wrapper.appendChild(cardContainer);
    });
    return wrapper;
}

function createGroupElement(group, groupType, data) {
    const div = document.createElement("div");
    const title = createTitle(group.info.name);
    const groupImg = createGroupImage(group.info.url, groupType);
    const cardsWrapper = createCardsWrapper(group.cards, data);
    div.appendChild(groupImg);
    div.appendChild(title);
    div.appendChild(cardsWrapper);
    return div;
}

function renderGroups(data, groupType, container) {
    Object.values(data.groups).forEach(group => {
        const groupElement = createGroupElement(group, groupType, data);
        container.appendChild(groupElement);
    });
}

async function sendValue(radio, csrf, container) {
    try {
        const data = await postData(radio, csrf);
        container.innerHTML = "";
        renderGroups(data, radio.value, container);
    } catch (error) {
        console.error("Erreur :", error);
    }
}

const showCard = document.querySelector("#showCard");
const radios = showCard.querySelectorAll("input[name='groupBy']");
const container = document.createElement("div");
const csrfToken = showCard.querySelector("input[name='csrfToken']");
const checkedRadio = document.querySelector('input[name="groupBy"]:checked');
showCard.appendChild(container);
if (checkedRadio) sendValue(checkedRadio, csrfToken, container);
radios.forEach(radio => {
    radio.addEventListener("change", async () => {
        await sendValue(radio, csrfToken, container);
    });
});
