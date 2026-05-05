@extends('layouts.dashboard')

@section('dashboard-title', 'Movimentos de Carteira')

@section('dashboard-content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Movimentos de Carteira do Utilizador</h1>
            <p class="text-sm text-gray-500 mt-1">Visualize o extrato completo de movimentos financeiros do utilizador.</p>
        </div>
        <div class="space-y-1 text-right text-sm text-gray-500">
            <div>{{ $user->name }} · {{ $user->email }}</div>
            <div>ID: {{ $user->id }}</div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Saldo atual</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($wallet?->saldo ?? 0, 0, ',', '.') }} Kz</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Saldo pendente</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($wallet?->saldo_pendente ?? 0, 0, ',', '.') }} Kz</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Mínimo para saque</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($wallet?->saque_minimo ?? 0, 0, ',', '.') }} Kz</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <div class="flex items-center justify-between gap-4 mb-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold text-gray-900">Extrato de Movimentos</h2>
                <p class="text-sm text-gray-500">Movimentos recentes relacionados com saldos, saques e ajustamentos.</p>
            </div>
            <a href="{{ route('admin.support') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                Voltar para Suporte
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-gray-600">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Valor</th>
                        <th class="px-4 py-3">Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50">
                            <td class="px-4 py-4 text-xs text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-4 font-semibold text-gray-700">{{ ucfirst(str_replace('_', ' ', $transaction->tipo)) }}</td>
                            <td class="px-4 py-4 font-semibold {{ $transaction->valor < 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($transaction->valor, 0, ',', '.') }} Kz</td>
                            <td class="px-4 py-4 text-gray-600">{{ $transaction->descricao }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">Sem movimentos registados para este utilizador.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
