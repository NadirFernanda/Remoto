<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterClientRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Events\AffiliateCommissionEarned;

class RegisterController extends Controller
{
    public function registerClient(RegisterClientRequest $request)
    {
        $validated = $request->validated();

        // Gera código de afiliado único para o novo utilizador
        do {
            $affiliateCode = strtoupper(Str::random(8));
        } while (User::where('affiliate_code', $affiliateCode)->exists());

        $user = User::create([
            'name'           => strip_tags($validated['name']),
            'email'          => $validated['email'],
            'password'       => bcrypt($validated['password']),
            'affiliate_code' => $affiliateCode,
        ]);
        // role atribuído explicitamente — não está em $fillable (OWASP A03)
        $user->role = $validated['role'] === 'freelancer' ? 'freelancer' : 'cliente';
        $user->save();

        // Processa referral: lê da URL ou do cookie de 30 dias
        $ref = $request->query('ref') ?: $request->cookie('affiliate_ref');
        if ($ref) {
            (new AffiliateService())->recordReferral($user, strtoupper(trim($ref)), $request);
        }

        // $user->sendEmailVerificationNotification(); // Desativado: fluxo OTP

        event(new \App\Events\ClientRegistered($user));

        return redirect('/login')->with('status', 'Cadastro realizado! Faça login com seu e-mail e senha.');
    }

    public function showFreelancerForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        try {
            // Gera código de afiliado único para o novo utilizador
            do {
                $affiliateCode = strtoupper(Str::random(8));
            } while (User::where('affiliate_code', $affiliateCode)->exists());

            $user = User::create([
                'name'           => strip_tags($validated['name']),
                'email'          => $validated['email'],
                'password'       => bcrypt($validated['password']),
                'affiliate_code' => $affiliateCode,
            ]);
            // role atribuído explicitamente — não está em $fillable (OWASP A03)
            // 'creator' foi integrado no freelancer — apenas 2 roles de registo
            $user->role = $validated['role'] === 'freelancer' ? 'freelancer' : 'cliente';
            $user->save();

            // Seed multi-profile flags: freelancer tem automaticamente acesso ao módulo de criador
            $profileFlags = match ($user->role) {
                'freelancer' => ['has_freelancer_profile' => true, 'has_creator_profile' => true],
                'cliente'    => ['has_cliente_profile'    => true],
                default      => [],
            };
            if ($profileFlags) {
                $user->update($profileFlags);
            }

            // Processa referral: lê da URL (?ref=) ou do cookie de 30 dias
            $ref = $request->query('ref') ?: $request->cookie('affiliate_ref');
            if ($ref) {
                (new AffiliateService())->recordReferral($user, strtoupper(trim($ref)), $request);
            }

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
