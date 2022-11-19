
function redirectToPage(page, delay = 0) {
    setTimeout(() => {
        window.location.href = page;
    }, delay)
}