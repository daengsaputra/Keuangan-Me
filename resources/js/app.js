const menuButton = document.querySelector('[data-menu]');
const sidebar = document.querySelector('#sidebar');

const setMenuOpen = (isOpen) => {
    sidebar?.classList.toggle('open', isOpen);
    menuButton?.setAttribute('aria-expanded', String(isOpen));
};

menuButton?.addEventListener('click', () => {
    setMenuOpen(!sidebar?.classList.contains('open'));
});

document.addEventListener('click', (event) => {
    if (window.innerWidth <= 760 && sidebar?.classList.contains('open') && !sidebar.contains(event.target) && !menuButton?.contains(event.target)) {
        setMenuOpen(false);
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setMenuOpen(false);
    }
});
