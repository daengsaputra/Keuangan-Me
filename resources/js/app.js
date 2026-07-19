const menuButton = document.querySelector('[data-menu]');
const sidebar = document.querySelector('#sidebar');

menuButton?.addEventListener('click', () => sidebar?.classList.toggle('open'));
document.addEventListener('click', (event) => {
    if (window.innerWidth <= 760 && sidebar?.classList.contains('open') && !sidebar.contains(event.target) && !menuButton?.contains(event.target)) {
        sidebar.classList.remove('open');
    }
});
