const role = document.querySelector("#roles");
const roleList = document.createElement("ul")
role.appendChild(roleList)

fetch(`roleList/roleList.php?code=${new URLSearchParams(window.location.search).get("code")}`)
    .then(res => res.json())
    .then(data => {
        Object.values(data.roles).forEach(role => {
            const li = document.createElement("li");
            li.innerHTML = `
                <img src="../assets/img/role/${role.url}" alt="${role.name}">
                <span>${role.name}</span>
                <span>x${role.count}</span>
            `;
            roleList.appendChild(li);
        });
    });