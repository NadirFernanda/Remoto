<?php

namespace App\Livewire\Social;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SocialPost;
use App\Models\WalletLog;
use App\Models\Wallet as WalletModel;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MyPosts extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all | active | archived
    public ?int $confirmDeleteId = null;

    // ─── Saque das Publicações ────────────────────────────────────────
    public bool  $showSaqueModal       = false;
    public float $valorSaquePublicacoes = 0;
    public string $saqueMsg            = '';
    public string $saqueMsgType        = 'success';

    protected $paginationTheme = 'tailwind';

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function abrirSaque(): void
    {
        $this->saqueMsg      = '';
        $this->saqueMsgType  = 'success';
        $this->showSaqueModal = true;
    }

    public function fecharSaque(): void
    {
        $this->showSaqueModal        = false;
        $this->valorSaquePublicacoes = 0;
        $this->saqueMsg              = '';
    }

    public function solicitarSaque(): void
    {
        $user = Auth::user();

        $totalGanho = WalletLog::where('user_id', $user->id)
            ->where('fonte', 'publicacoes')
            ->where('valor', '>', 0)
            ->sum('valor');

        $totalSacado = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'publicacoes')
            ->sum(DB::raw('ABS(valor)'));

        $saldoDisponivel = max(0, $totalGanho - $totalSacado);

        $minAmount = (float) PlatformSetting::get('withdrawal_min_amount', 1000);

        $this->validate([
            'valorSaquePublicacoes' => ['required', 'numeric', 'min:' . $minAmount],
        ], [
            'valorSaquePublicacoes.min' => 'O valor mínimo de saque é Kz ' . number_format($minAmount, 0, ',', '.') . '.',
        ]);

        if ($this->valorSaquePublicacoes > $saldoDisponivel) {
            $this->addError('valorSaquePublicacoes', 'Saldo insuficiente. Disponível: Kz ' . number_format($saldoDisponivel, 0, ',', '.') . '.');
            return;
        }

        $pendente = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'publicacoes')
            ->exists();

        if ($pendente) {
            $this->addError('valorSaquePublicacoes', 'Já tem um saque de Publicações pendente de aprovação. Aguarde a resolução.');
            return;
        }

        $feeFixed   = (float) PlatformSetting::get('withdraw_fee_fixed', 0);
        $feePercent = (float) PlatformSetting::get('withdraw_fee_percent', 0);
        $fee        = round($feeFixed + ($this->valorSaquePublicacoes * $feePercent / 100), 2);
        $liquido    = round($this->valorSaquePublicacoes - $fee, 2);

        $wallet = WalletModel::firstOrCreate(
            ['user_id' => $user->id],
            ['saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => $minAmount, 'taxa_saque' => 0]
        );

        DB::transaction(function () use ($wallet, $user, $fee, $liquido) {
            WalletLog::create([
                'user_id'   => $user->id,
                'wallet_id' => $wallet->id,
                'valor'     => -$this->valorSaquePublicacoes,
                'tipo'      => 'saque_solicitado',
                'fonte'     => 'publicacoes',
                'descricao' => 'Saque de Publicações: Kz ' . number_format($this->valorSaquePublicacoes, 0, ',', '.') . ' — taxa: Kz ' . number_format($fee, 2, ',', '.') . ' — a receber: Kz ' . number_format($liquido, 2, ',', '.') . ' — aguarda aprovação.',
            ]);
        });

        $this->saqueMsg             = 'Saque de Kz ' . number_format($this->valorSaquePublicacoes, 0, ',', '.') . ' solicitado! Receberá Kz ' . number_format($liquido, 0, ',', '.') . ' em até 2 dias úteis.';
        $this->saqueMsgType         = 'success';
        $this->valorSaquePublicacoes = 0;
        $this->showSaqueModal       = false;
        $this->resetErrorBag('valorSaquePublicacoes');
    }

    public function deletePost(int $id): void
    {
        $post = SocialPost::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Delete associated media files
        foreach ($post->media as $media) {
            if ($media->path) {
                Storage::disk('public')->delete($media->path);
            }
        }
        $post->media()->delete();
        $post->likes()->delete();
        $post->comments()->delete();
        $post->delete();

        $this->confirmDeleteId = null;
        session()->flash('success', 'Publicação eliminada com sucesso.');
    }

    public function toggleStatus(int $id): void
    {
        $post = SocialPost::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $post->status = $post->status === 'active' ? 'archived' : 'active';
        $post->save();
    }

    public function render()
    {
        $user = Auth::user();

        $query = SocialPost::where('user_id', $user->id)
            ->with(['media', 'likes', 'comments'])
            ->latest();

        if ($this->filter === 'active') {
            $query->where('status', 'active');
        } elseif ($this->filter === 'archived') {
            $query->where('status', 'archived');
        }

        // ── Saldo publicações ────────────────────────────────────────
        $totalGanho = WalletLog::where('user_id', $user->id)
            ->where('fonte', 'publicacoes')
            ->where('valor', '>', 0)
            ->sum('valor');
        $totalSacado = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'publicacoes')
            ->sum(DB::raw('ABS(valor)'));
        $saldoPublicacoesDisponivel = max(0, $totalGanho - $totalSacado);
        $sakePendentePublicacoes    = WalletLog::where('user_id', $user->id)
            ->where('tipo', 'saque_solicitado')
            ->where('fonte', 'publicacoes')
            ->exists();

        return view('livewire.social.my-posts', [
            'posts'                      => $query->paginate(15),
            'saldoPublicacoesDisponivel' => $saldoPublicacoesDisponivel,
            'sakePendentePublicacoes'    => $sakePendentePublicacoes,
        ])->layout('layouts.dashboard', [
            'dashboardTitle' => '',
        ]);
    }
}

