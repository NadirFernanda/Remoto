<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Logs de Auditoria</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 11px; color: #222; padding: 20px; }
    h1 { font-size: 16px; color: #0099cc; margin-bottom: 4px; }
    .meta { font-size: 10px; color: #666; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #0099cc; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
    tbody tr:nth-child(even) { background: #f5fbfe; }
    tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
    .footer { margin-top: 20px; font-size: 9px; color: #aaa; text-align: right; }
    @media print {
        body { padding: 0; }
        .footer { position: fixed; bottom: 10px; right: 20px; }
    }
</style>
</head>
<body>
    <h1>Logs de Auditoria</h1>
    <p class="meta">
        Categoria: <strong>{{ ucfirst($category) }}</strong> &nbsp;|&nbsp;
        Período: <strong>{{ $dateFrom }}</strong> a <strong>{{ $dateTo }}</strong> &nbsp;|&nbsp;
        Gerado em: <strong>{{ now()->format('d/m/Y H:i') }}</strong> &nbsp;|&nbsp;
        Total de registos: <strong>{{ count($rows) }}</strong>
    </p>
    <table>
        <thead>
            <tr>
                @foreach($headers as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($headers) }}" style="text-align:center;padding:20px;color:#aaa;">Nenhum registo encontrado</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">24Horas Remoto &mdash; Relatório de Auditoria</div>
</body>
</html>
