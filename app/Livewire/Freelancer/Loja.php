<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Jobs\PollAppyPayInfoprodutoPatrocinioCheckoutJob;
use App\Models\Infoproduto;
use App\Models\InfoprodutoPatrocinioCheckout;
use App\Modules\Loja\Services\PatrocinioService;
use App\Modules\Payments\Services\AppyPayGateway;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Loja extends Component
{
    use WithFileUploads;

    // ─── UI state ───────────────────────────────────────────────────
    public bool $showForm        = false;
    public bool $showSponsorModal = false;
    public string $feedback      = '';
    public string $feedbackType  = 'success';

    // ─── Product form ───────────────────────────────────────────────
    public ?int $editingId  = null;
    public string $titulo   = '';
    public string $descricao = '';
    public string $tipo     = 'ebook';
    public string $preco    = '';
    public $capa            = null;
    public $arquivo         = null;

    // ─── Sponsorship ────────────────────────────────────────────────
    public ?int $sponsoring = null;
    public int  $dias       = 3;
    public string $sponsor_payment_method = 'wallet'; // wallet | express
    public string $sponsor_phone_number   = '';
    public string $sponsor_step           = 'form'; // form | waiting
    public ?int $sponsor_checkout_id      = null;
    public ?string $sponsor_charge_id     = null;
    public string $sponsor_error          = '';

    // ─── Saque (Loja) ───────────────────────────────────────────────
    public bool  $showSaqueModal  = false;
    public float $valorSaqueLoja  = 0;
    public string $saqueMsg       = '';
    public string $saqueMsgType   = 'success';

    // ─────────────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $produto = Infoproduto::where('freelancer_id', auth()->id())->findOrFail($id);

        $this->editingId  = $id;
        $this->titulo     = $produto->titulo;
        $this->descricao  = $produto->descricao ?? '';
        $this->tipo       = $produto->tipo;
        $this->preco      = (string) $produto->preco;
        $this->capa       = null;
        $this->arquivo    = null;
        $this->showForm   = true;
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function saveProduto(): void
    {
        $rules = [
            'titulo'   => 'required|string|max:200',
            'descricao' => 'required|string|max:5000',
            'tipo'     => 'required|in:ebook,audio,literatura_digital,outro',
            'preco'    => 'required|numeric|min:5000',
            'capa'     => ($this->editingId ? 'nullable' : 'required') . '|image|max:4096',
            'arquivo'  => ($this->editingId ? 'nullable' : 'required') . '|file|max:102400',
        ];

        $messages = [
            'preco.min'      => 'O preço mínimo aceite pela plataforma é de 5.000 Kz.',
            'capa.required'  => 'É necessário uma imagem de capa para o produto.',
            'arquivo.required' => 'É necessário o ficheiro do produto (PDF, MP3, etc.).',
        ];

        $this->validate($rules, $messages);

        $user = auth()->user();

        $data = [
            'titulo'   => $this->titulo,
            'descricao' => $this->descricao,
            'tipo'     => $this->tipo,
            'preco'    => (float) $this->preco,
            'status'   => 'ativo',
        ];

        if ($this->capa) {
            $data['capa_path'] = $this->capa->store('infoprodutos/capas', 'public');
        }

        if ($this->arquivo) {
            $data['arquivo_path'] = $this->arquivo->store('infoprodutos/arquivos', 'private');
        }

        if ($this->editingId) {
            $produto = Infoproduto::where('freelancer_id', $user->id)->findOrFail($this->editingId);

            if ($this->capa && $produto->capa_path) {
                Storage::disk('public')->delete($produto->capa_path);
            }

            $produto->update($data);
            $this->feedback     = 'Produto atualizado e publicado na loja.';
        } else {
            Infoproduto::create(array_merge($data, [
                'freelancer_id' => $user->id,
                'slug'  => Str::slug($this->titulo) . '-' . Str::random(6),
            ]));
            $this->feedback = 'Produto publicado na loja com sucesso!';
        }

        $this->feedbackType = 'success';
        $this->resetForm();
        $this->showForm = false;
    }

    public function deleteProduto(int $id): void
    {
        $produto = Infoproduto::where('freelancer_id', auth()->id())->findOrFail($id);

        if ($produto->capa_path) {
            Storage::disk('public')->delete($produto->capa_path);
        }
        if ($produto->arquivo_path) {
            Storage::disk('private')->delete($produto->arquivo_path);
        }

        $produto->delete();
        $this->feedback     = 'Produto excluído.';
        $this->feedbackType = 'success';
    }

    public function openSponsor(int $id): void
    {
        $this->sponsoring              = $id;
        $this->dias                    = 3;
        $this->sponsor_payment_method  = 'wallet';
        $this->sponsor_phone_number    = '';
        $this->sponsor_step            = 'form';
        $this->sponsor_checkout_id     = null;
        $this->sponsor_charge_id       = null;
        $this->sponsor_error           = '';
        $this->showSponsorModal        = true;
    }

    public function valorPatrocinio(): float
    {
        return max(1, $this->dias) * \App\Services\FeeService::patrocinioDiario();
    }

    private function sponsoredProduto(): Infoproduto
    {
        return Infoproduto::where('freelancer_id', auth()->id())
            ->where('status', 'ativo')
            ->findOrFail($this->sponsoring);
    }

    // ── Saldo da carteira ──────────────────────────────────────────────────

    public function confirmarPatrocinio(): void
    {
        $this->validate(['dias' => 'required|integer|min:1|max:365']);

        $user    = auth()->user();
        $valor   = $this->valorPatrocinio();
        $produto = $this->sponsoredProduto();

        try {
            app(PatrocinioService::class)->patrocinar($user, $produto, $this->dias, $valor);
        } catch (\RuntimeException $e) {
            $this->feedbackType     = 'error';
            $this->feedback         = $e->getMessage();
            $this->showSponsorModal = false;
            return;
        }

        $this->feedbackType     = 'success';
        $this->feedback         = "Patrocínio ativo! Kz " . number_format($valor, 0, ',', '.') . " debitados. O produto ficará em destaque por {$this->dias} dia(s).";
        $this->showSponsorModal = false;
        $this->sponsoring       = null;
    }

    // ── AppyPay: Multicaixa Express (telefone) ─────────────────────────────

    public function chargeSponsorAppyPayPhone(): void
    {
        if ($this->sponsor_step !== 'form') {
            return;
        }

        $this->sponsor_error = '';

        $this->validate([
            'dias'                  => 'required|integer|min:1|max:365',
            'sponsor_phone_number'  => ['required', 'regex:/^9[0-9]{8}$/'],
        ], [
            'sponsor_phone_number.required' => 'Indique o número de telefone Multicaixa Express.',
            'sponsor_phone_number.regex'    => 'Número inválido — use 9 dígitos (ex: 923456789).',
        ]);

        $user    = auth()->user();
        $valor   = $this->valorPatrocinio();
        $produto = $this->sponsoredProduto();

        $checkout = InfoprodutoPatrocinioCheckout::create([
            'infoproduto_id' => $produto->id,
            'user_id'        => $user->id,
            'dias'           => $this->dias,
            'amount'         => $valor,
            'payment_status' => 'initiated',
        ]);

        $result = (new AppyPayGateway())->chargeByPhone(
            $this->sponsor_phone_number,
            $valor,
            'Patrocínio de "' . $produto->titulo . '" #' . $checkout->id,
            strtoupper(Str::random(12))
        );

        if (empty($result['success'])) {
            $checkout->update(['payment_status' => 'failed']);
            $this->sponsor_error = $result['message'] ?? 'Não foi possível iniciar o pagamento. Tente novamente.';
            return;
        }

        $checkout->update([
            'payment_method_used' => 'appypay_gpo',
            'appypay_charge_id'   => $result['charge_id'],
        ]);

        PollAppyPayInfoprodutoPatrocinioCheckoutJob::dispatch($checkout, $result['charge_id'])->delay(now()->addSeconds(30));

        $this->sponsor_checkout_id = $checkout->id;
        $this->sponsor_charge_id   = $result['charge_id'];
        $this->sponsor_step        = 'waiting';
    }

    /** Chamado via wire:poll no modal — confirma o estado directamente na AppyPay. */
    public function checkSponsorAppyPayStatus(): void
    {
        if (!$this->sponsor_checkout_id || !$this->sponsor_charge_id) {
            return;
        }

        $checkout = InfoprodutoPatrocinioCheckout::find($this->sponsor_checkout_id);
        if (!$checkout) {
            return;
        }

        if ($checkout->payment_status === 'paid') {
            $this->feedbackType     = 'success';
            $this->feedback         = "Patrocínio ativo! Kz " . number_format((float) $checkout->amount, 0, ',', '.') . " pagos via Multicaixa Express. O produto ficará em destaque por {$checkout->dias} dia(s).";
            $this->showSponsorModal = false;
            $this->sponsoring       = null;
            $this->sponsor_step     = 'form';
            return;
        }

        if ($checkout->payment_status === 'failed') {
            $this->sponsor_error = 'O pagamento não foi confirmado. Tente novamente ou escolha outro método.';
            $this->sponsor_step  = 'form';
            return;
        }

        $charge = (new AppyPayGateway())->getCharge($this->sponsor_charge_id);
        if ($charge['success'] && in_array(strtolower((string) $charge['status']), ['paid', 'completed', 'success', 'approved'], true)) {
            app(\App\Modules\Payments\Services\AppyPayReconciliationService::class)->markPaidByChargeId($this->sponsor_charge_id);
            $checkout->refresh();
            $this->feedbackType     = 'success';
            $this->feedback         = "Patrocínio ativo! Kz " . number_format((float) $checkout->amount, 0, ',', '.') . " pagos via Multicaixa Express. O produto ficará em destaque por {$checkout->dias} dia(s).";
            $this->showSponsorModal = false;
            $this->sponsoring       = null;
            $this->sponsor_step     = 'form';
        }
    }

    public function cancelarSponsor(): void
    {
        $this->showSponsorModal = false;
        $this->sponsoring       = null;
        $this->sponsor_step     = 'form';
    }

    public function getLinkProduto(int $id): string
    {
        $produto = Infoproduto::where('freelancer_id', auth()->id())->findOrFail($id);
        return route('loja.show', $produto->slug);
    }

    private function resetForm(): void
    {
        $this->editingId   = null;
        $this->titulo      = '';
        $this->descricao   = '';
        $this->tipo        = 'ebook';
        $this->preco       = '';
        $this->capa        = null;
        $this->arquivo     = null;
    }

    public function abrirSaqueLoja(): void
    {
        $this->saqueMsg    = '';
        $this->saqueMsgType = 'success';
        $this->showSaqueModal = true;
    }

    public function fecharSaqueLoja(): void
    {
        $this->showSaqueModal = false;
        $this->valorSaqueLoja = 0;
        $this->saqueMsg = '';
    }

    public function solicitarSaqueLoja(): void
    {
        $user = auth()->user();

        $totalGanhoLoja = \App\Models\InfoprodutoCompra::whereHas('infoproduto', fn($q) => $q->where('freelancer_id', $user->id))
            ->sum('valor_freelancer');

        $totalSacadoLoja = \App\Models\WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'loja')
            ->sum(\Illuminate\Support\Facades\DB::raw('ABS(valor)'));

        $saldoDisponivel = max(0, $totalGanhoLoja - $totalSacadoLoja);

        $minAmount = (float) \App\Models\PlatformSetting::get('withdrawal_min_amount', 1000);

        $this->validate([
            'valorSaqueLoja' => ['required', 'numeric', 'min:' . $minAmount],
        ], [
            'valorSaqueLoja.min' => 'O valor mínimo de saque é Kz ' . number_format($minAmount, 0, ',', '.') . '.',
        ]);

        if ($this->valorSaqueLoja > $saldoDisponivel) {
            $this->addError('valorSaqueLoja', 'Saldo da loja insuficiente. Disponível: Kz ' . number_format($saldoDisponivel, 0, ',', '.') . '.');
            return;
        }

        $jaPendente = \App\Models\WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'loja')
            ->exists();

        if ($jaPendente) {
            $this->addError('valorSaqueLoja', 'Já tem um saque pendente da Loja. Aguarde a aprovação.');
            return;
        }

        $feeFixed   = (float) \App\Models\PlatformSetting::get('withdraw_fee_fixed', 0);
        $feePercent = (float) \App\Models\PlatformSetting::get('withdraw_fee_percent', 0);
        $fee        = round($feeFixed + ($this->valorSaqueLoja * $feePercent / 100), 2);
        $liquido    = round($this->valorSaqueLoja - $fee, 2);

        $wallet = \App\Models\Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => $minAmount, 'taxa_saque' => 0]
        );

        \Illuminate\Support\Facades\DB::transaction(function () use ($wallet, $user, $fee, $liquido) {
            \App\Models\WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$this->valorSaqueLoja,
                'tipo'      => 'saque_solicitado',
                'fonte'     => 'loja',
                'descricao' => 'Saque da Loja solicitado: Kz ' . number_format($this->valorSaqueLoja, 0, ',', '.') . ' — taxa: Kz ' . number_format($fee, 2, ',', '.') . ' — a receber: Kz ' . number_format($liquido, 2, ',', '.') . ' — aguarda aprovação.',
            ]);
        });

        $this->saqueMsg    = 'Saque de Kz ' . number_format($this->valorSaqueLoja, 0, ',', '.') . ' solicitado! Receberá Kz ' . number_format($liquido, 0, ',', '.') . ' em até 2 dias úteis.';
        $this->saqueMsgType = 'success';
        $this->valorSaqueLoja = 0;
        $this->showSaqueModal = false;
        $this->feedback     = $this->saqueMsg;
        $this->feedbackType = 'success';
        $this->resetErrorBag('valorSaqueLoja');
    }

    public function render()
    {
        $user    = auth()->user();
        $produtos = Infoproduto::where('freelancer_id', $user->id)
            ->withCount('compras')
            ->orderByDesc('created_at')
            ->get();

        $totalGanhoLoja = \App\Models\InfoprodutoCompra::whereHas('infoproduto', fn($q) => $q->where('freelancer_id', $user->id))
            ->sum('valor_freelancer');
        $totalSacadoLoja = \App\Models\WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'loja')
            ->sum(\Illuminate\Support\Facades\DB::raw('ABS(valor)'));
        $saldoLojaDisponivel = max(0, $totalGanhoLoja - $totalSacadoLoja);
        $sakePendenteLoja    = \App\Models\WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'loja')
            ->exists();

        return view('livewire.freelancer.loja', [
            'produtos'            => $produtos,
            'wallet'              => $user->wallet,
            'saldoLojaDisponivel' => $saldoLojaDisponivel,
            'sakePendenteLoja'    => $sakePendenteLoja,
        ])->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
