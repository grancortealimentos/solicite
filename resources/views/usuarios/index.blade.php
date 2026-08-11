@extends('layouts.admin', ['title' => 'Usuários'])

@section('content')
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Nome</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">E-mail</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Papel</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($usuarios as $usuario)
                    <tr>
                        <td class="px-4 py-2">{{ $usuario->name }}</td>
                        <td class="px-4 py-2">{{ $usuario->email }}</td>
                        <td class="px-4 py-2">{{ $usuario->roles->pluck('name')->join(', ') ?: '—' }}</td>
                        <td class="px-4 py-2 text-right">
                            @can('usuarios.gerenciar_permissoes')
                                <a href="{{ route('usuarios.permissoes', $usuario) }}" class="text-gray-600 underline">
                                    Permissões
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $usuarios->links() }}</div>
@endsection
