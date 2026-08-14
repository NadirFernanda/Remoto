<?php

namespace App\Livewire\Loja;

use Livewire\Component;
use App\Models\Infoproduto;
use Illuminate\Support\Facades\Storage;

class ProdutoDetalhe extends Component
{
    public Infoproduto $produto;

    public function mount(Infoproduto $produto): void
    {
        if ($produto->status !== 'ativo') {
            abort(404);
        }
        $this->produto = $produto;
    }

    public function downloadArquivo()
    {
        $user = auth()->user();

        if (!$user || !$this->produto->jaCompradoPor($user->id)) {
            abort(403);
        }

        return Storage::disk('private')->download(
            $this->produto->arquivo_path,
            basename($this->produto->arquivo_path)
        );
    }

    public function render()
    {
        $jaComprado = auth()->check() && $this->produto->jaCompradoPor(auth()->id());
        $patrocinado = $this->produto->isPatrocinado();

        $relacionados = Infoproduto::where('status', 'ativo')
            ->where('id', '!=', $this->produto->id)
            ->where(function ($q) {
                $q->where('freelancer_id', $this->produto->freelancer_id)
                  ->orWhere('tipo', $this->produto->tipo);
            })
            ->with(['freelancer:id,name,profile_photo'])
            ->orderByRaw('freelancer_id = ? DESC', [$this->produto->freelancer_id])
            ->orderByDesc('vendas_count')
            ->limit(4)
            ->get();

        return view('livewire.loja.produto-detalhe', [
            'jaComprado'   => $jaComprado,
            'patrocinado'  => $patrocinado,
            'relacionados' => $relacionados,
        ])->layout('layouts.dashboard', ['dashboardTitle' => '']);
    }
}
