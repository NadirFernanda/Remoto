<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Relatório de Saques</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 11px; color: #222; padding: 20px; }
    h1 { font-size: 16px; color: #0099cc; margin-bottom: 4px; }
    .meta { font-size: 10px; color: #666; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #0099cc; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
    tbody tr:nth-child(even) { background: #f5fbfe; }
    tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
    td:last-child { text-align: right; font-weight: 600; }
    .footer { margin-top: 20px; font-size: 9px; color: #aaa; text-align: right; }
    @media print {
        body { padding: 0; }
        .footer { position: fixed; bottom: 10px; right: 20px; }
    }
</style>
</head>
<body>
    <h1>Relatório de Solicitações de Saque</h1>
    <p class="meta">
        Período: <strong>{{ $start->format('d/m/Y') }}</strong> a <strong>{{ $end->format('d/m/Y') }}</strong>
        &nbsp;|&nbsp; Total: <strong>{{ count($rows) }}</strong> registos
        &nbsp;|&nbsp; Gerado em: <strong>{{ now()->format('d/m/Y H:i') }}</strong>
    </p>
    <table>
        <thead>
            <tr>@foreach($headers as $h)<th>{{ $h }}</th>@endforeach</tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse($rows as $row)
                @php $total += (float) str_replace(['.', ','], ['', '.'], $row[5]); @endphp
                <tr>@foreach($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
            @empty
                <tr><td colspan="{{ count($headers) }}" style="text-align:center;padding:20px;color:#aaa;">Nenhum registo encontrado</td></tr>
            @endforelse
            @if(count($rows))
            <tr style="background:#e0f5ff; font-weight:bold;">
                <td colspan="5">TOTAL</td>
                <td>{{ number_format($total, 2, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>
    <div class="footer">24Horas Remoto &mdash; Relatório de Saques</div>
</body>
</html>
