function addPreviewEvents(container, preview) {
    container.addEventListener("mouseover", (e) => handleMouseOver(e, preview));
    container.addEventListener("mousemove", (e) => handleMouseMove(e, preview));
    container.addEventListener("mouseout", (e) => handleMouseOut(e, preview));
}

function handleMouseOver(e, preview) {
    if (!e.target.classList.contains("card-img")) return;
    preview.src = e.target.src;
    preview.style.display = "block";
}

function handleMouseMove(e, preview) {
    if (!e.target.classList.contains("card-img")) return;
    positionPreview(e, preview);
}

function handleMouseOut(e, preview) {
    if (!e.target.classList.contains("card-img")) return;
    preview.style.display = "none";
}

function positionPreview(e, preview) {
    const offset = 20;
    const previewWidth = preview.offsetWidth;
    const previewHeight = preview.offsetHeight;
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    let left = e.pageX + offset;
    let top = e.clientY + offset;
    if (left + previewWidth > viewportWidth) left = e.pageX - previewWidth - offset;
    if (top + previewHeight > viewportHeight) top = e.clientY - previewHeight - offset;
    preview.style.left = left + "px";
    preview.style.top = top + "px";
}

if (window.innerWidth < 992) return;
const container = document.querySelector("#showCard");
const preview = document.querySelector("#showCard > img");
addPreviewEvents(container, preview);
