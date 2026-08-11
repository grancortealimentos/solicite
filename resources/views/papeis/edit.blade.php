@extends('layouts.admin', ['title' => 'Editar papel'])

@section('content')
    <form method="POST" action="{{ route('papeis.update', $role) }}" class="max-w-2xl space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
            <input id="name" type="text" name="name" value="{{ old('name', $role->name) }}" required autofocus
                @disabled($role->isSystemRole())
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none disabled:bg-gray-100">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @if ($role->isSystemRole())
            <p class="text-sm text-amber-700">
                O papel Admin tem acesso irrestrito a todo o sistema e não recebe permissões individuais.
            </p>
        @else
            @include('papeis.partials.grid-permissoes')
        @endif

        @unless ($role->isSystemRole())
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Salvar
            </button>
        @endunless
    </form>
@endsection
