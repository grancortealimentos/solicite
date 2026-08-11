@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <p class="mb-4 text-sm text-gray-600">
        Olá, <strong>{{ auth()->user()->name }}</strong>.
    </p>

    <div class="space-y-2 text-sm text-gray-500">
        <p>E-mail: {{ auth()->user()->email }}</p>
        <p>Perfis: {{ auth()->user()->getRoleNames()->join(', ') ?: '—' }}</p>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="w-full rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Sair
        </button>
    </form>
@endsection
