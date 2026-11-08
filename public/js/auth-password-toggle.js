document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-password-toggle]');

    if (!button) {
        return;
    }

    const input = button.closest('.auth-input-wrap, .member-password-input')?.querySelector('input');

    if (!input) {
        return;
    }

    const shouldShow = input.type === 'password';

    input.type = shouldShow ? 'text' : 'password';
    button.setAttribute('aria-pressed', String(shouldShow));
    button.setAttribute('aria-label', shouldShow ? 'Sembunyikan password' : 'Tampilkan password');
});
