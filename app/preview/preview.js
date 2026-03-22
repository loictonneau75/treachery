function addPreviewEvents(container, preview) {
    container.addEventListener("mouseover", (e) => handleMouseOver(e, preview));
    container.addEventListener("mousemove", (e) => handleMouseMove(e, preview));
    container.addEventListener("mouseout", (e) => handleMouseOut(e, preview));
}

function handleMouseOver(e, preview) {
    if (!e.target.closest(".preview-target")) return;
    preview.src = e.target.src;
    preview.style.display = "block";
}

function handleMouseMove(e, preview) {
    if (!e.target.closest(".preview-target")) return;
    positionPreview(e, preview);
}

function handleMouseOut(e, preview) {
    if (!e.target.closest(".preview-target")) return;
    preview.style.display = "none";
}

function positionPreview(e, preview) {
    const offset = 20;
    let left = e.clientX + offset;
    let top = e.clientY + offset;
    if (left + preview.offsetWidth > window.innerWidth) left = e.clientX - preview.offsetWidth - offset;
    if (top + preview.offsetHeight > window.innerHeight) top = e.clientY - preview.offsetHeight - offset;
    preview.style.left = left + "px";
    preview.style.top = top + "px";
}

if (window.innerWidth >= 992) {
    const preview = document.createElement("img");
    preview.id = "preview";
    document.body.appendChild(preview);
    addPreviewEvents(document.querySelector("#showCard"), preview);
}

