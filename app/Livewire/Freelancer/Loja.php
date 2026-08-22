<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Jobs\InitiateAppyPaySponsorshipChargeJob;
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
            $data['capa_path'] = \App\Services\ImageOptimizer::store($this->capa, 'infoprodutos/capas', 'public', maxWidth: 1000, quality: 82);
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

        InitiateAppyPaySponsorshipChargeJob::dispatch(
            $checkout,
            $this->sponsor_phone_number,
            $valor,
            'Patrocínio de "' . $produto->titulo . '" #' . $checkout->id,
            strtoupper(Str::random(12))
        );

        $this->sponsor_checkout_id = $checkout->id;
        $this->sponsor_step        = 'waiting';
    }

    /** Chamado via wire:poll no modal — confirma o estado directamente na AppyPay. */
    public function checkSponsorAppyPayStatus(): void
    {
        if (!$this->sponsor_checkout_id) {
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

        // O job em segundo plano ainda pode não ter conseguido o charge_id —
        // sem ele não há nada para consultar ainda; o próximo ciclo tenta de novo.
        $chargeId = $this->sponsor_charge_id ?: $checkout->appypay_charge_id;
        if (!$chargeId) {
            return;
        }
        $this->sponsor_charge_id = $chargeId;

        $charge = (new AppyPayGateway())->getCharge($chargeId);
        if ($charge['success'] && in_array(strtolower((string) $charge['status']), ['paid', 'completed', 'success', 'approved'], true)) {
            app(\App\Modules\Payments\Services\AppyPayReconciliationService::class)->markPaidByChargeId($chargeId);
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

    public function render()
    {
        $user    = auth()->user();
        $produtos = Infoproduto::where('freelancer_id', $user->id)
            ->withCount('compras')
            ->orderByDesc('created_at')
            ->get();

        // Estatística informativa — o saque em si é feito só no Painel Financeiro
        // (/freelancer/financeiro), que é a única fonte de verdade do saldo real.
        $totalGanhoLoja = \App\Models\InfoprodutoCompra::whereHas('infoproduto', fn($q) => $q->where('freelancer_id', $user->id))
            ->sum('valor_freelancer');

        return view('livewire.freelancer.loja', [
            'produtos'       => $produtos,
            'wallet'         => $user->wallet,
            'totalGanhoLoja' => $totalGanhoLoja,
        ])->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
