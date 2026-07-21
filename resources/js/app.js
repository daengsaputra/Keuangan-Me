const menuButton = document.querySelector('[data-menu]');
const sidebar = document.querySelector('#sidebar');

const setMenuOpen = (isOpen) => {
    sidebar?.classList.toggle('open', isOpen);
    menuButton?.setAttribute('aria-expanded', String(isOpen));
};

menuButton?.addEventListener('click', () => {
    setMenuOpen(!sidebar?.classList.contains('open'));
});

sidebar?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => setMenuOpen(false));
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

window.addEventListener('resize', () => {
    if (window.innerWidth > 760) {
        setMenuOpen(false);
    }
});

const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

document.querySelectorAll('[data-interactive-chart]').forEach((chart) => {
    const chartButtons = chart.querySelectorAll('[data-chart-item]');

    const positionTooltip = (button) => {
        const tooltip = button.querySelector('.bar-tooltip');
        if (!tooltip) return;

        const buttonRect = button.getBoundingClientRect();
        const tooltipWidth = tooltip.offsetWidth;
        const tooltipHeight = tooltip.offsetHeight;
        const buttonCenter = buttonRect.left + (buttonRect.width / 2);
        const idealLeft = buttonCenter - (tooltipWidth / 2);
        const safeLeft = Math.max(8, Math.min(idealLeft, window.innerWidth - tooltipWidth - 8));
        const safeTop = Math.max(8, buttonRect.top - tooltipHeight - 10);
        const arrowLeft = Math.max(14, Math.min(buttonCenter - safeLeft, tooltipWidth - 14));

        tooltip.style.left = `${safeLeft}px`;
        tooltip.style.top = `${safeTop}px`;
        tooltip.style.setProperty('--arrow-left', `${arrowLeft}px`);
    };

    chartButtons.forEach((button) => {
        button.addEventListener('pointerenter', () => positionTooltip(button));
        button.addEventListener('focus', () => positionTooltip(button));
        button.addEventListener('click', () => {
            chartButtons.forEach((item) => item.classList.remove('selected'));
            button.classList.add('selected');
            positionTooltip(button);
        });
    });

    window.addEventListener('resize', () => {
        const selected = chart.querySelector('[data-chart-item].selected');
        if (selected) positionTooltip(selected);
    });

    document.addEventListener('scroll', () => {
        const selected = chart.querySelector('[data-chart-item].selected');
        if (selected) positionTooltip(selected);
    }, true);
});

const mealMonitor = document.querySelector('[data-meal-monitor]');
if (mealMonitor) {
    const budgetInput = mealMonitor.querySelector('[data-meal-budget]');
    const divisorInput = mealMonitor.querySelector('[data-meal-divisor]');
    const month = mealMonitor.dataset.month;
    const foodSpent = Number(mealMonitor.dataset.foodSpent) || 0;
    const storageKey = `meal-budget-${month}`;
    const divisorStorageKey = `meal-divisor-${month}`;

    const updateMealMonitor = () => {
        const budget = Math.max(0, Number(budgetInput.value) || 0);
        const divisor = Math.max(1, Number(divisorInput.value) || 1);
        const dailyBudget = budget / divisor;
        const remaining = Math.max(0, budget - foodSpent);
        const usage = budget > 0 ? Math.min(100, (foodSpent / budget) * 100) : 0;

        mealMonitor.querySelector('[data-daily-budget]').textContent = rupiah.format(dailyBudget);
        mealMonitor.querySelector('[data-meal-left]').textContent = rupiah.format(remaining);
        mealMonitor.querySelector('[data-meal-saving]').textContent = rupiah.format(remaining);
        mealMonitor.querySelector('[data-meal-progress]').style.width = `${usage}%`;
        mealMonitor.querySelector('[data-meal-status]').textContent = budget > 0
            ? `${rupiah.format(budget)} ÷ ${divisor} hari = ${rupiah.format(dailyBudget)} per hari`
            : 'Masukkan total uang makan dan jumlah hari.';

        try {
            localStorage.setItem(storageKey, String(budget));
            localStorage.setItem(divisorStorageKey, String(divisor));
        } catch {
            // Kalkulator tetap berfungsi jika penyimpanan browser dibatasi.
        }
    };

    try {
        budgetInput.value = localStorage.getItem(storageKey) || '';
        divisorInput.value = localStorage.getItem(divisorStorageKey) || divisorInput.value;
    } catch {
        budgetInput.value = '';
    }

    budgetInput.addEventListener('input', updateMealMonitor);
    divisorInput.addEventListener('input', updateMealMonitor);
    updateMealMonitor();
}
