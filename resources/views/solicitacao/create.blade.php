<x-layouts.app :title="'Nova solicitação'">
    <div class="space-y-6">
        <h1 class="text-xl font-semibold text-ink">Nova solicitação</h1>
        <form method="POST" action="{{ route('pessoas.store') }}" class="space-y-6">
            @csrf

            <div class="bg-surface border border-border rounded-xl p-6 space-y-4">
                <h2 class="text-sm font-semibold text-ink">Dados da solicitação</h2>

                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label for="id_solicitacao" class="block text-sm font-medium text-ink-muted mb-2">ID da solicitação</label>
                        <input id="id_solicitacao" type="text" value="007012" disabled readonly
                            class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink-muted cursor-not-allowed">
                    </div>

                    <div>
                        <label for="solicitante" class="block text-sm font-medium text-ink-muted mb-2">Solicitante</label>
                        <input id="solicitante" type="text" value="{{ auth()->user()->name }}" disabled readonly
                            class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink-muted cursor-not-allowed">
                    </div>

                    <div>
                        <label for="data_emissao" class="block text-sm font-medium text-ink-muted mb-2">Data de emissão</label>
                        <input id="data_emissao" type="text" value="{{ now()->format('d/m/Y') }}" disabled readonly
                            class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink-muted cursor-not-allowed">
                    </div>

                    <div>
                        <label for="filial" class="block text-sm font-medium text-ink-muted mb-2">Filial</label>
                        <input id="filial" type="text" value="010101" disabled readonly
                            class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink-muted cursor-not-allowed">
                    </div>
                </div>
            </div>

            <livewire:solicitacao-itens />
        </form>
    </div>
</x-layouts.app>