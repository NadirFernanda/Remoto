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

    // Tipos de WalletLog que representam rendimento real do freelancer
    private const REVENUE_TIPOS = [
        'ganho_servico',               // pagamento libertado pelo cliente (ServiceEscrowController)
        'reembolso_parcial_freelancer',// freelancer retém parte num reembolso parcial
        'comissao_afiliado',           // comissão por referência
        'ajuste_admin',                // crédito manual do admin (filtramos valor > 0 abaixo)
    ];

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

        // Soma de todos os créditos de rendimento (colunas: valor, tipo — não amount/type)
        $receitaBruta = WalletLog::where('user_id', $this->user->id)
            ->whereIn('tipo', self::REVENUE_TIPOS)
            ->where('valor', '>', 0)
            ->sum('valor');

        // Subtrai chargebacks (dinheiro congelado após estorno bancário)
        $chargebacks = WalletLog::where('user_id', $this->user->id)
            ->where('tipo', 'chargeback_congelado')
            ->sum('valor'); // valor é negativo neste tipo, então sum() já é negativo

        $receitaLiquida = (float)$receitaBruta + (float)$chargebacks; // chargebacks é negativo

        $profile = FreelancerProfile::where('user_id', $this->user->id)->first();

        if ($profile) {
            $metrics                   = $profile->metrics ?? [];
            $metrics['receita_total']  = round(max(0, $receitaLiquida), 2);
            $metrics['receita_bruta']  = round((float)$receitaBruta, 2);
            $profile->update(['metrics' => $metrics]);
        }
    }
}
