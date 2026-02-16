<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de Transação</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; background: #f5f7fa; }
        .receipt-header {
            text-align: center;
            margin-bottom: 24px;
            background: #0e4c92;
            color: #fff;
            padding: 16px 0;
            border-radius: 8px 8px 0 0;
            box-shadow: 0 2px 8px rgba(14,76,146,0.08);
        }
        .recibo-box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(14,76,146,0.08);
            padding: 24px;
            margin-bottom: 16px;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }
        .recibo-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .recibo-table th, .recibo-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        .recibo-table th {
            background: #f5f7fa;
            font-weight: bold;
        }
        .info { margin-bottom: 10px; }
        .label { font-weight: bold; }
        .footer { text-align: center; color: #555; margin-top: 24px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="receipt-header">
        <h2 style="margin:0;font-size:1.6rem;">Recibo de Transação</h2>
        <div style="font-size:1rem;">Data: {{ now()->format('d/m/Y H:i') }}</div>
    </div>
    <div class="recibo-box">
        <table class="recibo-table">
            <tr>
                <th>Cliente</th>
                <td>{{ $user->name }} ({{ $user->email }})</td>
            </tr>
            <tr>
                <th>ID do Serviço</th>
                <td>{{ $service->id }}</td>
            </tr>
            <tr>
                <th>Título</th>
                <td>{{ $service->titulo }}</td>
            </tr>
            <tr>
                <th>Valor</th>
                <td>{{ money_aoa($service->valor) }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ ucfirst($service->status) }}</td>
            </tr>
            <tr>
                <th>Data da transação</th>
                <td>{{ $service->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>
    <div class="footer">Este recibo foi gerado automaticamente pelo sistema.</div>
</body>
</html>
