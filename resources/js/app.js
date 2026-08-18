import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import 'preline';

// O Livewire já embute e inicia sua própria instância do Alpine nas páginas
// autenticadas (via @livewireScripts). Só assumimos o Alpine aqui quando ele
// ainda não foi fornecido (páginas guest, como login, que não usam Livewire).
if (!window.Alpine) {
    window.Alpine = Alpine;
}

window.Alpine.plugin(collapse);

document.addEventListener('alpine:init', () => {
    window.Alpine.data('passwordValidator', () => ({
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

if (!window.Livewire) {
    Alpine.start();
}
