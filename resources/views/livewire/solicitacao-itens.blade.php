<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-ink">Itens da solicitação</h2>

        @unless ($formAberto)
            <div class="flex items-center gap-3">
                @unless ($filial)
                    <span class="text-xs text-ink-muted">Escolha a filial para liberar a inclusão de itens.</span>
                @endunless

                <button type="button" wire:click="iniciarNovoItem" @disabled(! $filial)
                    title="{{ $filial ? '' : 'Selecione a filial primeiro' }}"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="bi bi-plus-lg"></i>
                    Adicionar item
                </button>
            </div>
        @endunless
    </div>

    {{-- Painel inline do item (novo ou em edição) --}}
    @if ($formAberto)
        <div class="bg-surface border border-primary/40 rounded-xl p-5">
            <p class="text-sm font-semibold text-primary mb-4">
                {{ $itemEmEdicao !== null ? "Editando item {$itemEmEdicao}" : 'Novo item' }}
            </p>

            <div class="flex flex-wrap items-end justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-ink-muted">Produto</p>
                    <p class="mt-1 text-sm font-semibold text-ink">
                        {{ $produtoSelecionado['description'] ?? 'Nenhum produto escolhido' }}
                    </p>
                    @if ($produtoSelecionado)
                        <p class="mt-0.5 text-xs text-ink-muted">Código: {{ $produtoSelecionado['code'] }}</p>
                        <div class="mt-2">
                            @if ($estoqueProdutoSelecionado === null)
                                <span class="text-xs text-ink-muted">Selecione a filial</span>
                            @elseif ($estoqueProdutoSelecionado > 0)
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-success/15 px-2.5 py-1 text-xs font-semibold text-success">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Em estoque ({{ $estoqueProdutoSelecionado }})
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-danger/15 px-2.5 py-1 text-xs font-semibold text-danger">
                                    <i class="bi bi-x-circle-fill"></i>
                                    Sem estoque
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <button type="button" wire:click="abrirModalBusca"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-accent text-white hover:bg-accent-hover shrink-0">
                    <i class="bi bi-search"></i>
                    Buscar produto
                </button>
            </div>

            <form wire:submit="confirmarItem">
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="quantidade" class="block text-xs font-semibold text-ink mb-2">
                            Quantidade <span class="text-danger">*</span>
                        </label>
                        <input id="quantidade" type="number" min="1" step="1" wire:model="quantidade"
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

                    <div class="sm:col-span-2 lg:col-span-2" wire:key="centro-custo-{{ $filial['code'] ?? 'sem-filial' }}"
                        x-data="{
                            aberto: false,
                            busca: '',
                            opcoes: @js($this->centrosCustoDisponiveis),
                            get filtradas() {
                                const termo = this.busca.toLowerCase();
                                return Object.entries(this.opcoes).filter(([codigo, rotulo]) => rotulo.toLowerCase().includes(termo));
                            },
                            get rotuloSelecionado() {
                                return this.opcoes[$wire.centroCusto] ?? '';
                            },
                        }"
                        x-on:click.outside="aberto = false">
                        <label for="centroCusto" class="block text-xs font-semibold text-ink mb-2">
                            Centro de custo <span class="text-danger">*</span>
                        </label>

                        <div class="relative">
                            <button type="button" id="centroCusto" x-on:click="aberto = ! aberto; busca = ''"
                                class="py-2.5 px-3.5 flex items-center justify-between w-full bg-canvas border border-border rounded-lg text-sm text-start focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary @error('centroCusto') border-danger @enderror">
                                <span :class="rotuloSelecionado ? 'text-ink' : 'text-ink-muted'"
                                    x-text="rotuloSelecionado || 'Selecione'"></span>
                                <i class="bi bi-chevron-down text-ink-muted"></i>
                            </button>

                            <div x-show="aberto" x-cloak
                                class="absolute z-10 mt-1 w-full bg-surface border border-border rounded-lg shadow-lg overflow-hidden">
                                <div class="p-2 border-b border-border">
                                    <input type="text" x-model="busca" x-ref="buscaCentroCusto"
                                        x-init="$watch('aberto', (valor) => valor && $nextTick(() => $refs.buscaCentroCusto.focus()))"
                                        placeholder="Buscar centro de custo..."
                                        class="py-2 px-3 block w-full bg-canvas border border-border rounded-md text-sm text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                </div>
                                <div class="max-h-56 overflow-y-auto">
                                    <template x-for="[codigo, rotulo] in filtradas" :key="codigo">
                                        <button type="button"
                                            x-on:click="$wire.centroCusto = codigo; aberto = false"
                                            class="w-full px-3 py-2 text-start text-sm text-ink hover:bg-surface-hover"
                                            :class="$wire.centroCusto === codigo && 'bg-primary/10'"
                                            x-text="rotulo"></button>
                                    </template>
                                    <p x-show="filtradas.length === 0" class="px-3 py-4 text-sm text-ink-muted text-center">
                                        Nenhum centro de custo encontrado.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @error('centroCusto')
                            <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="observacao" class="block text-xs font-semibold text-ink mb-2">
                        Observação <span class="text-danger">*</span>
                    </label>
                    <textarea id="observacao" rows="3" wire:model="observacao"
                        placeholder="Descreva aqui detalhes importantes sobre este item (ex: motivo da compra, especificação técnica, local de entrega...)"
                        class="py-2.5 px-3.5 block w-full bg-canvas border border-border rounded-lg text-sm text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none @error('observacao') border-danger @enderror"></textarea>
                    @error('observacao')
                        <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Imagens do item --}}
                <div class="mt-4 bg-canvas border border-border rounded-xl p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-ink">Imagens do item</p>
                            <p class="text-xs text-ink-muted">
                                Você pode anexar até 3 fotos (JPG, PNG ou WEBP). Máximo 5 MB cada.
                            </p>
                        </div>
                        @if (count($imagens) > 0)
                            <span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">
                                {{ count($imagens) }} de 3
                            </span>
                        @endif
                    </div>

                    @if (count($imagens) >= 3)
                        <p class="text-sm font-medium text-caution">Limite de 3 imagens atingido.</p>
                    @else
                        <label
                            class="group flex w-full flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-border bg-surface p-6 cursor-pointer transition-colors hover:border-primary hover:bg-primary/5">
                            <input type="file" wire:model="novaImagem" accept="image/jpeg,image/png,image/webp"
                                class="hidden">
                            <div wire:loading wire:target="novaImagem"
                                class="size-14 grid place-items-center rounded-full bg-primary/10">
                                <svg class="size-6 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.37 0 0 5.37 0 12h4z"></path>
                                </svg>
                            </div>
                            <div wire:loading.remove wire:target="novaImagem"
                                class="size-14 grid place-items-center rounded-full bg-primary/10 group-hover:bg-primary/20">
                                <i class="bi bi-upload text-primary text-lg"></i>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-semibold text-ink" wire:loading.remove wire:target="novaImagem">
                                    Clique para escolher uma imagem</p>
                                <p class="text-sm font-semibold text-ink" wire:loading wire:target="novaImagem">
                                    Enviando imagem…</p>
                                <p class="mt-1 text-xs text-ink-muted">JPG, PNG ou WEBP · Máx. 5 MB · Até 3 imagens</p>
                            </div>
                        </label>
                        @error('novaImagem')
                            <p class="text-xs text-danger">{{ $message }}</p>
                        @enderror
                    @endif

                    @if (count($imagens) > 0)
                        <div class="flex flex-wrap gap-3">
                            @foreach ($imagens as $caminho)
                                <div class="group relative" wire:key="imagem-{{ $caminho }}">
                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($caminho) }}" target="_blank"
                                        class="block size-24 overflow-hidden rounded-lg border border-border bg-surface">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($caminho) }}" alt="Imagem do item"
                                            class="size-full object-cover">
                                    </a>
                                    <button type="button" wire:click="removerImagem('{{ $caminho }}')"
                                        wire:confirm="Remover esta imagem?"
                                        class="absolute inset-0 hidden items-center justify-center rounded-lg bg-black/50 group-hover:flex">
                                        <span class="size-8 grid place-items-center rounded-full bg-danger text-white">
                                            <i class="bi bi-x-lg"></i>
                                        </span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button type="submit" wire:loading.attr="disabled" wire:target="confirmarItem"
                        class="py-2.5 px-5 inline-flex items-center gap-2 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary/40 disabled:opacity-70 disabled:cursor-not-allowed transition-colors">
                        <span wire:loading.remove wire:target="confirmarItem">
                            {{ $itemEmEdicao !== null ? 'Salvar alterações' : 'Incluir item' }}
                        </span>
                        <span wire:loading wire:target="confirmarItem">Salvando...</span>
                    </button>
                    <button type="button" wire:click="cancelarFormulario"
                        class="py-2.5 px-5 text-sm font-medium rounded-lg text-ink-muted hover:bg-surface-hover hover:text-ink transition-colors">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-surface border border-border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border text-sm">
                <thead class="bg-surface-hover">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Item</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Código</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Descrição</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Estoque na filial</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Quantidade</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Data prazo</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Observação</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Centro de custo</th>
                        <th class="px-4 py-3 text-start font-medium text-ink-muted">Imagens</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @php $itensVisiveis = collect($itens)->reject(fn ($i) => $i['item'] === $itemEmEdicao); @endphp
                    @forelse ($itensVisiveis as $item)
                        <tr wire:key="item-{{ $item['item'] }}">
                            <td class="px-4 py-3 text-ink-muted font-mono text-xs">{{ $item['item'] }}</td>
                            <td class="px-4 py-3 text-ink">{{ $item['codigo'] }}</td>
                            <td class="px-4 py-3 text-ink font-semibold">{{ $item['descricao'] }}</td>
                            <td class="px-4 py-3">
                                @if (($item['estoque_filial'] ?? 0) > 0)
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-success/15 px-2.5 py-1 text-xs font-semibold text-success">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Em estoque ({{ $item['estoque_filial'] }})
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-danger/15 px-2.5 py-1 text-xs font-semibold text-danger">
                                        <i class="bi bi-x-circle-fill"></i>
                                        Sem estoque
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-ink">{{ $item['quantidade'] }}</td>
                            <td class="px-4 py-3 text-ink">
                                {{ \Illuminate\Support\Carbon::parse($item['data_prazo'])->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-ink">{{ $item['observacao'] }}</td>
                            <td class="px-4 py-3 text-ink">{{ $item['centro_custo'] }}</td>
                            <td class="px-4 py-3 text-ink-muted">
                                {{ count($item['imagens'] ?? []) > 0 ? count($item['imagens']).' imagem(ns)' : '—' }}
                            </td>
                            <td class="px-4 py-3 text-end whitespace-nowrap">
                                <button type="button" wire:click="iniciarEdicao({{ $item['item'] }})"
                                    class="size-8 inline-flex justify-center items-center rounded-lg text-primary hover:bg-primary/10">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" wire:click="removerItem({{ $item['item'] }})"
                                    wire:confirm="Remover este item?"
                                    class="size-8 inline-flex justify-center items-center rounded-lg text-danger hover:bg-danger/10">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-6 text-center text-ink-muted">
                                @if ($itemEmEdicao !== null)
                                    Você está editando o único item da solicitação.
                                @else
                                    Nenhum item adicionado.
                                @endif
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
                        placeholder="Buscar por código ou descrição..."
                        class="py-3 px-3.5 block w-full bg-canvas border border-border rounded-xl sm:text-sm text-ink uppercase placeholder:text-ink-muted placeholder:normal-case focus:border-primary focus:ring-primary/20">

                    <div wire:loading wire:target="termoBusca" class="text-xs text-ink-muted mt-2">
                        Buscando...
                    </div>
                </div>

                <div class="flex-1 min-h-0 overflow-y-auto divide-y divide-border">
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

                <div class="p-4 border-t border-border flex items-center justify-between shrink-0">
                    <button type="button" wire:click="paginaAnterior" @disabled($pagina <= 1)
                        class="py-2 px-3 inline-flex items-center gap-x-1 text-sm font-medium rounded-lg border border-border text-ink hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent transition-colors">
                        <i class="bi bi-chevron-left"></i>
                        Anterior
                    </button>

                    <span class="text-sm text-ink-muted">{{ $this->resultadoBusca['total'] }} itens encontrados</span>

                    <button type="button" wire:click="proximaPagina"
                        @disabled(($pagina * $porPagina) >= $this->resultadoBusca['total'])
                        class="py-2 px-3 inline-flex items-center gap-x-1 text-sm font-medium rounded-lg border border-border text-ink hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent transition-colors">
                        Próxima
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Avisos: item duplicado, quantidade alta e/ou saldo na filial --}}
    @if ($avisosAberto && $itemPendente)
        <div class="fixed inset-0 z-[80] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60" wire:click="cancelarAvisos"></div>

            <div class="relative bg-surface border border-border rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-5 border-b border-border">
                    <h3 class="text-base font-semibold text-ink flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-caution"></i>
                        Confira antes de continuar
                    </h3>
                </div>

                <div class="px-6 py-5">
                    <ul class="list-disc pl-5 space-y-2 text-sm text-ink">
                        @foreach ($avisos as $aviso)
                            <li>{{ $aviso }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="px-6 py-4 border-t border-border flex items-center justify-end gap-3 bg-surface">
                    <button type="button" wire:click="cancelarAvisos"
                        class="py-2.5 px-4 text-sm font-medium rounded-lg text-ink-muted hover:bg-surface-hover hover:text-ink transition-colors">
                        Não, voltar
                    </button>
                    <button type="button" wire:click="confirmarComAvisos" wire:loading.attr="disabled"
                        wire:target="confirmarComAvisos"
                        class="py-2.5 px-4 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary/40 disabled:opacity-70 disabled:cursor-not-allowed transition-colors">
                        Sim, continuar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
