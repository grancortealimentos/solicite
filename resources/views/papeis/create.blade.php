@extends('layouts.admin', ['title' => 'Novo papel'])

@section('content')
    <form method="POST" action="{{ route('papeis.store') }}" class="max-w-2xl space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @include('papeis.partials.grid-permissoes')

        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            Criar papel
        </button>
    </form>
@endsection
