<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Service;
use App\Models\WalletLog;

/**
 * Corrige projectos de negociação directa (service_type=direct_invite) que já
 * receberam o primeiro pagamento em escrow mas ficaram presos em
 * 'negotiating'/'accepted' em vez de avançar para 'in_progress'.
 *
 * Motivo: durante algum tempo, o ecrã "Confirmar Valor Acordado" do chat
 * decidia se era "primeiro pagamento" (cobra o valor total) ou "ajuste"
 * (cobra só a diferença) com base no status do projecto. Projectos afectados
 * por bugs anteriores desta app podem ter recebido o pagamento sem o status
 * avançar correctamente — o que faz o ecrã voltar a tratá-los como se nada
 * tivesse sido pago, cobrando o valor novo por inteiro outra vez.
 *
 * Como o wallet_logs não guarda service_id (só descrição em texto), a
 * correspondência é feita por cliente + tipo + título do projecto na
 * descrição — por isso o comando corre sempre em modo de pré-visualização
 * por omissão; usa --apply para gravar depois de reveres a lista.
 *
 * Usage:
 *   php artisan servicos:corrigir-negociacoes-pagas
 *   php artisan servicos:corrigir-negociacoes-pagas --apply
 */
class BackfillDirectNegotiationStatus extends Command
{
    protected $signature = 'servicos:corrigir-negociacoes-pagas {--apply : Aplica as correcções (por omissão só mostra o que seria alterado)}';
    protected $description = 'Corrige projectos de negociação directa presos em negotiating/accepted apesar de já terem escrow pago';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        if (!$apply) {
            $this->warn('[PRÉ-VISUALIZAÇÃO] Nenhuma alteração será gravada. Usa --apply para aplicar.');
        }

        $candidatos = Service::whereIn('status', ['negotiating', 'accepted'])
            ->where('service_type', 'direct_invite')
            ->whereNotNull('freelancer_id')
            ->get();

        if ($candidatos->isEmpty()) {
            $this->info('Nenhum projecto de negociação directa pendente encontrado.');
            return self::SUCCESS;
        }

        $corrigidos = 0;

        foreach ($candidatos as $service) {
            $log = WalletLog::where('user_id', $service->cliente_id)
                ->where('tipo', 'escrow_retido')
                ->where('descricao', 'like', '%projecto: ' . $service->titulo . '%')
                ->orderByDesc('created_at')
                ->first();

            if (!$log) {
                continue;
            }

            $this->line(sprintf(
                '#%d "%s" — status actual: %s | valor serviço: %s Kz | escrow encontrado: %s Kz (log #%d, %s)',
                $service->id,
                $service->titulo,
                $service->status,
                number_format((float) $service->valor, 2, ',', '.'),
                number_format(abs((float) $log->valor), 2, ',', '.'),
                $log->id,
                $log->created_at->format('d/m/Y H:i')
            ));

            if ($apply && $service->payment_status === 'paid') {
                $service->status = 'in_progress';
                $service->save();
                $this->info('  -> corrigido para in_progress');
            } elseif ($apply) {
                $this->warn('  -> ignorado: pagamento não confirmado');
            }

            $corrigidos++;
        }

        if ($corrigidos === 0) {
            $this->info('Nenhum projecto preso encontrado com escrow já pago — nada a corrigir.');
            return self::SUCCESS;
        }

        $this->newLine();
        if ($apply) {
            $this->info($corrigidos . ' projecto(s) corrigido(s) com sucesso.');
        } else {
            $this->comment($corrigidos . ' projecto(s) encontrado(s) acima. Revê a lista e corre novamente com --apply para aplicar a correcção.');
        }

        return self::SUCCESS;
    }
}
