<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Factura Nº FAT-{{ now()->year }}-{{ str_pad($service->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; background: #f0f4f8; color: #1a202c; }

        /* Print: hide browser chrome, fill page */
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .invoice-wrap { box-shadow: none !important; border: none !important; margin: 0; max-width: 100%; }
        }

        /* Screen action bar */
        .action-bar {
            background: #0052cc;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .action-bar span { color: #fff; font-size: 13px; opacity: .85; }
        .btn-print {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fff; color: #0052cc; border: none;
            padding: 8px 20px; border-radius: 20px;
            font-size: 13px; font-weight: 700; cursor: pointer;
            transition: opacity .15s;
        }
        .btn-print:hover { opacity: .85; }

        /* Invoice wrapper */
        .invoice-wrap {
            max-width: 680px;
            margin: 32px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10);
            overflow: hidden;
        }

        /* Header band */
        .inv-header {
            background: linear-gradient(135deg, #0052cc 0%, #0a1228 100%);
            color: #fff;
            padding: 32px 36px 28px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .inv-header .company-name { font-size: 22px; font-weight: 800; letter-spacing: -.5px; }
        .inv-header .company-sub  { font-size: 11px; opacity: .7; margin-top: 3px; }
        .inv-header .inv-title    { text-align: right; }
        .inv-header .inv-title h1 { font-size: 28px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; opacity: .95; }
        .inv-header .inv-num      { font-size: 13px; opacity: .75; margin-top: 4px; }
        .inv-header .inv-date     { font-size: 12px; opacity: .65; margin-top: 2px; }

        /* Status strip */
        .status-strip {
            padding: 10px 36px;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: .4px;
            text-transform: uppercase;
        }
        .status-strip.pago       { background: #d1fae5; color: #065f46; }
        .status-strip.andamento  { background: #fef3c7; color: #92400e; }
        .status-strip.cancelado  { background: #fee2e2; color: #991b1b; }
        .status-strip .dot       { width: 8px; height: 8px; border-radius: 50%; background: currentColor; }

        /* Body */
        .inv-body { padding: 28px 36px; }

        /* Two-column grid */
        .parties { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; }
        .party h3 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 8px; }
        .party p  { font-size: 13px; line-height: 1.6; color: #1a202c; }
        .party .name { font-weight: 700; font-size: 14px; }

        /* Service table */
        .inv-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .inv-table thead tr { background: #f8fafc; }
        .inv-table th {
            padding: 10px 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }
        .inv-table td {
            padding: 12px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #374151;
            vertical-align: top;
        }
        .inv-table .right { text-align: right; }
        .inv-table tfoot td {
            padding: 14px 12px;
            border-top: 2px solid #e2e8f0;
            font-weight: 700;
            font-size: 15px;
            color: #0052cc;
        }

        /* Notes */
        .inv-note {
            background: #f8fafc;
            border-left: 3px solid #0052cc;
            padding: 12px 16px;
            border-radius: 0 8px 8px 0;
            font-size: 12px;
            color: #475569;
            margin-top: 4px;
        }

        /* Footer */
        .inv-footer {
            border-top: 1px solid #f1f5f9;
            padding: 18px 36px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            background: #fafcff;
        }
    </style>
</head>
<body>

@php
    $fatNum = 'FAT-' . now()->year . '-' . str_pad($service->id, 5, '0', STR_PAD_LEFT);

    $statusLabel = match($service->status) {
        'completed', 'concluido'                     => ['label' => 'Pago — Concluído',  'class' => 'pago'],
        'delivered'                                  => ['label' => 'Pago — Entregue',   'class' => 'pago'],
        'in_progress', 'em_andamento', 'em andamento'=> ['label' => 'Pago — Em Execução','class' => 'andamento'],
        'cancelled', 'cancelado'                     => ['label' => 'Cancelado',          'class' => 'cancelado'],
        default                                      => ['label' => ucfirst($service->status), 'class' => 'andamento'],
    };

    $bruto   = (float) ($service->valor ?? 0);
    $liquido = (float) ($service->valor_liquido ?? $bruto * 0.9);
    $comissao = $bruto - $liquido;

    $freelancerName = optional($service->freelancer)->name ?? '—';
@endphp

{{-- Action bar (screen only) --}}
<div class="action-bar no-print">
    <span>Factura de Serviço · {{ $fatNum }}</span>
    <button class="btn-print" onclick="window.print()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Imprimir / Guardar PDF
    </button>
</div>

<div class="invoice-wrap">

    {{-- Header --}}
    <div class="inv-header">
        <div>
            <div class="company-name">24 Horas</div>
            <div class="company-sub">Plataforma de Freelancers · Angola</div>
            <div class="company-sub" style="margin-top:10px;">support@24horasfreelancer.com</div>
        </div>
        <div class="inv-title">
            <h1>Factura</h1>
            <div class="inv-num">Nº {{ $fatNum }}</div>
            <div class="inv-date">Emitida em {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- Payment status strip --}}
    <div class="status-strip {{ $statusLabel['class'] }}">
        <span class="dot"></span>
        {{ $statusLabel['label'] }}
    </div>

    <div class="inv-body">

        {{-- Parties --}}
        <div class="parties">
            <div class="party">
                <h3>Faturado A (Cliente)</h3>
                <p class="name">{{ $user->name }}</p>
                <p>{{ $user->email }}</p>
                @if($user->telefone)<p>{{ $user->telefone }}</p>@endif
            </div>
            <div class="party">
                <h3>Prestador de Serviço</h3>
                <p class="name">{{ $freelancerName }}</p>
                @if(optional($service->freelancer)->email)
                    <p>{{ $service->freelancer->email }}</p>
                @endif
            </div>
        </div>

        {{-- Service table --}}
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Descrição do Serviço</th>
                    <th>Data</th>
                    <th class="right">Valor Bruto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $service->titulo ?? 'Serviço #' . $service->id }}</strong>
                        @if($service->descricao)
                            <br><span style="font-size:11px;color:#94a3b8;margin-top:3px;display:block;">{{ Str::limit($service->descricao, 120) }}</span>
                        @endif
                    </td>
                    <td>{{ $service->created_at->format('d/m/Y') }}</td>
                    <td class="right">{{ number_format($bruto, 2, ',', '.') }} Kz</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="right">Total Pago</td>
                    <td class="right">{{ number_format($bruto, 2, ',', '.') }} Kz</td>
                </tr>
            </tfoot>
        </table>

        {{-- Note --}}
        <div class="inv-note">
            Este documento comprova o pagamento do serviço acima referido através da plataforma 24 Horas.
            Conserve este comprovativo para os seus registos contabilísticos.
        </div>

    </div>

    {{-- Footer --}}
    <div class="inv-footer">
        Factura gerada automaticamente pela plataforma 24 Horas · {{ now()->format('d/m/Y H:i') }}
        · Documento não substitui factura fiscal oficial
    </div>

</div>
</body>
</html>
