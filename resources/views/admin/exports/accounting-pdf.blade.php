<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Extrato para Contabilidade</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 10px; color: #222; padding: 20px; }
    h1 { font-size: 15px; color: #0099cc; margin-bottom: 4px; }
    .meta { font-size: 9px; color: #666; margin-bottom: 14px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #0099cc; color: #fff; padding: 5px 6px; text-align: left; font-size: 9px; white-space: nowrap; }
    tbody tr:nth-child(even) { background: #f5fbfe; }
    tbody td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
    .total-row td { background: #e0f5ff; font-weight: bold; }
    td:nth-child(n+6) { text-align: right; white-space: nowrap; }
    .badge-fl { background:#dbeafe; color:#1d4ed8; border-radius:4px; padding:1px 5px; }
    .badge-ip { background:#ffedd5; color:#c2410c; border-radius:4px; padding:1px 5px; }
    .badge-cr { background:#ede9fe; color:#7c3aed; border-radius:4px; padding:1px 5px; }
    .footer { margin-top: 20px; font-size: 9px; color: #aaa; text-align: right; }
    @media print {
        body { padding: 0; font-size: 8px; }
        .footer { position: fixed; bottom: 10px; right: 20px; }
    }
</style>
</head>
<body>
    <h1>Extrato para Contabilidade</h1>
    <p class="meta">
        Período: <strong>{{ $start->format('d/m/Y') }}</strong> a <strong>{{ $end->format('d/m/Y') }}</strong>
        &nbsp;|&nbsp; Total de registos: <strong>{{ count($rows) }}</strong>
        &nbsp;|&nbsp; Gerado em: <strong>{{ now()->format('d/m/Y H:i') }}</strong>
    </p>
    <table>
        <thead>
            <tr>@foreach($headers as $h)<th>{{ $h }}</th>@endforeach</tr>
        </thead>
        <tbody>
            @php $tBruto = $tComissao = $tLiquido = 0; @endphp
            @foreach($rows as $row)
                @php
                    // row: [nome, data, tipo, origem, destino, bruto, comissao, liquido, status]
                    $brutoRaw = (float) str_replace(['.', ','], ['', '.'], $row[5]);
                    $comRaw   = (float) str_replace(['.', ','], ['', '.'], $row[6]);
                    $liqRaw   = (float) str_replace(['.', ','], ['', '.'], $row[7]);
                    $tBruto    += $brutoRaw;
                    $tComissao += $comRaw;
                    $tLiquido  += $liqRaw;
                    $badgeClass = match($row[2]) { 'Freelancing' => 'badge-fl', 'Infoproduto' => 'badge-ip', 'Creator' => 'badge-cr', default => '' };
                @endphp
                <tr>
                    <td>{{ $row[0] }}</td>
                    <td style="white-space:nowrap">{{ $row[1] }}</td>
                    <td><span class="{{ $badgeClass }}">{{ $row[2] }}</span></td>
                    <td>{{ $row[3] }}</td>
                    <td>{{ $row[4] }}</td>
                    <td>{{ $row[5] }}</td>
                    <td>{{ $row[6] }}</td>
                    <td>{{ $row[7] }}</td>
                    <td>{{ $row[8] }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5">TOTAL</td>
                <td>{{ number_format($tBruto,    2, ',', '.') }}</td>
                <td>{{ number_format($tComissao, 2, ',', '.') }}</td>
                <td>{{ number_format($tLiquido,  2, ',', '.') }}</td>
                <td>—</td>
            </tr>
        </tbody>
    </table>
    <div class="footer">24Horas Remoto &mdash; Extrato Contabilidade</div>
</body>
</html>
