function updateUsers() {
    fetch(`playersList/playersList.php?code=${new URLSearchParams(window.location.search).get("code")}`)
        .then(res => res.json())
        .then(data => {

            const oldPlayersList = document.querySelector("#playersList");
            if (oldPlayersList) oldPlayersList.remove();

            const newPlayerList = document.createElement("ul");
            newPlayerList.id = "playersList";

            data.playerName.forEach(player => {
                const li = document.createElement("li");
                li.textContent = player;
                newPlayerList.appendChild(li);
            });

            document.querySelector(".players").appendChild(newPlayerList);

            console.log("ok");

            if (data.started === true) {
                clearInterval(interval);
            }
        });
}

const interval = setInterval(updateUsers, 3000);
updateUsers();