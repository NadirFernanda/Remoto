<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; font-size: 11px; }
table { border-collapse: collapse; width: 100%; }
th { background: #0052cc; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
tr:nth-child(even) td { background: #f8fafc; }
.titulo { font-size: 14px; font-weight: bold; margin-bottom: 4px; color: #0052cc; }
.sub { font-size: 10px; color: #64748b; margin-bottom: 12px; }
.totais td { font-weight: bold; background: #f0f9ff; border-top: 2px solid #0052cc; }
</style>
</head>
<body>
<div class="titulo">Facturação Genérica — 24 Horas</div>
<div class="sub">Período: {{ $start->format('d/m/Y') }} a {{ $end->format('d/m/Y') }} · Gerado em: {{ now()->format('d/m/Y H:i') }}</div>

<table>
    <thead>
        <tr>
            @foreach($headers as $h)
            <th>{{ $h }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php
            $totalBruto = 0; $totalComissao = 0; $totalLiquido = 0;
        @endphp
        @foreach($rows as $row)
        @php
            // Colunas: Nº, Data, Tipo, Descricao, Cliente, Email, Prestador, Bruto, Comissao, Liquido, Estado
            $bruto    = str_replace(['.', ','], ['', '.'], $row[7] ?? '0');
            $comissao = str_replace(['.', ','], ['', '.'], $row[8] ?? '0');
            $liquido  = str_replace(['.', ','], ['', '.'], $row[9] ?? '0');
            $totalBruto    += (float)$bruto;
            $totalComissao += (float)$comissao;
            $totalLiquido  += (float)$liquido;
        @endphp
        <tr>
            @foreach($row as $cell)
            <td>{{ $cell }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="totais">
            <td colspan="7"><strong>TOTAL</strong></td>
            <td>{{ number_format($totalBruto, 2, ',', '.') }}</td>
            <td>{{ number_format($totalComissao, 2, ',', '.') }}</td>
            <td>{{ number_format($totalLiquido, 2, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
</body>
</html>
