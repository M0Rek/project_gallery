(() => {
    //Enable bootstrap tooltips
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
})()

function redirectToPage(page, delay = 0) {
    setTimeout(() => {
        window.location.href = page;
    }, delay)
}

validatePassword = (password) => !(password.match(/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,20}$/g) == null)
