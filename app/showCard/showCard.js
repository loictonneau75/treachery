import{deleteCardBtn} from "../deleteCard/deleteCard.js"

function createCardImage(card) {
    const img = document.createElement("img");
    img.src = `assets/img/cards/${card.path}`;
    img.classList.add("preview-target");
    img.addEventListener("click", () => {
        img.classList.toggle("active");
    });
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
        body: JSON.stringify({groupBy: radio.value, csrf_token: csrf.value})
    });
    if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
    return await response.json();
}

function createCardsWrapper(cards, data, csrf) {
    const wrapper = document.createElement("div");
    wrapper.classList.add("cards-wrapper")
    cards.forEach(card => {
        const cardContainer = document.createElement("div");
        cardContainer.classList.add("card-container")
        const cardImg = createCardImage(card);
        cardContainer.appendChild(cardImg);
        deleteCardBtn(card, data, cardContainer, csrf, () => fetchAndRenderGroups(document.querySelector('input[name="groupBy"]:checked'), csrfToken, container));
        wrapper.appendChild(cardContainer);
    });
    return wrapper;
}

function createGroupElement(group, groupType, data, csrf) {
    const div = document.createElement("div");
    const titleWrapper = document.createElement("div")
    titleWrapper.classList.add("title-wrapper")
    const title = createTitle(group.info.name);
    const groupImg = createGroupImage(group.info.url, groupType);
    const cardsWrapper = createCardsWrapper(group.cards, data, csrf);
    titleWrapper.appendChild(groupImg);
    titleWrapper.appendChild(title);
    div.appendChild(titleWrapper)
    div.appendChild(cardsWrapper);
    return div;
}

function renderGroups(data, groupType, container, csrf) {
    Object.values(data.groups).forEach(group => {
        const groupElement = createGroupElement(group, groupType, data, csrf);
        container.appendChild(groupElement);
    });
}

export async function fetchAndRenderGroups(radio, csrf, container) {
    try {
        const data = await postData(radio, csrf);
        container.innerHTML = "";
        renderGroups(data, radio.value, container, csrf);
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
if (checkedRadio) fetchAndRenderGroups(checkedRadio, csrfToken, container);
radios.forEach(radio => {
    radio.addEventListener("change", async () => {
        await fetchAndRenderGroups(radio, csrfToken, container);
    });
});
