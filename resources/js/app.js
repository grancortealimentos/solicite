import Alpine from 'alpinejs';
import 'preline';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('passwordValidator', () => ({
        password: '',
        confirmation: '',

        get rules() {
            return [
                { label: 'Mínimo de 8 caracteres', valid: this.password.length >= 8 },
                { label: 'Uma letra maiúscula', valid: /[A-Z]/.test(this.password) },
                { label: 'Uma letra minúscula', valid: /[a-z]/.test(this.password) },
                { label: 'Um caractere especial', valid: /[^A-Za-z0-9]/.test(this.password) },
            ];
        },
        get passwordsMatch() {
            return this.password === this.confirmation;
        },
        get isValid() {
            return this.rules.every(r => r.valid)
                && this.passwordsMatch
                && this.confirmation.length > 0;
        }
    }));
});

Alpine.start();
