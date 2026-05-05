<?php

namespace App\Modules\Admin\Controllers;

use App\Models\User;
use App\Models\WalletLog;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class WalletHistoryController extends Controller
{
    public function __invoke(User $user)
    {
        abort_if(Auth::user()?->role !== 'admin', 403);

        $wallet = $user->wallet;
        $transactions = WalletLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.wallet-history', compact('user', 'wallet', 'transactions'));
    }
}
