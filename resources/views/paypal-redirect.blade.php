@extends('layouts.main')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50/40">
        <div class="max-w-3xl mx-auto px-6 py-16">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#00baff] to-blue-600 flex items-center justify-center shadow-lg shadow-sky-200 mx-auto mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">Redirecionando para o PayPal...</h2>
                <p class="mt-3 text-sm text-slate-500">Será redireccionado para o PayPal para finalizar o pagamento.</p>
                <form method="get" action="/pagamento/paypal/retorno" class="mt-6">
                    <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#00baff] to-blue-600 hover:opacity-90 text-white text-sm font-semibold transition shadow-md shadow-sky-200">
                        Simular pagamento e retornar
                    </button>
                </form>
                <p class="mt-5 text-xs text-slate-400">(Simulação: clique no botão para concluir o pagamento e publicar o pedido)</p>
            </div>
        </div>
    </div>
@endsection
