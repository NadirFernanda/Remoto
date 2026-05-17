<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal — Simulação de Pagamento</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.646 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.471z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Modo de Teste</p>
                <p class="text-base font-bold text-gray-800">Simulação PayPal</p>
            </div>
        </div>

        {{-- Aviso de ambiente --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mb-6 flex gap-2 items-start">
            <svg class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-xs text-yellow-700">
                Esta é uma <strong>simulação local</strong>. Nenhum pagamento real é efectuado. Activo porque <code class="bg-yellow-100 px-1 rounded">PAYPAL_MODE=fake</code>.
            </p>
        </div>

        {{-- Detalhes do pagamento --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <p class="text-xs text-gray-400 uppercase tracking-wide mb-3 font-medium">Resumo do pagamento</p>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Valor (USD)</span>
                <span class="text-xl font-bold text-gray-800">
                    ${{ number_format((float)$amount, 2) }}
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-gray-200">
                <p class="text-[11px] text-gray-400 font-mono break-all">Token: {{ $token }}</p>
            </div>
        </div>

        {{-- Botões --}}
        <div class="space-y-3">
            <a href="{{ $return_url . (str_contains($return_url, '?') ? '&' : '?') . 'token=' . $token }}"
               class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition">
                Aprovar pagamento (simulado)
            </a>

            <a href="{{ $cancel_url }}"
               class="block w-full text-center bg-white border border-gray-200 hover:border-gray-300 text-gray-600 font-medium py-3 rounded-xl transition">
                Cancelar
            </a>
        </div>

    </div>

</body>
</html>
