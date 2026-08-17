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
.sem-conta { color: #dc2626; font-weight: bold; }
</style>
</head>
<body>
<div class="titulo">Ficheiro de Pagamento Bancário — Saques Pendentes — 24 Horas</div>
<div class="sub">Gerado em: {{ now()->format('d/m/Y H:i') }} · Confirme o formato exigido pelo portal do seu banco antes de submeter este ficheiro.</div>

<table>
    <thead>
        <tr>
            @foreach($headers as $h)
            <th>{{ $h }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @foreach($rows as $row)
        @php $total += (float) str_replace(['.', ','], ['', '.'], $row[3] ?? '0'); @endphp
        <tr>
            @foreach($row as $i => $cell)
            <td class="{{ $i === 1 && str_contains($cell, 'SEM CONTA') ? 'sem-conta' : '' }}">{{ $cell }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="totais">
            <td colspan="3"><strong>TOTAL A PAGAR</strong></td>
            <td>{{ number_format($total, 2, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
</body>
</html>
