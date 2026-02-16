@extends('layouts.main')

@section('content')
<div class="max-w-3xl mx-auto mt-2">
    {{-- Toolbar: title + optional actions --}}
    <div class="flex items-center justify-between py-3">
        <h1 class="text-2xl font-bold">Editar Perfil</h1>
        <div></div>
    </div>

    {{-- Profile edit card (only content on this page) --}}
    <div class="bg-white p-6 rounded-2xl shadow-md">
        @livewire('freelancer.profile-editor')
    </div>

    {{-- Password change card directly below profile card --}}
    <div class="bg-white p-6 rounded-2xl shadow-md mt-6">
        <h2 class="text-xl font-semibold mb-4">Alterar senha</h2>
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Senha atual</label>
                    <input type="password" name="current_password" class="block w-full rounded-lg border border-gray-200 bg-white py-2 px-3 placeholder-gray-400" required>
                    @error('current_password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Nova senha</label>
                    <input type="password" name="new_password" class="block w-full rounded-lg border border-gray-200 bg-white py-2 px-3 placeholder-gray-400" required>
                    @error('new_password') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Confirmar nova senha</label>
                    <input type="password" name="new_password_confirmation" class="block w-full rounded-lg border border-gray-200 bg-white py-2 px-3 placeholder-gray-400" required>
                </div>
            </div>
            <div class="mt-6 action-row" role="toolbar" aria-label="Ações de senha">
                <button type="submit" class="btn-eq btn-primary" aria-label="Alterar senha">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0-1.657-1.343-3-3-3S6 9.343 6 11v2h6v-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 11V9a5 5 0 10-10 0v2" />
                    </svg>
                        @include('components.icon', ['name' => 'edit', 'class' => 'h-4 w-4'])
                        <span>Alterar senha</span>
                </button>
                <a href="{{ route('profile.edit') }}" class="btn-eq btn-outline" aria-label="Voltar ao perfil">Voltar</a>
            </div>
        </form>
    </div>
</div>
@endsection
