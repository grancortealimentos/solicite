@extends('layouts.admin', ['title' => 'Papéis'])

@section('content')
    <div class="mb-4 flex justify-end">
        @can('papeis.criar')
            <a href="{{ route('papeis.create') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Novo papel
            </a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Nome</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Usuários</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($roles as $role)
                    <tr>
                        <td class="px-4 py-2">{{ $role->name }}</td>
                        <td class="px-4 py-2">{{ $role->users_count }}</td>
                        <td class="px-4 py-2 text-right space-x-3">
                            @can('papeis.editar')
                                <a href="{{ route('papeis.edit', $role) }}" class="text-gray-600 underline">Editar</a>
                            @endcan
                            @can('papeis.excluir')
                                @unless ($role->isSystemRole())
                                    <form method="POST" action="{{ route('papeis.destroy', $role) }}" class="inline"
                                        onsubmit="return confirm('Excluir este papel?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 underline">Excluir</button>
                                    </form>
                                @endunless
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $roles->links() }}</div>
@endsection
