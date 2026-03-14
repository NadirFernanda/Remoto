<?php

namespace App\Modules\Loja\Controllers;

use App\Models\Infoproduto;
use App\Modules\Loja\Services\LojaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProdutoController extends Controller
{
    public function __construct(private LojaService $lojaService)
    {
    }

    public function comprar(Request $request, Infoproduto $produto): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($produto->status !== 'ativo') {
            abort(404);
        }

        try {
            $this->lojaService->comprar($user, $produto);
        } catch (\RuntimeException $e) {
            return redirect()->route('loja.show', $produto->slug)
                ->with('error_loja', $e->getMessage());
        }

        return redirect()->route('loja.show', $produto->slug)
            ->with('success_loja', 'Compra realizada! Faça o download abaixo.');
    }
}
