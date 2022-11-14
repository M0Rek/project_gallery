(() => {
    const addAlbumForm = document.getElementById("add-album-form");
    const albumInput = document.getElementById("add-album-input");
    const maxChars = 100;

    let charsLeft = maxChars;

    albumInput.addEventListener("input", () => {
        charsLeft = maxChars - albumInput.value.length;
        document.getElementById("chars-left-hint").innerHTML = charsLeft;

        //TODO fix empty field invalid shows valid
        if(albumInput.value == null || albumInput.value.match(/[^\s\\]/g) === []) {
            albumInput.reportValidity()
        }
    })

    addAlbumForm.addEventListener('submit', event => {

        const htmlValidation = addAlbumForm.checkValidity()
        const customValidation = IsValidAlbumForm()

        if (!htmlValidation || !customValidation) {
            event.preventDefault()
            event.stopPropagation()
        }
    }, false)
})()

function IsValidAlbumForm() {
    const addAlbum = document.getElementById("add-album-input")

    let result = true

    if (addAlbum.value == null || addAlbum.value.trim() === "") {
        result = false
    }

    document.getElementById("add-album-form").classList.add('was-validated')
    return result
}