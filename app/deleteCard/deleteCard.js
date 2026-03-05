async function deleteCard(card, csrfToken) {
        const response = await fetch("app/deleteCard/deleteCard.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ card: card, csrf_token: csrfToken.value })
        });
        const result = await response.json();
        return result;
}

export function deleteCardBtn(card, data, container, csrf, refreshCallback) {
    if (card.added_by == data.id || data.admin) {
        const deleteBtn = document.createElement("button");
        deleteBtn.innerText = "X";
        deleteBtn.addEventListener("click", async () => {
            if (!window.confirm("Êtes-vous sûr de vouloir supprimer cette carte ?")) return;
            const result = await deleteCard(card, csrf);
            if (result.success) {
                refreshCallback();
            } else {
                alert(result.message);
            }
        });
        container.appendChild(deleteBtn);
    }
}