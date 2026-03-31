<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Infoproduto;
use App\Models\InfoprodutoPatrocinio;
use App\Models\Wallet;
use App\Models\WalletLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
            'status'   => 'em_moderacao',
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
            $this->feedback     = 'Produto atualizado! Enviado novamente para moderação.';
        } else {
            Infoproduto::create(array_merge($data, [
                'freelancer_id' => $user->id,
                'slug'  => Str::slug($this->titulo) . '-' . Str::random(6),
            ]));
            $this->feedback = 'Produto criado e enviado para moderação. Será publicado após aprovação.';
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
        $this->sponsoring        = $id;
        $this->dias              = 3;
        $this->showSponsorModal  = true;
    }

    public function valorPatrocinio(): float
    {
        return max(1, $this->dias) * \App\Services\FeeService::patrocinioDiario();
    }

    public function confirmarPatrocinio(): void
    {
        $this->validate(['dias' => 'required|integer|min:1|max:365']);

        $user   = auth()->user();
        $wallet = $user->wallet;
        $valor  = $this->valorPatrocinio();

        if (!$wallet || $wallet->saldo < $valor) {
            $this->feedbackType      = 'error';
            $this->feedback          = 'Saldo insuficiente. Recarregue a sua carteira antes de patrocinar.';
            $this->showSponsorModal  = false;
            return;
        }

        $produto = Infoproduto::where('freelancer_id', $user->id)
            ->where('status', 'ativo')
            ->findOrFail($this->sponsoring);

        DB::transaction(function () use ($produto, $user, $wallet, $valor) {
            // Cancel any running sponsorship for this product
            InfoprodutoPatrocinio::where('infoproduto_id', $produto->id)
                ->where('status', 'ativo')
                ->update(['status' => 'cancelado']);

            $inicio = Carbon::today();
            $fim    = $inicio->copy()->addDays($this->dias - 1);

            InfoprodutoPatrocinio::create([
                'infoproduto_id' => $produto->id,
                'user_id'        => $user->id,
                'data_inicio'    => $inicio,
                'data_fim'       => $fim,
                'dias'           => $this->dias,
                'valor_total'    => $valor,
                'status'         => 'ativo',
            ]);

            $wallet->decrement('saldo', $valor);

            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$valor,
                'tipo'      => 'patrocinio',
                'descricao' => "Patrocínio do infoproduto \"{$produto->titulo}\" por {$this->dias} dia(s) — Kz " . number_format($valor, 0, ',', '.') . '.',
            ]);
        });

        $this->feedbackType     = 'success';
        $this->feedback         = "Patrocínio ativo! Kz " . number_format($valor, 0, ',', '.') . " debitados. O produto ficará em destaque por {$this->dias} dia(s).";
        $this->showSponsorModal = false;
        $this->sponsoring       = null;
    }

    public function cancelarSponsor(): void
    {
        $this->showSponsorModal = false;
        $this->sponsoring       = null;
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

        return view('livewire.freelancer.loja', [
            'produtos' => $produtos,
            'wallet'   => $user->wallet,
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Minha Loja']);
    }
}
