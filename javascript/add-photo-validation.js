(() => {

    const addPhotoForm = document.getElementById("add-photo-form")
    const photoDescInput = document.getElementById("add-photo-input")
    const maxChars = 255

    console.log('Im here')

    let charsLeft = maxChars

    photoDescInput.addEventListener("input", () => {
        charsLeft = maxChars - photoDescInput.value.length
        console.log(charsLeft)
        document.getElementById("chars-left-hint").innerHTML = charsLeft
    })

    addPhotoForm.addEventListener('submit', event => {

        const htmlValidation = addPhotoForm.checkValidity()
        const customValidation = IsValidPhotoForm()

        if (!htmlValidation || !customValidation) {
            event.preventDefault()
            event.stopPropagation()
        }
    }, false)
})()

function IsValidPhotoForm() {
    const photoFileInput = document.getElementById("add-photo-file");

    let result = true

    if (photoFileInput.value === "") {
        result = false
    }

    document.getElementById("add-photo-form").classList.add('was-validated')
    return result
}