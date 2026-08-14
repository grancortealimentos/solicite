<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-ink">Itens da solicitação</h2>
        <button type="button" wire:click="abrirModalBusca"
            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
            <i class="bi bi-plus-lg"></i>
            Adicionar item
        </button>
    </div>

    <div class="bg-surface border border-border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border text-sm">
                <thead class="bg-surface-hover">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Código</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Descrição</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Unidade</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Armazém</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Cta contábil</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Grupo produto</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Quantidade</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Data prazo</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Observação</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Centro de custo</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($itens as $item)
                        <tr wire:key="item-{{ $item['codigo'] }}">
                            <td class="px-4 py-3 text-ink">{{ $item['codigo'] }}</td>
                            <td class="px-4 py-3 text-ink">{{ $item['descricao'] }}</td>
                            <td class="px-4 py-3 text-ink">{{ $item['unidade_medida'] }}</td>
                            <td class="px-4 py-3 text-ink">{{ $item['armazem'] }}</td>
                            <td class="px-4 py-3 text-ink">{{ $item['cta_contabil'] }}</td>
                            <td class="px-4 py-3 text-ink">{{ $item['grupo_produto'] }}</td>
                            <td class="px-4 py-3 text-ink">{{ $item['quantidade'] }}</td>
                            <td class="px-4 py-3 text-ink">
                                {{ \Illuminate\Support\Carbon::parse($item['data_prazo'])->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-ink">{{ $item['observacao'] }}</td>
                            <td class="px-4 py-3 text-ink">{{ $item['centro_custo'] }}</td>
                            <td class="px-4 py-3 text-end">
                                <button type="button" wire:click="removerItem('{{ $item['codigo'] }}')"
                                    class="size-8 inline-flex justify-center items-center rounded-lg text-danger hover:bg-danger/10">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-6 text-center text-ink-muted">
                                Nenhum item adicionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal de busca de produto --}}
    @if ($buscaModalAberta)
        <div class="fixed inset-0 z-[70] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60" wire:click="fecharModalBusca"></div>

            <div
                class="relative bg-surface border border-border rounded-xl shadow-xl w-full max-w-2xl max-h-[80vh] flex flex-col">
                <div class="p-7 border-b border-border flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-ink">Buscar produto</h3>
                    <button type="button" wire:click="fecharModalBusca" class="text-ink-muted hover:text-ink">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="p-7 border-b border-border">
                    <input type="text" wire:model.live.debounce.300ms="termoBusca" autofocus
                        placeholder="Buscar item por descrição..."
                        class="py-3 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:border-primary focus:ring-primary/20">

                    <div wire:loading wire:target="termoBusca" class="text-xs text-ink-muted mt-2">
                        Buscando...
                    </div>
                </div>

                <div class="overflow-y-auto divide-y divide-border">
                    @forelse ($this->resultadoBusca['data'] as $produto)
                        <div wire:key="produto-{{ $produto->code }}"
                            wire:dblclick="selecionarProduto('{{ $produto->code }}')"
                            class="p-4 flex items-center justify-between gap-4 hover:bg-surface-hover cursor-pointer">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-ink truncate">{{ $produto->description }}</p>
                                <p class="text-xs text-ink-muted">
                                    Código: {{ $produto->code }} — {{ $produto->unitMeasurement }}
                                </p>
                            </div>
                            <button type="button" wire:click.stop="selecionarProduto('{{ $produto->code }}')"
                                class="py-1.5 px-3 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-hover shrink-0">
                                Adicionar
                            </button>
                        </div>
                    @empty
                        <p class="p-4 text-sm text-ink-muted text-center">Nenhum produto encontrado.</p>
                    @endforelse
                </div>

                <div class="p-4 border-t border-border flex items-center justify-between">
                    <button type="button" wire:click="paginaAnterior" @disabled($pagina <= 1)
                        class="text-sm disabled:opacity-50">
                        Anterior
                    </button>

                    <span class="text-sm text-ink-muted">{{ $this->resultadoBusca['total'] }} itens encontrados</span>

                    <button type="button" wire:click="proximaPagina"
                        @disabled(($pagina * $porPagina) >= $this->resultadoBusca['total'])
                        class="text-sm disabled:opacity-50">
                        Próxima
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de detalhes do item --}}
    @if ($detalheModalAberta && $produtoSelecionado)
        <div class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/60" wire:click="cancelarDetalhe"></div>

            <!-- Container do Modal -->
            <div class="relative bg-surface border border-border rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">

                <!-- Cabeçalho (Padding Lateral: px-6 sm:px-8) -->
                <div class="px-6 sm:px-8 py-5 border-b border-border">
                    <h3 class="text-base font-semibold text-ink">Detalhes do item</h3>
                    <p class="text-xs text-ink-muted mt-1 leading-relaxed">
                        <span class="font-medium text-ink">{{ $produtoSelecionado['code'] }}</span> —
                        {{ $produtoSelecionado['description'] }}
                    </p>
                </div>

                <!-- Formulário -->
                <form wire:submit="confirmarItem">

                    <!-- Corpo do Formulário (Padding Interno Isolado) -->
                    <div class="px-6 sm:px-8 py-6">

                        <!-- Bloco 1: Quantidade e Data -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                            <div>
                                <label for="quantidade" class="block text-xs font-semibold text-ink mb-2">
                                    Quantidade <span class="text-danger">*</span>
                                </label>
                                <input id="quantidade" type="number" min="1" step="1" wire:model="quantidade" autofocus
                                    class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-lg text-sm text-ink focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('quantidade') border-danger @enderror">
                                @error('quantidade')
                                    <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="dataPrazo" class="block text-xs font-semibold text-ink mb-2">
                                    Data prazo <span class="text-danger">*</span>
                                </label>
                                <input id="dataPrazo" type="date" wire:model="dataPrazo"
                                    class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-lg text-sm text-ink focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('dataPrazo') border-danger @enderror">
                                @error('dataPrazo')
                                    <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Bloco 2: Centro de Custo -->
                        <div class="mb-5">
                            <label for="centroCusto" class="block text-xs font-semibold text-ink mb-2">
                                Centro de custo <span class="text-danger">*</span>
                            </label>
                            <select id="centroCusto" wire:model="centroCusto"
                                class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-lg text-sm text-ink focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('centroCusto') border-danger @enderror">
                                <option value="">Selecione</option>
                                @foreach ($this->centrosCustoDisponiveis as $valor => $label)
                                    <option value="{{ $valor }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('centroCusto')
                                <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bloco 3: Observação -->
                        <div>
                            <label for="observacao" class="block text-xs font-semibold text-ink mb-2">
                                Observação <span class="text-danger">*</span>
                            </label>
                            <textarea id="observacao" rows="3" wire:model="observacao"
                                class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-lg text-sm text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none @error('observacao') border-danger @enderror"></textarea>
                            @error('observacao')
                                <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Rodapé das Ações (Padding Lateral Combinado) -->
                    <div class="px-6 sm:px-8 py-4 border-t border-border flex items-center justify-end gap-3 bg-surface">
                        <button type="button" wire:click="cancelarDetalhe"
                            class="py-2.5 px-4 text-sm font-medium rounded-lg text-ink-muted hover:bg-surface-hover hover:text-ink transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="confirmarItem"
                            class="py-2.5 px-4 inline-flex items-center gap-2 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary/40 disabled:opacity-70 disabled:cursor-not-allowed transition-colors">
                            <svg wire:loading wire:target="confirmarItem" class="size-4 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z">
                                </path>
                            </svg>
                            <span wire:loading.remove wire:target="confirmarItem">Adicionar item</span>
                            <span wire:loading wire:target="confirmarItem">Adicionando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
