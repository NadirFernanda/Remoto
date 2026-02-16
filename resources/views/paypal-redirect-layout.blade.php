@extends('layouts.main')

@section('content')
    <div style="text-align:center; margin-top:80px">
        <h2 style="color:#2563eb; font-size:2rem;">Redirecionando para o PayPal...</h2>
        <p style="margin:20px 0;">Você será redirecionado para o PayPal para finalizar o pagamento.</p>
        <form method="get" action="/pagamento/paypal/retorno">
            <button class="btn-primary" style="font-size:1.05rem; padding:.6rem 1.25rem;">Simular pagamento e retornar</button>
        </form>
        <p style="margin-top:30px; color:#888;">(Simulação: clique no botão para concluir o pagamento e publicar o pedido)</p>
    </div>
@endsection
