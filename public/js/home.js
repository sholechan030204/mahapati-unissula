const menuToggle = document.getElementById('menuToggle');
const mainNav = document.getElementById('mainNav');

if (menuToggle && mainNav) {
    const closeMenu = () => {
        menuToggle.classList.remove('is-open');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.setAttribute('aria-label', 'Buka menu navigasi');
        mainNav.classList.remove('is-open');
    };

    menuToggle.addEventListener('click', () => {
        const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';

        menuToggle.classList.toggle('is-open', !isOpen);
        menuToggle.setAttribute('aria-expanded', String(!isOpen));
        menuToggle.setAttribute(
            'aria-label',
            isOpen ? 'Buka menu navigasi' : 'Tutup menu navigasi'
        );
        mainNav.classList.toggle('is-open', !isOpen);
    });

    mainNav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            closeMenu();
        });
    });

    document.addEventListener('click', (event) => {
        if (!mainNav.contains(event.target) && !menuToggle.contains(event.target)) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });
}

const profileMenus = document.querySelectorAll('[data-profile-menu]');

if (profileMenus.length > 0) {
    document.addEventListener('click', (event) => {
        profileMenus.forEach((profileMenu) => {
            if (!profileMenu.contains(event.target)) {
                profileMenu.removeAttribute('open');
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            profileMenus.forEach((profileMenu) => {
                if (profileMenu.open) {
                    profileMenu.removeAttribute('open');
                    profileMenu.querySelector('summary')?.focus();
                }
            });
        }
    });
}

