@extends('layouts.dashboard')

@section('dashboard-title', 'Movimentos de Carteira')

@section('dashboard-content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl">
                <h2 class="text-2xl font-extrabold">Movimentos de Carteira</h2>
                <p class="mt-2 text-sm text-white/90">Acompanhe aqui todo o histórico de transações do utilizador e valide cada movimento sem sair do painel.</p>
            </div>
            <div class="rounded-2xl bg-white/10 border border-white/20 p-4 text-sm text-white">
                <div class="text-white/80">Conta</div>
                <div class="mt-2 text-base font-semibold text-white">{{ $user->name }}</div>
                <div class="text-sm text-white/80">{{ $user->email }}</div>
                <div class="mt-3 inline-flex items-center rounded-full bg-white/10 px-3 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-white/90">ID {{ $user->id }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-2xl shadow-lg border border-white/10 bg-slate-900/70 p-6 transition hover:border-sky-400/40">
            <p class="text-sm font-medium text-slate-400">Saldo Atual</p>
            <p class="mt-4 text-3xl font-bold text-white">{{ number_format($wallet?->saldo ?? 0, 0, ',', '.') }} Kz</p>
            <p class="mt-2 text-sm text-slate-400">Disponível para levantamento ou pagamento.</p>
        </div>
        <div class="rounded-2xl shadow-lg border border-white/10 bg-slate-900/70 p-6 transition hover:border-sky-400/40">
            <p class="text-sm font-medium text-slate-400">Saldo Pendente</p>
            <p class="mt-4 text-3xl font-bold text-white">{{ number_format($wallet?->saldo_pendente ?? 0, 0, ',', '.') }} Kz</p>
            <p class="mt-2 text-sm text-slate-400">Valores em processamento ou retenção.</p>
        </div>
        <div class="rounded-2xl shadow-lg border border-white/10 bg-slate-900/70 p-6 transition hover:border-sky-400/40">
            <p class="text-sm font-medium text-slate-400">Mínimo de Saque</p>
            <p class="mt-4 text-3xl font-bold text-white">{{ number_format($wallet?->saque_minimo ?? 0, 0, ',', '.') }} Kz</p>
            <p class="mt-2 text-sm text-slate-400">Limite mínimo para solicitação de saque.</p>
        </div>
    </div>

    <div class="rounded-2xl shadow-lg border border-white/10 bg-slate-900/70 overflow-hidden">
        <div class="flex flex-col gap-4 border-b border-white/10 bg-slate-950/50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Extrato</p>
                <h3 class="mt-2 text-lg font-semibold text-white">Histórico de movimentos</h3>
                <p class="mt-1 text-sm text-slate-400">Confira todas as entradas e saídas do saldo do utilizador.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.support') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-200 shadow-sm transition hover:bg-slate-800">Voltar para Suporte</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.16em] text-slate-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Data</th>
                        <th class="px-6 py-4 font-semibold">Tipo</th>
                        <th class="px-6 py-4 font-semibold">Valor</th>
                        <th class="px-6 py-4 font-semibold">Descrição</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 bg-slate-900/70">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-slate-800/60 transition-colors">
                            <td class="px-6 py-4 text-xs text-slate-400">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-slate-200">{{ ucfirst(str_replace('_', ' ', $transaction->tipo)) }}</td>
                            <td class="px-6 py-4 font-semibold {{ $transaction->valor < 0 ? 'text-rose-300' : 'text-emerald-300' }}">{{ number_format($transaction->valor, 0, ',', '.') }} Kz</td>
                            <td class="px-6 py-4 text-slate-300">{{ $transaction->descricao }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-400">Sem movimentos registados para este utilizador.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-white/10 bg-slate-950/40 px-6 py-4 text-slate-300">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
