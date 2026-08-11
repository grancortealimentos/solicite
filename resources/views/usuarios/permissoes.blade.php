@extends('layouts.admin', ['title' => 'Permissões de ' . $usuario->name])

@section('content')
    <form method="POST" action="{{ route('usuarios.permissoes.update', $usuario) }}" class="max-w-2xl space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Papel</label>
            <select id="role" name="role"
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none">
                <option value="">— nenhum —</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" @selected($papelAtual === $role->name)>{{ $role->name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">O usuário tem no máximo um papel; ao trocar, o anterior é removido.</p>
        </div>

        <div class="space-y-4">
            @foreach ($grupos as $grupo)
                <fieldset class="rounded-md border border-gray-200 p-4">
                    <legend class="px-1 text-sm font-semibold text-gray-700">{{ $grupo['label'] }}</legend>
                    <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach ($grupo['permissoes'] as $nome => $descricao)
                            @php $viaRole = in_array($nome, $viaPapel, true); @endphp
                            <label class="flex items-start gap-2 text-sm text-gray-700">
                                <input
                                    type="checkbox"
                                    name="permissoes[]"
                                    value="{{ $nome }}"
                                    class="mt-0.5"
                                    @checked($viaRole || in_array($nome, $diretas, true))
                                    @disabled($viaRole)
                                >
                                <span>
                                    <span class="block font-medium">
                                        {{ $nome }}
                                        @if ($viaRole)
                                            <span class="text-xs font-normal text-gray-400">(via papel)</span>
                                        @endif
                                    </span>
                                    <span class="block text-xs text-gray-500">{{ $descricao }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach
        </div>

        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            Salvar
        </button>
    </form>
@endsection
