<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fatura/Recibo</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header { margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; }
        .info { margin-bottom: 10px; }
        .total { font-size: 18px; font-weight: bold; margin-top: 20px; }
        .section { margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Fatura/Recibo</div>
        <div class="info">Nº: {{ $service->id }}</div>
        <div class="info">Data: {{ $service->payment_released_at ? $service->payment_released_at->format('d/m/Y') : now()->format('d/m/Y') }}</div>
    </div>
    <div class="section">
        <strong>Cliente:</strong> {{ $cliente->name }}<br>
        <strong>Email:</strong> {{ $cliente->email }}
    </div>
    <div class="section">
        <strong>Freelancer:</strong> {{ $freelancer->name ?? '-' }}<br>
        <strong>Email:</strong> {{ $freelancer->email ?? '-' }}
    </div>
    <div class="section">
        <strong>Serviço:</strong> {{ $service->titulo }}<br>
        <strong>Descrição:</strong> {{ $service->briefing }}
    </div>
    <div class="section">
        <strong>Valor:</strong> Kz {{ number_format($service->valor, 2, ',', '.') }}<br>
        <strong>Taxa:</strong> {{ $service->taxa }}%<br>
        <strong>Valor Líquido:</strong> Kz {{ number_format($service->valor_liquido, 2, ',', '.') }}
    </div>
    <div class="total">
        Total Pago: Kz {{ number_format($service->valor, 2, ',', '.') }}
    </div>
</body>
</html>
