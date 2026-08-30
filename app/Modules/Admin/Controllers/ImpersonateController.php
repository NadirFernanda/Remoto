<?php

namespace App\Modules\Admin\Controllers;

use App\Models\User;
use App\Modules\Admin\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonateController extends Controller
{
    public function start(User $user, Request $request)
    {
        $admin = Auth::user();

        if ($user->role === 'admin') {
            abort(403, 'Não é possível aceder como outro administrador.');
        }

        Session::put('impersonating_admin_id', $admin->id);

        Auth::login($user);
        $request->session()->regenerate();

        // Nenhuma outra acção sensível de admin fica sem registo — esta
        // (acesso total à conta de outro utilizador) não tinha nenhum,
        // encontrado em auditoria de segurança.
        AuditLogger::log(
            'impersonation_started',
            "Admin {$admin->name} ({$admin->email}) entrou como {$user->name} ({$user->email})",
            'User',
            $user->id,
            category: 'sistema'
        );

        return redirect('/')->with('impersonation_notice', 'A aceder como ' . $user->name . '. Clique em "Sair" para voltar à conta admin.');
    }

    public function stop(Request $request)
    {
        $adminId = Session::pull('impersonating_admin_id');

        if (!$adminId) {
            return redirect()->route('admin.support');
        }

        $impersonated = Auth::user();
        $admin = User::findOrFail($adminId);
        Auth::login($admin);
        $request->session()->regenerate();

        AuditLogger::log(
            'impersonation_stopped',
            "Admin {$admin->name} ({$admin->email}) saiu da conta de {$impersonated?->name} ({$impersonated?->email})",
            'User',
            $impersonated?->id,
            category: 'sistema'
        );

        return redirect()->route('admin.support')->with('success', 'Voltou à conta de administrador.');
    }
}
