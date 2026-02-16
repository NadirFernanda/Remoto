<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisterController extends Controller
{
    public function registerClient(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:client',
        ]);

        // Gera código de afiliado aleatório
        $affiliateCode = strtoupper(substr(bin2hex(random_bytes(8)), 0, 8));

        // Captura código de afiliado da URL
        $ref = $request->query('ref');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'affiliate_code' => $affiliateCode,
        ]);

        // Se veio ref, registra indicação com segurança
        if ($ref) {
            $affiliate = User::where('affiliate_code', $ref)
                ->where('role', $validated['role'] === 'client' ? 'freelancer' : 'client')
                ->where('status', 'active')
                ->first();
            // Impede auto-indicação
            if ($affiliate && $affiliate->id !== $user->id) {
                // Verifica se já existe indicação para esse usuário
                $alreadyReferred = \App\Models\Referral::where('user_id', $user->id)->exists();
                // Limite de indicações por IP: 10 por dia
                $ip = $request->ip();
                $today = now()->startOfDay();
                $ipCount = \App\Models\Referral::where('ip_address', $ip)
                    ->where('created_at', '>=', $today)
                    ->count();
                if (!$alreadyReferred && $ipCount < 10) {
                    \App\Models\Referral::create([
                        'user_id' => $user->id,
                        'affiliate_id' => $affiliate->id,
                        'ip_address' => $ip,
                        'user_agent' => $request->userAgent(),
                    ]);
                    // Creditar comissão ao afiliado
                    $wallet = $affiliate->wallet;
                    if ($wallet) {
                        $wallet->saldo += 300;
                        $wallet->save();
                        \App\Models\WalletLog::create([
                            'user_id' => $affiliate->id,
                            'wallet_id' => $wallet->id,
                            'valor' => 300,
                            'tipo' => 'comissao_afiliado',
                            'descricao' => 'Comissão por indicação de usuário ID ' . $user->id,
                        ]);
                    }
                }
            }
        }

    	// $user->sendEmailVerificationNotification(); // Desativado: fluxo OTP

        event(new \App\Events\ClientRegistered($user));

        return redirect('/login')->with('status', 'Cadastro realizado! Faça login com seu e-mail e senha.');
    }
    public function showFreelancerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:freelancer,cliente',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role' => $validated['role'],
            ]);

            // $user->sendEmailVerificationNotification(); // Desativado: fluxo OTP

            // Dispara evento para ações pós-cadastro
            if ($user->role === 'freelancer') {
                event(new \App\Events\FreelancerRegistered($user));
            } else {
                event(new \App\Events\ClientRegistered($user));
            }

            return redirect('/login')->with('status', 'Cadastro realizado! Faça login com seu e-mail e senha.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['Ocorreu um erro ao cadastrar. Tente novamente. Detalhe: ' . $e->getMessage()]);
        }
    }
}
