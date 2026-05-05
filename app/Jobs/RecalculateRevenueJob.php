<?php

namespace App\Jobs;

use App\Models\FreelancerProfile;
use App\Models\User;
use App\Models\WalletLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Recalcula a receita total do freelancer somando todos os créditos
 * de rendimento no WalletLog e actualiza FreelancerProfile.metrics.
 *
 * ShouldBeUnique garante que apenas um job por utilizador fica na fila
 * de cada vez — se chegar um segundo dispatch enquanto o primeiro ainda
 * não correu, é descartado em vez de duplicar o cálculo.
 */
class RecalculateRevenueJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    // Tipos de WalletLog que representam rendimento real do freelancer (apenas positivos)
    private const REVENUE_TIPOS = [
        'ganho_servico',               // pagamento libertado pelo cliente (ServiceEscrowController)
        'reembolso_parcial_freelancer',// freelancer retém parte num reembolso parcial
        'comissao_afiliado',           // comissão por referência
        'ajuste_admin',                // crédito manual do admin na carteira (valor > 0)
    ];

    // Ajustes explícitos de receita feitos pelo admin (podem ser positivos ou negativos)
    private const REVENUE_ADJUSTMENT_TIPO = 'ajuste_receita_admin';

    public function __construct(
        private readonly User $user
    ) {}

    /**
     * Chave de unicidade: um job por utilizador na fila.
     */
    public function uniqueId(): string
    {
        return 'recalculate_revenue_' . $this->user->id;
    }

    public function handle(): void
    {
        if ($this->user->role !== 'freelancer') {
            return;
        }

        // Rendimento bruto (apenas positivos — ganhos reais)
        $receitaBruta = (float) WalletLog::where('user_id', $this->user->id)
            ->whereIn('tipo', self::REVENUE_TIPOS)
            ->where('valor', '>', 0)
            ->sum('valor');

        // Ajustes manuais de receita pelo admin (podem ser positivos ou negativos)
        $ajustesAdmin = (float) WalletLog::where('user_id', $this->user->id)
            ->where('tipo', self::REVENUE_ADJUSTMENT_TIPO)
            ->sum('valor');

        // Chargebacks (valor negativo, já foi congelado da carteira)
        $chargebacks = (float) WalletLog::where('user_id', $this->user->id)
            ->where('tipo', 'chargeback_congelado')
            ->sum('valor');

        $receitaLiquida = $receitaBruta + $ajustesAdmin + $chargebacks;

        $profile = FreelancerProfile::where('user_id', $this->user->id)->first();

        if ($profile) {
            $metrics                   = $profile->metrics ?? [];
            $metrics['receita_total']  = round(max(0, $receitaLiquida), 2);
            $metrics['receita_bruta']  = round($receitaBruta, 2);
            $metrics['ajustes_admin']  = round($ajustesAdmin, 2);
            $profile->update(['metrics' => $metrics]);
        }
    }
}
