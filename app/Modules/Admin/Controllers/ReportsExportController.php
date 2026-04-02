<?php

namespace App\Modules\Admin\Controllers;

use App\Models\WalletLog;
use App\Models\Service;
use App\Models\InfoprodutoCompra;
use App\Models\CreatorSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReportsExportController extends Controller
{
    // ── Shared: resolve date range from request ───────────────────────────────
    private function range(Request $request): array
    {
        $start = $request->filled('date_start')
            ? Carbon::parse($request->date_start)->startOfDay()
            : match ($request->get('period', 'month')) {
                'week' => Carbon::now()->startOfWeek(),
                'year' => Carbon::now()->startOfYear(),
                default => Carbon::now()->startOfMonth(),
            };

        $end = $request->filled('date_end')
            ? Carbon::parse($request->date_end)->endOfDay()
            : Carbon::now()->endOfDay();

        return [$start, $end];
    }

    // ══════════════════════════════════════════════════════════════════════════
    // A) FLUXO DE CAIXA
    // ══════════════════════════════════════════════════════════════════════════

    private function cashFlowRows(Request $request): array
    {
        [$start, $end] = $this->range($request);

        $headers = ['Origem', 'Entradas (AOA)', 'Saídas (AOA)', 'Comissão (AOA)', 'Saldo (AOA)'];

        $grupos = [
            'Freelances' => [
                'entradas' => (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'escrow_retido')->sum('valor'),
                'saidas'   => (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'saque_aprovado')->sum('valor'),
                'comissao' => (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'pagamento_projeto')->sum('valor') * 10 / 90,
            ],
            'Criador' => [
                'entradas' => (float) CreatorSubscription::whereBetween('created_at', [$start, $end])->sum('amount'),
                'saidas'   => (float) CreatorSubscription::whereBetween('created_at', [$start, $end])->sum('net_amount'),
                'comissao' => (float) CreatorSubscription::whereBetween('created_at', [$start, $end])->sum('platform_fee'),
            ],
            'Infoprodutos' => [
                'entradas' => (float) InfoprodutoCompra::whereBetween('created_at', [$start, $end])->sum('valor_pago'),
                'saidas'   => (float) InfoprodutoCompra::whereBetween('created_at', [$start, $end])->sum('valor_freelancer'),
                'comissao' => (float) InfoprodutoCompra::whereBetween('created_at', [$start, $end])->sum('comissao_plataforma'),
            ],
            'Afiliados' => [
                'entradas' => (float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'comissao_afiliado')->where('valor', '>', 0)->sum('valor'),
                'saidas'   => abs((float) WalletLog::whereBetween('created_at', [$start, $end])->where('tipo', 'comissao_afiliado')->where('valor', '<', 0)->sum('valor')),
                'comissao' => 0,
            ],
        ];

        $rows = [$headers];
        foreach ($grupos as $origem => $g) {
            $rows[] = [
                $origem,
                number_format($g['entradas'], 2, ',', '.'),
                number_format($g['saidas'],   2, ',', '.'),
                number_format($g['comissao'], 2, ',', '.'),
                number_format($g['entradas'] - $g['saidas'], 2, ',', '.'),
            ];
        }

        $totalEntradas = array_sum(array_column(array_slice($rows, 1), 1));
        $totalSaidas   = array_sum(array_column(array_slice($rows, 1), 2));
        $totalComissao = array_sum(array_column(array_slice($rows, 1), 3));

        // Re-calculate totals from float grupos
        $tE = array_sum(array_column(array_values($grupos), 'entradas'));
        $tS = array_sum(array_column(array_values($grupos), 'saidas'));
        $tC = array_sum(array_column(array_values($grupos), 'comissao'));

        $rows[] = [
            'TOTAL',
            number_format($tE, 2, ',', '.'),
            number_format($tS, 2, ',', '.'),
            number_format($tC, 2, ',', '.'),
            number_format($tE - $tS, 2, ',', '.'),
        ];

        return $rows;
    }

    public function cashFlowCsv(Request $request)
    {
        $rows     = $this->cashFlowRows($request);
        $filename = 'fluxo_caixa_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($rows) {
            $h = fopen('php://output', 'w');
            fwrite($h, "\xEF\xBB\xBF");
            foreach ($rows as $row) { fputcsv($h, $row); }
            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function cashFlowExcel(Request $request)
    {
        $rows     = $this->cashFlowRows($request);
        $filename = 'fluxo_caixa_' . now()->format('Ymd_His') . '.xls';
        [$start, $end] = $this->range($request);

        return response(
            view('admin.exports.cash-flow-pdf', ['rows' => array_slice($rows, 1), 'headers' => $rows[0], 'start' => $start, 'end' => $end, 'excel' => true])->render(),
            200,
            [
                'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    public function cashFlowPdf(Request $request)
    {
        $rows  = $this->cashFlowRows($request);
        [$start, $end] = $this->range($request);

        return response(
            view('admin.exports.cash-flow-pdf', ['rows' => array_slice($rows, 1), 'headers' => $rows[0], 'start' => $start, 'end' => $end, 'excel' => false])->render(),
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    // ══════════════════════════════════════════════════════════════════════════
    // B) EXTRATO CONTABILIDADE
    // ══════════════════════════════════════════════════════════════════════════

    private function accountingRows(Request $request): array
    {
        [$start, $end] = $this->range($request);
        $tipo = $request->get('tipo', '');

        $headers = ['Serviço / Produto', 'Data', 'Tipo', 'Utilizador Origem', 'Utilizador Destino', 'Valor Bruto (AOA)', 'Comissão (AOA)', 'Valor Líquido (AOA)', 'Status'];
        $rows    = [$headers];

        if (in_array($tipo, ['', 'freelancing'])) {
            Service::with(['cliente:id,name', 'freelancer:id,name'])
                ->whereNotNull('valor')
                ->whereIn('status', ['in_progress', 'delivered', 'completed', 'cancelled'])
                ->orderByDesc('updated_at')
                ->get()
                ->each(function ($s) use (&$rows) {
                    $bruto   = (float)($s->valor ?? 0);
                    $liquido = (float)($s->valor_liquido ?? $bruto * 0.9);
                    $rows[]  = [
                        $s->titulo ?? 'Projecto #' . $s->id,
                        $s->updated_at->format('d/m/Y'),
                        'Freelances',
                        optional($s->cliente)->name    ?? '—',
                        optional($s->freelancer)->name ?? '—',
                        number_format($bruto,          2, ',', '.'),
                        number_format($bruto - $liquido, 2, ',', '.'),
                        number_format($liquido,         2, ',', '.'),
                        ucfirst($s->status ?? '—'),
                    ];
                });
        }

        if (in_array($tipo, ['', 'infoproduto'])) {
            InfoprodutoCompra::with(['infoproduto:id,titulo,user_id', 'comprador:id,name', 'infoproduto.user:id,name'])
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at')
                ->get()
                ->each(function ($c) use (&$rows) {
                    $rows[] = [
                        optional($c->infoproduto)->titulo ?? 'Infoproduto #' . $c->infoproduto_id,
                        $c->created_at->format('d/m/Y'),
                        'Infoproduto',
                        optional($c->comprador)->name                           ?? '—',
                        optional(optional($c->infoproduto)->user)->name         ?? '—',
                        number_format((float)$c->valor_pago,          2, ',', '.'),
                        number_format((float)$c->comissao_plataforma, 2, ',', '.'),
                        number_format((float)$c->valor_freelancer,    2, ',', '.'),
                        'Concluído',
                    ];
                });
        }

        if (in_array($tipo, ['', 'creator'])) {
            CreatorSubscription::with(['subscriber:id,name', 'creator:id,name'])
                ->whereBetween('created_at', [$start, $end])
                ->orderByDesc('created_at')
                ->get()
                ->each(function ($sub) use (&$rows) {
                    $rows[] = [
                        'Assinatura Criador',
                        $sub->created_at->format('d/m/Y'),
                        'Criador',
                        optional($sub->subscriber)->name ?? '—',
                        optional($sub->creator)->name    ?? '—',
                        number_format((float)$sub->amount,       2, ',', '.'),
                        number_format((float)$sub->platform_fee, 2, ',', '.'),
                        number_format((float)$sub->net_amount,   2, ',', '.'),
                        ucfirst($sub->status ?? 'active'),
                    ];
                });
        }

        return $rows;
    }

    public function accountingCsv(Request $request)
    {
        $rows     = $this->accountingRows($request);
        $filename = 'extrato_contabilidade_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($rows) {
            $h = fopen('php://output', 'w');
            fwrite($h, "\xEF\xBB\xBF");
            foreach ($rows as $row) { fputcsv($h, $row); }
            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function accountingExcel(Request $request)
    {
        $rows     = $this->accountingRows($request);
        $filename = 'extrato_contabilidade_' . now()->format('Ymd_His') . '.xls';
        [$start, $end] = $this->range($request);

        return response(
            view('admin.exports.accounting-pdf', ['rows' => array_slice($rows, 1), 'headers' => $rows[0], 'start' => $start, 'end' => $end, 'excel' => true])->render(),
            200,
            [
                'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    public function accountingPdf(Request $request)
    {
        $rows  = $this->accountingRows($request);
        [$start, $end] = $this->range($request);

        return response(
            view('admin.exports.accounting-pdf', ['rows' => array_slice($rows, 1), 'headers' => $rows[0], 'start' => $start, 'end' => $end, 'excel' => false])->render(),
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    // ══════════════════════════════════════════════════════════════════════════
    // C) SAQUES
    // ══════════════════════════════════════════════════════════════════════════

    private function withdrawalRows(Request $request): array
    {
        [$start, $end] = $this->range($request);
        $status = $request->get('status', '');
        $search = $request->get('search', '');

        $tipos = $status
            ? [$status]
            : ['saque_solicitado', 'saque_aprovado', 'saque_rejeitado'];

        $headers = ['Nome', 'Email', 'Data', 'Status', 'Origem', 'Valor (AOA)'];
        $rows    = [$headers];

        WalletLog::with('user:id,name,email,role')
            ->whereIn('tipo', $tipos)
            ->whereBetween('created_at', [$start, $end])
            ->when($search, fn ($q) =>
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%'))
            )
            ->orderByDesc('created_at')
            ->get()
            ->each(function ($log) use (&$rows) {
                $statusLabel = match($log->tipo) {
                    'saque_solicitado' => 'Pendente',
                    'saque_aprovado'   => 'Aprovado',
                    'saque_rejeitado'  => 'Rejeitado',
                    default            => $log->tipo,
                };
                $origem = ucfirst(optional($log->user)->role ?? '—');

                $rows[] = [
                    optional($log->user)->name  ?? '—',
                    optional($log->user)->email ?? '—',
                    $log->created_at->format('d/m/Y H:i'),
                    $statusLabel,
                    $origem,
                    number_format(abs((float)$log->valor), 2, ',', '.'),
                ];
            });

        return $rows;
    }

    public function withdrawalsCsv(Request $request)
    {
        $rows     = $this->withdrawalRows($request);
        $filename = 'saques_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($rows) {
            $h = fopen('php://output', 'w');
            fwrite($h, "\xEF\xBB\xBF");
            foreach ($rows as $row) { fputcsv($h, $row); }
            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function withdrawalsPdf(Request $request)
    {
        $rows  = $this->withdrawalRows($request);
        [$start, $end] = $this->range($request);

        return response(
            view('admin.exports.withdrawal-pdf', ['rows' => array_slice($rows, 1), 'headers' => $rows[0], 'start' => $start, 'end' => $end])->render(),
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }
}
