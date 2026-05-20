async function leaveRoom() {
    const response = await fetch(`/treachery/rooms/leaveRoom/leaveRoom.php?code=${new URLSearchParams(window.location.search).get("code")}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            }
        });
        const data = await response.json();
        if (data.success) {
            window.location.href = data.redirect;
        }
}

document.getElementById("leaveBtn").addEventListener("click", leaveRoom)
