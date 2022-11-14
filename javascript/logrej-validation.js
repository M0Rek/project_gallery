(() => {
    'use strict'

    const regForm = document.getElementById("registration-form");
    const loginForm = document.getElementById("login-form");
    const password = document.getElementById("register-password")
    const retyped = document.getElementById("register-password-retyped")

    //Dodatkowy validity check potrzebny dla bootstrap
    retyped.addEventListener("input", () => {
        if (password.value !== retyped.value) {
            retyped.reportValidity();
        }
    })

    regForm.addEventListener('submit', event => {

        const htmlValidation = regForm.checkValidity()
        const customValidation = IsValidRegistration()

        if (!htmlValidation || !customValidation) {
            event.preventDefault()
            event.stopPropagation()
        }
    }, false)


    loginForm.addEventListener('submit', event => {

        const htmlValidation = loginForm.checkValidity()
        const customValidation = IsValidLogin()

        if (!htmlValidation || !customValidation) {
            event.preventDefault()
            event.stopPropagation()
        }
    }, false)
})()


function IsValidRegistration() {
    const login = document.getElementById("register-username")
    const email = document.getElementById("register-email")
    const password = document.getElementById("register-password")
    const retyped = document.getElementById("register-password-retyped")

    let result = true

    if (login.value.trim().match(/^[A-Za-z0-9]{8,16}$/g) == null) {
        result = false
    }

    if (password.value.match(/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*]{8,20}$/g) == null) {
        result = false
    }

    if (password.value !== retyped.value) {
        result = false
    }

    if (email.value == null || email.value.trim() === "") {
        result = false
    }

    document.getElementById("registration-form").classList.add('was-validated')
    return result
}

function IsValidLogin() {
    const login = document.getElementById("login-username")
    const password = document.getElementById("login-password")

    let result = true


    //Formalna poprawność w trakcie logowania składa się tylko z sprawdzenia
    // czy pola nie są puste.
    //
    // Zakładam, że zasady tworzenia loginów i haseł mogą zmieniać się w czasie
    // i nie powinno się blokować możliwości logowania kontom sprzed zmiany zasad.
    //
    // Gdyby nie zważać na to, zostosowałbym tę samą weryfikację pól jaka następuje
    // przy próbie rejestracji.

    if (login.value == null || login.value.trim() === "") {
        result = false
    }

    if (password.value == null || password.value === "") {
        result = false
    }

    document.getElementById("login-form").classList.add('was-validated')
    return result
}
