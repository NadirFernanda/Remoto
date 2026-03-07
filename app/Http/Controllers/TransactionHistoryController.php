<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class TransactionHistoryController extends Controller
{
    /**
     * Exibe o histórico de transações do usuário (cliente ou freelancer).
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }
        $asClient = Service::where('cliente_id', $user->id)->orderByDesc('created_at')->get();
        $asFreelancer = Service::where('freelancer_id', $user->id)->orderByDesc('created_at')->get();
        return view('transactions.history', [
            'asClient' => $asClient,
            'asFreelancer' => $asFreelancer,
        ]);
    }
}
