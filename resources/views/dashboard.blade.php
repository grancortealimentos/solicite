<x-layouts.app :title="'Página inicial'">

    <div class="space-y-6">

        {{-- Boas-vindas --}}
        <div class="bg-surface border border-border rounded-xl p-6">
            <h1 class="text-2xl font-semibold text-ink">
                Bem-vindo(a), {{ auth()->user()->name }} 👋
            </h1>
            <p class="mt-1 text-sm text-ink-muted">
                Aqui está um resumo da sua conta.
            </p>
        </div>

        {{-- Dados da conta --}}
        <div class="bg-surface border border-border rounded-xl p-6">
            <h2 class="text-lg font-semibold text-ink mb-4">Seus dados</h2>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-ink-muted">E-mail</dt>
                    <dd class="mt-1 text-sm text-ink">{{ auth()->user()->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-ink-muted">Perfis</dt>
                    <dd class="mt-1 text-sm text-ink">{{ auth()->user()->getRoleNames()->join(', ') ?: '—' }}</dd>
                </div>
            </dl>
        </div>

    </div>

</x-layouts.app>