import {fetchAndRenderGroups} from "../showCard/showCard.js"

async function deleteCard(card, csrfToken, container) {
    try {
        const response = await fetch("app/deleteCard/deleteCard.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                card: card,
                csrf_token: csrfToken.value
            })
        });
        const result = await response.json();
        if (result.success) {
            const checkedRadio = document.querySelector('input[name="groupBy"]:checked');
            fetchAndRenderGroups(checkedRadio, csrfToken, container)
        } else {
            alert(result.message);
        }

    } catch (error) {
        console.error("Erreur suppression :", error);
    }
}

export function deleteCardBtn(card, data, container, csrf){
    if (card.added_by == data.id || data.admin) {
            const deleteBtn = document.createElement("button");
            deleteBtn.innerText = "X"
            deleteBtn.addEventListener("click", () => deleteCard(card, csrf, container));
            container.appendChild(deleteBtn);
        }
}