const redoForm = document.getElementById("redo-form")

redoForm.addEventListener('submit', event => {

    const htmlValidation = redoForm.checkValidity()
    const customValidation = isValidRedoForm()

    if (!htmlValidation || !customValidation) {
        event.preventDefault()
        event.stopPropagation()
    }
}, false)


emailCheckbox = _ => document.getElementById("email-checkbox").checked
pwdCheckbox = _ => document.getElementById("pwd-checkbox").checked

toggleSubmit = _ => {
    document.getElementById("change-submit").disabled = !emailCheckbox() && !pwdCheckbox()
}


isValidRedoForm = _ => {
    const emailInput = document.getElementById("new-email-input")
    const pwdInput = document.getElementById("new-pwd-input")

    let password = pwdInput.value
    let email = emailInput.value
    let result = true

    if (!validatePassword(password) && pwdCheckbox()) {
        result = false
        document.getElementById("new-pwd-validation").classList.remove("invisible")
    }

    if ((email == null || email.trim() === "") && emailCheckbox()) {
        result = false
        document.getElementById("new-email-validation").classList.remove("invisible")
    }

    document.getElementById("redo-form").classList.add('was-validated')

    return result
}

toggleEmailModification = _ => {

    const emailInput = document.getElementById("new-email-input")
    emailInput.disabled = !emailCheckbox()
    toggleSubmit();
}

togglePwdModification = _ => {

    const pwdInput = document.getElementById("new-pwd-input")
    pwdInput.disabled = !pwdCheckbox()
    toggleSubmit();
}








