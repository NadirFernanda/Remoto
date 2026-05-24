<?php

namespace App\Modules\Admin\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonateController extends Controller
{
    public function start(User $user)
    {
        $admin = Auth::user();

        if ($user->role === 'admin') {
            abort(403, 'Não é possível aceder como outro administrador.');
        }

        Session::put('impersonating_admin_id', $admin->id);

        Auth::login($user);

        return redirect('/')->with('impersonation_notice', 'A aceder como ' . $user->name . '. Clique em "Sair" para voltar à conta admin.');
    }

    public function stop()
    {
        $adminId = Session::pull('impersonating_admin_id');

        if (!$adminId) {
            return redirect()->route('admin.support');
        }

        $admin = User::findOrFail($adminId);
        Auth::login($admin);

        return redirect()->route('admin.support')->with('success', 'Voltou à conta de administrador.');
    }
}
