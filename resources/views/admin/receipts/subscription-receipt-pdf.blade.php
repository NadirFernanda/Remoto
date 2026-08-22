<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $refNum = 'ASS' . $subscription->created_at->format('Y') . str_pad($subscription->id, 7, '0', STR_PAD_LEFT);

        $statusLabel = match($subscription->status) {
            'active'    => ['label' => 'PAGO — ASSINATURA ACTIVA', 'color' => '#16a34a'],
            'cancelled' => ['label' => 'PAGO — CANCELADA',         'color' => '#dc2626'],
            'expired'   => ['label' => 'PAGO — EXPIRADA',          'color' => '#64748b'],
            default     => ['label' => strtoupper($subscription->status), 'color' => '#64748b'],
        };

        $paidAt = $subscription->created_at->format('d/m/Y') . ' às ' . $subscription->created_at->format('H:i');
    @endphp
    <title>Comprovativo {{ $refNum }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #e5e7eb;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 12px 40px;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .slip-wrap { box-shadow: none !important; margin: 0; }
        }

        .action-bar {
            width: 100%;
            max-width: 380px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .action-bar span { font-family: Arial, sans-serif; font-size: 12px; color: #6b7280; }
        .btn-print {
            display: inline-flex; align-items: center; gap: 6px;
            background: #1e40af; color: #fff; border: none;
            padding: 8px 18px; border-radius: 20px;
            font-family: Arial, sans-serif; font-size: 12px;
            font-weight: 700; cursor: pointer; transition: opacity .15s;
        }
        .btn-print:hover { opacity: .85; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 5px;
            font-family: Arial, sans-serif; font-size: 12px;
            color: #4b5563; text-decoration: none;
            padding: 7px 14px; border-radius: 20px;
            border: 1px solid #d1d5db; background: #fff;
        }
        .btn-back:hover { background: #f9fafb; }

        .slip-wrap {
            width: 100%;
            max-width: 380px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 4px 24px rgba(0,0,0,.18);
            overflow: hidden;
            position: relative;
        }

        .tear-top, .tear-bottom {
            width: 100%;
            height: 16px;
            background: repeating-linear-gradient(90deg, #fff 0, #fff 8px, transparent 8px, transparent 16px);
            background-color: #e5e7eb;
        }

        .slip-header {
            background: #0a1228;
            color: #fff;
            text-align: center;
            padding: 20px 16px 16px;
        }
        .slip-header .platform { font-size: 20px; font-weight: 900; letter-spacing: 2px; font-family: Arial, sans-serif; }
        .slip-header .platform-sub { font-size: 10px; letter-spacing: 1px; opacity: .6; margin-top: 2px; font-family: Arial, sans-serif; }
        .slip-header .receipt-type { margin-top: 10px; font-size: 11px; letter-spacing: 3px; opacity: .8; }

        .status-band {
            text-align: center;
            padding: 10px 0 8px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 2px;
            color: {{ $statusLabel['color'] }};
            border-bottom: 1px dashed #d1d5db;
        }
        .status-dot {
            display: inline-block;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: {{ $statusLabel['color'] }};
            margin-right: 5px;
            vertical-align: middle;
        }

        .slip-body { padding: 16px 20px; }

        .slip-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 5px 0;
            font-size: 11px;
            line-height: 1.4;
        }
        .slip-row .lbl { color: #6b7280; text-transform: uppercase; letter-spacing: .5px; flex-shrink: 0; padding-right: 8px; }
        .slip-row .val { font-weight: 600; text-align: right; word-break: break-all; }

        .divider-dash { border: none; border-top: 1px dashed #d1d5db; margin: 10px 0; }
        .divider-solid { border: none; border-top: 2px solid #0a1228; margin: 12px 0; }

        .amount-block { text-align: center; padding: 14px 0 10px; }
        .amount-block .amount-label { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: #6b7280; }
        .amount-block .amount-value { font-size: 28px; font-weight: 900; color: #0a1228; font-family: Arial, sans-serif; margin-top: 4px; }
        .amount-block .amount-currency { font-size: 13px; color: #6b7280; }

        .ref-block {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            text-align: center;
            padding: 10px 12px;
            margin: 10px 0;
        }
        .ref-block .ref-label { font-size: 9px; text-transform: uppercase; letter-spacing: 2px; color: #94a3b8; }
        .ref-block .ref-value { font-size: 17px; font-weight: 900; letter-spacing: 3px; color: #0a1228; margin-top: 3px; font-family: Arial, sans-serif; }

        .barcode { display: flex; justify-content: center; align-items: flex-end; gap: 1px; margin: 12px 0 4px; height: 36px; }
        .bar { background: #0a1228; border-radius: 1px; }

        .slip-footer {
            background: #f8fafc;
            border-top: 1px dashed #d1d5db;
            text-align: center;
            padding: 12px 16px;
            font-size: 9px;
            color: #94a3b8;
            line-height: 1.6;
            letter-spacing: .3px;
        }
        .slip-footer strong { color: #6b7280; }
    </style>
</head>
<body>

<div class="action-bar no-print">
    <a href="javascript:history.back()" class="btn-back">← Voltar</a>
    <button class="btn-print" onclick="window.print()">🖨 Imprimir / PDF</button>
</div>

<div class="slip-wrap">
    <div class="tear-top"></div>

    <div class="slip-header">
        <div class="platform">24 HORAS</div>
        <div class="platform-sub">PLATAFORMA DE FREELANCERS · ANGOLA</div>
        <div class="receipt-type">COMPROVATIVO DE ASSINATURA</div>
    </div>

    <div class="status-band">
        <span class="status-dot"></span>{{ $statusLabel['label'] }}
    </div>

    <div class="slip-body">

        <div class="slip-row">
            <span class="lbl">Data/Hora</span>
            <span class="val">{{ $paidAt }}</span>
        </div>

        <hr class="divider-dash">

        <div class="ref-block">
            <div class="ref-label">Referência de Pagamento</div>
            <div class="ref-value">{{ $refNum }}</div>
        </div>

        <hr class="divider-dash">

        <div class="amount-block">
            <div class="amount-label">Montante Total Pago</div>
            <div class="amount-value">{{ number_format($subscription->amount, 2, ',', '.') }} <span class="amount-currency">Kz</span></div>
        </div>

        <div class="slip-row" style="font-size:10px;">
            <span class="lbl" style="color:#94a3b8;">Valor da Assinatura</span>
            <span class="val" style="color:#94a3b8;">{{ number_format($subscription->amount, 2, ',', '.') }} Kz</span>
        </div>
        <div class="slip-row" style="font-size:10px;">
            <span class="lbl" style="color:#94a3b8;">Taxa Plataforma</span>
            <span class="val" style="color:#94a3b8;">{{ number_format($subscription->platform_fee, 2, ',', '.') }} Kz</span>
        </div>
        <div class="slip-row" style="font-size:10px;">
            <span class="lbl" style="color:#94a3b8;">Valor Líquido ao Criador</span>
            <span class="val" style="color:#94a3b8;">{{ number_format($subscription->net_amount, 2, ',', '.') }} Kz</span>
        </div>

        <hr class="divider-solid">

        <div class="slip-row">
            <span class="lbl">Assinante</span>
            <span class="val">{{ optional($subscription->subscriber)->name ?? '—' }}</span>
        </div>
        <div class="slip-row">
            <span class="lbl">E-mail</span>
            <span class="val">{{ optional($subscription->subscriber)->email ?? '—' }}</span>
        </div>

        <hr class="divider-dash">

        <div class="slip-row">
            <span class="lbl">Criador</span>
            <span class="val">{{ optional($subscription->creator)->name ?? '—' }}</span>
        </div>
        @if($subscription->starts_at)
        <div class="slip-row">
            <span class="lbl">Início</span>
            <span class="val">{{ $subscription->starts_at->format('d/m/Y') }}</span>
        </div>
        @endif
        @if($subscription->expires_at)
        <div class="slip-row">
            <span class="lbl">Renova/Expira</span>
            <span class="val">{{ $subscription->expires_at->format('d/m/Y') }}</span>
        </div>
        @endif

        <hr class="divider-dash">

        @php
            $bars = [];
            foreach(str_split(preg_replace('/\D/', '', $refNum)) as $d) {
                $bars[] = (int)$d + 1;
                $bars[] = max(1, 5 - (int)$d);
            }
        @endphp
        <div class="barcode">
            @foreach($bars as $b)
                <div class="bar" style="width:{{ $b <= 2 ? 1 : ($b <= 4 ? 2 : 3) }}px; height:{{ 18 + ($b * 2) }}px;"></div>
            @endforeach
        </div>
        <p style="text-align:center;font-size:9px;color:#94a3b8;letter-spacing:2px;margin-bottom:8px;">{{ $refNum }}</p>

        <div style="text-align:center;font-size:9px;color:#94a3b8;padding-bottom:4px;">
            Conserve este comprovativo para os seus registos
        </div>

    </div>

    <div class="tear-bottom"></div>

    <div class="slip-footer">
        <strong>24 Horas Freelancer · Angola</strong><br>
        support@24horasfreelancer.com<br>
        Documento gerado em {{ now()->format('d/m/Y \à\s H:i') }}<br>
        Este comprovativo não substitui factura fiscal oficial.
    </div>
</div>

</body>
</html>
