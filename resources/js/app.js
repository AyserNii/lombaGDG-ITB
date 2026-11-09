import './bootstrap';

window.togglePasswordVisibility = function (button) {
    const container = button.closest('.relative') || button.parentElement;
    const input = container ? container.querySelector('input') : document.getElementById('password');
    if (!input) return;

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    const eyeIcon = button.querySelector('.eye-icon');
    const eyeSlashIcon = button.querySelector('.eye-slash-icon');
    if (eyeIcon && eyeSlashIcon) {
        if (isPassword) {
            eyeIcon.classList.add('hidden');
            eyeSlashIcon.classList.remove('hidden');
        } else {
            eyeIcon.classList.remove('hidden');
            eyeSlashIcon.classList.add('hidden');
        }
    }
};

document.querySelectorAll('[data-password-toggle]:not([onclick])').forEach((button) => {
    button.addEventListener('click', () => {
        window.togglePasswordVisibility(button);
    });
});

document.querySelectorAll('[data-like]').forEach((button) => {
    button.addEventListener('click', () => {
        button.classList.toggle('text-rose-500');
        button.classList.toggle('text-[#4c54d2]');
    });
});
