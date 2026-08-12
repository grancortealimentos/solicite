<x-layouts.app :title="'Papéis'">

    <div class="space-y-6">

        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-ink">Papéis</h1>
            @can('papeis.criar')
                <a href="{{ route('papeis.create') }}"
                    class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    Novo papel
                </a>
            @endcan
        </div>

        <div class="bg-surface border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead class="bg-surface-hover">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-ink-muted">Nome</th>
                            <th class="px-4 py-3 text-left font-medium text-ink-muted">Usuários</th>
                            <th class="px-4 py-3 text-right font-medium text-ink-muted">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($roles as $role)
                            <tr class="hover:bg-surface-hover">
                                <td class="px-4 py-3 text-ink">{{ $role->name }}</td>
                                <td class="px-4 py-3 text-ink-muted">{{ $role->users_count }}</td>
                                <td class="px-4 py-3 text-right space-x-3">
                                    @can('papeis.editar')
                                        <a href="{{ route('papeis.edit', $role) }}"
                                            class="text-primary-light hover:underline">Editar</a>
                                    @endcan
                                    @can('papeis.excluir')
                                        @unless ($role->isSystemRole())
                                            <form method="POST" action="{{ route('papeis.destroy', $role) }}" class="inline"
                                                onsubmit="return confirm('Excluir este papel?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-danger hover:underline">Excluir</button>
                                            </form>
                                        @endunless
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{ $roles->links() }}

    </div>

</x-layouts.app>