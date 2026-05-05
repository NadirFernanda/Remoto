<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\Service;
use App\Models\User;
use App\Modules\Admin\Services\AuditLogger;
use App\Modules\Payments\Services\PaymentGateway;
use App\Notifications\PaymentRetryFailedNotification;
use App\Notifications\PaymentRetrySuccessNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RetryPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    // Espera 1 hora entre cada tentativa (Laravel aplica automaticamente via backoff)
    public array $backoff = [3600, 3600];

    public int $timeout = 120;

    public function __construct(
        private readonly Service $service,
        private readonly User $user,
        private readonly float $amount,
        private readonly array $paymentData = []
    ) {}

    public function handle(): void
    {
        $paymentResult = (new PaymentGateway())->charge(array_merge([
            'amount'      => $this->amount,
            'description' => 'Retry: Pagamento do serviço "' . $this->service->titulo . '"',
        ], $this->paymentData));

        if (!empty($paymentResult['success'])) {
            $transactionId = $paymentResult['transaction_id'] ?? null;

            $this->service->status          = 'published';
            $this->service->payment_status  = 'paid';
            $this->service->transaction_id  = $transactionId;
            $this->service->save();

            // Notificação in-app ao utilizador
            Notification::create([
                'user_id' => $this->user->id,
                'type'    => 'payment_retry_success',
                'title'   => 'Pagamento processado',
                'message' => 'O teu pagamento de ' . number_format($this->amount, 0, ',', '.') . ' Kz'
                    . ' para o projeto "' . $this->service->titulo . '" foi processado com sucesso na segunda tentativa.',
            ]);

            // Email ao utilizador
            $this->user->notify(new PaymentRetrySuccessNotification($this->service, $this->amount));

            // Notificar freelancers disponíveis
            NotifyFreelancersOfNewProject::dispatch($this->service);

            AuditLogger::log(
                'payment_retry_success',
                "Retry bem-sucedido — serviço #{$this->service->id}, utilizador {$this->user->name}, valor: {$this->amount} Kz",
                'Service',
                $this->service->id
            );

            return;
        }

        // Falha nesta tentativa
        if ($this->attempts() >= $this->tries) {
            // Última tentativa: falha definitiva
            $this->service->payment_status = 'failed';
            $this->service->save();

            // Notificação in-app ao utilizador
            Notification::create([
                'user_id' => $this->user->id,
                'type'    => 'payment_failed_final',
                'title'   => 'Pagamento não processado',
                'message' => 'Não foi possível processar o teu pagamento após várias tentativas.'
                    . ' Por favor, tenta com outro método de pagamento ou entra em contacto com o suporte.',
            ]);

            // Notificação in-app + email para os admins (financeiro)
            $admins = User::where('role', 'admin')->limit(5)->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id'     => $admin->id,
                    'type'        => 'payment_retry_failed',
                    'target_role' => 'admin',
                    'title'       => 'Falha definitiva de pagamento',
                    'message'     => "Pagamento do utilizador \"{$this->user->name}\" falhou definitivamente."
                        . " Serviço: \"{$this->service->titulo}\" (#{$this->service->id})."
                        . " Valor: " . number_format($this->amount, 0, ',', '.') . " Kz."
                        . " Motivo: " . ($paymentResult['message'] ?? 'desconhecido'),
                ]);

                $admin->notify(new PaymentRetryFailedNotification(
                    $this->service,
                    $this->user,
                    $this->amount,
                    $paymentResult['message'] ?? 'Falha definitiva no retry.'
                ));
            }

            AuditLogger::log(
                'payment_retry_failed_final',
                "Falha definitiva após {$this->tries} tentativas — serviço #{$this->service->id}, utilizador {$this->user->name}, valor: {$this->amount} Kz",
                'Service',
                $this->service->id
            );

            return; // não throw → sem mais retries
        }

        // Tentativa não-final: lança exceção para o Laravel aplicar o backoff automático
        throw new \RuntimeException(
            $paymentResult['message'] ?? 'Falha no pagamento — tentativa ' . $this->attempts() . ' de ' . $this->tries
        );
    }
}
