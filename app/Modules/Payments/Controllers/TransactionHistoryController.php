<?php

namespace App\Modules\Payments\Controllers;

use App\Models\WalletLog;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionHistoryController extends Controller
{
    /**
     * Exibe o histórico de movimentos de carteira do utilizador autenticado.
     */
    public function index()
    {
        $user = Auth::user();

        $transactions = WalletLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('transactions.history', compact('transactions'));
    }
}
