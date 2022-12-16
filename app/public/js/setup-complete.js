function redirectLogin() {
    document.location.href = "/admin";
}

setTimeout(() => {
    redirectLogin();
}, 5000);