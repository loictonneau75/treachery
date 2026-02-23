if (window.innerWidth >= 992) {
    const container = document.querySelector("#showCard");
    const preview = document.querySelector("#showCard > img");

    container.addEventListener("mouseover", (e) => {
        if (e.target.classList.contains("card-img")) {
            preview.src = e.target.src;
            preview.style.display = "block";
        }
    });

    container.addEventListener("mousemove", (e) => {
        if (!e.target.classList.contains("card-img")) return;

        const offset = 20;

        const previewWidth = preview.offsetWidth;
        const previewHeight = preview.offsetHeight;

        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        let left = e.pageX + offset;
        let top = e.clientY + offset;

        if (left + previewWidth > viewportWidth) {
            left = e.pageX - previewWidth - offset;
        }

        if (top + previewHeight > viewportHeight) {
            top = e.clientY - previewHeight - offset;
        }

        preview.style.left = left + "px";
        preview.style.top = top + "px";
    });


    container.addEventListener("mouseout", (e) => {
        if (e.target.classList.contains("card-img")) {
            preview.style.display = "none";
        }
    });

}

