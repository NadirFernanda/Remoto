@extends('layouts.dashboard')

@section('dashboard-title', 'Painel do Cliente')

@section('dashboard-actions')
    <a href="{{ route('client.briefing') }}" class="btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        <span>Novo Pedido</span>
    </a>
    <a href="{{ route('client.orders') }}" class="btn-outline">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M4 6h12M4 10h12M4 14h12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>
        <span>Meus Pedidos</span>
    </a>
@endsection

@section('dashboard-content')
    <livewire:client.dashboard />
@endsection
