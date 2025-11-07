const userPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
document.documentElement.setAttribute('data-bs-theme', userPrefersDark ? 'dark' : 'light');

document.addEventListener('DOMContentLoaded', function () {
    let toastElList = [].slice.call(document.querySelectorAll('.toast'));
    let toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 10000
        });
    });





    setTimeout(function (){
        toastList.forEach(toast => toast.show());
    }, 1000);
});
