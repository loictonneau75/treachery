const customInputFiles = document.querySelectorAll('.custom-input-file');
customInputFiles.forEach(customInputFile => {
    const input = customInputFile.querySelector('input[type="file"]');
    const fileName = customInputFile.querySelector('span');

    input.addEventListener('change', () => {
        fileName.textContent = input.files[0]?.name || "Aucun fichier";
    });
})

