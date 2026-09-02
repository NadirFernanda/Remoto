<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recibos em Lote — {{ count($services) }} serviço(s)</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #e5e7eb;
            padding: 20px 12px 40px;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .slip-wrap { box-shadow: none !important; }
            .page-break { page-break-after: always; }
        }

        /* Action bar */
        .action-bar {
            max-width: 820px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #0a1228;
            padding: 12px 20px;
            border-radius: 12px;
        }
        .action-bar span { font-family: Arial, sans-serif; font-size: 13px; color: #94a3b8; }
        .action-bar strong { color: #fff; }
        .btn-print {
            display: inline-flex; align-items: center; gap: 6px;
            background: linear-gradient(135deg, #00c8ff, #0055ff);
            color: #fff; border: none;
            padding: 9px 22px; border-radius: 20px;
            font-family: Arial, sans-serif; font-size: 13px;
            font-weight: 700; cursor: pointer; transition: opacity .15s;
        }
        .btn-print:hover { opacity: .85; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 5px;
            font-family: Arial, sans-serif; font-size: 12px;
            color: #94a3b8; text-decoration: none;
        }
        .btn-back:hover { color: #fff; }

        /* Grid of slips */
        .slips-grid {
            max-width: 820px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 24px;
        }

        /* Slip card */
        .slip-wrap {
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 4px 24px rgba(0,0,0,.18);
            overflow: hidden;
        }
        .tear-top, .tear-bottom {
            width: 100%; height: 14px;
            background: repeating-linear-gradient(90deg, #fff 0, #fff 8px, transparent 8px, transparent 16px);
            background-color: #e5e7eb;
        }
        .slip-header {
            background: #0a1228; color: #fff;
            text-align: center; padding: 16px 16px 12px;
        }
        .slip-header .platform { font-size: 18px; font-weight: 900; letter-spacing: 2px; font-family: Arial, sans-serif; }
        .slip-header .platform-sub { font-size: 9px; letter-spacing: 1px; opacity: .6; margin-top: 2px; font-family: Arial, sans-serif; }
        .slip-header .receipt-type { margin-top: 8px; font-size: 10px; letter-spacing: 3px; opacity: .8; }
        .status-band {
            text-align: center; padding: 8px 0 6px;
            font-size: 10px; font-weight: bold; letter-spacing: 2px;
            border-bottom: 1px dashed #d1d5db;
        }
        .status-dot {
            display: inline-block; width: 6px; height: 6px;
            border-radius: 50%; margin-right: 5px; vertical-align: middle;
        }
        .slip-body { padding: 12px 18px; }
        .slip-row {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding: 4px 0; font-size: 10px; line-height: 1.4;
        }
        .slip-row .lbl { color: #6b7280; text-transform: uppercase; letter-spacing: .5px; flex-shrink: 0; padding-right: 8px; }
        .slip-row .val { font-weight: 600; text-align: right; word-break: break-all; }
        .divider-dash { border: none; border-top: 1px dashed #d1d5db; margin: 8px 0; }
        .divider-solid { border: none; border-top: 2px solid #0a1228; margin: 10px 0; }
        .amount-block { text-align: center; padding: 10px 0 8px; }
        .amount-block .amount-label { font-size: 9px; text-transform: uppercase; letter-spacing: 2px; color: #6b7280; }
        .amount-block .amount-value { font-size: 22px; font-weight: 900; color: #0a1228; font-family: Arial, sans-serif; margin-top: 3px; }
        .amount-block .amount-currency { font-size: 11px; color: #6b7280; }
        .ref-block {
            background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px;
            text-align: center; padding: 8px 12px; margin: 8px 0;
        }
        .ref-block .ref-label { font-size: 8px; text-transform: uppercase; letter-spacing: 2px; color: #94a3b8; }
        .ref-block .ref-value { font-size: 14px; font-weight: 900; letter-spacing: 3px; color: #0a1228; margin-top: 2px; font-family: Arial, sans-serif; }
        .slip-footer {
            background: #f8fafc; border-top: 1px dashed #d1d5db;
            text-align: center; padding: 10px 16px;
            font-size: 8px; color: #94a3b8; line-height: 1.6; letter-spacing: .3px;
        }
        .slip-footer strong { color: #6b7280; }
        .slip-index {
            background: linear-gradient(135deg, #00c8ff, #0055ff);
            color: #fff; font-family: Arial, sans-serif; font-size: 10px;
            font-weight: 700; text-align: center; padding: 4px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<div class="action-bar no-print">
    <div>
        <a href="javascript:history.back()" class="btn-back">@include('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4']) Voltar</a>
    </div>
    <span><strong>{{ count($services) }}</strong> recibo(s) seleccionado(s)</span>
    <button class="btn-print" onclick="window.print()">
        <span class="inline-flex items-center gap-2"><svg class="w-4 h-4" ...> ... </svg>Imprimir / Guardar PDF</span>
    </button>
</div>

<div class="slips-grid">
    @foreach($services as $service)
        @php
            $refNum      = 'REF' . now()->year . str_pad($service->id, 7, '0', STR_PAD_LEFT);
            $bruto       = (float) ($service->valor ?? 0);
            $taxa_cli    = round($bruto * 0.10, 2);
            $statusLabel = match($service->status) {
                'published'                                  => ['label' => 'PAGO — RETIDO EM ESCROW', 'color' => '#0052cc'],
                'accepted', 'negotiating'                    => ['label' => 'PAGO — EM NEGOCIAÇÃO',    'color' => '#6366f1'],
                'in_progress','em_andamento','em andamento'  => ['label' => 'PAGO — EM EXECUÇÃO',      'color' => '#d97706'],
                'delivered'                                  => ['label' => 'PAGO — AGUARDA REVISÃO',  'color' => '#0891b2'],
                'completed','concluido'                      => ['label' => 'PAGO — CONCLUÍDO',        'color' => '#16a34a'],
                'cancelled','cancelado'                      => ['label' => 'CANCELADO',               'color' => '#dc2626'],
                default                                      => ['label' => strtoupper($service->status), 'color' => '#64748b'],
            };
            $clientName    = optional($service->cliente)->name   ?? '—';
            $clientEmail   = optional($service->cliente)->email  ?? '—';
            $freelancerName = optional($service->freelancer)->name ?? 'A atribuir';
            $paidAt = $service->created_at->format('d/m/Y') . ' às ' . $service->created_at->format('H:i');
        @endphp

        <div class="slip-wrap">
            <div class="slip-index">RECIBO {{ $loop->iteration }}/{{ count($services) }}</div>
            <div class="tear-top"></div>

            <div class="slip-header">
                <div class="platform">24 HORAS</div>
                <div class="platform-sub">PLATAFORMA DE FREELANCERS · ANGOLA</div>
                <div class="receipt-type">COMPROVATIVO DE PAGAMENTO</div>
            </div>

            <div class="status-band" style="color:{{ $statusLabel['color'] }};">
                <span class="status-dot" style="background:{{ $statusLabel['color'] }};"></span>
                {{ $statusLabel['label'] }}
            </div>

            <div class="slip-body">
                <div class="slip-row">
                    <span class="lbl">Data/Hora</span>
                    <span class="val">{{ $paidAt }}</span>
                </div>
                <div class="slip-row">
                    <span class="lbl">Entidade</span>
                    <span class="val">99924</span>
                </div>

                <hr class="divider-dash">

                <div class="ref-block">
                    <div class="ref-label">Referência de Pagamento</div>
                    <div class="ref-value">{{ $refNum }}</div>
                </div>

                <hr class="divider-dash">

                <div class="amount-block">
                    <div class="amount-label">Montante Total Pago</div>
                    <div class="amount-value">
                        {{ number_format($bruto, 2, ',', '.') }}
                        <span class="amount-currency">Kz</span>
                    </div>
                </div>

                <div class="slip-row" style="font-size:9px;">
                    <span class="lbl" style="color:#94a3b8;">Taxa Plataforma (10%)</span>
                    <span class="val" style="color:#94a3b8;">{{ number_format($taxa_cli, 2, ',', '.') }} Kz</span>
                </div>

                <hr class="divider-solid">

                <div class="slip-row">
                    <span class="lbl">Cliente</span>
                    <span class="val">{{ $clientName }}</span>
                </div>
                <div class="slip-row">
                    <span class="lbl">E-mail</span>
                    <span class="val">{{ $clientEmail }}</span>
                </div>

                <hr class="divider-dash">

                <div class="slip-row">
                    <span class="lbl">Projecto</span>
                    <span class="val">{{ Str::limit($service->titulo ?? 'Projecto #'.$service->id, 35) }}</span>
                </div>
                <div class="slip-row">
                    <span class="lbl">Freelancer</span>
                    <span class="val">{{ $freelancerName }}</span>
                </div>
            </div>

            <div class="tear-bottom"></div>

            <div class="slip-footer">
                <strong>24 Horas Freelancer · Angola</strong><br>
                Documento gerado em {{ now()->format('d/m/Y \à\s H:i') }}
            </div>
        </div>
    @endforeach
</div>

</body>
</html>
