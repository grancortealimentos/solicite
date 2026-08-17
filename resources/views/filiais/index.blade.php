<x-layouts.app :title="'Filiais'">

    <div class="space-y-6">

        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-ink">Filiais</h1>
            @can('filiais.criar')
                <a href="{{ route('filiais.create') }}"
                    class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    Nova filial
                </a>
            @endcan
        </div>

        <div class="bg-surface border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead class="bg-surface-hover">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-ink-muted">Código</th>
                            <th class="px-4 py-3 text-left font-medium text-ink-muted">Nome</th>
                            <th class="px-4 py-3 text-left font-medium text-ink-muted">Cidade/UF</th>
                            <th class="px-4 py-3 text-left font-medium text-ink-muted">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-ink-muted">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($companies as $company)
                            <tr class="hover:bg-surface-hover">
                                <td class="px-4 py-3 text-ink">{{ $company->code }}</td>
                                <td class="px-4 py-3 text-ink">{{ $company->name }}</td>
                                <td class="px-4 py-3 text-ink-muted">
                                    {{ $company->city && $company->state ? "{$company->city}/{$company->state}" : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($company->is_active)
                                        <span class="text-success">Ativa</span>
                                    @else
                                        <span class="text-ink-muted">Inativa</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-3">
                                    @can('filiais.editar')
                                        <a href="{{ route('filiais.edit', $company) }}"
                                            class="text-primary-light hover:underline">Editar</a>
                                    @endcan
                                    @can('filiais.excluir')
                                        <form method="POST" action="{{ route('filiais.destroy', $company) }}" class="inline"
                                            onsubmit="return confirm('Excluir esta filial?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-danger hover:underline">Excluir</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{ $companies->links() }}

    </div>

</x-layouts.app>
