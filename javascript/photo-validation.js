(() => {
    const addRatingForm = document.getElementById("add-rating-form")
    const addCommentForm = document.getElementById("add-comment-form")

    addRatingForm.addEventListener('submit', event => {

        const htmlValidation = addRatingForm.checkValidity()
        const customValidation = IsValidRatingForm()

        if (!htmlValidation || !customValidation) {
            event.preventDefault()
            event.stopPropagation()
        }
    }, false)

    addCommentForm.addEventListener('submit', event => {

        const htmlValidation = addCommentForm.checkValidity()
        const customValidation = IsValidCommentForm()

        if (!htmlValidation || !customValidation) {
            event.preventDefault()
            event.stopPropagation()
        }
    }, false)
})()

function IsValidRatingForm() {
    const ratingInput = document.getElementById("add-rating-input")

    let result = true

    if (ratingInput.value > 10 || ratingInput.value < 1) {
        result = false
    }

    document.getElementById("add-rating-form").classList.add('was-validated')
    return result
}

function IsValidCommentForm() {
    const commentInput = document.getElementById("add-comment-input")

    let result = true

    if (commentInput.value == null || commentInput.value.trim() === "") {
        result = false
    }

    document.getElementById("add-comment-form").classList.add('was-validated')
    return result
}