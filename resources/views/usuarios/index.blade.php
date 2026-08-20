<x-layouts.app :title="'Usuários'">

    <div class="space-y-6">

        <h1 class="text-xl font-semibold text-ink">Usuários</h1>

        <div class="bg-surface border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead class="bg-surface-hover">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-ink-muted">Nome</th>
                            <th class="px-4 py-3 text-left font-medium text-ink-muted">E-mail</th>
                            <th class="px-4 py-3 text-left font-medium text-ink-muted">Papel</th>
                            <th class="px-4 py-3 text-right font-medium text-ink-muted">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($usuarios as $usuario)
                            <tr class="hover:bg-surface-hover">
                                <td class="px-4 py-3 text-ink">{{ $usuario->name }}</td>
                                <td class="px-4 py-3 text-ink-muted">{{ $usuario->email }}</td>
                                <td class="px-4 py-3 text-ink-muted">{{ $usuario->roles->pluck('name')->join(', ') ?: '—' }}
                                </td>
                                <td class="px-4 py-3 text-right gap-4">
                                    @can('usuarios.gerenciar_permissoes')
                                        <a href="{{ route('usuarios.permissoes', $usuario) }}" class="text-primary-light">
                                            <i class="bi bi-shield-shaded"></i>
                                        </a>
                                    @endcan

                                    @can('usuarios.configuracoes')
                                        <a href="{{ route('usuarios.configuracoes', $usuario) }}" class="text-primary-light">
                                            <i class="bi bi-gear-fill"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{ $usuarios->links() }}

    </div>

</x-layouts.app>